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
        .honor-wrapper {
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 40px;
            border: 1px solid #ccc;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* 頒獎台排列方式 */
        .award-stand {
            position: relative;
            display: flex;
            padding-top: 50px;
            justify-content: center;
            align-items: flex-end;
            gap: 20px;
            margin-top: 20px;
        }

        /* 頒獎台平台 */
        .award-stand::before {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 20px;
            background: #555;
            border-radius: 5px 5px 0 0;
            z-index: 1;
        }

        /* 獎項外觀 */
        .award-item {
            position: relative;
            width: auto;
            max-width: 250px;
            min-width: 120px;
            padding: 20px;
            background: transparent;
            color: #333;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;

            /* 載入時從下往上淡入 */
            opacity: 0;
            transform: translateY(30px);
            animation: rise 0.6s ease-out forwards;
        }
        .award-item:nth-child(1) { animation-delay: 0.4s; }
        .award-item:nth-child(2) { animation-delay: 0.6s; }
        .award-item:nth-child(3) { animation-delay: 0.8s; }

        /* 獎項內容區 */
        .award-content {
            display: flex;
            flex-direction: column;
            background: linear-gradient(to bottom, #ffffff, #e2e2e2);
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            flex-grow: 1;
            border-radius: 15px;
            max-height: 200px;
            min-height: 100px;
            width: 100%;
            box-sizing: border-box;
        }

        /* 底座共通樣式 */
        .award-base {
            width: 100%;
            padding: 12px 8px;
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            box-shadow:
                inset 0 4px 8px rgba(0, 0, 0, 0.2),  /* 內陰影讓平台有厚度 */
                0 4px 10px rgba(0, 0, 0, 0.1);       /* 外陰影讓平台立體 */
            position: relative;
            z-index: 1;
        }

        /* 金獎：最高中間台階 */
        .award-base.gold {
            height: 100px;
            background: linear-gradient(to top, #cfa700, #ffd700);
            animation: glow 2s ease-in-out infinite alternate;
        }

        /* 銀獎：右側第二高 */
        .award-base.silver {
            height: 70px;
            background: linear-gradient(to top, #a8a8a8, #e0e0e0);
            animation: glow 2.5s ease-in-out infinite alternate;
        }

        /* 銅獎：左側最矮 */
        .award-base.bronze {
            height: 50px;
            background: linear-gradient(to top, #8a4f2c, #cd7f32);
            animation: glow 3s ease-in-out infinite alternate;
        }

        /* 閃爍效果 */
        @keyframes glow {
            from {
                box-shadow:
                    inset 0 4px 8px rgba(0, 0, 0, 0.2),
                    0 0 8px rgba(255, 255, 255, 0.2);
            }
            to {
                box-shadow:
                    inset 0 4px 8px rgba(0, 0, 0, 0.3),
                    0 0 16px rgba(255, 255, 255, 0.4);
            }
        }

        /* 小圖示樣式 */
        .honor-icon {
            margin-right: 6px;
            color: white;
        }


        /* — 動畫定義 — */
        @keyframes rise {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
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
    <!-- 榮譽榜 -->
    <div class="honor-wrapper">
    <div class="award-stand">
        <?php
        require_once "db_connect.php";

        // 查詢捐款金額前 3 名的第一等級用戶
        $honor_sql = "
            SELECT h.Honor_Type, u.User_Name, SUM(d.Donation_Amount) AS Total_Donated
            FROM HonorTag h
            JOIN UserAccount u ON h.User_ID = u.User_ID
            LEFT JOIN Donation d ON h.User_ID = d.User_ID
            WHERE h.Honor_Type = '第一等級'
            GROUP BY h.User_ID
            ORDER BY Total_Donated DESC
            LIMIT 3;
        ";

        $honors_result = $link->query($honor_sql);

        if ($honors_result === false) {
            die("資料庫查詢錯誤: " . $link->error);
        }

        $honors = [];

        // 儲存前 3 名（或缺席名次）
        while ($row = $honors_result->fetch_assoc()) {
            $honors[] = $row;
        }

        // 若不足 3 位，就補隨機資料
        if (count($honors) < 3) {
            $remaining = 3 - count($honors);

            // 找出尚未在前3名的第一等級用戶來補位
            $filled_ids = array_map(function ($h) {
                return "'" . $h['User_Name'] . "'";
            }, $honors);

            $exclude_clause = count($filled_ids) > 0 ? "AND u.User_Name NOT IN (" . implode(",", $filled_ids) . ")" : "";

            $fallback_sql = "
                SELECT h.Honor_Type, u.User_Name
                FROM HonorTag h
                JOIN UserAccount u ON h.User_ID = u.User_ID
                WHERE h.Honor_Type = '第一等級' $exclude_clause
                ORDER BY RAND()
                LIMIT $remaining;
            ";

            $fallback_result = $link->query($fallback_sql);
            while ($row = $fallback_result->fetch_assoc()) {
                $honors[] = $row;
            }
        }

        // 固定順序：銅（金銀銅顯示位置），但名次仍是「第一名」「第二名」「第三名」
        $positions = [
            ['rank' => '第三名', 'color' => 'bronze'],
            ['rank' => '第一名', 'color' => 'gold'],
            ['rank' => '第二名', 'color' => 'silver'],
        ];

        // 實際顯示順序：左-銅，中-金，右-銀
        for ($i = 0; $i < 3; $i++) {
            // 金→銀→銅 對應資料順序：$honors[0], $honors[1], $honors[2]
            $dataIndex = match ($i) {
                0 => 2, // 左邊顯示第三名（銅）
                1 => 0, // 中間顯示第一名（金）
                2 => 1, // 右邊顯示第二名（銀）
            };

            $data = $honors[$dataIndex] ?? null;
            $rank_name = $positions[$i]['rank'];
            $color_class = $positions[$i]['color'];

            echo '<div class="award-item">';
            echo '<div class="award-content">';
            echo '<h5>' . $rank_name . '</h5>';

            if ($data) {
                echo '<p>' . htmlspecialchars($data['User_Name']) . '！</p>';
                if (isset($data['Total_Donated'])) {
                    echo '<p>捐款總額：' . (empty($data['Total_Donated']) ? '無捐款紀錄' : htmlspecialchars($data['Total_Donated']) . ' 元') . '</p>';
                }
            } else {
                echo '<p>尚未有人</p>';
            }

            echo '</div>';
            echo '<div class="award-base ' . $color_class . '"><i class="fas fa-trophy honor-icon"></i> ' . $rank_name . '</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>





</body>

</html>