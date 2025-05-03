<?php
session_start();
$is_logged_in = isset($_SESSION['User_Name']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

require_once "db_connect.php";

// 查詢公告資料
$news_sql = "SELECT News_ID, News_Title, News_Content, Update_At FROM News ORDER BY Update_At DESC";
$news_result = $link->query($news_sql);

// 顯示刪除成功訊息
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
if ($success_message) {
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>公告 丨 輔仁大學愛校建言捐款系統</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <style>
        body {
            max-width: 85%;
            margin: 0 auto;
            padding: 30px;
            font-size: 1.1rem;
            font-family: "Noto Serif TC", serif;
            line-height: 1.8;
            background-repeat: repeat;
            background-color: transparent;
            overflow-x: hidden;
        }

        h3 {
            margin: 20px 0;
            font-weight: bold;
        }

        .icon {
            font-size: 1.5rem;
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 10px;
            display: inline-block;
        }

        .cards {
            margin: 40px 0;
        }

        a {
            text-decoration: none;
            color: inherit; /* 保留父元素的文字顏色 */
        }

        .card {
            background-color: #f8f9fa;
            padding: 10px 30px;
            border-radius: 30px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
            font-weight: bold;
            font-size: 1.1rem;
            color: #333;
            border: 1px solid var(--bs-border-color-translucent);
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-weight: bold;
            color: rgb(0, 102, 255);
        }

        .card-body {
            position: relative;
        }

        .content-row {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .content-text {
            white-space: pre-wrap;
            min-width: 0;
            margin-right: 160px;
            flex: 1;
        }

        .action-buttons {
            position: absolute;
            top: 50%;
            right: 30px;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .action-buttons .btn-edit,
        .action-buttons .btn-delete {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            padding: 8px 50px;
            font-weight: bold;
            transition: 0.3s ease;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .btn-edit {
            background-color: rgb(156, 219, 84);
            color: white;
        }

        .btn-edit:hover {
            background-color: rgb(132, 228, 228);
            transform: scale(1.05);
        }

        .btn-delete {
            background-color: rgb(231, 66, 66);
            color: white;
        }

        .btn-delete:hover {
            background-color: rgb(132, 228, 228);
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="cards">
        <div class="row row-cols-1 g-4">
            <?php
            $news_result->data_seek(0);
            while ($row = $news_result->fetch_assoc()) {
                echo "<div class='col'>";
                
                // 整個卡片區域包裹在 <a> 標籤內，指向公告的詳細頁面
                if ($is_logged_in) {
                    echo "<a href='news_detail.php?id=" . urlencode($row['News_ID']) . "' class='text-decoration-none'>";
                } else {
                    echo "<a href='login.php' class='text-decoration-none'>";
                }
                echo "<div class='card'>";
                echo "<div class='card-body'>";
                echo "<p><h5 class='card-title'><i class='icon fa-solid fa-bullhorn icon'></i>" . htmlspecialchars($row['News_Title']) . "</h5></p>";
                echo "<div class='content-row'>";
                // 左側內容
                echo "<p class='content-text'>" . mb_substr(htmlspecialchars($row['News_Content']), 0, 75, 'UTF-8') . "...</p>";
                // 右側按鈕（僅管理員）
                if ($is_admin) {
                    echo "<div class='action-buttons'>";
                    echo "<a href='news_edit.php?id=" . urlencode($row['News_ID']) . "' class='btn btn-edit'>編輯</a>";
                    echo "<a href='news_delete.php?id=" . urlencode($row['News_ID']) . "' class='btn btn-delete' onclick='return confirm(\"確定要刪除這則公告嗎？\")'>刪除</a>";
                    echo "</div>";
                }
                echo "</div>"; // content-row
                echo "<p class='card-text mt-3'><small class='text-muted'>更新時間： " . date("Y-m-d H:i", strtotime($row['Update_At'])) . "</small></p>";
                echo "</div>"; // card-body
                echo "</div>"; // card
                echo "</a>"; // 包裹卡片區域的 <a> 標籤
                echo "</div>"; // col
            }
            ?>
        </div>
    </div>
</body>
</html>