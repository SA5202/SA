<?php
session_start();
$is_logged_in = isset($_SESSION['username']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

require_once "db_connect.php";

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
    <title>輔仁大學愛校建言捐款系統</title>
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

        .honor-wrapper {
            width: 100%;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
        }

        .honor-item {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .honor-item h5 {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .honor-icon {
            color: #ffc107;
            margin-right: 10px;
        }

        .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
            border-radius: 20px;
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
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
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
            color: gray;
        }

        .btn-view {
            position: absolute;
            bottom: 20px;
            right: 20px;
            padding: 4px 20px;
            background-color: rgba(0, 230, 219, 0.4);
            color: rgb(0, 76, 148);
            font-weight: 750;
            border-radius: 10px;
        }

        .btn-view:hover {
            background-color: rgba(0, 176, 224, 0.6);
            color: white;
        }

        .carousel-item {
            position: relative;
            background-color: #f8f9fa;
            padding: 25px 30px;
            border-radius: 20px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
            font-weight: bold;
            font-size: 1.1rem;
            color: #333;
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
        }

        .carousel-inner {
            border-radius: 20px;
        }

        .carousel-indicators [data-bs-target] {
            background-color: #999;
            width: 40px;
            height: 4px;
            transition: background-color 0.3s ease;
        }

        .carousel-indicators .active {
            background-color: #007bff;
        }

    </style>
</head>

<body>
    <!-- 公告卡片 -->
    <h3><i class="icon fa-solid fa-bell"></i> 最新公告</h3>
    <div id="announcementCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <!-- 進度條 -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="公告 1"></button>
            <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="1" aria-label="公告 2"></button>
        </div>

        <!-- 輪播內容 -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="announcement-card">
                    <div class="announcement-card-header">
                        <i class="icon fa-solid fa-bullhorn"></i> 系統維護通知
                    </div>
                    <div class="announcement-card-body">
                        <p>🔧 7/8 系統將進行年度保養，請使用者留意，屆時將暫停服務。</p>
                    </div>
                </div>
            </div>

            <div class="carousel-item">
                <div class="announcement-card">
                    <div class="announcement-card-header">
                        <i class="icon fa-solid fa-bullhorn"></i> 暑假志工活動開放報名
                    </div>
                    <div class="announcement-card-body">
                        <p>🌟 歡迎同學報名暑期校園志工活動，報名截止日為 7/20，詳細資訊請見學務處官網。</p>
                    </div>
                </div>
            </div>
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
                            <a href="suggestion_detail.php?id=<?= $row['Suggestion_ID'] ?>" class="btn btn-view">查看建言</a>
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
                            <a href="suggestion_detail.php?id=<?= $row['Suggestion_ID'] ?>" class="btn btn-view">查看建言</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 榮譽榜 -->
    <div class="honor-wrapper">
        <h3><i class="icon fas fa-medal"></i> 榮譽榜</h3>
        <div class="honor-item">
            <h5><i class="fas fa-trophy honor-icon"></i> 卓越貢獻獎</h5>
            <p>感謝李珍校友捐贈百萬 為輔大永續發展注入愛與希望</p>
        </div>
        <div class="honor-item">
            <h5><i class="fas fa-trophy honor-icon"></i> 卓越貢獻獎</h5>
            <p>感謝張氏家庭百萬美金捐贈化學系及民生學院 助力輔大教育永續發展</p>
        </div>
    </div>
</body>

</html>