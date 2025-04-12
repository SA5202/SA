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
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }

        /* 玻璃感效果 */
        .glass-effect {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.55), rgba(245, 245, 245, 0.35));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(200, 200, 200, 0.4);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            transition: all 0.35s ease;
        }

        /* 表單區塊，應用玻璃效果 */
        form {
            display: flex;
            flex-wrap: nowrap;
            gap: 1.2rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            border-radius: 20px;
            align-items: flex-end;
            overflow-x: auto;

            /* 更深的灰色背景，與卡片內容區區分 */
            background: rgba(225, 225, 225, 0.5); /* 深灰色背景 */
            backdrop-filter: blur(8px); /* 稍微減弱模糊效果 */
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(200, 200, 200, 0.4);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            transition: all 0.3s ease;
        }

        form:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 28px rgba(0, 0, 0, 0.18);
        }

        form > div {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 160px;
        }

        form > div:nth-child(4) {
            flex: 1.5;
        }

        form > div:last-child {
            flex: 0 0 auto;
        }

        label {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
            color: #4c5b63; /* 深灰藍色 */
        }

        input[type="text"],
        select {
            padding: 0.6rem;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            font-size: 0.95rem;
            transition: border 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: #999;
            box-shadow: 0 0 0 3px rgba(180, 180, 180, 0.2);
            outline: none;
        }

        /* 按鈕樣式 */
        button {
            padding: 0.5rem 15px;
            background-color: #6c757d; /* 深灰色 */
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        button:hover {
            background-color: #5a6268; /* 深灰色 */
            transform: translateY(-2px);
        }

        /* 卡片群組 */
        .cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        /* 卡片樣式：帶漸層+玻璃感+陰影 */
        .card {
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid rgba(200, 200, 200, 0.3);
            background: rgba(235, 235, 235, 0.95); /* 淺灰色背景，避免與標題區過於相似 */
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        /* 卡片標題區 */
        .card h4 {
            font-size: 1.2rem;
            color: #34495e; /* 深灰藍色標題 */
            margin: -2rem -2rem 1rem;
            padding: 1rem 2rem;
            background-color: rgba(240, 240, 240, 0.95); /* 淺灰色背景 */
            border-bottom: 1px solid #ddd;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            font-weight: bold;
        }

        /* 卡片內容區域 */
        .card .content {
            background: rgba(200, 200, 200);
            padding: 1rem;
            border-radius: 8px;
            color: #444;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* 讓卡片內容區有對比的顏色 */
        .card .meta {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 1rem;
        }

        /* 操作按鈕區 */
        .card .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        /* 操作按鈕樣式 */
        .card .btn {
            padding: 0.6rem 1.2rem;
            background-color: rgba(240, 240, 240);
            color: #333;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card .btn:hover {
            background-color: rgba(220, 220, 220, 0.95);
            transform: translateY(-2px);
        }

        /* 喜歡數樣式 */
        .likes {
            font-size: 0.9rem;
            color: #444;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .likes::before {
            content: "❤️ ";
            color: #cc3333;
            margin-right: 5px;
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
            <button type="submit">查 詢</button>
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
