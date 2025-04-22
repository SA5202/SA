<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>捐款進度 | 輔仁大學愛校建言捐款系統</title>

    <!-- 樣式與字體 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: transparent;
            font-family: "Noto Serif TC", serif;
            font-size: 1.1rem;
            line-height: 1.8;
            margin: 0;
            padding: 30px;
            color: #333;
        }

        h3 {
            margin-bottom: 25px;
            font-weight: bold;
        }

        .icon {
            font-size: 1.5rem;
            /* 設定圖示的基本大小 */
            width: 1.5rem;
            /* 設定寬度 */
            height: 1.5rem;
            /* 設定高度 */
            margin-right: 10px;
            display: inline-block;
            /* 確保圖示作為區塊顯示 */
        }

        .donate-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
            /* 加這行才會顯示框線 */
        }

        .card {
            margin-bottom: 25px;
        }

        .progress {
            height: 25px;
            background-color: #e9ecef;
        }

        .progress-bar {
            font-weight: bold;
        }

        .donation-info {
            margin-bottom: 10px;
            font-size: 1rem;
        }
    </style>
</head>

<body>

    <div class="donate-wrapper">
        <h3><i class="icon fas fa-hand-holding-usd"></i> 捐款進度</h3>

        <div class="card shadow-sm">
            <div class="card-header">項目：圖書館翻新計畫</div>
            <div class="card-body">
                <div class="donation-info">目標金額：NT$500,000</div>
                <div class="donation-info">已募得：NT$320,000</div>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 64%;">64%</div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">項目：校園環境綠化</div>
            <div class="card-body">
                <div class="donation-info">目標金額：NT$200,000</div>
                <div class="donation-info">已募得：NT$85,000</div>
                <div class="progress">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 42.5%;">42.5%</div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>