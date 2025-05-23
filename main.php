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
            margin: 30px 0;
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
            font-weight: 600;
            color: #888;
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


       /* 榮譽標章容器 */
        /* 榮譽榜容器樣式 */
        .honor-wrapper {
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.95); /* 淺色背景區別於白色 */
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }

        /* 排名卡片的樣式 */
        .rank-card {
            background-color: transparent; /* 透明背景 */
            border: none; /* 無邊框 */
            padding: 20px;
            transition: transform 0.3s ease;
        }

        /* 卡片的懸浮效果 */
        .rank-card:hover {
            transform: scale(1.05);
        }

        /* 圖片內部的間隔 */
        .avatar-wrapper {
            position: relative;
            display: inline-block;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.7), rgba(200, 200, 200, 0.7)); /* 輕微白色漸層 */
            overflow: hidden;
            margin-bottom: 15px;
            padding: 5px; /* 圖片和外框之間的間隔 */
        }

        /* 外層容器新增一層包裹圖片 */
        .avatar-wrapper img {
            width: 100%; /* 保證圖片充滿容器 */
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        /* 用戶名稱樣式 */
        .user-name {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        /* 用戶得分樣式 */
        .user-score {
            font-size: 1rem;
            color: #888;
        }

        /* 排名標籤 */
        .rank-label {
            font-size: 1.1rem;
            font-weight: bold;
            margin-top: 10px;
        }

        /* 第一名標籤 - 金色 */
        .rank-label-1 {
            color: #FFD700; /* 金色 */
        }

        /* 第二名標籤 - 銀色 */
        .rank-label-2 {
            color: #C0C0C0; /* 銀色 */
        }

        /* 第三名標籤 - 銅色 */
        .rank-label-3 {
            color: #CD7F32; /* 銅色 */
        }

        /* 排名卡片的透明背景設置（包括第一名） */
        .rank-1, .rank-2, .rank-3 {
            background-color: transparent; /* 透明背景 */
        }

        /* 第一名特殊樣式 (中間突出) */
        .rank-1 {
            background-color: transparent; /* 透明背景 */
        }

        /* 第二名 */
        .rank-2 {
            background-color: transparent; /* 透明背景 */
        }

        /* 第三名 */
        .rank-3 {
            background-color: transparent; /* 透明背景 */
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
                            <div class="suggestion-meta">已經在網站上獲得了
                                <?= ($row['LikeCount'] >= 10000) 
                                    ? number_format($row['LikeCount'] / 10000, 1) . ' 萬個 ❤️' 
                                    : $row['LikeCount'] . ' 個 ❤️'; ?>
                            </div>
                            <a href="<?="suggestion_detail.php?id=" . $row['Suggestion_ID'] ?>" class="btn btn-view">查看建言</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <h3><i class="icon fas fa-medal"></i> 本月榮譽榜</h3> 
<div class="honor-wrapper">
    <div class="row justify-content-center">
        <?php
        // 連接資料庫
        require_once "db_connect.php";

        // 設定預設圖片
        $default_avatar = 'https://i.pinimg.com/736x/15/46/d1/1546d15ce5dd2946573b3506df109d00.jpg';

        // 查詢捐款金額最多的前三名用戶，改為選擇 Nickname 和 Avatar
        $ranking_sql = "
            SELECT u.Nickname, u.Avatar, SUM(d.Donation_Amount) AS total_donation
            FROM Donation d
            JOIN UserAccount u ON d.User_ID = u.User_ID
            WHERE MONTH(d.Donation_Date) = MONTH(CURRENT_DATE()) AND YEAR(d.Donation_Date) = YEAR(CURRENT_DATE())
            GROUP BY u.User_ID
            ORDER BY total_donation DESC
            LIMIT 3;
        ";

        // 執行查詢
        $ranking_result = $link->query($ranking_sql);

        // 儲存所有排名資料
        $rankings = [];
        if ($ranking_result && $ranking_result->num_rows > 0) {
            while ($row = $ranking_result->fetch_assoc()) {
                $rankings[] = $row;
            }
        }

        // 設定顯示的排名順序：第二名、第一名、第三名
        $display_order = [1, 0, 2];

        // 預設資料 (尚未有人捐款)
        $default_ranking = [
            'Nickname' => '尚未有人捐款',
            'Avatar' => $default_avatar,
            'total_donation' => '尚未有人捐款'
        ];

        // 根據顯示順序動態顯示排名
        foreach ($display_order as $rank_index) {
            // 如果有捐款者，則使用捐款者的資料，否則顯示預設資料
            $ranking_data = isset($rankings[$rank_index]) ? $rankings[$rank_index] : $default_ranking;

            $nickname = $ranking_data['Nickname'];
            $avatar = $ranking_data['Avatar'];
            $donation_amount = $ranking_data['total_donation'];
            $rank = $rank_index + 1; // 排名從 1 開始

            echo "<div class='col-md-4 text-center'>
                    <div class='rank-card rank-{$rank}'>
                        <div class='avatar-wrapper avatar-{$rank}'>
                            <img src='{$avatar}' alt='User {$rank}' class='avatar'>
                        </div>
                        <h5 class='user-name'>{$nickname}</h5>
                        <p class='user-score'>捐贈金額：{$donation_amount}</p>
                        <p class='rank-label rank-label-{$rank}'>第{$rank}名</p>
                    </div>
                </div>";
        }
        ?>
    </div>
</div>



</body>

</html>