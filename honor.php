<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}

require_once "dblink3.php"; // 資料庫連線

// 捐款排行榜（依捐款總額）
$donation_sql = "
    SELECT ua.Nickname, ua.Avatar, SUM(d.Donation_Amount) AS total_donation
    FROM donation d
    JOIN useraccount ua ON d.User_ID = ua.User_ID
    GROUP BY ua.Nickname
    ORDER BY total_donation DESC
    LIMIT 10
";
$donation_result = $conn->query($donation_sql);

// 檢查查詢是否成功
if (!$donation_result) {
    die("捐款查詢錯誤: " . $conn->error);  // 顯示具體的錯誤訊息
}

// 建言排行榜（依建言數量）
$suggestion_sql = "
    SELECT ua.Nickname, ua.Avatar, COUNT(*) AS suggestion_count
    FROM suggestion s
    JOIN useraccount ua ON s.User_ID = ua.User_ID
    GROUP BY ua.Nickname
    ORDER BY suggestion_count DESC
    LIMIT 10
";
$suggestion_result = $conn->query($suggestion_sql);

// 檢查查詢是否成功
if (!$suggestion_result) {
    die("建言查詢錯誤: " . $conn->error);  // 顯示具體的錯誤訊息
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>榮譽榜 | 輔仁大學愛校建言捐款系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>

    <style>
        body {
            max-width: 85%;
            margin: 0 auto;
            padding: 30px;
            font-family: "Noto Serif TC", serif;
            line-height: 1.8;
            background-color: transparent;
            overflow-x: hidden;
            color: #2c3e50;
        }

        .ranking-wrapper {
            max-width: 100%;
            margin: 60px auto;
            background: white;
            padding: 50px;
            border-radius: 40px;
            transition: transform 0.2s ease-in-out;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
        }

        .ranking-wrapper:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .nav-link {
            color: #777;
            background-color: #f0f8ff;
            padding: 10px 30px;
            font-size: 1rem;
            font-weight: bold;
            border: 1px solid transparent;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
            margin-right: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nav-link.active {
            background-color: #3c8dbc !important;
            color: #fff !important;
            border-color: #3c8dbc #3c8dbc transparent;
        }

        .nav-link:hover {
            background-color: #76c1e1;
            color: #fff;
        }

        .table {
            border-collapse: collapse;
            border: 2px solid #dee2e6;
        }

        .table th,
        .table td {
            border-left: none;
            border-right: none;
            border-top: 2px solid #dee2e6;
            border-bottom: 2px solid #dee2e6;
        }

        .table th:first-child,
        .table td:first-child {
            border-left: none;
        }

        .table th:last-child,
        .table td:last-child {
            border-right: none;
        }

        .table th,
        .table td:nth-child(1),
        .table td:nth-child(3) {
            text-align: center;
        }
        
        .table td:nth-child(2) {
            padding-left: 50px;  /* 調整距離左邊的距離，可以根據需要改變數值 */
        }

        .table th {
            background-color: #3c8dbc;
            color: white;
            font-size: 1rem;
            padding: 0.8rem 1.5rem;
        }

        .table tbody td {
            color: #555;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.4rem 1.5rem;
        }

        .table tbody tr:nth-child(even) {
            background-color: #e6f0f8;
        }
    </style>
</head>
<body>

    <div class="ranking-wrapper">
        <ul class="nav nav-tabs" id="rankingTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="donation-tab" data-bs-toggle="tab" href="#donation" role="tab" aria-controls="donation" aria-selected="true">
                    捐款金額排行榜
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="suggestion-tab" data-bs-toggle="tab" href="#suggestion" role="tab" aria-controls="suggestion" aria-selected="false">
                    建言發布數排行榜
                </a>
            </li>
        </ul>
        
        <div class="tab-content" id="rankingTabContent">
            <div class="tab-pane fade show active" id="donation" role="tabpanel" aria-labelledby="donation-tab">
                <table class="table table-bordered">
                    <colgroup>
                        <col style="width: 30%;">
                        <col style="width: 40%;">
                        <col style="width: 30%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>名次</th>
                            <th>用戶</th>
                            <th>累積捐款金額 (NT$)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; while ($row = $donation_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $rank++ ?></td>
                            <?php
                            $avatar = !empty($row['Avatar']) ? $row['Avatar'] : 'images/default-avatar.png';
                            ?>
                            <td>
                                <img src="<?= htmlspecialchars($avatar) ?>" alt="User Avatar"
                                    style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px; vertical-align: middle;">
                                <?= htmlspecialchars($row['Nickname']) ?>
                            </td>
                            <td><?= number_format($row['total_donation']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="suggestion" role="tabpanel" aria-labelledby="suggestion-tab">
                <table class="table table-bordered">
                    <colgroup>
                        <col style="width: 30%;">
                        <col style="width: 40%;">
                        <col style="width: 30%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>名次</th>
                            <th>用戶</th>
                            <th>建言發布數</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; while ($row = $suggestion_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $rank++ ?></td>
                            <?php
                            $avatar = !empty($row['Avatar']) ? $row['Avatar'] : 'images/default-avatar.png';
                            ?>
                            <td>
                                <img src="<?= htmlspecialchars($avatar) ?>" alt="User Avatar"
                                    style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px; vertical-align: middle;">
                                <?= htmlspecialchars($row['Nickname']) ?>
                            </td>
                            <td><?= $row['suggestion_count'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>