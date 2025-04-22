<?php
session_start();

$is_logged_in = isset($_SESSION['User_Name']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>輔仁大學愛校建言捐款系統</title>

    <!-- 外部資源 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/png" href="https://www.design-thinking.tw/assets/images/school-logo/FJU.png" />
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

        .sidebar {
            width: 340px;
            background:
                linear-gradient(rgba(170, 197, 212, 0.62), rgba(24, 54, 65, 0.83)),
                url('https://doqvf81n9htmm.cloudfront.net/data/TommyHuang_147/0705/0714/ntd.jpg');
            background-size: 350px;
            background-repeat: repeat;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 30px 20px;
            position: fixed;
            height: 100%;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2);
            z-index: 100;
            transition: width 0.3s ease;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar h1 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed h1 {
            opacity: 0;
            visibility: hidden;
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
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(10px);
        }

        .sidebar.collapsed a span {
            display: none;
        }

        .icon {
            font-size: 1.5rem;
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 10px;
            vertical-align: middle;
            display: inline-block;
        }

        /* Position the sidebar toggle button at the bottom of the sidebar */
        .toggle-btn {
            position: fixed;
            bottom: 0px;
            /* Position 20px from the bottom */
            left: 340px;
            /* Align with the sidebar */
            background-color: rgba(0, 0, 0, 0.1);
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 150;
            transition: left 0.3s ease, transform 0.3s ease;
            /* Smooth transition */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            /* Add shadow for depth */
            font-size: 1.5rem;
            /* Increase the size of the icon */
            color: #fff;
            text-align: center;
        }

        /* Button hover effect */
        .toggle-btn:hover {
            background-color: rgba(0, 0, 0, 0.7);
            /* Darken on hover */
            transform: translateY(-5px);
            /* Slight upward movement */
        }

        /* When the sidebar is collapsed, move the button to the left */
        .sidebar.collapsed+.toggle-btn {
            left: 80px;
            /* Adjust position when sidebar is collapsed */
        }


        .content {
            position: absolute;
            top: 0;
            left: 340px;
            right: 0;
            bottom: 60px;
            overflow: hidden;
            background: url('https://sis.fju.edu.tw/images/fju_fx_3.svg');
            background-size: 110% auto;
            background-repeat: no-repeat;
            background-position: center;
            padding: 20px;
            transition: left 0.3s ease;
        }

        .sidebar.collapsed~.content {
            left: 80px;
        }

        .content iframe {
            width: 100%;
            height: 100%;
            border: none;
            background-color: transparent;
        }

        .btn-position {
            position: fixed;
            top: 30px;
            right: 40px;
            z-index: 200;
        }

        .btn-custom {
            background-color: rgba(190, 225, 230);
            color: midnightblue;
            border-radius: 25px;
            padding: 5px 20px;
            font-size: 1rem;
            transition: 0.3s;
            border: none;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-custom:hover {
            background-color: rgb(104, 105, 121, 0.5);
            color: #fff;
            transform: translateY(-3px);
        }

        .footer {
            height: 60px;
            line-height: 60px;
            text-align: center;
            background: linear-gradient(135deg, rgba(24, 54, 65, 0.83), rgba(170, 197, 212, 0.62));
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 99;
        }

        .back-to-top {
            position: fixed;
            bottom: 80px;
            right: 40px;
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
        }

        .back-to-top:hover {
            background-color: rgba(0, 0, 0, 0.7);
            transform: translateY(-5px);
        }
    </style>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <a href="main.php" target="contentFrame">
            <h1>FJU I-Money</h1>
        </a>
        <a href="main.php" target="contentFrame"><i class="icon fas fa-home"></i><span><b> 首頁</b></span></a>
        <a href="record.php" target="contentFrame"><i class="icon fas fa-user"></i><span><b> 個人檔案</b></span></a>
        <a href="suggestions.php" target="contentFrame"><i class="icon fas fa-scroll"></i><span><b> 建言總覽</b></span></a>
        <a href="make_suggestions.php" target="contentFrame"><i class="icon fas fa-comment-dots"></i><span><b> 提出建言</b></span></a>
        <a href="donate.php" target="contentFrame"><i class="icon fas fa-hand-holding-usd"></i><span><b> 捐款進度</b></span></a>
        <a href="honor.php" target="contentFrame"><i class="icon fas fa-medal"></i><span><b> 榮譽機制</b></span></a>
        <a href="contact.php" target="contentFrame"><i class="icon fas fa-phone-alt"></i><span><b> 聯絡我們</b></span></a>
    </div>

    <!-- 收合按鈕 -->
    <button class="toggle-btn" onclick="toggleSidebar(this)">
        <i class="fas fa-chevron-left"></i>
    </button>

    <!-- 登入/登出 -->
    <?php if ($is_logged_in): ?>
        <a href="logout.php" target="contentFrame">
            <button class="btn btn-custom btn-position"><b><i class="fa-solid fa-circle-user"></i> 登出</b></button>
        </a>
    <?php else: ?>
        <a href="login.php" target="contentFrame">
            <button class="btn btn-custom btn-outline-success btn-position"><b><i class="fa-solid fa-circle-user"></i> 登入</b></button>
        </a>
    <?php endif; ?>

    <div class="content">
        <iframe name="contentFrame" src="main.php" allowtransparency="true"></iframe>
    </div>

    <div class="footer">
        <b>2025 © 天主教輔仁大學 愛校建言捐款系統</b>
    </div>

    <div class="back-to-top" id="backToTopBtn">↑</div>

    <script>
        const backToTopBtn = document.getElementById('backToTopBtn');
        const iframe = document.querySelector('iframe[name="contentFrame"]');

        backToTopBtn.addEventListener('click', () => {
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });

        iframe.addEventListener('load', () => {
            const iframeWindow = iframe.contentWindow;

            function toggleBackToTop() {
                const scrollTop = iframeWindow.scrollY || iframeWindow.pageYOffset;
                backToTopBtn.style.display = scrollTop > 200 ? 'block' : 'none';
            }
            toggleBackToTop();
            iframeWindow.addEventListener('scroll', toggleBackToTop);
        });

        function toggleSidebar(btn) {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('collapsed');

            const icon = btn.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right'); // 展開 icon
            } else {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-left'); // 收起 icon
            }
        }
    </script>

</body>

</html>