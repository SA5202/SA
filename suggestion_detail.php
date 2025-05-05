<?php
session_start();
require_once "db_connect.php";

$is_logged_in = isset($_SESSION['User_Name']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "無效的建言 ID";
    exit;
}

$id = intval($_GET['id']);

$sql = "
SELECT s.Suggestion_ID, s.Title, s.Description, s.Updated_At,s.User_ID,u.User_Name,
       f.Facility_Type,
       b.Building_Name,
       (SELECT COUNT(*) FROM Upvote u WHERE u.Suggestion_ID = s.Suggestion_ID AND u.Is_Upvoted = 1) AS LikeCount
FROM Suggestion s
JOIN Facility f ON s.Facility_ID = f.Facility_ID
JOIN Building b ON s.Building_ID = b.Building_ID
JOIN Useraccount u ON s.User_ID = u.User_ID
WHERE s.Suggestion_ID = ? 
";

$stmt = $link->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "找不到該建言";
    exit;
}

$user_id = $_SESSION['User_ID'] ?? null;
$hasLiked = false;

if ($user_id) {
    $like_sql = "SELECT Is_Upvoted FROM Upvote WHERE User_ID = ? AND Suggestion_ID = ? AND Is_Upvoted = 1";
    $like_stmt = $link->prepare($like_sql);
    $like_stmt->bind_param("ii", $user_id, $id);
    $like_stmt->execute();
    $like_result = $like_stmt->get_result();
    $hasLiked = $like_result->num_rows > 0;
}

?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>建言詳情 | <?= htmlspecialchars($row['Title']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            max-width: 80%;
            margin: 80px auto;
            padding: 20px;
            font-family: "Noto Serif TC", serif;
            background-color: transparent;
            color: #333;
        }

        .card {
            background: white;
            border-radius: 40px;
            padding: 30px 60px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            display: flex;
            gap: 40px;
            transition: transform 0.2s ease-in-out;
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h3 {
            font-size: 1.6rem;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .meta {
            font-size: 1rem;
            font-weight: 600;
            color: #666;
            margin-bottom: 1.5rem;
        }

        .description {
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .likes {
            margin-top: 1.5rem;
            font-size: 1rem;
            color: #cc3333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .likes-info {
            display: inline-flex;
            align-items: center;
        }

        a.back {
            display: inline-block;
            margin: 1.5rem 0;
            font-size: 1.1rem;
            text-decoration: none;
            color: #3498db;
        }

        a.back:hover {
            color: #2980b9;
        }

        a.author-link {
            color: #444;
            /* 比黑淡一點的深灰 */
            font-weight: 500;
            text-decoration: none;
        }

        a.author-link:hover {
            color: #2980b9;
            /* 保留 hover 回饋感 */
            text-decoration: underline;
        }

        .timeline {
            height: 190px;
            flex: 1;
            position: relative;
            top: 20px;
            margin: 0;
            padding: 0;
            list-style: none;
            border-left: 3px solid #ccc;
        }

        .timeline li {
            position: relative;
            margin-bottom: 30px;
            padding-left: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
        }

        .timeline li::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ccc;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* 微妙的陰影效果 */
            transition: all 0.3s ease; /* 添加平滑過渡 */
        }

        .timeline li.active::before {
            background: #4CAF50;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3); /* 更強烈的陰影效果，讓進度點有立體感 */
        }

        .timeline li .timestamp {
            font-size: 12px;
            color: #999;
            margin-top: 10px;  /* 讓時間顯示距離進度點一些距離 */
            position: absolute;
            bottom: -18px;  /* 固定顯示在進度條下方，無論狀態在哪 */
        }

        .timeline li .status {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50; /* 保持文字顏色 */
            margin-bottom: 5px;
        }

        .timeline li:not(.active) .status {
            color: #aaa; /* 非活躍狀態下文字顏色改為淡灰色 */
        }

        .timeline li.active {
            padding-left: 30px;
            transition: padding 0.3s ease; /* 當狀態變化時，動畫效果 */
        }

        .timeline li.active .status {
            color: #2c3e50;
            font-weight: bold;
        }

        /* 調整整體邊框的細節，讓進度條線條看起來更精緻 */
        .timeline li::before {
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); /* 內陰影讓圓點看起來更加立體 */
        }

        .timeline li:last-child {
            margin-bottom: 0;
        }

        .timeline li .status button {
            background: none;
            border: none;
            color: #2c3e50;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .timeline li .status button:hover {
            color: #4CAF50; /* 滑鼠移過去的效果 */
        }

        .timeline li .status button:focus {
            outline: none;
        }


        .custom-select {
            padding: 8px 16px;
            border: 1px solid #ccc;
            border-radius: 30px;
            background-color: #f8f8f8;
            font-size: 1rem;
            font-family: "Noto Serif TC", serif;
            color: #333;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23333" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
            transition: border-color 0.3s ease;
        }

        .custom-select:hover {
            border-color: #999;
        }

        .custom-select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.3);
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 30px;
            padding: 8px 20px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #3e8e41;
        }

        .container {
            display: flex;
            gap: 40px;
        }

        .content {
            flex: 3;
        }

        .likes {
            font-size: 1.05rem;
            font-weight: 600;
        }

        .like-btn {
            background-color: #fff;
            color: #cc3333;
            font-size: 1rem;
            margin-top: 4px;
            margin-right: 5px;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s, color 0.3s, transform 0.2s;
        }

        .like-btn.liked {
            color: #cc3333;
            border: none;
        }

        .like-btn.liked #heart-icon {
            color: #cc3333;
            border: none;
        }

        .like-count {
            margin-right: 5px;
        }

        #like-count {
            margin-right: 5px;
        }

        .timeline li button {
            font: inherit;
            background: none;
            border: none;
            padding: 0;
            margin: 0;
            color: #2980b9;
            cursor: pointer;
        }

        .timeline li button:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="content">
            <?php if ($is_admin): ?>
                <h3>
                    <a href="suggestion_edit.php?suggestion_id=<?= $row['Suggestion_ID'] ?>" style="text-decoration: none; color: #2c3e50;">
                        <?= htmlspecialchars($row['Title']) ?>
                    </a>
                </h3>
            <?php else: ?>
                <h3><?= htmlspecialchars($row['Title']) ?></h3>
            <?php endif; ?>
            <?php if ($is_admin): ?>
                <div class="meta">
                    發佈者： <a href="user_profile.php?id=<?= $row['User_ID'] ?>" class="author-link"><?= htmlspecialchars($row['User_Name']) ?></a><br>
                    關聯設施： <?= htmlspecialchars($row['Facility_Type']) ?><br>
                    關聯建築物： <?= htmlspecialchars($row['Building_Name']) ?><br>
                    更新時間： <?= date("Y-m-d H:i", strtotime($row["Updated_At"])) ?>
                </div>
            <?php else: ?>
                <div class="meta">
                    關聯設施： <?= htmlspecialchars($row['Facility_Type']) ?><br>
                    關聯建築物： <?= htmlspecialchars($row['Building_Name']) ?><br>
                    更新時間： <?= date("Y-m-d H:i", strtotime($row["Updated_At"])) ?>
                </div>
            <?php endif; ?>

            <div class="description">
                <?= nl2br(htmlspecialchars($row['Description'])) ?>
            </div>

            <div class="likes">
                <div class="likes-info">
                    <?php if (!$is_admin): ?>
                        <button id="like-button" class="like-btn <?= $hasLiked ? 'liked' : '' ?>"
                            data-suggestion-id="<?= intval($row['Suggestion_ID']) ?>"
                            data-liked="<?= $hasLiked ? 'true' : 'false' ?>">
                            <?php
                            $heartClass = $hasLiked ? 'fas' : 'far';
                            ?>
                            <i class="<?= $heartClass ?> fa-heart" id="heart-icon"></i>
                        </button>
                    <?php endif; ?>
                    <span id="like-count"><?= $row['LikeCount'] ?></span>人喜歡這則建言
                </div>
            </div>
            <br>

            <a href="suggestions.php" class="back"><b>⬅ 回建言總覽</b></a>
        </div>

        <!-- 動態產生進度紀錄 Timeline -->
        <!-- 進度狀態時間軸（點擊進度直接更新） -->
        <ul class="timeline">
            <?php
            // 檢查是否已有進度紀錄
            $progress_sql = "SELECT Status, Updated_At FROM Progress WHERE Suggestion_ID = ? ORDER BY Updated_At DESC LIMIT 1";
            $progress_stmt = $link->prepare($progress_sql);
            $progress_stmt->bind_param("i", $id);
            $progress_stmt->execute();
            $progress_result = $progress_stmt->get_result();

            // 設置時區為 Asia/Taipei（UTC +8）
            date_default_timezone_set('Asia/Taipei');

            // 檢查資料庫是否有相關紀錄
            if ($progress_result->num_rows == 0) {
                // 插入預設的「未受理」狀態紀錄
                $default_status = '未受理';
                $default_time = date("Y/m/d H:i");  // 這裡會自動根據設定的時區返回當前時間
                
                // 插入預設狀態
                $insert_sql = "INSERT INTO Progress (Suggestion_ID, Status, Updated_At) VALUES (?, ?, ?)";
                $insert_stmt = $link->prepare($insert_sql);
                $insert_stmt->bind_param("iss", $id, $default_status, $default_time);  // 不需要進行 mb_convert_encoding
                $insert_stmt->execute();
                
                // 設置最新狀態為「未受理」
                $latest_status = $default_status;
                $latest_time = $default_time;  // 使用設定時區的時間
            }else {
                // 如果有進度紀錄，則抓取最新的狀態
                $progress_row = $progress_result->fetch_assoc();
                $latest_status = mb_convert_encoding($progress_row['Status'], 'UTF-8', 'auto'); // 確保 Status 是 UTF-8 編碼
                $latest_time = date("Y/m/d H:i", strtotime($progress_row['Updated_At']));

                // 如果需要將時間也處理為 UTF-8 格式
                $latest_time = mb_convert_encoding($latest_time, 'UTF-8', 'auto');
            }

            // 定義狀態
            $stages = ['未受理', '審核中', '處理中', '已完成'];
            $status_index = array_search($latest_status, $stages);

            // 顯示進度條
            foreach ($stages as $i => $stage) {
                $is_active = ($status_index !== false && $i <= $status_index) ? 'active' : '';
                $timestamp = ($i === $status_index && $latest_time) ? $latest_time : '';
                echo "<li class='{$is_active}'>";
                echo "  <div class='timestamp'>{$timestamp}</div>";
                echo "  <div class='status'>";
                if ($is_admin) {
                    echo "<form method='POST' action='update_progress.php' style='display:inline;'>";
                    echo "  <input type='hidden' name='suggestion_id' value='{$id}'>";
                    echo "  <input type='hidden' name='new_status' value='{$stage}'>";
                    echo "  <button type='submit' style='background:none; border:none; color:#2c3e50; font-weight:bold; cursor:pointer;'>{$stage}</button>";
                    echo "</form>";
                } else {
                    echo $stage;
                }
                echo "  </div>";
                echo "</li>";
            }
            ?>
        </ul>

    </div>
</body>

<script>
    const button = document.getElementById('like-button');
    const heartIcon = document.getElementById('heart-icon');

    if (button.dataset.liked === 'true') {
        button.classList.add('liked');
        heartIcon.classList.remove('far');
        heartIcon.classList.add('fas');
    } else {
        button.classList.remove('liked');
        heartIcon.classList.remove('fas');
        heartIcon.classList.add('far');
    }

    document.getElementById('like-button').addEventListener('click', function() {
        const button = this;
        const suggestionId = button.getAttribute('data-suggestion-id');

        fetch('like_suggestion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'Suggestion_ID=' + suggestionId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const likeCountSpan = document.getElementById('like-count');
                    let currentLikes = parseInt(likeCountSpan.textContent);
                    const heartIcon = document.getElementById('heart-icon');

                    if (data.liked) {
                        button.setAttribute('data-liked', 'true');
                        button.classList.add('liked');
                        heartIcon.classList.remove('far'); // 空心
                        heartIcon.classList.add('fas'); // 實心
                        likeCountSpan.textContent = currentLikes + 1;
                    } else {
                        button.setAttribute('data-liked', 'false');
                        button.classList.remove('liked');
                        heartIcon.classList.remove('fas'); // 實心
                        heartIcon.classList.add('far'); // 空心
                        likeCountSpan.textContent = currentLikes - 1;
                    }

                } else {
                    if (data.redirect) {
                        alert(data.message);
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(error => console.error('錯誤:', error));
    });
</script>

</html>