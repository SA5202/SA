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
?>


<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($row['Title']) ?> | 建言詳情</title>
    <style>
        body {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            font-family: "Noto Serif TC", serif;
            background-color: transparent;
            color: #333;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            display: flex;
            gap: 40px;
            /* 讓內容和進度條有間距 */
        }

        h2 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .meta {
            font-size: 0.95rem;
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
        }

        a.back {
            display: inline-block;
            margin-top: 2rem;
            text-decoration: none;
            color: #3498db;
        }

        .timeline {
            flex: 1;
            /* 右邊占 1 的比例 */
            position: relative;
            margin: 0;
            padding: 0;
            list-style: none;
            border-left: 3px solid #ccc;
        }


        .timeline li {
            position: relative;
            margin-bottom: 30px;
            padding-left: 20px;
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
        }

        .timeline li.active::before {
            background: #4CAF50;
            /* 綠色表示完成 */
        }

        .timeline li .timestamp {
            font-size: 12px;
            color: #999;
            margin-bottom: 5px;
        }

        .timeline li .status {
            font-size: 16px;
            font-weight: bold;
        }

        .container {
            display: flex;
            gap: 40px;
            /* 左右間距 */
        }

        .content {
            flex: 3;
            /* 左邊占 3 的比例 */
        }

        .like-btn {
            background-color: #fff;
            border: 2px solid rgb(147, 188, 205);
            color: #cc3333;
            font-size: 1rem;
            padding: 8px 16px;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s, transform 0.2s;
        }



        .like-btn.liked {
            color: #fff;
        }

        .like-btn.liked #heart-icon {
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="content">
            <h2><?= htmlspecialchars($row['Title']) ?></h2>
            <?php if ($is_admin): ?>
                <div class="meta">
                    發布人：<?= htmlspecialchars($row['User_Name']) ?><br>
                    關聯設施：<?= htmlspecialchars($row['Facility_Type']) ?><br>
                    關聯建築物：<?= htmlspecialchars($row['Building_Name']) ?><br>
                    更新時間：<?= $row['Updated_At'] ?>
                </div>
            <?php else: ?>
                <div class="meta">
                    關聯設施：<?= htmlspecialchars($row['Facility_Type']) ?><br>
                    關聯建築物：<?= htmlspecialchars($row['Building_Name']) ?><br>
                    更新時間：<?= $row['Updated_At'] ?>
                </div>
            <?php endif; ?>


            <div class="description">
                <?= nl2br(htmlspecialchars($row['Description'])) ?>
            </div>

            <div class="likes">
                ❤️ <span id="like-count"><?= $row['LikeCount'] ?></span> 人喜歡這則建言
            </div>
            <br>
            <?php if (!$is_admin): ?>
                <div class="likes">
                    <button id="like-button" class="like-btn" data-suggestion-id="<?= intval($row['Suggestion_ID']) ?>" data-liked="false">
                        <i class="fas fa-heart" id="heart-icon">🤍</i>
                    </button>
                </div>
            <?php endif; ?>


            <a href="suggestions.php" class="back">← 回建言總覽</a>
        </div>

        <ul class="timeline">
            <li class="active">
                <div class="timestamp">2025/04/14 14:56</div>
                <div class="status">建言已受理</div>
            </li>
            <li class="active">
                <div class="timestamp">2025/04/17 05:50</div>
                <div class="status">處理中</div>
            </li>
            <li>
                <div class="timestamp">2025/04/18 09:10</div>
                <div class="status">處理完成</div>
            </li>
        </ul>
    </div>
</body>
<script>
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
                        // 按讚成功
                        button.setAttribute('data-liked', 'true');
                        button.classList.add('liked');
                        heartIcon.textContent = '❤️'; // 實心愛心
                        likeCountSpan.textContent = currentLikes + 1;
                    } else {
                        // 取消讚
                        button.setAttribute('data-liked', 'false');
                        button.classList.remove('liked');
                        heartIcon.textContent = '🤍'; // 空心愛心
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