<!--導覽列iframe-->
<?php
session_start();

// 判斷是否登入和是否為管理員
$is_logged_in = isset($_SESSION['User_Name']);
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
            margin: 0;
            display: flex;
            min-height: 100vh;
            color: #333;
        }

        .sidebar {
            width: 340px;
            background: linear-gradient(135deg, rgb(160, 164, 138), rgb(15, 21, 24));
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

        .footer {
            text-align: center;
            background: linear-gradient(135deg, rgb(160, 164, 138), rgb(15, 21, 24));
            padding: 20px;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
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
            background-color: rgb(198, 225, 230, 0.7);
            border-radius: 20px;
            margin: 20px;
            padding: 4px 20px;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.3s;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-custom:hover {
            background-color: rgb(104, 105, 121);
            transform: translateY(-5px);
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
            bottom: 80px;
            right: 20px;
            background-color: rgba(0, 0, 0, 0.4);
            color: white;
            font-size: 2rem;
            padding: 10px 20px;
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
            width: 900px;
            border: none;
            /* 移除邊框 */
            margin-left: 400px;
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

        @media (min-width: 768px) {

            /*大於768px*/
            .grid-containers {
                display: grid;
                grid-template-columns: 50% 50%;
            }
        }

        @media (max-width: 768px) {

            /*小於768px*/
            .grid-containers {
                display: grid;
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!--導覽列-->
    <div class="header">
        <div class="sidebar">
            <a href="main.php" target="contentFrame">
                <h1>FJU I-Money</h1>
            </a>
            <a href="main.php" target="contentFrame"><i class="icon fas fa-home"></i><b> 首頁</b></a>
            <a href="suggestions.php" target="contentFrame"><i class="icon fas fa-scroll"></i><b> 建言總覽</b></a>
            <a href="make_suggestions.php" target="contentFrame"><i class="icon fas fa-comment-dots"></i><b> 提出建言</b></a>
            <a href="record.php" target="contentFrame"><i class="icon fas fa-clipboard-list"></i><b>建言紀錄</b></a>
            <a href="#" target="contentFrame"><i class="icon fas fa-hand-holding-usd"></i><b>捐款進度</b></a> <!--考完再嵌連結 donate.php-->
            <a href="honor.php" target="contentFrame"><i class="icon fas fa-medal"></i><b> 榮譽機制</b></a>
            <a href="contact.php" target="contentFrame"><i class="icon fas fa-phone-alt"></i><b> 聯絡我們</b></a>
        </div>
        <div class="main-content">
            <!-- 顯示登入或登出按鈕 -->
            <?php if ($is_logged_in): ?>
                <a href="logout.php" target="contentFrame">
                    <button type="button" class="btn btn-custom btn-custom-logout btn-position"><b>登出</b></button>
                </a>
            <?php else: ?>
                <a href="login.php" target="contentFrame">
                    <button type="button" class="btn btn-custom btn-outline-success btn-position"><b>登入</b></button>
                </a>
            <?php endif; ?>

        </div>
        <div class="content">
            <iframe name="contentFrame" src="main.php" width="100%" height="1000" allowtransparency="true"></iframe>
            
        </div>


        <!--footer-->
        <div class="footer">
            <b>2025 © 天主教輔仁大學 愛校建言捐款系統</b>
        </div>

        <!-- Back to Top Button -->
        <div class="back-to-top" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
            ↑
        </div>
</body>

</html>