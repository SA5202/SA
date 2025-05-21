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
            background-color:rgb(77, 112, 128);
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

        /* Modal 動畫效果 */
        .modal.fade .modal-dialog {
            transform: translateY(-20px);
            opacity: 0;
            transition: transform 0.3s ease-out, opacity 0.3s ease-in-out;
        }

        .modal.show .modal-dialog {
            transform: translateY(0);
            opacity: 1;
        }

        /* Modal 背景遮罩透明 */
        .modal-backdrop {
            background-color: transparent !important;
            opacity: 1 !important;
        }

        /* Modal 外框 */
        .modal-dialog {
            max-width: 75%;
            top: 30px;
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
            border-radius: 25px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
        }

        /* Modal 內容區模糊 */
        .modal-content {
            height: 100%; /* 確保內容區塊高度填滿 */
            background-color: #fff;
            backdrop-filter: blur(15px);
            border-radius: 25px;
            border: none;
        }

        /* Modal 標題區 */
        .modal-header {
            background: linear-gradient(135deg, #55a4ba, #3793c1);
            color: white;
            border-bottom: none;
            padding: 1.5rem 2rem;
        }

        .modal-header .modal-title {
            font-size: 1.2rem;
            font-weight: bold;
        }

        /* Modal 內容區內推與圓角 */
        .modal-body {
            padding: 1.5rem 2rem;
            border-radius: 25px;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
            flex-grow: 1; /* 自動擴展並填滿剩餘空間 */
            overflow-y: auto; /* 內容區可以垂直滾動 */
        }

        .modal-body p {
            font-size: 1.05rem;
            margin-bottom: 1rem;
        }

        .modal-body li {
            font-size: 1.05rem;
            margin-bottom: 0.6rem;
        }

        /* 共用迷你錦旗基底 */
        .mini-pennant {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 56px;
        clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
        position: relative;
        margin-right: 8px;
        vertical-align: middle;
        font-family: 'Microsoft JhengHei', sans-serif;
        font-size: 12px;
        font-weight: bold;
        color: #c00;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        /* 頂部橫條 */
        .mini-pennant::before {
        content: "";
        position: absolute;
        top: 0;
        width: 100%;
        height: 4px;
        background: currentColor;
        opacity: 0.6;
        }

        /* 底部流蘇 */
        .mini-pennant::after {
        content: "";
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 6px;
        background: repeating-linear-gradient(
            to right,
            rgba(255,255,255,0.7) 0 3px,
            rgba(0,0,0,0.1) 3px 6px
        );
        clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 50%, 0 100%);
        }

        /* 各等級變化 */
        .mini-pennant.vip1 {
            background: #ffeb3b;          /* 純黃色 */
            color: #b8860b;              /* 文字深金色 */
        }

        .mini-pennant.vip2 {
            background: #ffeb3b;         /* 純黃色 */
            color: #b8860b;              /* 文字深金色 */
        }

        .mini-pennant.vip3 {
            background: linear-gradient(to bottom, #ffe600, #ff6600); /* 和 vip2 一樣的顏色 */
        }

        .mini-pennant.vip4 {
            background: linear-gradient(to bottom, #ffe600, #ff6600); /* 和 vip3 一樣的顏色 */
            box-shadow: 0 2px 6px rgba(255, 140, 0, 0.3), inset 0 0 8px rgba(255,255,255,0.3); /* 更柔和的陰影 */
        }

        .mini-pennant.vip5 {
            background: linear-gradient(to bottom, #ffec8b, #ff4500); /* 現在不變 */
            box-shadow: 0 2px 8px rgba(255, 69, 0, 0.8), inset 0 0 12px rgba(255,255,255,0.7);
            animation: glow 2s infinite alternate;
        }

        /* 閃爍動畫 */
        @keyframes glow {
            from { 
                box-shadow: 0 2px 8px rgba(255, 69, 0, 0.8), inset 0 0 12px rgba(255,255,255,0.7); 
            }
            to { 
                box-shadow: 0 2px 12px rgba(255, 69, 0, 1), inset 0 0 16px rgba(255,255,255,1); 
            }
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
                <!-- 標題列 -->
                <div class="modal-header">
                    <h5 class="modal-title" id="helpModalLabel">
                        <i class="fa-solid fa-circle-info me-2"></i> 榮譽機制與排名規則說明
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                
                <div class="modal-body">
                    <!-- 用法 
                    <p class="mb-3">🎉 歡迎使用 <strong>輔仁大學愛校建言捐款系統</strong> 的榮譽榜功能！以下是詳細說明：</p>

                    <ul class="list-unstyled ps-4">
                        <li class="mb-3">
                            <i class="fa-solid fa-hand-holding-dollar text-success me-2"></i>
                            <strong>捐款金額排行榜：</strong> 依照用戶累積捐款金額排序，顯示前 10 名。
                        </li>
                        <li class="mb-3">
                            <i class="fa-solid fa-lightbulb text-warning me-2"></i>
                            <strong>建言發布數排行榜：</strong> 依照用戶提交建言次數排序。
                        </li>
                        <li class="mb-3">
                            <i class="fa-solid fa-user-circle text-primary me-2"></i>
                            上傳的個人頭像會顯示在排行榜中。
                        </li>
                        <li class="mb-3">
                            <i class="fa-solid fa-layer-group text-info me-2"></i>
                            使用上方的「分頁」可以快速切換排行榜分類。
                        </li>
                    </ul>

                    <p class="mt-4 text-info-emphasis">
                        ✨ <strong>此功能旨在表揚積極參與的同學與校友，感謝您的熱情與支持！</strong>
                    </p>-->
                    
                    <!-- 榮譽等級說明 -->
                    <h5 class="mt-4">
                    <i class="fa-solid fa-trophy me-2"></i> 榮譽等級說明
                    </h5>
                    <ul class="list-unstyled ps-4">
                    <li class="mb-3">
                        <span class="mini-pennant vip1">VIP1</span>
                        <strong>VIP1：</strong> 只要有進行捐款，即可獲得此榮譽等級（捐款金額：無門檻）。
                    </li>
                    <li class="mb-3">
                        <span class="mini-pennant vip2">VIP2</span>
                        <strong>VIP2：</strong> 捐款金額達 1,000 元以上者，將獲得此榮譽等級。
                    </li>
                    <li class="mb-3">
                        <span class="mini-pennant vip3">VIP3</span>
                        <strong>VIP3：</strong> 捐款金額達 5,000 元以上者，將獲得此榮譽等級。
                    </li>
                    <li class="mb-3">
                        <span class="mini-pennant vip4">VIP4</span>
                        <strong>VIP4：</strong> 捐款金額達 10,000 元以上者，將獲得此榮譽等級。
                    </li>
                    <li class="mb-3">
                        <span class="mini-pennant vip5">VIP5</span>
                        <strong>VIP5：</strong> 捐款金額總數排名前 10 名者，將獲得此榮譽等級。
                    </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


</body>

</html>