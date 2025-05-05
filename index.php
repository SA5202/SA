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
            font-family: "Noto Serif TC", serif;
            margin: 0;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        .sidebar {
            width: 340px;
            background:
                linear-gradient(rgba(170, 197, 212, 0.5), rgba(24, 54, 65, 0.8)),
                url('https://cdn.pixabay.com/photo/2015/07/25/15/24/money-860128_1280.jpg');
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
            width: 100px;
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
            opacity: 0;
            transform: translateX(-20px);
            animation: slideIn 0.6s forwards;
        }

        .sidebar a:nth-child(2) {
            animation-delay: 0.1s;
        }

        .sidebar a:nth-child(3) {
            animation-delay: 0.2s;
        }

        .sidebar a:nth-child(4) {
            animation-delay: 0.3s;
        }

        .sidebar a:nth-child(5) {
            animation-delay: 0.4s;
        }

        .sidebar a:nth-child(6) {
            animation-delay: 0.5s;
        }

        .sidebar a:nth-child(7) {
            animation-delay: 0.6s;
        }

        .sidebar a:nth-child(8) {
            animation-delay: 0.7s;
        }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
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
            height: 1.8rem;
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
            background-color: transparent;
            border: none;
            padding: 10px 20px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 150;
            transition: transform 0.3s ease, background-color 0.3s ease, rotate 0.4s;
            /* Smooth transition */
            /* Add shadow for depth */
            font-size: 1.5rem;
            /* Increase the size of the icon */
            color: #fff;
            text-align: center;
        }

        /* Button hover effect */
        .toggle-btn:hover {
            /* Darken on hover */
            transform: translateY(-5px) rotate(10deg);
            /* Slight upward movement */
        }

        /* When the sidebar is collapsed, move the button to the left */
        .sidebar.collapsed+.toggle-btn {
            left: 100px;
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
            background-size: 120% auto;
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
            top: 20px;
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
            background-color: #002b5b;
            color: white;
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
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
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .back-to-top:hover {
            background-color: rgba(0, 0, 0, 0.7);
            transform: scale(1.1) translateY(-5px);
        }

        .user-info-logout {
            position: fixed;
            top: 20px;
            right: 40px;
            z-index: 200;
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: rgba(190, 225, 230, 0.95);
            color: midnightblue;
            border-radius: 25px;
            padding: 8px 20px;
            font-size: 1rem;
            font-weight: bold;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .user-info-logout .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info-logout .username {
            font-weight: 600;
            color: #2c3e50;
        }

        .user-info-logout .logout-link {
            font-size: 0.9rem;
            padding: 5px 12px;
            border-radius: 20px;
        }

        .btn-prussian {
            color: #003153;
            border: 2px solid #003153;
            background-color: transparent;
            border-radius: 20px;
            padding: 6px 14px;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .btn-prussian:hover {
            background-color: #003153;
            color: white;
            transform: scale(1.05);
            color: #fff;
        }

        /* iframe內容淡入 */
        iframe {
            opacity: 0;
            animation: fadeInIframe 1.5s forwards;
        }

        @keyframes fadeInIframe {
            to {
                opacity: 1;
            }
        }

        /* 側邊欄項目選中時的樣式 */
        .sidebar-link.active {
            background-color: rgba(255, 255, 255, 0.25);
            /* 背景顏色變為灰色 */
            color: rgb(245, 249, 196);
            /* 文字顏色變為藍色 */
            font-weight: bold;
            /* 字體加粗 */
        }
    </style>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <a href="main.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
            <h1>FJU I-Money</h1>
        </a>
        <?php if ($is_admin): ?>
            <!-- 管理者的側邊欄 -->
            <a href="main.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-home"></i><span><b> 首頁</b></span>
            </a>
            <a href="news.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fa-solid fa-wrench"></i><span><b> 公告管理</b></span>
            </a>
            <a href="news_insert.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fa-solid fa-notes-medical"></i><span><b> 新增公告</b></span>
            </a>
            <a href="suggestions.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-cogs"></i><span><b> 建言進度管理</b></span>
            </a>
            <a href="funding_detail.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-donate"></i><span><b> 募款建言管理</b></span>
            </a>
            <a href="fundingsuggestion.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-hand-holding-usd"></i><span><b> 新增募款建言</b></span>
            </a>
        <?php else: ?>
            <!-- 一般使用者的側邊欄 -->
            <a href="main.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-home"></i><span><b> 首頁</b></span>
            </a>
            <a href="news.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fa-solid fa-bell"></i><span><b> 公告</b></span>
            </a>
            <a href="suggestions.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-scroll"></i><span><b> 建言總覽</b></span>
            </a>
            <a href="suggestions_make.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-comment-dots"></i><span><b> 提出建言</b></span>
            </a>
            <a href="donate.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-hand-holding-usd"></i><span><b> 捐款進度</b></span>
            </a>
            <a href="honor.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-medal"></i><span><b> 榮譽機制</b></span>
            </a>
            <a href="contact.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-phone-alt"></i><span><b> 聯絡我們</b></span>
            </a>
        <?php endif; ?>
    </div>

    <!-- 收合按鈕 -->
    <button class="toggle-btn" onclick="toggleSidebar(this)">
        <i class="fas fa-chevron-left"></i>
    </button>


    <!-- 登入/登出 -->
    <?php if ($is_logged_in): ?>
        <div class="user-info-logout btn-position">
            <img src="https://th.bing.com/th/id/OIP.sL-PTY6gaFaZu6VVwZgqaQHaHQ?w=178&h=180&c=7&r=0&o=5&dpr=1.5&pid=1.7" alt="頭像" class="avatar">
            <a href="record.php" target="contentFrame" style="text-decoration: none;"><span class="username"><b><?= htmlspecialchars($_SESSION['User_Name']) ?></b></span></a>
            <a href="logout.php" target="contentFrame" class="btn btn-prussian logout-link">登出</a>
        </div>

    <?php else: ?>
        <a href="login.php" target="contentFrame">
            <button class="btn btn-custom btn-outline-success btn-position">
                <b><i class="fa-solid fa-circle-user"></i> 登入</b>
            </button>
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
    <script>
        function setActive(element) {
            // 移除所有側邊欄項目的 active 類別
            var links = document.querySelectorAll('.sidebar-link');
            links.forEach(function(link) {
                link.classList.remove('active');
            });

            // 為當前點擊的項目添加 active 類別
            element.classList.add('active');
        }

        // 頁面加載時自動設置當前頁面的 active 類別
        document.addEventListener('DOMContentLoaded', function() {
            var currentPath = window.location.pathname;
            var links = document.querySelectorAll('.sidebar-link');
            links.forEach(function(link) {
                if (link.href.includes(currentPath)) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>