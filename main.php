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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <style>
        body {
            max-width: 85%;
            margin: 0 auto;
            padding: 30px;
            font-size: 1.1rem;
            line-height: 1.8;
            background-repeat: repeat;
            background-color: transparent;
            overflow-x: hidden;
        }

        h3 {
            margin-top: 30px;
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

        .marquee-wrapper {
            max-width: 1000px;
            margin: 0 auto 30px auto;
        }

        #mqmain {
            background: linear-gradient(45deg, rgb(162, 198, 212), rgb(91, 137, 155));
            color: white;
            font-size: 1.1rem;
            padding: 10px;
            border-radius: 10px;
            font-weight: bold;
            overflow: hidden;
            width: 100%;
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
            background-color: rgba(40, 140, 168, 0.8);
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            padding: 20px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .card-body {
            flex-grow: 1;
            padding: 20px;
        }

        .suggestion-item {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f1f4f9;
            border-radius: 15px;
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
            margin-top: 10px;
            background-color:rgba(255, 193, 7, 0.5);
            color: black;
            font-weight: 500;
            border: none;
        }

        .btn-view:hover {
            background-color:rgba(224, 168, 0, 0.75);
            color: white;
        }
    </style>
</head>

<body>
    <h3><i class="icon fa-solid fa-bell"></i> 重要資訊</h3>
    <div class="marquee-wrapper">
        <marquee id="mqmain" scrollamount="8">7/8 系統將進行年度保養，請使用者留意。</marquee>
    </div>

    <h3><i class="icon fa-solid fa-list"></i> 建言一覽</h3>
    <div class="row">
        <!-- 最新建言 -->
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-pen-to-square"></i> 最新建言</div>
                <div class="card-body">
                    <?php while ($row = $newest_result->fetch_assoc()): ?>
                        <div class="suggestion-item">
                            <div class="suggestion-title"><?= htmlspecialchars($row['Title']) ?></div>
                            <div class="suggestion-meta">更新時間：<?= $row['Updated_At'] ?></div>
                            <a href="suggestion_detail.php?id=<?= $row['Suggestion_ID'] ?>" class="btn btn-view">查看建言</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- 熱門建言 -->
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-fire"></i> 熱門建言</div>
                <div class="card-body">
                    <?php while ($row = $popular_result->fetch_assoc()): ?>
                        <div class="suggestion-item">
                            <div class="suggestion-title"><?= htmlspecialchars($row['Title']) ?></div>
                            <div class="suggestion-meta">獲得 <?= $row['LikeCount'] ?> ❤️</div>
                            <a href="suggestion_detail.php?id=<?= $row['Suggestion_ID'] ?>" class="btn btn-view">查看建言</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 榮譽榜 -->
    <div class="honor-wrapper">
        <h3><i class="fas fa-medal"></i> 榮譽榜</h3>
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