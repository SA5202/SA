<?php
session_start();

// 判斷是否登入和是否為管理員
$is_logged_in = isset($_SESSION['username']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>輔仁大學愛校建言捐款系統</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@550&display=swap">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@200..900&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            text-decoration: none;
            font-family: "Noto Serif TC", serif;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            display: flex;
            min-height: 100vh;
            color: #333;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, rgb(165, 179, 109), rgb(116, 136, 66));
            color: white;
            padding: 30px;
            position: fixed;
            height: 100%;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2);
            z-index: 100;
        }

        .sidebar h3 {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(10px);
        }


        .card {
            margin-bottom: 30px;
            border-radius: 12px;
            border: none;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: rgb(90, 108, 26);
            color: white;
            font-size: 1.5rem;
            font-weight: 500;
            padding: 15px;
            border-radius: 12px 12px 0 0;
        }

        .progress-bar {
            height: 30px;
            background-color: #28a745;
            font-weight: bold;
            text-align: center;
            line-height: 30px;
            border-radius: 8px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background: rgb(90, 108, 26);
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .text-link {
            color: rgb(1, 1, 1);
            text-decoration: none;
        }

        .text-link:hover {
            text-decoration: underline;
        }

        .row {
            margin-top: 40px;
        }

        .icon {
            font-size: 2rem;
            margin-right: 10px;
        }

        .content-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .card-body p {
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .btn-custom {
            border-radius: 50px;
            padding: 10px 30px;
            font-size: 1.1rem;
            transition: background-color 0.3s, transform 0.3s;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-custom:hover {
            background-color: #6f8c3c;
            transform: translateY(-5px);
        }

        .btn-custom-logout {
            background-color: #d9534f;
            border-color: #d43f00;
        }

        .btn-custom-logout:hover {
            background-color: #c9302c;
            border-color: #ac2925;
        }

        /* Align login/logout button to the top right */
        .btn-position {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 200;
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 1.5rem;
            padding: 10px 15px;
            border-radius: 50%;
            transition: background-color 0.3s, transform 0.3s;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 300;
        }

        .back-to-top:hover {
            background-color: rgba(0, 0, 0, 0.7);
            transform: translateY(-5px);
        }

        .main-content {
            display: flex;
            justify-content: center;
            /* 水平置中 */
            align-items: center;
            /* 垂直置中 */
        }

        .content iframe {
            width: 1100px;
            border: none;
            /* 移除邊框 */
            margin-left: 290px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .content {
            display: flex;
            justify-content: center;
            /* 水平置中 */
            align-items: center;
            /* 垂直置中 */
            padding: 20px;
            /* 增加內邊距避免貼邊 */
        }

        iframe {
            font-family: 'Noto Serif TC', serif;
        }
    </style>
</head>

<body>
    <!--導覽列-->
    <div class="header">
        <div class="sidebar">
            <h3>輔仁大學愛校建言捐款系統</h3>
            <a href="main.php" target="contentFrame"><i class="icon fas fa-home"></i><b> 首頁</b></a>
            <a href="suggestions.php" target="contentFrame"><i class="icon fas fa-scroll"></i><b> 建言總覽</b></a>
            <a href="donate.php" target="contentFrame"><i class="icon fas fa-money-bill-wave"></i><b> 捐款進度</b></a>
            <a href="statement.php" target="contentFrame"><i class="icon fas fa-chart-pie"></i><b> 捐款報表</b></a>
            <a href="honor.php" target="contentFrame"><i class="icon fas fa-medal"></i><b> 榮譽機制</b></a>
            <a href="contact.php" target="contentFrame"><i class="icon fas fa-phone-alt"></i><b> 聯絡我們</b></a>
            <a href="make_suggestions.php" target="contentFrame"><i class="icon fas fa-comment-dots"></i> 提出建言</a>
        </div>
        <div class="main-content">
            <!-- 顯示登入或登出按鈕 -->
            <?php if ($is_logged_in): ?>
                <a href="logout.php" target="contentFrame">
                    <button type="button" class="btn btn-custom btn-custom-logout btn-position">
                        登出
                    </button>
                </a>
            <?php else: ?>
                <a href="login.php" target="contentFrame">
                    <button type="button" class="btn btn-custom btn-outline-success btn-position">
                        登入
                    </button>
                </a>
            <?php endif; ?>
        </div>
        <div class="content">
            <iframe name="contentFrame" src="main.php" width="100%" height="1000" allowtransparency="true"></iframe>
        </div>


        <!--footer-->
        <div class="footer">
            2025 © 天主教輔仁大學 愛校建言捐款系統
        </div>

        <!-- Back to Top Button -->
        <div class="back-to-top" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
            ↑
        </div>
</body>

</html>