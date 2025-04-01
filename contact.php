<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>輔大愛校建言捐款系統</title>
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

        .main-content {
            margin-left: 300px;
            padding: 50px;
            flex-grow: 1;
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
    </style>
</head>

<body>
    <div class="sidebar">
        <h3>輔大愛校建言</h3>
        <a href="main.php"><i class="icon fas fa-home"></i> 首頁</a>
        <a href="suggestions.php"><i class="icon fas fa-scroll"></i> 建言總覽</a>
        <a href="donate.php"><i class="icon fas fa-money-bill-wave"></i> 捐款進度</a>
        <a href="statement.php"><i class="icon fas fa-chart-pie"></i>捐款報表</a>
        <a href="honor.php"><i class="icon fas fa-medal"></i>榮譽機制</a>
        <a href="contact.php"><i class="icon fas fa-phone-alt"></i> 聯絡我們</a>
    </div>
    <div class="main-content">
        <h2 class="mb-4 text-primary">聯絡我們</h2>
        <div style="margin-left: 20px; margin-top: 40px; font-size: 18px;">
            <i class="fa-solid fa-location-dot"></i> 地址：242062 新北市新莊區中正路 510 號 天主教輔仁大學
            <br><i class="fa-solid fa-phone"></i>生輔組電話：(02)2905-2264
            <br><i class="fa-solid fa-envelope"></i>生輔組服務信箱：<a
                href="mailto:sld@mail.fju.edu.tw">sld@mail.fju.edu.tw</a>
            <br>
            <p>承辦人 邱小姐 電話 02-2905-3031</p>
            <p>日間部辦公室(于斌樓一樓YP104)</p>
            <p>學期中：週一至週五 08：00～16：30</p>
            <p>寒暑假：週一至週四 08：00～16：30</p>
            <br>
            <p>進修部辦公室(進修部大樓二樓ES201)</p>
            <p>學期中：週一至週五 15：00～22：00</p>
            <p>寒暑假：週一至週四 08：00～16：30</p>
            <p>(寒暑假作息請參照本校行事曆)</p>
        </div>
    </div>
    <div class="footer">
        2025 © 輔仁大學 愛校建言系統
    </div>
</body>

</html>