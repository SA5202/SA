<?php
session_start();
$is_logged_in = isset($_SESSION['User_Name']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

require_once "db_connect.php";

// 查詢公告資料
$news_sql = "SELECT News_ID, News_Title, News_Content, Update_At FROM News ORDER BY Update_At DESC LIMIT 5";
$news_result = $link->query($news_sql);

// 最新建言（依照時間）
$newest_sql = "
SELECT s.Suggestion_ID, s.Title, s.Description, s.Updated_At,
       f.Facility_Type,
       b.Building_Name,
       (SELECT COUNT(*) FROM Upvote u WHERE u.Suggestion_ID = s.Suggestion_ID AND u.Is_Upvoted = 1) AS LikeCount
FROM Suggestion s
JOIN Facility f ON s.Facility_ID = f.Facility_ID
JOIN Building b ON s.Building_ID = b.Building_ID
ORDER BY s.Updated_At DESC
LIMIT 3
";
$newest_result = $link->query($newest_sql);

// 熱門建言（依照讚數）
$popular_sql = "
SELECT s.Suggestion_ID, s.Title, s.Updated_At,
       (SELECT COUNT(*) FROM Upvote u WHERE u.Suggestion_ID = s.Suggestion_ID AND u.Is_Upvoted = 1) AS LikeCount
FROM Suggestion s
ORDER BY LikeCount DESC, s.Updated_At DESC
LIMIT 3
";
$popular_result = $link->query($popular_sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>首頁 丨 輔仁大學愛校建言捐款系統</title>
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


        .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
            border-radius: 25px;
            background-color: white;
            height: auto;
            min-height: 350px;
        }

        .card .card-header {
            background-color: rgba(85, 164, 186, 0.8);
            color: white;
            font-weight: bold;
            font-size: 1.3rem;
            padding: 20px 30px;
            border-top-left-radius: 25px;
            border-top-right-radius: 25px;
        }

        .card-body {
            flex-grow: 1;
            padding: 25px;
        }

        .suggestion-item {
            position: relative;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f1f4f9;
            border-radius: 15px;
            min-height: 120px;
            transition: transform 0.2s ease-in-out;
        }

        .suggestion-item:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .suggestion-title {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .suggestion-meta {
            font-size: 0.9rem;
            font-weight: bold;
            color: gray;
        }

        .btn-view {
            position: absolute;
            bottom: 20px;
            right: 20px;
            padding: 4px 20px;
            background-color: rgba(170, 216, 224, 0.3);
            color: rgb(0, 76, 148);
            font-weight: 750;
            border-radius: 10px;
        }

        .btn-view:hover {
            background-color: rgba(0, 176, 224, 0.3);
            color: white;
        }

        .carousel-item {
            height: 200px;
            position: relative;
            background-color: #f8f9fa;
            padding: 30px 50px;
            border-radius: 25px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
            font-weight: bold;
            font-size: 1.1rem;
            color: #333;
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
            height: 100%;
            /* 讓卡片自適應內容的高度 */
            max-height: 300px;
            /* 設定一個最大高度，超過時會被限制 */
            overflow: hidden;
            /* 隱藏超過的內容 */
        }

        .carousel-inner {
            border-radius: 20px;
        }

        .announcement-card-header {
            font-weight: bold;
            color:rgb(0, 102, 255);
        }

        .carousel-indicators [data-bs-target] {
            background-color: #999;
            width: 40px;
            height: 2.5px;
            transition: background-color 0.3s ease;
        }

        .carousel-indicators .active {
            background-color:rgb(0, 174, 225);
        }

        .card-text {
            overflow: hidden;
            /* 確保文字不會超出 */
            text-overflow: ellipsis;
            /* 使用省略號來處理過長的文字 */
            display: -webkit-box;
            -webkit-line-clamp: 1;
            /* 限制顯示行數 */
            -webkit-box-orient: vertical;
        }

        .see-more {
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
        }

        .see-more:hover {
            color: #0056b3;
            text-decoration: none;
        }


        /*榮譽標章*/
        /* 榮譽榜容器 */
        .honor-wrapper {
            margin: 50px auto;
            padding: 40px 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* 頒獎台的排列 */
        .award-stand {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 40px; /* 項目之間的間距 */
            margin-top: 60px;
        }

        /* 獎項的外觀設計 */
        .award-item {
            width: 180px;
            height: 250px;
            padding: 30px;
            background: linear-gradient(to bottom, #ffffff, #e2e2e2);
            border-radius: 15px;
            color: #333;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        /* 懸停效果：獎項放大並增強陰影 */
        .award-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        /* 獎項的圖標 */
        .honor-icon {
            font-size: 30px;
            color: #f1c40f; /* 金色圖標 */
            margin-bottom: 15px;
        }

        /* 獎項標題的樣式 */
        .award-item h5 {
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 10px;
        }

        /* 金獎的特殊樣式 */
        .award-item.gold {
            background: linear-gradient(to bottom, #f9d71c, #f39c12);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.5);
            transform: translateY(-15px); /* 顯示金獎時稍微突出 */
        }

        /* 銀獎的特殊樣式 */
        .award-item.silver {
            background: linear-gradient(to bottom, #bdc3c7, #95a5a6);
            box-shadow: 0 10px 30px rgba(192, 192, 192, 0.6);
            transform: translateY(-10px); /* 顯示銀獎時稍微突出 */
        }

        /* 銅獎的特殊樣式 */
        .award-item.bronze {
            background: linear-gradient(to bottom, #cd7f32, #e67e22);
            box-shadow: 0 10px 30px rgba(205, 127, 50, 0.6);
            transform: translateY(-5px); /* 顯示銅獎時稍微突出 */
        }

    </style>
</head>

<body>
    <!-- 公告卡片 -->
    <h3><i class="icon fa-solid fa-bell"></i> 最新公告</h3>
    <div id="announcementCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <?php
            $news_result->data_seek(0);
            $indicator_index = 0;
            while ($news_result->fetch_assoc()) {
                $active_class = ($indicator_index == 0) ? "active" : "";
                echo "<button type='button' data-bs-target='#announcementCarousel' data-bs-slide-to='$indicator_index' class='$active_class' aria-label='公告 " . ($indicator_index + 1) . "'></button>";
                $indicator_index++;
            }
            ?>
        </div>

        <div class="carousel-inner">
            <?php
            $news_result->data_seek(0);
            $carousel_index = 0;
            while ($row = $news_result->fetch_assoc()):
                $active_class = ($carousel_index == 0) ? " active" : "";
                $max_length = 50;
                $content_full = htmlspecialchars($row['News_Content']);
                $link_target = "news_detail.php?id=" . urlencode($row['News_ID']);
                $content_short = (mb_strlen($content_full, 'UTF-8') > $max_length)
                    ? mb_substr($content_full, 0, $max_length-7, 'UTF-8') . "⋯ <a href='$link_target' class='see-more'>查看更多</a>"
                    : $content_full;
            ?>
                <div class="carousel-item<?= $active_class ?>">
                    <div class="announcement-card">
                        <div class="announcement-card-header">
                            <p><i class="icon fa-solid fa-bullhorn"></i> <?= htmlspecialchars($row['News_Title']) ?></p>
                        </div>
                        <div class="announcement-card-body">
                            <p class="card-text"><?= $content_short ?></p>
                            <p class="card-text"><small class="text-muted">更新時間： <?= date("Y-m-d H:i", strtotime($row['Update_At'])) ?></small></p>
                        </div>
                    </div>
                </div>
            <?php
            $carousel_index++;
            endwhile;
            ?>
        </div>
    </div>

    <h3><i class="icon fa-solid fa-list"></i> 建言一覽</h3>
    <div class="row">
        <!-- 最新建言 -->
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-header"><i class="icon fa-solid fa-pen-to-square"></i> 最新發佈</div>
                <div class="card-body">
                    <?php while ($row = $newest_result->fetch_assoc()): ?>
                        <div class="suggestion-item">
                            <div class="suggestion-title"><?= htmlspecialchars($row['Title']) ?></div>
                            <div class="suggestion-meta">更新時間：<?= date('Y-m-d H:i', strtotime($row['Updated_At'])) ?></div>
                            <a href="<?="suggestion_detail.php?id=" . $row['Suggestion_ID'] ?>" class="btn btn-view">查看建言</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- 熱門建言 -->
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-header"><i class="icon fa-solid fa-fire"></i> 熱度最高</div>
                <div class="card-body">
                    <?php while ($row = $popular_result->fetch_assoc()): ?>
                        <div class="suggestion-item">
                            <div class="suggestion-title"><?= htmlspecialchars($row['Title']) ?></div>
                            <div class="suggestion-meta">已經在網站上獲得了 <?= $row['LikeCount'] ?> 個 ❤️</div>
                            <a href="<?="suggestion_detail.php?id=" . $row['Suggestion_ID'] ?>" class="btn btn-view">查看建言</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 榮譽榜 -->
    <div class="honor-wrapper">
        <h3><i class="icon fas fa-medal"></i> 榮譽榜</h3>
        <div class="award-stand">
            <?php
            // 引入資料庫連線設定檔
            require_once "db_connect.php"; // 請確保路徑正確

            // 查詢「第一等級」榮譽標章的 SQL 語句，並從 UserAccount 表格中獲取 User_Name
            $honor_sql = "
                SELECT h.Honor_Type, u.User_Name
                FROM HonorTag h
                JOIN UserAccount u ON h.User_ID = u.User_ID
                WHERE h.Honor_Type = '第一等級'  -- 只查詢「第一等級」
                ORDER BY h.Honor_ID ASC  -- 以 ID 排序，顯示順序
            ";
            
            // 執行查詢並取得結果
            $honors_result = $link->query($honor_sql);

            // 檢查 SQL 查詢是否成功
            if ($honors_result === false) {
                die("資料庫查詢錯誤: " . $link->error);
            }

            // 檢查查詢結果是否有資料
            if ($honors_result->num_rows > 0) {
                // 顯示每個「第一等級」的獎項
                while ($honor = $honors_result->fetch_assoc()) {
                    // 設置背景顏色
                    $background_color = '#FFD700'; // 金色背景

                    // 顯示每個名次的獎項
                    echo '<div class="award-item" style="background-color: ' . $background_color . ';">';
                    echo '<h5><i class="fas fa-trophy honor-icon"></i> ' . htmlspecialchars($honor['Honor_Type']) . '</h5>';
                    echo '<p>' . htmlspecialchars($honor['User_Name']) . ' ！</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>目前沒有任何「第一等級」榮譽標章。</p>';
            }
            ?>
        </div>
    </div>



</body>

</html>