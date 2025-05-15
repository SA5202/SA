<?php
session_start();
require_once "db_connect.php";

$is_logged_in = isset($_SESSION['User_Name']);


$keyword = $_GET['keyword'] ?? '';
$facility = $_GET['facility'] ?? '';
$building = $_GET['building'] ?? '';
$sort = $_GET['sort'] ?? 'latest';

$sql = "
SELECT 
    s.Suggestion_ID, 
    s.Title, 
    s.Description, 
    s.Updated_At,
    f.Facility_Type,
    b.Building_Name,
    (
        SELECT COUNT(*) 
        FROM Upvote u 
        WHERE u.Suggestion_ID = s.Suggestion_ID 
        AND u.Is_Upvoted = 1
    ) AS LikeCount,
    (
        SELECT p1.Status
        FROM Progress p1
        WHERE p1.Suggestion_ID = s.Suggestion_ID
        ORDER BY p1.Updated_At DESC
        LIMIT 1
    ) AS CurrentStatus
FROM Suggestion s
JOIN Facility f ON s.Facility_ID = f.Facility_ID
JOIN Building b ON s.Building_ID = b.Building_ID
WHERE 1=1
";


$progress_enum = [
    'all' => '所有進度',
    'unprocessed' => '未受理',
    'reviewing'   => '審核中',
    'processing'  => '處理中',
    'completed'   => '已完成',
];

$progress = $_GET['progress'] ?? '';
if (!empty($progress) && $progress !== 'all' && isset($progress_enum[$progress])) {
    $status_str = $progress_enum[$progress];
    $sql .= "
        AND (
            SELECT p1.Status
            FROM Progress p1
            WHERE p1.Suggestion_ID = s.Suggestion_ID
            ORDER BY p1.Updated_At DESC
            LIMIT 1
        ) = '{$status_str}'
    ";
}


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
$buildings = $link->query("
    SELECT DISTINCT Building_Name 
    FROM Building 
    ORDER BY SUBSTRING_INDEX(Building_Name, '(', -1) ASC
");
$facilities = $link->query("SELECT DISTINCT Facility_Type FROM Facility ORDER BY Facility_ID");
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>建言總覽 | 輔仁大學愛校建言捐款系統</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            max-width: 85%;
            margin: 50px auto;
            padding: 30px;
            background-color: transparent;
            font-family: "Noto Serif TC", serif;
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
            border-radius: 25px;
            align-items: flex-end;
            overflow-x: auto;

            /* 更深的灰色背景，與卡片內容區區分 */
            background: rgba(241, 244, 249, 0.7);
            backdrop-filter: blur(8px);
            /* 稍微減弱模糊效果 */
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(200, 200, 200, 0.4);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            transition: transform 0.2s ease-in-out;
        }

        form:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 28px rgba(0, 0, 0, 0.18);
        }

        form>div {
            display: flex;
            flex-direction: column;
            flex: 1.2;
            min-width: 10px;
        }

        form>div:nth-child(5) {
            flex: 1.6;
        }

        form>div:last-child {
            flex: 0 0 auto;
        }

        label {
            font-weight: 750;
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
            color: #4c5b63;
            /* 深灰藍色 */
        }

        input[type="text"],
        select {
            padding: 0.4rem;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            font-size: 0.95rem;
            font-family: "Noto Serif TC", serif;
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
            padding: 0.4rem 20px;
            background-color: rgb(120, 128, 130);
            /* 深灰色 */
            color: white;
            border: none;
            border-radius: 10px;
            font-family: "Noto Serif TC", serif;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        button:hover {
            background-color: #5a6268;
            /* 深灰色 */
            transform: translateY(-2px);
        }

        /* 卡片群組 */
        .cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        /* 卡片樣式：帶漸層+玻璃感+陰影 */
        .card {
            padding: 2.5rem;
            border-radius: 25px;
            border: 1px solid rgba(200, 200, 200, 0.3);
            background-color: white;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            transition: transform 0.2s ease-in-out;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
            height: 100%;
            /* 讓卡片自適應內容的高度 */
            max-height: 300px;
            /* 設定一個最大高度，超過時會被限制 */
            overflow: hidden;
            /* 隱藏超過的內容 */
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        /* 卡片標題區 */
        .card h4 {
            font-size: 1.2rem;
            color: #fff;
            margin: -2.5rem -2.5rem 0.5rem;
            padding: 1rem 2.5rem;
            background-color: rgba(85, 164, 186, 0.8);
            border-bottom: 1px solid #ddd;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            font-weight: bold;
        }

        .card h4 i {
            font-size: 1.2rem;
            width: 1.5rem;
            height: 1.5rem;
            display: inline-block;
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

        .card-description {
            overflow: hidden;
            /* 確保文字不會超出 */
            text-overflow: ellipsis;
            /* 使用省略號來處理過長的文字 */
            display: -webkit-box;
            -webkit-line-clamp: 1;
            /* 限制顯示行數 */
            -webkit-box-orient: vertical;
            margin-bottom: 1em;
            font-size: 1.05rem;
            line-height: 1.6;
        }

        /* 讓卡片內容區有對比的顏色 */
        .card .meta {
            font-size: 0.85rem;
            font-weight: 550;
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
            padding: 0.5rem 1.5rem;
            background-color: rgba(170, 216, 224, 0.3);
            color: rgb(0, 76, 148);
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 750;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .card .btn:hover {
            background-color: rgba(0, 176, 224, 0.3);
            color: white;
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
            <label>處理進度</label>
            <select name="progress">
                <option value="all" <?= $progress == "all" ? "selected" : "" ?>>所有進度</option>
                <option value="unprocessed" <?= $progress == "unprocessed" ? "selected" : "" ?>>未受理</option>
                <option value="reviewing" <?= $progress == "reviewing" ? "selected" : "" ?>>審核中</option>
                <option value="processing" <?= $progress == "processing" ? "selected" : "" ?>>處理中</option>
                <option value="completed" <?= $progress == "completed" ? "selected" : "" ?>>已完成</option>

            </select>
        </div>
        <div>
            <label>搜尋關鍵字</label>
            <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="輸入標題或描述">
        </div>
        <div>
            <button type="submit">查詢</button>
        </div>
    </form>

    <!-- 建言卡片 -->
    <div class="cards">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <h4><i class="icon fas fa-list"></i> <?= htmlspecialchars($row['Title']) ?></h4>
                <p class="card-description"><?= mb_strimwidth(strip_tags($row['Description']), 0, 130, "⋯") ?></p>
                <div class="meta">
                    關聯設施： <?= htmlspecialchars($row['Facility_Type']) ?> ｜ 關聯建築物： <?= htmlspecialchars($row['Building_Name']) ?><br>
                    更新時間： <?= date('Y-m-d H:i', strtotime($row['Updated_At'])) ?>
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