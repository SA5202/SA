<?php
session_start();
require_once "db_connect.php";

// 確認有提供公告 ID
if (!isset($_GET['id'])) {
    die("無效的公告 ID");
}

$news_id = intval($_GET['id']);

// 查詢公告資料
$news_sql = "SELECT News_Title, News_Content, Update_At FROM News WHERE News_ID = ?";
$stmt = $link->prepare($news_sql);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($news_title, $news_content, $update_at);
$stmt->fetch();
$stmt->close();

// 若找不到公告，顯示錯誤
if (!$news_title) {
    die("公告未找到");
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>公告詳情 丨 <?= htmlspecialchars($news_title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

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

        h2 {
            margin: 20px 0;
            font-weight: bold;
        }

        .card {
            background-color: #f8f9fa;
            padding: 10px 30px;
            border-radius: 25px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
            font-weight: bold;
            font-size: 1.1rem;
            color: #333;
            border: 1px solid var(--bs-border-color-translucent);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        a {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h2><?= htmlspecialchars($news_title) ?></h2>
        <p class="text-muted"><b>更新時間： <?= date("Y-m-d H:i", strtotime($update_at)) ?></b></p>
        <div class="card mt-4 mb-4">
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($news_content)) ?></p>
            </div>
        </div>
        <a href="news.php" class="back"><b>⬅ 返回上一頁</b></a>
    </div>
    
</body>
</html>