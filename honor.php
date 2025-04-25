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
    <title>榮譽機制 | 輔仁大學愛校建言捐款系統</title>

    <!-- 字體與樣式 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .honor-wrapper {
            max-width: 1000px;
            margin: 50px auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
            /* 加這行才會顯示框線 */
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

        .honor-item {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .honor-item h5 {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .honor-icon {
            color: #ffc107;
            margin-right: 10px;
        }

        .table-wrapper {
            margin-top: 40px;
        }

        .table thead {
            background-color: #ffc107;
            color: #fff;
        }

        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
        }

        .table tbody tr:nth-child(even) {
            background-color: #fdf7e2;
        }
    </style>
</head>

<body>

    <div class="honor-wrapper">
        <h3><i class="icon fas fa-medal"></i> 榮譽機制</h3>

        <div class="honor-item">
            <h5><i class="fas fa-star honor-icon"></i> 愛校之星</h5>
            <p>凡於學期內提出三項以上建言並經採納者，即可獲頒「愛校之星」證書與小禮物。</p>
        </div>

        <div class="honor-item">
            <h5><i class="fas fa-trophy honor-icon"></i> 卓越貢獻獎</h5>
            <p>年度建言貢獻度前五名者，將於校內頒獎典禮中公開表揚，並獲贈榮譽證書與紀念品。</p>
        </div>

        <div class="honor-item">
            <h5><i class="fas fa-heart honor-icon"></i> 捐款感謝榜</h5>
            <p>所有參與捐款者，將可選擇是否列名於感謝榜，以示表揚其對校園的熱心支持。</p>
        </div>


    </div>

</body>

</html>