<?php
session_start();
require_once "db_connect.php";

$keyword = $_GET['keyword'] ?? '';
$facility = $_GET['facility'] ?? '';
$building = $_GET['building'] ?? '';
$sort = $_GET['sort'] ?? 'latest';

$sql = "
SELECT s.Suggestion_ID, s.Title, s.Description, s.Updated_At,
       f.Facility_Type,
       b.Building_Name,
       (SELECT COUNT(*) FROM Upvote u WHERE u.Suggestion_ID = s.Suggestion_ID AND u.Is_Upvoted = 1) AS LikeCount
FROM Suggestion s
JOIN Facility f ON s.Facility_ID = f.Facility_ID
JOIN Building b ON s.Building_ID = b.Building_ID
WHERE 1=1
";

if (!empty($keyword)) {
    $sql .= " AND (s.Title LIKE '%$keyword%' OR s.Description LIKE '%$keyword%')";
}
if (!empty($facility)) {
    $sql .= " AND f.Facility_Type = '$facility'";
}
if (!empty($building)) {
    $sql .= " AND b.Building_Name = '$building'";
}
$sql .= $sort == 'likes' ? " ORDER BY LikeCount DESC" : " ORDER BY s.Updated_At DESC";

$result = $link->query($sql);

// 抓建築與設施選單
$buildings = $link->query("SELECT DISTINCT Building_Name FROM Building ORDER BY Building_Name");
$facilities = $link->query("SELECT DISTINCT Facility_Type FROM Facility ORDER BY Facility_Type");
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>建言總覽</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", sans-serif;
            margin: 0;
            padding: 2rem;
            
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 2rem;
        }

        form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        label {
            font-weight: bold;
            margin-bottom: 0.25rem;
            display: block;
        }

        input[type="text"],
        select {
            padding: 0.5rem;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            padding: 0.6rem;
            background-color: #0077cc;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #005fa3;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .card h5 {
            margin-top: 0;
            font-size: 1.2rem;
            margin-bottom: 0.75rem;
            color: #222;
        }

        .card p {
            color: #555;
            line-height: 1.5;
        }

        .card .meta {
            font-size: 0.85rem;
            color: #777;
            margin-bottom: 1rem;
        }

        .card .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card .btn {
            padding: 0.4rem 1rem;
            background-color: #e6f0ff;
            color: #0077cc;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .card .btn:hover {
            background-color: #cce0ff;
        }

        .likes {
            font-size: 0.9rem;
            color: #333;
        }

        .likes::before {
            content: "👍 ";
        }
    </style>
</head>

<body>
    <h1>建言總覽</h1>

    <!-- 篩選表單 -->
    <form method="get">
        <div>
            <label>搜尋關鍵字</label>
            <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="輸入標題或描述">
        </div>
        <div>
            <label>設施分類</label>
            <select name="facility">
                <option value="">全部設施</option>
                <?php while ($f = $facilities->fetch_assoc()): ?>
                    <option value="<?= $f['Facility_Type'] ?>" <?= $facility == $f['Facility_Type'] ? "selected" : "" ?>>
                        <?= $f['Facility_Type'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label>建築分類</label>
            <select name="building">
                <option value="">全部建築</option>
                <?php while ($b = $buildings->fetch_assoc()): ?>
                    <option value="<?= $b['Building_Name'] ?>" <?= $building == $b['Building_Name'] ? "selected" : "" ?>>
                        <?= $b['Building_Name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label>排序依據</label>
            <select name="sort">
                <option value="latest" <?= $sort == "latest" ? "selected" : "" ?>>最新建言</option>
                <option value="likes" <?= $sort == "likes" ? "selected" : "" ?>>最多點讚</option>
            </select>
        </div>
        <div>
            <button type="submit">搜尋</button>
        </div>
    </form>

    <!-- 建言卡片 -->
    <div class="cards">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <h5><?= htmlspecialchars($row['Title']) ?></h5>
                <p><?= mb_strimwidth(strip_tags($row['Description']), 0, 100, "...") ?></p>
                <div class="meta">
                    設施：<?= htmlspecialchars($row['Facility_Type']) ?> ｜ 建築：<?= htmlspecialchars($row['Building_Name']) ?><br>
                    更新：<?= $row['Updated_At'] ?>
                </div>
                <div class="actions">
                    <a href="suggestion_detail.php?id=<?= $row['Suggestion_ID'] ?>" class="btn">查看建言</a>
                    <span class="likes"><?= $row['LikeCount'] ?> 讚</span>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>

</html>