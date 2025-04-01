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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            height: 20px;
            background-color: #28a745;
            font-weight: bold;
            text-align: center;
            line-height: 20px;
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
        /*捐款卡片 */
        .card-custom {
            height: 100%; 
            display: flex;
            flex-direction: column;
        }

        .card-body {
            flex-grow: 1; 
        }

        .card-footer {
            margin-top: auto; 
        }

        .row {
            margin-top: 20px;
        }

        .col-md-6 {
            margin-bottom: 20px;
        }

        .card {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }
        .text-pink {
            color: #ff69b4; /* 粉紅色 */
        }

    </style>
</head>

<body>
    <div class="sidebar">
    <h3>輔仁大學愛校建言捐款系統</h3>
        <a href="main.php"><i class="icon fas fa-home"></i> 首頁</a>
        <a href="suggestions.php"><i class="icon fas fa-scroll"></i> 建言總覽</a>
        <a href="donate.php"><i class="icon fas fa-money-bill-wave"></i> 捐款進度</a>
        <a href="statement.php"><i class="icon fas fa-chart-pie"></i>捐款報表</a>
        <a href="honor.php"><i class="icon fas fa-medal"></i>榮譽機制</a>
        <a href="contact.php"><i class="icon fas fa-phone-alt"></i> 聯絡我們</a>
        <a href="make_suggestions.php"><i class="icon fas fa-comment-dots"></i> 提出建言</a>
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
        <h2 class="mb-4 text-primary">輔大愛校建言捐款系統</h2>
        <div class="row">
            <!-- 建言 1 -->
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header">建言1</div>
                    <div class="card-body">
                        <p><strong>區域：</strong>學生希望改善校內飲水機品質</p>
                        <p><strong>內容：</strong>學生表示校內飲水機水質不好，建議增設更多飲水機，並定期清潔。</p>
                    </div>
                    <div class="card-footer">
                        <div class="progress mb-3">
                            <div class="progress-bar" style="width: 60%;">已捐款 60%</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="#"><i class="fa-solid fa-piggy-bank text-pink">點我捐款</i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 建言 2 -->
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header">建言2</div>
                    <div class="card-body">
                        <p><strong>區域：</strong>學校餐廳菜單多樣化</p>
                        <p><strong>內容：</strong>學生建議學校餐廳增加素食選項並改善餐廳環境。</p>
                    </div>
                    <div class="card-footer">
                        <div class="progress mb-3">
                            <div class="progress-bar" style="width: 40%;">已捐款 40%</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="#"><i class="fa-solid fa-piggy-bank text-pink">點我捐款</i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 建言 3 -->
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header">建言3</div>
                    <div class="card-body">
                        <p><strong>區域：</strong>學校圖書館閱讀空間改善</p>
                        <p><strong>內容：</strong>學生建議學校圖書館增加更多舒適的閱讀區，並提供更多安靜的學習空間。</p>
                    </div>
                    <div class="card-footer">
                        <div class="progress mb-3">
                            <div class="progress-bar" style="width: 30%;">已捐款 30%</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="#"><i class="fa-solid fa-piggy-bank text-pink">點我捐款</i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 建言 4 -->
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header">建言4</div>
                    <div class="card-body">
                        <p><strong>區域：</strong>改善校內電腦設備</p>
                        <p><strong>內容：</strong>學生建議更新校內電腦設備，以提高學習效率和舒適度。</p>
                    </div>
                    <div class="card-footer">
                        <div class="progress mb-3">
                            <div class="progress-bar" style="width: 20%;">已捐款 20%</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="#"><i class="fa-solid fa-piggy-bank text-pink">點我捐款</i></a>
                        </div>
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