<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}

require_once "dblink3.php";
require_once "honor_helper.php"; // 加上這行

// 取得 VIP 資訊（只取一次）
$User_ID = $_SESSION['User_ID'];
$vipInfo = getVipLevel($conn, $User_ID); // 使用 conn 作為 DB 連線物件

// 捐款排行榜
$donation_month_sql = "
    SELECT ua.User_ID, ua.Nickname, ua.Avatar, SUM(d.Donation_Amount) AS total_donation
    FROM donation d
    JOIN useraccount ua ON d.User_ID = ua.User_ID
    WHERE MONTH(d.Donation_Date) = MONTH(CURRENT_DATE())
      AND YEAR(d.Donation_Date) = YEAR(CURRENT_DATE())
    GROUP BY ua.User_ID, ua.Nickname, ua.Avatar
    ORDER BY total_donation DESC
";
$donation_month_result = $conn->query($donation_month_sql);
if (!$donation_month_result) {
    die("本月捐款排名查詢錯誤: " . $conn->error);
}

// 歷史捐款排行榜
$donation_history_sql = "
    SELECT ua.User_ID, ua.Nickname, ua.Avatar, SUM(d.Donation_Amount) AS total_donation
    FROM donation d
    JOIN useraccount ua ON d.User_ID = ua.User_ID
    GROUP BY ua.User_ID, ua.Nickname, ua.Avatar
    ORDER BY total_donation DESC
";
$donation_history_result = $conn->query($donation_history_sql);
if (!$donation_history_result) {
    die("歷史捐款排名查詢錯誤: " . $conn->error);
}

?>


<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>榮譽榜 | 輔仁大學愛校建言捐款系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <style>
        body {
            max-width: 90%;
            margin: 0 auto;
            padding: 30px;
            font-family: "Noto Serif TC", serif;
            line-height: 1.8;
            background-color: transparent;
            overflow-x: hidden;
            color: #2c3e50;
        }

        .ranking-wrapper {
            position: relative;
            /* 加這行讓子元素可絕對定位 */
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
            padding: 0.5rem 40px;
            font-size: 1rem;
            font-weight: bold;
            border: 1px solid #dee2e6 !important;
            border-top-left-radius: 15px !important;
            border-top-right-radius: 15px !important;
            margin-right: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nav-link.active {
            background-color: rgba(85, 164, 186) !important;
            color: #fff !important;
        }

        .nav-link:hover {
            background-color: rgb(77, 112, 128);
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

        .table th:nth-child(1),
        .table th:nth-child(3),
        .table th:nth-child(4),
        .table td:nth-child(1),
        .table td:nth-child(3),
        .table td:nth-child(4) {
            text-align: center;
        }

        .table th:nth-child(2),
        .table td:nth-child(2) {
            padding-left: 50px;
        }

        .table th {
            background-color: rgba(85, 164, 186);
            color: white;
            font-size: 1rem;
            font-weight: bold;
            padding: 0.8rem 1.5rem;
        }

        .table tbody td {
            color: #555;
            font-size: 0.95rem;
            font-weight: bold;
            padding: 0.4rem 1.5rem;
        }
        /*
        .table tbody tr:nth-child(even) {
            background-color: #e6f0f8;
        }*/

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
            align-items: center;    /* 垂直置中 */
            justify-content: flex-start;  /* 水平靠左 */
        }
        .table td, .table th {
            vertical-align: middle;
            text-align: center;
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
            height: 85%;
            /* 確保內容區塊高度填滿 */
            max-width: 70%;
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
            height: 100%;
            /* 確保內容區塊高度填滿 */
            backdrop-filter: blur(15px);
            border-radius: 25px;
            border: none;
        }

        /* Modal 標題區 */
        .modal-header {
            background: linear-gradient(135deg, #55a4ba, #3793c1);
            color: white;
            border-bottom: none;
            padding: 1.5rem 3rem;
        }

        .modal-header .modal-title {
            font-size: 1.2rem;
            font-weight: bold;
        }

        /* Modal 內容區內推與圓角 */
        .modal-body {
            padding: 1rem 3rem;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
            flex-grow: 1;
            /* 自動擴展並填滿剩餘空間 */
            overflow-y: auto;
            /* 內容區可以垂直滾動 */
        }

        .modal-body h6 {
            color: #2a4d69;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 2rem 0;
        }

        .icon {
            font-size: 1.2rem;
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 10px;
            vertical-align: middle;
            display: inline-block;
        }

        .modal-body p {
            color: #666;
            font-size: 1rem;
            font-weight: 600;
        }

        .modal-body li {
            color: #666;
            font-size: 0.95rem;
            font-weight: 600;
        }

        /* 共用錦旗設定 */
        .mini-pennant {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 45px;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
            position: relative;
            vertical-align: middle;
            font-family: "Noto Serif TC", serif;
            font-size: 0.9rem;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* 頂部橫條 */
        .mini-pennant::before {
            content: "";
            position: absolute;
            top: 0;
            width: 100%;
            height: 5px;
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
            background: repeating-linear-gradient(to right,
                    rgba(255, 255, 255, 0.7) 0 3px,
                    rgba(0, 0, 0, 0.1) 3px 6px);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 50%, 0 100%);
        }

        /* VIP 等級藍綠色＋強漸層風格 */
        .mini-pennant.vip1 {
            background: linear-gradient(135deg, #d4f9f9, #a0e9eb);
            color: #007777;
        }

        .mini-pennant.vip2 {
            background: linear-gradient(135deg, #a0e9eb, #5ed9d1);
            color: #006666;
        }

        .mini-pennant.vip3 {
            background: linear-gradient(135deg, #72e2dc, #34c9c2, #1fb3ac);
            color: #ffffff;
            box-shadow: inset 0 0 4px rgba(255, 255, 255, 0.3);
        }

        .mini-pennant.vip4 {
            background: linear-gradient(135deg, #4bd2cb, #1fa49c, #007c75);
            color: #e0ffff;
            box-shadow: 0 2px 6px rgba(0, 120, 120, 0.5), inset 0 0 10px rgba(255, 255, 255, 0.4);
        }

        .mini-pennant.vip5 {
            background: linear-gradient(135deg, #3ccac2, #009a94, #005e58);
            color: #f0ffff;
            box-shadow: 0 2px 10px rgba(0, 128, 128, 0.9), inset 0 0 14px rgba(255, 255, 255, 0.8);
            animation: glow-strong-teal 2s infinite alternate;
        }

        /* 強光暈動畫 */
        @keyframes glow-strong-teal {
            from {
                box-shadow: 0 2px 10px rgba(0, 128, 128, 0.8), inset 0 0 14px rgba(255, 255, 255, 0.6);
            }

            to {
                box-shadow: 0 2px 14px rgba(0, 200, 200, 1), inset 0 0 18px rgba(255, 255, 255, 1);
            }
        }


        /* 放大旗幟 */
        /* 頂部橫條 */
        .mini-pennant.large {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 90px;
            height: 120px;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
            position: relative;
            margin-bottom: 10px;
            vertical-align: middle;
            font-family: "Noto Serif TC", serif;
            font-size: 1.5rem;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .mini-pennant.large::before {
            content: "";
            position: absolute;
            top: 0;
            width: 100%;
            height: 10px;
            background: currentColor;
            opacity: 0.6;
        }

        /* 底部流蘇 */
        .mini-pennant.large::after {
            content: "";
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 12px;
            background: repeating-linear-gradient(to right,
                    rgba(255, 255, 255, 0.7) 0 3px,
                    rgba(0, 0, 0, 0.1) 3px 6px);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 50%, 0 100%);
        }

        .mini-pennant.vip1 .large {
            background: linear-gradient(135deg, #d4f9f9, #a0e9eb);
            color: #007777;
        }

        .mini-pennant.vip2 .large {
            background: linear-gradient(135deg, #a0e9eb, #5ed9d1);
            color: #006666;
        }

        .mini-pennant.vip3 .large {
            background: linear-gradient(135deg, #72e2dc, #34c9c2, #1fb3ac);
            color: #ffffff;
            box-shadow: inset 0 0 4px rgba(255, 255, 255, 0.3);
        }

        .mini-pennant.vip4 .large {
            background: linear-gradient(135deg, #4bd2cb, #1fa49c, #007c75);
            color: #e0ffff;
            box-shadow: 0 2px 6px rgba(0, 120, 120, 0.5), inset 0 0 10px rgba(255, 255, 255, 0.4);
        }

        .mini-pennant.vip5 .large {
            background: linear-gradient(135deg, #3ccac2, #009a94, #005e58);
            color: #f0ffff;
            box-shadow: 0 2px 10px rgba(0, 128, 128, 0.9), inset 0 0 14px rgba(255, 255, 255, 0.8);
            animation: glow-strong-teal 2s infinite alternate;
        }

        #vipCarousel .carousel-inner {
            min-height: 250px;
        }

        #vipCarousel .carousel-item {
            height: 100%;
            min-height: 250px;
            display: none;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        #vipCarousel .carousel-item.active {
            display: flex;
        }

        .carousel-control-prev,
        .carousel-control-next {
            top: 50%;
            margin: 0 1rem;
            transform: translateY(-50%);
            width: 60px;
            /* 調整按鈕寬度 */
            height: 60px;
            /* 調整按鈕高度 */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: all 0.3s ease;
        }

        .carousel-control-prev span,
        .carousel-control-next span {
            font-size: 1rem;
            font-weight: bold;
            color: #555;
        }

        .carousel-control-prev {
            left: -40px;
            /* 調整與輪播邊界的距離，可依需要調 */
        }

        .carousel-control-next {
            right: -40px;
        }

        /* 防止輪播按鈕重疊文字 */
        #vipCarousel .carousel-inner {
            padding-right: 50px;
            /* 右側留出空間給輪播按鈕 */
            padding-left: 50px;
            /* 左側留出空間給輪播按鈕 */
        }

        #vipCarousel .carousel-control-prev,
        #vipCarousel .carousel-control-next {
            z-index: 10;
            /* 確保按鈕位於文字區域的前面 */
        }

        .avatar-nickname-wrapper {
            display: flex;
            align-items: center;
            gap: 10px; /* avatar 跟暱稱間距 */
        }

        .avatar-wrapper {
            position: relative;
            width: 40px;   /* 調整大小 */
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.7), rgba(200, 200, 200, 0.7));
            overflow: hidden;
            padding: 2px;
        }

        /* 內層圖片 */
        .avatar-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        /* VIP 動畫光暈 */
        @keyframes vip-glow {
            0%, 100% {
                box-shadow: 0 0 15px rgba(0, 255, 204, 0.4);
            }
            70% {
                box-shadow: 0 0 30px rgba(0, 204, 255, 0.8);
            }
        }

        /* VIP3 - 冰藍 */
        .avatar-wrapper.vip3 {
            background: linear-gradient(135deg, #ffffff, #00e5ff, #00bcd4);
            animation: vip-glow 3s ease-in-out infinite;
            box-shadow: 0 0 12px rgba(0, 191, 212, 0.5);
        }

        /* VIP4 - 紫粉 */
        .avatar-wrapper.vip4 {
            background: linear-gradient(135deg, #a18cd1, #fbc2eb);
            animation: vip-glow 3s ease-in-out infinite;
            box-shadow: 0 0 15px rgba(150, 90, 220, 0.7);
        }

        /* VIP5 - 綠藍 */
        .avatar-wrapper.vip5 {
            background: linear-gradient(135deg, #003366, #00c9ff, #92fe9d);
            animation: vip-glow 3s ease-in-out infinite;
            box-shadow: 0 0 30px rgba(0, 204, 255, 0.9);
        }

        /* 暱稱文字 */
        .nickname {
            font-weight: bold;
            font-size: 1rem;
            color: #333;
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
                <a class="nav-link active" id="donation-month-tab" data-bs-toggle="tab" href="#donation-month" role="tab" aria-controls="donation-month" aria-selected="true">
                    本月捐款金額排名
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="donation-history-tab" data-bs-toggle="tab" href="#donation-history" role="tab" aria-controls="donation-history" aria-selected="false">
                    歷史捐款金額排名
                </a>
            </li>
        </ul>

        <div class="tab-content" id="rankingTabContent">
            <!-- 本月捐款排行榜 -->
            <div class="tab-pane fade show active" id="donation-month" role="tabpanel" aria-labelledby="donation-month-tab">
                <table class="table table-bordered">
                    <colgroup>
                        <col style="width: 20%;">
                        <col style="width: 30%;">
                        <col style="width: 30%;">
                        <col style="width: 20%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>名次</th>
                            <th>用戶暱稱</th>
                            <th>本月捐款金額 (NT$)</th>
                            <th>榮譽等級</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        while ($row = $donation_month_result->fetch_assoc()):
                            // 每個用戶取得自己的 VIP 資訊
                            $vipInfo = getVipLevel($conn, $row['User_ID']);
                        ?>
                            <tr>
                                <td>
                                    <?= $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : ($rank == 3 ? '🥉' : $rank)) ?>
                                </td>
                                <td>
                                    <?php 
                                        $avatar = !empty($row['Avatar']) ? htmlspecialchars($row['Avatar']) : 'images/default-avatar.png';
                                        $vip_class = htmlspecialchars($vipInfo['class']);
                                    ?>
                                    <div class="avatar-nickname-wrapper" title="VIP 等級: <?= htmlspecialchars($vipInfo['label']) ?> - <?= htmlspecialchars($vipInfo['tooltip']) ?>">
                                        <div class="avatar-wrapper <?= $vip_class ?>">
                                            <img src="<?= $avatar ?>" alt="User <?= $rank ?>" class="avatar">
                                        </div>
                                        <span class="nickname"><?= htmlspecialchars($row['Nickname']) ?></span>
                                    </div>
                                </td>

                                <td>NT$ <?= number_format($row['total_donation']) ?></td>
                                <td>
                                    <?php if ($vipInfo['level'] != 0): ?>
                                        <span class="mini-pennant <?= $vipInfo['class'] ?>">
                                            <?= $vipInfo['label'] ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php 
                            $rank++;
                        endwhile; 
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- 歷史捐款排行榜 -->
            <div class="tab-pane fade" id="donation-history" role="tabpanel" aria-labelledby="donation-history-tab">
                <table class="table table-bordered">
                    <colgroup>
                        <col style="width: 20%;">
                        <col style="width: 30%;">
                        <col style="width: 30%;">
                        <col style="width: 20%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>名次</th>
                            <th>用戶暱稱</th>
                            <th>累計捐款金額 (NT$)</th>
                            <th>榮譽等級</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $rank = 1;
                            while ($row = $donation_history_result->fetch_assoc()):
                                // 每位使用者個別取得 VIP 等級資訊
                                $vipInfo = getVipLevel($conn, $row['User_ID']);
                        ?>
                            <tr>
                                <td>
                                    <?= $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : ($rank == 3 ? '🥉' : $rank)) ?>
                                </td>
                                <td>
                                    <?php 
                                        $avatar = !empty($row['Avatar']) ? htmlspecialchars($row['Avatar']) : 'images/default-avatar.png';
                                        $vip_class = htmlspecialchars($vipInfo['class']);
                                    ?>
                                    <div class="avatar-nickname-wrapper" title="VIP 等級: <?= htmlspecialchars($vipInfo['label']) ?> - <?= htmlspecialchars($vipInfo['tooltip']) ?>">
                                        <div class="avatar-wrapper <?= $vip_class ?>">
                                            <img src="<?= $avatar ?>" alt="User <?= $rank ?>" class="avatar">
                                        </div>
                                        <span class="nickname"><?= htmlspecialchars($row['Nickname']) ?></span>
                                    </div>
                                </td>
                                <td>NT$ <?= number_format($row['total_donation']) ?></td>
                                <td>
                                    <?php if ($vipInfo['level'] != 0): ?>
                                        <span class="mini-pennant <?= $vipInfo['class'] ?>">
                                            <?= $vipInfo['label'] ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                $rank++;
                            endwhile; 
                            ?>
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
                    <h6 class="mt-4"><i class="icon fa-solid fa-ranking-star me-2"></i> 榮譽排名規則</h6>
                    <ul class="list-unstyled ps-4">
                        <p>根據用戶的捐款金額，系統依以下規則進行榮譽排名計算：</p>
                        <li class="mb-3">
                            <strong>累計捐款金額排名前 3 名：</strong> 將會在榜單中顯示為「榮譽捐款者」，並且享有專屬的榮譽等級。
                        </li>
                        <li class="mb-3">
                            <strong>本月捐款金額榮譽榜：</strong> 本月榮譽排名會根據當月累計捐款金額進行實時排名。
                        </li>
                    </ul>


                    <!-- 榮譽等級說明 -->
                    <h6 class="mt-5"><i class="icon fa-solid fa-trophy me-2"></i> 榮譽等級取得條件</h6>
                    <ul class="list-unstyled ps-4">
                        <li class="mb-3">
                            <span class="mini-pennant vip1 mr-3">I</span>
                            <strong style="margin-left: 20px;">VIP 1：</strong> 進行捐款即可獲得此榮譽等級（捐款金額：無門檻）。
                        </li>
                        <li class="mb-3">
                            <span class="mini-pennant vip2 mr-3">II</span>
                            <strong style="margin-left: 20px;">VIP 2：</strong> 累計捐款金額達 NT$ 5,000 元以上的用戶可獲得此榮譽等級。
                        </li>
                        <li class="mb-3">
                            <span class="mini-pennant vip3 mr-3">III</span>
                            <strong style="margin-left: 20px;">VIP 3：</strong> 累計捐款金額達 NT$ 10,000 元以上的用戶可將獲得此榮譽等級。
                        </li>
                        <li class="mb-3">
                            <span class="mini-pennant vip4 mr-3">IV</span>
                            <strong style="margin-left: 20px;">VIP 4：</strong> 累計捐款金額達 NT$ 50,000 元以上的用戶可將獲得此榮譽等級。
                        </li>
                        <li class="mb-3">
                            <span class="mini-pennant vip5 mr-3">V</span>
                            <strong style="margin-left: 20px;">VIP 5：</strong> 累計捐款金額總數排名前三名的用戶可獲得此榮譽等級。
                        </li>
                    </ul>

                    <!-- 輪播區域開始 -->
                    <h6 class="mt-5"><i class="icon fa-solid fa-gift me-2"></i> 榮譽等級對應獎勵</h6>

                    <div id="vipCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="text-center">
                                    <span class="mini-pennant vip1 large">I</span>
                                    <p class="mt-4 mb-4">獲得此榮譽等級者，個人主頁將展示專屬榮譽等級徽章，以證明您的貢獻！</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="text-center">
                                    <span class="mini-pennant vip2 large">II</span>
                                    <p class="mt-4 mb-4">
                                        獲得此榮譽等級者，除上述獎勵外，<br>
                                        登入時享有 VIP 專屬的問候訊息以及彩帶歡迎特效！
                                    </p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="text-center">
                                    <span class="mini-pennant vip3 large">III</span>
                                    <p class="mt-4 mb-4">
                                        獲得此榮譽等級者，除上述獎勵外，<br>
                                        可獲得(尚在施工中)特權！
                                    </p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="text-center">
                                    <span class="mini-pennant vip4 large">IV</span>
                                    <p class="mt-4 mb-4">
                                        尊榮用戶！獲得此榮譽等級者，除上述獎勵外，<br>
                                        您發布的建言將被設置為置頂建言！
                                    </p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="text-center">
                                    <span class="mini-pennant vip5 large">V</span>
                                    <p class="mt-4 mb-4">
                                        無私的奉獻者！獲得此榮譽等級者，除上述獎勵外，<br>
                                        將被列在首頁榮譽榜特別表揚，並享有(尚在施工中)特權！
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- 輪播控制 -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#vipCarousel" data-bs-slide="prev">
                            <span aria-hidden="true"><i class="fa-solid fa-angle-left fa-2x"></i></span>
                            <span class="visually-hidden">上一頁</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#vipCarousel" data-bs-slide="next">
                            <span aria-hidden="true"><i class="fa-solid fa-angle-right fa-2x"></i></span>
                            <span class="visually-hidden">下一頁</span>
                        </button>
                    </div>


                </div>
            </div>
        </div>
    </div>

</body>

</html>