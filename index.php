<?php
session_start();

// 判斷是否登入和是否為管理員
$is_logged_in = isset($_SESSION['User_Name']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>輔仁大學愛校建言捐款系統</title>

    <!-- 外部資源 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="https://www.design-thinking.tw/assets/images/school-logo/FJU.png">
    <style>
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
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* 側邊欄 */
        .sidebar {
            width: 340px;
            background:
                linear-gradient(rgba(149, 147, 80, 0.62), rgba(0, 0, 0, 0.83)),
                url('https://cdn.pixabay.com/photo/2015/07/25/15/24/money-860128_1280.jpg');
            /* 輔仁大學圖案連結 */
            background-size: 350px;
            /* 控制浮水印圖案的大小 */
            background-repeat: repeat;
            /* 重複顯示浮水印 */
            background-position: center;
            background-attachment: fixed;
            /* 固定背景 */
            color: white;
            padding: 30px;
            position: fixed;
            height: 100%;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2);
            z-index: 100;
        }

        .sidebar h1 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(10px);
        }

        .icon {
            font-size: 1.5rem;
            /* 設定圖示的基本大小 */
            width: 1.5rem;
            /* 設定寬度 */
            height: 1.5rem;
            /* 設定高度 */
            margin-right: 10px;
            vertical-align: middle;
            /* 保證垂直居中 */
            display: inline-block;
            /* 確保圖示作為區塊顯示 */
        }

        /* 主內容區 iframe */
        .content {
            position: absolute;
            top: 0;
            left: 300px;
            right: 0;
            bottom: 60px;
            overflow: hidden;
            background: url('https://www.transparenttextures.com/patterns/brick-wall.png');

            padding: 20px;
        }




        .content iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
            background-color: transparent;
        }

        /* 登入-登出按鈕 */
        .btn-position {
            position: fixed;
            top: 30px;
            right: 40px;
            z-index: 200;
        }

        .btn-custom {
            background-color: rgba(198, 225, 230);
            color: midnightblue;
            border-radius: 25px;
            padding: 5px 25px;
            font-size: 1rem;
            transition: 0.3s;
            border: 1px solid rgba(104, 105, 121, 0.8);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-custom:hover {
            background-color: rgb(104, 105, 121, 0.7);
            color: #fff;
            transform: translateY(-3px);
        }

        /* Footer */
        .footer {
            height: 60px;
            line-height: 60px;
            text-align: center;
            background: linear-gradient(135deg, rgb(160, 164, 138), rgb(15, 21, 24));
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 99;
        }

        /* 回頂部按鈕 */
        .back-to-top {
            position: fixed;
            bottom: 80px;
            right: 20px;
            background-color: rgba(0, 0, 0, 0.4);
            color: white;
            font-size: 2rem;
            padding: 10px 20px;
            border-radius: 50%;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 300;
            transition: 0.3s;
        }

        .back-to-top:hover {
            background-color: rgba(0, 0, 0, 0.7);
            transform: translateY(-5px);
        }

        .back-to-top {
            position: fixed;
            bottom: 80px;
            right: 20px;
            background-color: rgba(0, 0, 0, 0.4);
            color: white;
            font-size: 2rem;
            padding: 10px 20px;
            border-radius: 50%;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 300;
            transition: 0.3s;
            display: none;
            /* 預設不顯示 */
        }

        .back-to-top:hover {
            background-color: rgba(0, 0, 0, 0.7);
            transform: translateY(-5px);
        }
    </style>
</head>

<body>
    <!-- 導覽列 -->
    <div class="sidebar">
        <a href="main.php" target="contentFrame">
            <h1>FJU I-Money</h1>
        </a>
        <a href="main.php" target="contentFrame"><i class="icon fas fa-home"></i><b> 首頁</b></a>
        <a href="record.php" target="contentFrame"><i class="icon fas fa-user"></i><b> 個人檔案</b></a>
        <a href="suggestions.php" target="contentFrame"><i class="icon fas fa-scroll"></i><b> 建言總覽</b></a>
        <a href="make_suggestions.php" target="contentFrame"><i class="icon fas fa-comment-dots"></i><b> 提出建言</b></a>
        <a href="donate.php" target="contentFrame"><i class="icon fas fa-hand-holding-usd"></i><b> 捐款進度</b></a>
        <a href="honor.php" target="contentFrame"><i class="icon fas fa-medal"></i><b> 榮譽機制</b></a>
        <a href="contact.php" target="contentFrame"><i class="icon fas fa-phone-alt"></i><b> 聯絡我們</b></a>
    </div>

    <!-- 登入/登出按鈕 -->
    <?php if ($is_logged_in): ?>
        <a href="logout.php" target="contentFrame">
            <button class="btn btn-custom btn-position"><b>登出</b></button>
        </a>
    <?php else: ?>
        <a href="login.php" target="contentFrame">
            <button class="btn btn-custom btn-outline-success btn-position"><b>登入</b></button>
        </a>
    <?php endif; ?>

    <!-- 主內容區 -->
    <div class="content">
        <iframe name="contentFrame" src="main.php" allowtransparency="true"></iframe>
    </div>

    <!-- 頁尾 -->
    <div class="footer">
        <b>2025 © 天主教輔仁大學 愛校建言捐款系統</b>
    </div>

    <!-- 回頂部按鈕 -->
    <div class="back-to-top" id="backToTopBtn">
        ↑
    </div>

    <script>
        const backToTopBtn = document.getElementById('backToTopBtn');
        const iframe = document.querySelector('iframe[name="contentFrame"]');

        // 點擊按鈕時滾動 iframe 頁面回頂部
        backToTopBtn.addEventListener('click', () => {
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });

        // iframe 載入新頁面後，掛載 scroll 事件
        iframe.addEventListener('load', () => {
            const iframeWindow = iframe.contentWindow;

            // 清除舊的事件（避免重複掛載）
            iframeWindow.removeEventListener('scroll', toggleBackToTop);

            // 定義滾動處理函式
            function toggleBackToTop() {
                const scrollTop = iframeWindow.scrollY || iframeWindow.pageYOffset;
                backToTopBtn.style.display = scrollTop > 200 ? 'block' : 'none';
            }

            // 初始執行一次以防一進來就已經有捲動
            toggleBackToTop();

            // 加上監聽
            iframeWindow.addEventListener('scroll', toggleBackToTop);
        });
    </script>

</body>

</html>