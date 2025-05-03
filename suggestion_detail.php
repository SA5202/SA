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

        .timeline {
            height: 50vh;
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
    </style>
</head>

<body>
    <div class="card">
        <div class="content">
            <h3><?= htmlspecialchars($row['Title']) ?></h3>
            <?php if ($is_admin): ?>
                <div class="meta">
                    發佈者： <?= htmlspecialchars($row['User_Name']) ?><br>
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