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

        /* <uniquifier>: Use a unique and descriptive class name */
        /* <weight>: Use a value from 200 to 900 */

        .pt-serif-regular {
            font-family: "PT Serif", serif;
            font-weight: 400;
            font-style: normal;
        }

        .pt-serif-bold {
            font-family: "PT Serif", serif;
            font-weight: 700;
            font-style: normal;
        }

        .pt-serif-regular-italic {
            font-family: "PT Serif", serif;
            font-weight: 400;
            font-style: italic;
        }

        .pt-serif-bold-italic {
            font-family: "PT Serif", serif;
            font-weight: 700;
            font-style: italic;
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
            background: linear-gradient(135deg, #1a2a6c, #b21f1f);
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
            background-color: #1a2a6c;
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
            background: #1a2a6c;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .text-link {
            color: #007bff;
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
    </style>
</head>

<body>
    <div class="sidebar">
        <h3>輔大愛校建言</h3>
        <a href="1.php"><i class="icon fas fa-home"></i> 首頁</a>
        <a href="suggestions.php"><i class="icon fas fa-scroll"></i> 建言總覽</a>
        <a href="donate.php"><i class="icon fas fa-money-bill-wave"></i> 捐款進度</a>
        <a href="statement.php"><i class="icon fas fa-chart-pie"></i>捐款報表</a>
        <a href="honor.php"><i class="icon fas fa-medal"></i>榮譽機制</a>
        <a href="contact.php"><i class="icon fas fa-phone-alt"></i> 聯絡我們</a>
    </div>
    <div class="main-content">
        <h2 class="mb-4 text-primary">輔大愛校建言捐款系統</h2>
        <div class="card">
            <div class="card-header">捐款進度</div>
            <div class="card-body">
                <div class="progress">
                    <div class="progress-bar" style="width: 60%;">60%</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">📜 最新建言</div>
                    <div class="card-body">
                        <p>學生希望改善校內飲水機品質...</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">📊 捐款報表</div>
                    <div class="card-body">
                        <p><a href="#" class="text-link">下載最新捐款報表</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        2025 © 輔仁大學 愛校建言系統
    </div>
</body>

</html>