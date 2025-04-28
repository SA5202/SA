<?php
session_start();
require_once "db_connect.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "無效的建言 ID";
    exit;
}

$id = intval($_GET['id']);

$sql = "
SELECT s.Suggestion_ID, s.Title, s.Description, s.Updated_At,
       f.Facility_Type,
       b.Building_Name,
       (SELECT COUNT(*) FROM Upvote u WHERE u.Suggestion_ID = s.Suggestion_ID AND u.Is_Upvoted = 1) AS LikeCount
FROM Suggestion s
JOIN Facility f ON s.Facility_ID = f.Facility_ID
JOIN Building b ON s.Building_ID = b.Building_ID
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
            font-family: 'Poppins', sans-serif;
            background-color: transparent;
            color: #333;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
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
            white-space: pre-wrap;
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
            position: relative;
            margin: 20px 0;
            padding: 0;
            list-style: none;
            border-left: 3px solid #ccc;
            flex: 1;
            /* 右邊佔1 */
            padding-top: 10px;
            /* 讓時間軸不要太緊 */
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
            /* 左邊佔3 */
        }

    </style>
</head>

<body>
    <div class="container">
        <div class="content">
            <h2><?= htmlspecialchars($row['Title']) ?></h2>
            <div class="meta">
                關聯設施：<?= htmlspecialchars($row['Facility_Type']) ?><br>
                關聯建築物：<?= htmlspecialchars($row['Building_Name']) ?><br>
                更新時間：<?= $row['Updated_At'] ?>
            </div>

            <div class="description">
                <?= nl2br(htmlspecialchars($row['Description'])) ?>
            </div>

            <div class="likes">
                ❤️ <?= $row['LikeCount'] ?> 人喜歡這則建言
            </div>

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

</html>