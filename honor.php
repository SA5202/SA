<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}

require_once "dblink3.php";

// 捐款排行榜
$donation_sql = "
    SELECT ua.Nickname, ua.Avatar, SUM(d.Donation_Amount) AS total_donation
    FROM donation d
    JOIN useraccount ua ON d.User_ID = ua.User_ID
    GROUP BY ua.Nickname
    ORDER BY total_donation DESC
    LIMIT 10
";
$donation_result = $conn->query($donation_sql);
if (!$donation_result) {
    die("捐款查詢錯誤: " . $conn->error);
}

// 建言排行榜
$suggestion_sql = "
    SELECT ua.Nickname, ua.Avatar, COUNT(*) AS suggestion_count
    FROM suggestion s
    JOIN useraccount ua ON s.User_ID = ua.User_ID
    GROUP BY ua.Nickname
    ORDER BY suggestion_count DESC
    LIMIT 10
";
$suggestion_result = $conn->query($suggestion_sql);
if (!$suggestion_result) {
    die("建言查詢錯誤: " . $conn->error);
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
            position: relative; /* 加這行讓子元素可絕對定位 */
            max-width: 100%;
            margin: 60px auto;
            background: white;
            padding: 60px;
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

        #card-help-icon {
            position: absolute;
            top: 30px;
            right: 60px;
            z-index: 10;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        #card-help-icon:hover {
            transform: scale(1.2);
        }

        .nav-link {
            color: #777;
            background-color: #f0f8ff;
            padding: 0.5rem 30px;
            font-size: 1rem;
            font-weight: bold;
            border: 1px solid #dee2e6 !important;
            border-top-left-radius: 15px !important;
            border-top-right-radius: 15px !important;
            margin-right: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nav-link.active {
            background-color: rgba(85, 164, 186)!important;
            color: #fff !important;
        }

        .nav-link:hover {
            background-color: #76c1e1;
            color: #fff;
        }

        .table {
            border-collapse: collapse;
            border: 1px solid #dee2e6;
            width: 100%;
        }

        .table th,
        .table td {
            border-left: none;
            border-right: none;
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
            padding-left: 50px;
        }

        .table th {
            background-color: rgba(85, 164, 186);
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

        .table tbody tr:hover {
            background-color: #d0ecff;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .table img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
        }

        .d-flex.align-items-center {
            display: flex;
            align-items: center;
        }

        /* 問號圖示樣式 */
        #help-icon {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        #help-icon:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>

    <div class="ranking-wrapper">

        <!-- 問號圖示放在卡片右上角 -->
        <a href="#" title="說明" id="card-help-icon" data-bs-toggle="modal" data-bs-target="#helpModal">
            <i class="fa-solid fa-circle-question fa-lg text-info"></i>
        </a>

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
                            <td>
                                <?= $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : ($rank == 3 ? '🥉' : $rank)) ?>
                            </td>
                            <td class="d-flex align-items-center">
                                <img src="<?= !empty($row['Avatar']) ? htmlspecialchars($row['Avatar']) : 'images/default-avatar.png' ?>" alt="User Avatar">
                                <?= htmlspecialchars($row['Nickname']) ?>
                            </td>
                            <td><?= number_format($row['total_donation']) ?></td>
                        </tr>
                        <?php $rank++; endwhile; ?>
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
                            <td>
                                <?= $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : ($rank == 3 ? '🥉' : $rank)) ?>
                            </td>
                            <td class="d-flex align-items-center">
                                <img src="<?= !empty($row['Avatar']) ? htmlspecialchars($row['Avatar']) : 'images/default-avatar.png' ?>" alt="User Avatar">
                                <?= htmlspecialchars($row['Nickname']) ?>
                            </td>
                            <td><?= $row['suggestion_count'] ?></td>
                        </tr>
                        <?php $rank++; endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap 說明 Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="helpModalLabel">榮譽榜說明</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>歡迎使用輔仁大學愛校建言捐款系統的榮譽榜功能！</p>
                    <ul>
                    <li><strong>捐款金額排行榜：</strong>依照各用戶累積捐款金額排序，前十名將顯示在頁面上。</li>
                    <li><strong>建言發布數排行榜：</strong>依照用戶提交建言的次數進行排序。</li>
                    <li>若用戶上傳了個人頭像，將會一併顯示在排行榜中。</li>
                    <li>點擊上方的「標籤頁籤」可以快速切換排行榜分類。</li>
                    </ul>
                    <p>此功能旨在表揚積極參與的同學與校友，感謝您的支持與貢獻！</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>