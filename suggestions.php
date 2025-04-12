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

// 根據選擇的排序條件修改 SQL 查詢
if ($sort == 'oldest') {
    $sql .= " ORDER BY s.Updated_At ASC";  // 由舊到新
} elseif ($sort == 'likes') {
    $sql .= " ORDER BY LikeCount DESC";  // 最多人點讚
} else {
    $sql .= " ORDER BY s.Updated_At DESC";  // 由新到舊（預設排序）
}

$result = $link->query($sql);

// 抓建築與設施選單
$buildings = $link->query("SELECT DISTINCT Building_Name FROM Building ORDER BY Building_Name");
$facilities = $link->query("SELECT DISTINCT Facility_Type FROM Facility ORDER BY Facility_Type");
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>建言總覽 | 輔仁大學愛校建言捐款系統</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            max-width: 80%;
            margin: 20px auto;
            padding: 30px;
            background-color: transparent;
            overflow-x: hidden;
            /* 防止 iframe 出現左右捲軸 */
        }

        form {
            display: flex;  /* 使用 flex 佈局 */
            flex-wrap: nowrap;  /* 確保不換行 */
            gap: 1rem;  /* 元素間的間距 */
            margin-bottom: 2rem;
            align-items: center;  /* 垂直置中對齊 */
        }

        form > div {
            display: flex;
            flex-direction: column;
            flex: 1 1 200px;  /* 設定所有表單欄位的最小寬度 */
        }

        form > div:first-child,
        form > div:nth-child(2),
        form > div:nth-child(3) {
            flex: 1;  /* 其他欄位占相同空間 */
        }

        form > div:nth-child(4) {
            flex: 1.5;  /* 搜尋關鍵字佔更多空間 */
        }

        form > div:last-child {
            flex: 0 0 auto;  /* 搜尋按鈕不佔空間，只會顯示需要的寬度 */
        }

        label {
            font-weight: bold;
            font-size: 1.05rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        input[type="text"],
        select {
            padding: 0.5rem;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        button {
            margin-top: 30px;
            padding: 5px 25px;
            background-color: #0077cc;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #005fa3;
        }

        .cards {
            max-width: 100%;
            display: grid;
            grid-template-columns: 1fr;  /* 只顯示一個卡片，單欄顯示 */
            gap: 1.5rem;
        }

        .card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .card h4 {
            margin-top: 0;
            font-size: 1.2rem;
            margin-bottom: 0.8rem;
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
            padding: 0.5rem 1rem;
            background-color: #e6f0ff;
            color: #0077cc;
            border: none;
            border-radius: 8px;
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
            content: "❤️ ";
        }
    </style>
</head>

<body>
    <!-- 篩選表單 -->
    <form method="get">
        <div>
            <label>依設施分類</label>
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
            <label>依建築物分類</label>
            <select name="building">
                <option value="">全部建築物</option>
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
                <option value="latest" <?= $sort == "latest" ? "selected" : "" ?>>由最新到最舊</option>
                <option value="oldest" <?= $sort == "oldest" ? "selected" : "" ?>>由最舊到最新</option>
                <option value="likes" <?= $sort == "likes" ? "selected" : "" ?>>最多人點讚</option>
            </select>
        </div>
        <div>
            <label>搜尋關鍵字</label>
            <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="輸入標題或描述">
        </div>
        <div>
            <button type="submit">搜尋</button>
        </div>
    </form>

    <!-- 建言卡片 -->
    <div class="cards">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <h4><?= htmlspecialchars($row['Title']) ?></h4>
                <p><?= mb_strimwidth(strip_tags($row['Description']), 0, 100, "...") ?></p>
                <div class="meta">
                    關聯設施： <?= htmlspecialchars($row['Facility_Type']) ?> ｜ 關聯建築物： <?= htmlspecialchars($row['Building_Name']) ?><br>
                    更新時間： <?= $row['Updated_At'] ?>
                </div>
                <div class="actions">
                    <a href="suggestion_detail.php?id=<?= $row['Suggestion_ID'] ?>" class="btn">查看完整建言</a>
                    <span class="likes"><?= $row['LikeCount'] ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>

</html>
