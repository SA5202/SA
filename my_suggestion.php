<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}

// 連接資料庫
$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

$sessionUserID = $_SESSION['User_ID'];
$sessionUserType = $_SESSION['User_Type'];

// 引入 honor_helper.php
include_once 'honor_helper.php';  // 引入這個檔案

// 取得要查看的用戶 ID
if (isset($_GET['id'])) {
    $viewUserID = intval($_GET['id']);
    // 確保只有當前用戶或管理員才能查看
    if ((int)$viewUserID !== (int)$sessionUserID && $sessionUserType !== 'admin') {
        die('無權限查看此使用者資訊。');
    }
} else {
    $viewUserID = isset($_GET['id']) ? (int)$_GET['id'] : (int)$sessionUserID;
}

// 查詢建議內容
$sql = "
SELECT s.Suggestion_ID, s.Title, s.Description, s.Updated_At, s.User_ID, u.User_Name,
       f.Facility_Type, b.Building_Name,
       (SELECT COUNT(*) FROM Upvote u2 WHERE u2.Suggestion_ID = s.Suggestion_ID AND u2.Is_Upvoted = 1) AS LikeCount,
       (SELECT Status FROM Progress p WHERE p.Suggestion_ID = s.Suggestion_ID ORDER BY Updated_At DESC LIMIT 1) AS LatestStatus
FROM Suggestion s
JOIN Facility f ON s.Facility_ID = f.Facility_ID
JOIN Building b ON s.Building_ID = b.Building_ID
JOIN Useraccount u ON s.User_ID = u.User_ID
WHERE s.User_ID = ?
ORDER BY s.Updated_At DESC
";

$stmt = $link->prepare($sql);
$stmt->bind_param("i", $viewUserID);
$stmt->execute();
$result = $stmt->get_result();

// 查詢用戶資訊
$sql_user = "SELECT * FROM useraccount WHERE User_ID = ?";
$stmt_user = $link->prepare($sql_user);
$stmt_user->bind_param("i", $viewUserID);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$row_user = $result_user->fetch_assoc();

// 獲取使用者的 VIP 資訊
$vipInfo = getVipLevel($link, $row_user['User_ID']);  // 獲取 VIP 等級資料
?>

<!DOCTYPE html>

<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>個人檔案 | 輔仁大學愛校建言捐款系統</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <style>
        body {
            max-width: 85%;
            margin: 0 auto;
            padding: 30px;
            font-size: 1.1rem;
            font-family: "Noto Serif TC", serif;
            line-height: 1.8;
            background-color: transparent;
            overflow-x: hidden;
            color: #333;
        }

        .icon {
            font-size: 1.5rem;
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 10px;
            display: inline-block;
        }

        h3 {
            margin: 30px 0;
            font-weight: bold;
        }

        .left {
            text-align: left;
            font-size: 1.05rem;
            font-weight: 600;
            color: #555;
        }

        .password-display-wrapper {
            position: relative;
            width: 200px;
        }

        #passwordDisplay {
            width: 100%;
            padding-right: 35px;
            font-weight: bold;
            border: none;
            background: transparent;
            font-size: 16px;
            color: #333;
            pointer-events: none;
        }

        #togglePassword {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
        }

        .custom-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 40px;
            font-size: 1rem;
            font-weight: 750;
            background: linear-gradient(to right, rgb(139, 186, 224), rgb(69, 109, 133));
            color: #fff;
            border-radius: 15px;
            text-decoration: none;
        }

        .custom-btn:hover {
            opacity: 0.6;
        }

        .pretty-btn {
            background: linear-gradient(to right, rgb(139, 186, 224), rgb(69, 109, 133));
            text-decoration: none;
            border: none;
            color: white;
            padding: 0.2rem 20px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 750;
        }

        .pretty-btn:hover {
            opacity: 0.6;
        }

        .pretty-btn i {
            margin-right: 6px;
        }

        .custom-badge {
            background: linear-gradient(to right, rgb(218, 240, 249), rgb(197, 226, 239));
            color: #2a4d69;
            font-size: 0.9rem;
            padding: 0.3rem 1.2rem;
            border-radius: 12px;
            font-weight: bold;
        }

        .table-responsive {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px 20px;
            border-radius: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .table-responsive:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }


        .table th {
            background-color: #f1f3f5;
            color: #555;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .table {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 20px;
            overflow: hidden;
            width: 100%;
            border-radius: 25px;
            border: 2px solid #dee2e6;
            border-collapse: separate;
            overflow: hidden;
            border-spacing: 0;
        }

        .table:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }


        .table th,
        .table td {
            padding: 10px 40px;
            border-radius: 0px;
            border-bottom: none;
        }

        .table-primary {
            background-color: #e9f5ff;
        }

        .title {
            color: #003153;
            font-weight: bold;
            text-decoration: none;
        }

        .table .update-time,
        .table .like-count,
        .table .edit-action {
            text-align: center;
            vertical-align: middle;
        }

        .update {
            font-size: 0.9rem;
            font-weight: bold;
            color: gray;
        }

        .text-muted {
            text-align: center;
            font-size: 1.05rem;
            font-weight: 600;
        }

        .funding-status-label {
            display: inline-block;
            padding: 0.5em 1.3em;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 4px;
            color: white;
        }

        .funding-status-進行中 {
            background: linear-gradient(90deg, rgb(192, 222, 132), rgb(139, 173, 79));
        }

        .funding-status-已暫停 {
            background: linear-gradient(90deg, rgb(255, 237, 150), rgb(244, 212, 54));
        }

        .funding-status-已完成 {
            background: linear-gradient(90deg, rgb(240, 165, 165), rgb(210, 82, 82));
        }

        .table td.highlight-title {
            color: #003153;
            font-weight: bold;
        }


        /* 共用迷你錦旗基底 */
        .mini-pennant {
            margin: 20px 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100px;
            height: 150px;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
            position: relative;
            margin-right: 80px;
            vertical-align: middle;
            font-family: "Noto Serif TC", serif;
            font-size: 2rem;
            font-weight: bold;
            color: #c00;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* 頂部橫條 */
        .mini-pennant::before {
            content: "";
            position: absolute;
            top: 0;
            width: 100%;
            height: 12px;
            background: currentColor;
            opacity: 0.6;
        }

        /* 底部流蘇 */
        .mini-pennant::after {
            content: "";
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 20px;
            background: repeating-linear-gradient(to right,
                    rgba(255, 255, 255, 0.7) 0 3px,
                    rgba(0, 0, 0, 0.1) 3px 6px);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 50%, 0 100%);
        }

        /* 各等級變化 */
        .mini-pennant.vip1 {
            background: #ffeb3b;
            /* 純黃色 */
            color: #b8860b;
            /* 文字深金色 */
        }

        .mini-pennant.vip2 {
            background: #ffeb3b;
            /* 純黃色 */
            color: #b8860b;
            /* 文字深金色 */
        }

        .mini-pennant.vip3 {
            background: linear-gradient(to bottom, #ffe600, #ff6600);
            /* 和 vip2 一樣的顏色 */
        }

        .mini-pennant.vip4 {
            background: linear-gradient(to bottom, #ffe600, #ff6600);
            /* 和 vip3 一樣的顏色 */
            box-shadow: 0 2px 6px rgba(255, 140, 0, 0.3), inset 0 0 8px rgba(255, 255, 255, 0.3);
            /* 更柔和的陰影 */
        }

        .mini-pennant.vip5 {
            background: linear-gradient(to bottom, #ffec8b, #ff4500);
            /* 現在不變 */
            box-shadow: 0 2px 8px rgba(255, 69, 0, 0.8), inset 0 0 12px rgba(255, 255, 255, 0.7);
            animation: glow 2s infinite alternate;
        }

        /* 閃爍動畫 */
        @keyframes glow {
            from {
                box-shadow: 0 2px 8px rgba(255, 69, 0, 0.8), inset 0 0 12px rgba(255, 255, 255, 0.7);
            }

            to {
                box-shadow: 0 2px 12px rgba(255, 69, 0, 1), inset 0 0 16px rgba(255, 255, 255, 1);
            }
        }

        .vip-pennant-wrapper {
            min-width: 60px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .vip-floating-tooltip {
            position: fixed;
            visibility: hidden;
            max-width: 100%;
            /* 限制最大寬度 */
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 80, 0.15);
            font-size: 0.8rem;
            font-weight: 600;
            color: #1a1a1a;
            pointer-events: none;
            z-index: 9999;
            opacity: 0;
            transform: scale(0);
            transition: opacity 0.8s ease, transform 0.8s ease;
            word-wrap: break-word;
            /* 使文字自動換行 */
            word-break: break-word;
            /* 防止過長的單詞溢出 */
        }

        .vip-floating-tooltip .tooltip-content {
            background-color: rgba(204, 204, 204, 0.7);
            border-radius: 10px;
            padding: 0 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: normal;
            /* 允許換行 */
            position: relative;
            min-height: 40px;
            /* 設定最小高度為原來的大小 */
            height: auto;
            /* 高度根據內容自動調整 */
        }

        /* 顯示動畫 */
        .vip-floating-tooltip.show {
            visibility: visible;
            opacity: 1;
            transform: scale(1.2);
        }

        .title2 {
            color: #003153;
            font-weight: bold;
            text-decoration: none;
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
    </style>


</head>

<body>

    <?php if ($sessionUserType == 'admin'): ?>
        <h3><i class="icon fas fa-clipboard-list"></i> <?= htmlspecialchars($row_user['User_Name']) ?> 的建言記錄</h3>
    <?php else: ?>
        <h3><i class="icon fas fa-clipboard-list"></i> 我的建言紀錄</h3>
    <?php endif; ?>

    <table class="table">
        <thead class="table-primary">
            <tr>
                <th class="fw-bold">建言標題</th>
                <th class="fw-bold text-center">更新時間</th>
                <th class="fw-bold text-center">獲得愛心數</th>
                <?php if ($sessionUserType != 'admin'): ?>
                    <th class="fw-bold text-center">編輯建言</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <a class="title2" href="suggestion_detail.php?id=<?= $row['Suggestion_ID'] ?>">
                                <?= htmlspecialchars(mb_strimwidth(strip_tags($row['Title']), 0, 30, "⋯")) ?>
                            </a>
                        </td>
                        <td class="text-center update-time">
                            <span class="update"><?= date('Y-m-d', strtotime($row['Updated_At'])) ?></span>
                        </td>
                        <td class="text-center like-count">
                            <span class="custom-badge">
                                <?= ($row['LikeCount'] >= 10000)
                                    ? number_format($row['LikeCount'] / 10000, 1) . ' 萬 ❤️'
                                    : $row['LikeCount'] . ' ❤️'; ?>
                            </span>
                        </td>
                        <?php if ($sessionUserType != 'admin'): ?>
                            <td class="text-center edit-action">
                                <?php if ($row['User_ID'] == $sessionUserID): ?>
                                    <form action="suggestion_update.php" method="get" style="display:inline;">
                                        <input type="hidden" name="Suggestion_ID" value="<?= $row['Suggestion_ID'] ?>">
                                        <button type="submit" class="pretty-btn">
                                            <i class="fas fa-pen-to-square"></i> 修改
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= ($sessionUserType != 'admin') ? 4 : 3 ?>" class="text-muted">目前沒有建言紀錄。</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>



    <?php
    $stmt->close();
    $link->close();
    ?>




</body>

</html>