<?php
session_start();

require_once 'honor_helper.php'; // 建議放在 session_start() 下方

// 基本登入狀態判斷
$is_logged_in = isset($_SESSION['User_Name']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
$user_type = $_SESSION['User_Type'] ?? ''; // 抓 user type: admin / general / department

// 連接資料庫
$link = new mysqli('localhost', 'root', '', 'SA');

// 處理 VIP 等級
$vipInfo = null;
$userVipLevel = 0; // 預設 0 表示沒VIP或未登入
if (isset($_SESSION['User_ID'])) {
    $vipInfo = getVipLevel($link, $_SESSION['User_ID']);
    $vipClass = $vipInfo['class'] ?? '';

    // vip 等級映射
    $vipLevelMap = [
        'vip1' => 1,
        'vip2' => 2,
        'vip3' => 3,
        'vip4' => 4,
        'vip5' => 5,
    ];
    $userVipLevel = $vipLevelMap[$vipClass] ?? 0;
}

// 查詢使用者暱稱
$nickname = '使用者'; // 預設顯示
if (isset($_SESSION['User_ID'])) {
    $stmt = $link->prepare("SELECT Nickname FROM useraccount WHERE User_ID = ?");
    $stmt->bind_param("i", $_SESSION['User_ID']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $nickname = $row['Nickname'];
    }
    $stmt->close();
}
?>

<script>
    const userVipLevel = <?= intval($userVipLevel) ?>;
</script>


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

    <script src="https://cdn.jsdelivr.net/npm/fireworks-js@2.10.0/dist/fireworks.js"></script>
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
            font-weight: bold;
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
            font-weight: bold;
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

        .sidebar-link {
            display: block;
            text-decoration: none;
            color: #333;
            cursor: pointer;
        }

        .sidebar-link:hover {
            background-color: #eee;
        }

        .arrow {
            font-size: 1.5rem;
            margin-left: auto;
            transition: transform 0.5s ease;
        }

        .arrow.collapsed {
            transform: rotate(-90deg);
        }

        .group-content {
            display: none;
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
            top: 25px;
            right: 40px;
            z-index: 200;
        }

        .btn-custom {
            background-color: rgba(190, 225, 230);
            color: midnightblue;
            border-radius: 25px;
            border: 1px solid #ccc !important;
            padding: 0.4rem 25px;
            font-size: 1rem;
            font-weight: bold;
            transition: 0.3s;
            border: none;
        }

        .btn-custom:hover {
            background-color: #002b5b;
            color: white;
            transform: scale(1.05) translateY(-2px);
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
            border: 1px solid #ccc;
            padding: 0.4rem 20px;
            font-size: 1rem;
            font-weight: bold;
        }

        .user-info-logout .avatar-frame {
            width: 40px;
            height: 40px;
            margin: 0;
            padding: 5px;
        }

        .user-info-logout .avatar-img {
            width: 35px;
            height: 35px;
        }

        .avatar-frame {
            border-radius: 50%;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 50px;
        }

        .avatar-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-img {
            border-radius: 50%;
            object-fit: cover;
        }

        /* VIP3 - 冰藍 */
        .avatar-frame.vip3 {
            background: linear-gradient(135deg, #ffffff, #00e5ff, #00bcd4);
            /* 淺冰藍漸層 */
            animation: vip-glow 3s ease-in-out infinite;
            /* 較快的閃爍動畫 */
            box-shadow: 0 0 12px rgba(0, 191, 212, 0.5);
            /* 輕微的藍色光暈 */
        }

        /* VIP4 - 紫粉 */
        @keyframes vip4-glow {
            0%, 100% {
                box-shadow: 0 0 15px rgba(161, 140, 209, 0.4);
            }
            70% {
                box-shadow: 0 0 30px rgba(150, 90, 220, 0.8);
            }
        }
        .avatar-frame.vip4 {
            background: linear-gradient(135deg, #a18cd1, #fbc2eb); /* 淡紫色漸層 */
            animation: vip4-glow 3s ease-in-out infinite; /* 中等速度的閃爍動畫 */
            box-shadow: 0 0 15px rgba(150, 90, 220, 0.7); /* 紫色光暈 */
        }

        /* VIP5 - 綠藍 */
        @keyframes vip5-glow {
            0%, 100% {
                box-shadow: 0 0 15px rgba(0, 255, 204, 0.4);
            }
            70% {
                box-shadow: 0 0 30px rgba(0, 204, 255, 0.8);
            }
        }
        .avatar-frame.vip5 {
            background: linear-gradient(135deg, #003366, #00c9ff, #92fe9d); /* 深藍 → 鮮亮藍 → 淺綠漸層 */
            animation: vip5-glow 3s ease-in-out infinite; /* 最慢的閃爍動畫 */
            box-shadow: 0 0 30px rgba(0, 204, 255, 0.9); /* 更強的藍光光暈 */
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
        /* 側邊欄項目選中時的樣式 */
        .sidebar-link.active {
            background-color: rgba(255, 255, 255, 0.33);
            /* 背景顏色變為灰色 */
            color: rgb(254, 251, 199);
            /* 文字顏色變為藍色 */
            font-weight: bold;
            /* 字體加粗 */
        }

        .avatar {
            width: 40px;
            height: 40px;
            margin: 0;
            padding: 5px;
            border-radius: 50%;
        }

        .nickname-link {
            text-decoration: none;
            color: rgb(7, 78, 150);
            /* 正常狀態的顏色，可自行調整 */
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .nickname-link:hover {
            color: rgb(1, 47, 92);
            /* 滑鼠懸停時的顏色，可自行調整 */
        }

        /* 畫布和視窗基礎樣式 */
        #confettiCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9998;
        }

        /* 主視窗樣式 */
        #welcomeModal {
            /* 定位與淡入動畫 */
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease;
            z-index: 9999;

            /* 視覺樣式 */
            background: rgba(255, 255, 255, 0.9);
            /* 柔和白底 */
            border-radius: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            height: 20%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(4px);
        }

        /* 顯示效果 */
        #welcomeModal.show {
            opacity: 1;
            pointer-events: auto;
        }

        /* 標題文字 */
        #welcomeModal .modal-title {
            color: #2a4d69;
            font-size: 1.3rem;
            font-weight: bold;
        }

        /* 關閉按鈕（統一樣式） */
        #closeModalBtn {
            margin-top: 30px;
            background-color: #4da6ff;
            border: none;
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.2rem 30px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #closeModalBtn:hover {
            background-color: #1565c0;
        }

        /* VIP5 升級版視窗 */
        #welcomeModal.upgraded {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            color: #111;
            border-radius: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        /* VIP2-4 基礎視窗 */
        #welcomeModal.basic {
            background: #fff;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #000;
        }

        /* 流星背景黑底：這邊移除背景設定，改用遮罩層 */
        body.meteor-active {
            /* background-color: black !important; 這邊不用了 */
            color: white;
            /* 可選：讓字色白色 */
            overflow: hidden;
        }

        /* 全螢幕黑色遮罩 */
        #blackOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 9997;
            /* 低於 confettiCanvas (9998) 和 welcomeModal (9999) */
            pointer-events: none;
            display: none;
            /* 預設隱藏 */
        }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <a href="main.php" target="contentFrame" class="sidebar-link active" onclick="setActive(this)">
            <h1>FJU I-Money</h1>
        </a>
        <?php if ($is_admin): ?>
            <?php
            $admin_type = $_SESSION['admin_type'] ?? '';
            ?>

            <!-- 共用首頁 -->
            <a href="main.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-home"></i><span> 首頁</span>
            </a>

            <?php if ($admin_type === 'super'): ?>
                <!-- Super Admin：全部功能 -->
                <a href="javascript:void(0);" class="sidebar-link" onclick="toggleGroup(this)">
                    <i class="icon fa-solid fa-bullhorn"></i><span> 管理公告</span>
                    <span class="arrow">▾</span>
                </a>
                <div class="group-content">
                    <a href="news.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                        <i class="icon fa-solid fa-wrench"></i><span> 公告列表</span>
                    </a>
                    <a href="news_insert.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                        <i class="icon fa-solid fa-notes-medical"></i><span> 發布公告</span>
                    </a>
                </div>

                <a href="javascript:void(0);" class="sidebar-link" onclick="toggleGroup(this)">
                    <i class="icon fas fa-list"></i><span> 建言管理</span>
                    <span class="arrow">▾</span>
                </a>
                <div class="group-content">
                    <a href="suggestions.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                        <i class="icon fas fa-cogs"></i><span> 管理建言進度</span>
                    </a>
                    <a href="funding_detail.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                        <i class="icon fas fa-donate"></i><span> 管理募款建言</span>
                    </a>
                    <a href="fundingsuggestion.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                        <i class="icon fas fa-hand-holding-usd"></i><span> 新增募款建言</span>
                    </a>
                </div>

                <a href="javascript:void(0);" class="sidebar-link" onclick="toggleGroup(this)">
                    <i class="icon fa-solid fa-layer-group"></i><span> 捐款管理</span>
                    <span class="arrow">▾</span>
                </a>
                <div class="group-content">
                    <a href="donation_admin_create.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                        <i class="icon fas fa-money-bill"></i><span> 手動捐款</span>
                    </a>
                    <a href="donation_list.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                        <i class="icon fas fa-folder"></i><span> 檢視捐款紀錄</span>
                    </a>
                </div>
                <a href="honor.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                    <i class="icon fas fa-medal"></i><span> 榮譽排名</span>
                </a>


            <?php elseif ($admin_type === 'general'): ?>
                <!-- General Admin：公告與捐款相關功能 -->
                <a href="news.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                    <i class="icon fa-solid fa-wrench"></i><span> 管理公告</span>
                </a>
                <a href="news_insert.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                    <i class="icon fa-solid fa-notes-medical"></i><span> 發布公告</span>
                </a>
                <a href="donation_admin_create.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                    <i class="icon fas fa-coins"></i><span> 手動捐款</span>
                </a>
                <a href="donation_list.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                    <i class="icon fas fa-clipboard-list"></i><span> 檢視捐款紀錄</span>
                </a>

            <?php elseif ($admin_type === 'department'): ?>
                <!-- Department Admin：建言與募款功能 -->
                <a href="suggestions.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                    <i class="icon fas fa-cogs"></i><span> 管理建言進度</span>
                </a>
                <a href="funding_detail.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                    <i class="icon fas fa-donate"></i><span> 管理募款建言</span>
                </a>
                <a href="fundingsuggestion.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                    <i class="icon fas fa-hand-holding-usd"></i><span> 新增募款建言</span>
                </a>
            <?php endif; ?>

        <?php else: ?>
            <!-- 一般使用者的側邊欄 -->
            <a href="main.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-home"></i><span> 首頁</span>
            </a>
            <a href="news.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fa-solid fa-bullhorn"></i><span> 公告</span>
            </a>
            <a href="suggestions.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-list"></i><span> 建言總覽</span>
            </a>
            <a href="suggestions_make.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-comment-dots"></i><span> 提出建言</span>
            </a>
            <a href="donate.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-hand-holding-usd"></i><span> 捐款進度</span>
            </a>
            <a href="honor.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-medal"></i><span> 榮譽排名</span>
            </a>
            <a href="contact.php" target="contentFrame" class="sidebar-link" onclick="setActive(this)">
                <i class="icon fas fa-phone-alt"></i><span> 聯絡我們</span>
            </a>
        <?php endif; ?>
    </div>

    <!-- 收合按鈕 -->
    <button class="toggle-btn" onclick="toggleSidebar(this)">
        <i class="fas fa-chevron-left"></i>
    </button>
    <?php if ($is_logged_in && isset($_SESSION['User_Name'])): ?>
        <?php
        $link = new mysqli('localhost', 'root', '', 'SA');
        if ($link->connect_error) {
            die("資料庫連線失敗: " . $link->connect_error);
        }

        $User_Name = $_SESSION['User_Name'];
        $stmt = $link->prepare("SELECT Nickname, Avatar FROM useraccount WHERE User_Name = ?");
        $stmt->bind_param("s", $User_Name);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $nickname = htmlspecialchars($user['Nickname'] ?? $User_Name);

        // ✅ 自訂預設頭像（你提供的網址）
        $defaultAvatar = "https://i.pinimg.com/736x/15/46/d1/1546d15ce5dd2946573b3506df109d00.jpg";

        // ✅ 如果資料庫沒有頭像就用預設
        $avatarPath = !empty($user['Avatar']) ? htmlspecialchars($user['Avatar']) : $defaultAvatar;
        $avatarWithTimestamp = $avatarPath . '?t=' . time(); // 防快取
        ?>
        <?php if ($is_admin): ?>
            <div class="user-info-logout btn-position">
                <a href="record.php" target="contentFrame">
                    <img src="<?= $avatarWithTimestamp ?>" alt="頭像" class="avatar" style="cursor:pointer;"
                        onerror="this.src='<?= $defaultAvatar ?>';">
                </a>
                <a href="record_admin.php" target="contentFrame" class="nickname-link">
                    <span class="Nickname"><b><?= $nickname ?></b></span>
                </a>
                <a href="logout.php" target="contentFrame" class="btn btn-prussian logout-link">登出</a>
            </div>
        <?php else: ?>
            <div class="user-info-logout btn-position">
                <a href="record.php" target="contentFrame">
                    <div class="avatar-frame <?= $vipInfo['class'] ?>" style="cursor:pointer;">
                        <div class="avatar-inner">
                            <img src="<?= $avatarWithTimestamp ?>"
                                alt="頭像"
                                class="avatar-img"
                                onerror="this.src='<?= $defaultAvatar ?>';">
                        </div>
                    </div>
                </a>
                <a href="record.php" target="contentFrame" class="nickname-link">
                    <span class="Nickname"><b><?= $nickname ?></b></span>
                </a>
                <a href="logout.php" target="contentFrame" class="btn btn-prussian logout-link">登出</a>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <a href="login.php" target="contentFrame">
            <button class="btn btn-custom btn-position">
                <i class="fa-solid fa-circle-user"></i> 登入
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
        function toggleGroup(element) {
            const content = element.nextElementSibling;
            const arrow = element.querySelector('.arrow');

            // 先關閉所有的內容區塊和箭頭
            const allContents = document.querySelectorAll('.group-content');
            const allArrows = document.querySelectorAll('.arrow');

            allContents.forEach((el) => {
                if (el !== content) {
                    el.style.display = 'none';
                }
            });

            allArrows.forEach((arrowElement) => {
                if (arrowElement !== arrow) {
                    arrowElement.textContent = '▾'; // 或根據需要更改箭頭符號
                }
            });

            // 切換當前點擊的內容區塊
            if (content && content.classList.contains('group-content')) {
                const isOpen = content.style.display === 'block';
                content.style.display = isOpen ? 'none' : 'block';
                if (arrow) {
                    arrow.textContent = isOpen ? '▾' : '▴';
                }
            }
        }

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

        // 新增的 setActive 函數
        function setActive(link) {
            const links = document.querySelectorAll('.sidebar-link');
            links.forEach(function(link) {
                link.classList.remove('active');
            });

            link.classList.add('active');
        }

        document.getElementById("avatarForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch("upload_avatar.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(".avatar").src = data.avatar + "?t=" + new Date().getTime(); // 避免快取
                    } else {
                        alert("上傳失敗：" + data.message);
                    }
                });
        });
    </script>


    <!-- 黑色遮罩層 -->
    <div id="blackOverlay"></div>
    <canvas id="confettiCanvas"></canvas>

    <div id="welcomeModal">
        <div class="modal-title" id="welcomeMessage"></div>
        <button id="closeModalBtn">關閉</button>
    </div>

    <script>
        (() => {
            let userVipLevel = <?php echo (int)$userVipLevel; ?>;
            let nickname = "<?php echo htmlspecialchars($nickname, ENT_QUOTES); ?>";

            if (!userVipLevel || userVipLevel < 2) {
                document.getElementById('confettiCanvas').style.display = 'none';
                document.getElementById('welcomeModal').style.display = 'none';
                return;
            }

            const canvas = document.getElementById('confettiCanvas');
            const modal = document.getElementById('welcomeModal');
            const welcomeMessage = document.getElementById('welcomeMessage');
            const closeBtn = document.getElementById('closeModalBtn');
            const blackOverlay = document.getElementById('blackOverlay');
            const ctx = canvas.getContext('2d');
            let W, H, animationFrameId = null;
            let autoCloseTimer = null;
            let modalClosed = false;

            function resize() {
                W = canvas.width = window.innerWidth;
                H = canvas.height = window.innerHeight;
            }
            window.addEventListener('resize', resize);
            resize();

            // 設定歡迎訊息與特效類型
            let effectType = 'confetti'; // 預設彩帶
            if (userVipLevel >= 5) {
                const effects = ['meteor', 'firework'];
                effectType = effects[Math.floor(Math.random() * effects.length)];
                welcomeMessage.textContent = `歡迎回來，尊貴的 ${nickname} ！`;
                modal.classList.add('upgraded');
                blackOverlay.style.display = 'block';
            } else {
                welcomeMessage.textContent = `您好，${nickname}！`;
                modal.classList.add('basic');
                blackOverlay.style.display = 'none';
            }

            canvas.style.display = 'block';
            modal.style.display = 'block';

            // ----------- 特效類別 ------------

            // 彩帶 (VIP 2~4)
            class Confetto {
                constructor(colors) {
                    this.colors = colors;
                    this.reset();
                }
                reset() {
                    this.x = Math.random() * W;
                    this.y = Math.random() * -H;
                    this.size = Math.random() * 5 + 7;
                    this.speedY = Math.random() * 2 + 1;
                    this.speedX = Math.random() - 0.5;
                    this.rotation = Math.random() * 2 * Math.PI;
                    this.rotationSpeed = (Math.random() - 0.5) * 0.1;
                    this.color = this.colors[Math.floor(Math.random() * this.colors.length)];
                    this.alpha = 1;
                }
                update() {
                    this.y += this.speedY;
                    this.x += this.speedX;
                    this.rotation += this.rotationSpeed;
                    if (this.y > H) this.y = Math.random() * -20;
                    if (this.x > W) this.x = 0;
                    if (this.x < 0) this.x = W;
                }
                draw(ctx) {
                    ctx.save();
                    ctx.translate(this.x, this.y);
                    ctx.rotate(this.rotation);
                    ctx.fillStyle = this.color;
                    ctx.globalAlpha = this.alpha;
                    ctx.fillRect(-this.size / 2, -this.size / 2, this.size, this.size * 0.4);
                    ctx.restore();
                    ctx.globalAlpha = 1;
                }
            }

            class Meteor {
                constructor() {
                    this.reset();
                }

                reset() {
                    this.x = Math.random() * W;
                    this.y = Math.random() * H * 0.5;
                    this.len = Math.random() * 100 + 150;
                    this.speed = Math.random() * 12 + 12;
                    this.angle = Math.PI / 4;
                    this.opacity = 0;
                    this.opacitySpeed = 0.03 + Math.random() * 0.02;
                    this.tailColors = [
                        'rgba(255,255,255,0.8)',
                        'rgba(250, 250, 155, 0.6)',
                        'rgba(249, 187, 121, 0.4)',
                        'rgba(255,100,50,0.2)'
                    ];
                    this.sparkle = 0;
                }

                update() {
                    this.x += this.speed * Math.cos(this.angle);
                    this.y += this.speed * Math.sin(this.angle);

                    this.opacity += this.opacitySpeed;
                    if (this.opacity > 1) this.opacity = 1;

                    // 閃爍效果：opacity微微波動
                    this.sparkle += 0.1;
                    this.opacity += Math.sin(this.sparkle) * 0.05;
                    this.opacity = Math.min(Math.max(this.opacity, 0.5), 1);

                    if (this.x > W + this.len || this.y > H + this.len) this.reset();
                }

                draw(ctx) {
                    ctx.save();

                    // 多層光暈 (用 shadow)
                    ctx.shadowColor = `rgba(255, 255, 255, ${this.opacity})`;
                    ctx.shadowBlur = 20;

                    // 逐段繪製尾巴漸變
                    for (let i = 0; i < this.tailColors.length; i++) {
                        ctx.strokeStyle = this.tailColors[i].replace(/[\d\.]+\)$/g, `${this.opacity * (1 - i * 0.25)})`);
                        ctx.lineWidth = 4 - i; // 尾巴越遠越細
                        ctx.beginPath();
                        let startX = this.x - (this.len * i / this.tailColors.length) * Math.cos(this.angle);
                        let startY = this.y - (this.len * i / this.tailColors.length) * Math.sin(this.angle);
                        let endX = this.x - (this.len * (i + 1) / this.tailColors.length) * Math.cos(this.angle);
                        let endY = this.y - (this.len * (i + 1) / this.tailColors.length) * Math.sin(this.angle);
                        ctx.moveTo(startX, startY);
                        ctx.lineTo(endX, endY);
                        ctx.stroke();
                    }

                    // 流星核心（亮點）
                    let gradient = ctx.createRadialGradient(this.x, this.y, 0, this.x, this.y, 8);
                    gradient.addColorStop(0, `rgba(255,255,255,${this.opacity})`);
                    gradient.addColorStop(1, `rgba(255,255,255,0)`);
                    ctx.fillStyle = gradient;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, 5, 0, 2 * Math.PI);
                    ctx.fill();

                    ctx.restore();
                }
            }


            // 煙火粒子 (VIP 5+ 煙火用)
            class FireworkParticle {
                constructor(x, y, color) {
                    this.x = x;
                    this.y = y;
                    this.radius = 2;
                    this.color = color;
                    this.angle = Math.random() * Math.PI * 2;
                    this.speed = Math.random() * 6 + 2;
                    this.alpha = 1;
                }
                update() {
                    this.x += Math.cos(this.angle) * this.speed;
                    this.y += Math.sin(this.angle) * this.speed;
                    this.speed *= 0.96;
                    this.alpha -= 0.008; // 調小透明度減少速度，拖尾變長
                    if (this.alpha < 0) this.alpha = 0;
                }
                draw(ctx) {
                    ctx.save();
                    ctx.globalAlpha = this.alpha;
                    ctx.fillStyle = this.color;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.radius, 0, 2 * Math.PI);
                    ctx.fill();
                    ctx.restore();
                }
            }

            // 煙火 (VIP 5+)
            class Firework {
                constructor() {
                    this.x = Math.random() * W * 0.8 + W * 0.1;
                    this.y = Math.random() * H * 0.4 + H * 0.1;
                    this.color = `hsl(${Math.random() * 360}, 100%, 60%)`;
                    this.particles = [];
                    for (let i = 0; i < 30; i++) {
                        this.particles.push(new FireworkParticle(this.x, this.y, this.color));
                    }
                    this.alive = true;
                }
                update() {
                    this.particles.forEach((p) => p.update());
                    if (this.particles.every((p) => p.alpha <= 0)) this.alive = false;
                }
                draw(ctx) {
                    this.particles.forEach((p) => p.draw(ctx));
                }
            }

            // ---------- 初始化特效陣列 ----------
            let particles = [];
            let fireworkTimer = null;
            const meteorCount = 30;
            const confettiCount = 200;
            const confettiColors = [
                "hsl(0, 100%, 70%)",
                "hsl(60, 100%, 70%)",
                "hsl(120, 100%, 70%)",
                "hsl(240, 100%, 70%)",
            ];

            if (userVipLevel >= 5) {
                if (effectType === "meteor") {
                    for (let i = 0; i < meteorCount; i++) {
                        particles.push(new Meteor());
                    }
                } else if (effectType === "firework") {
                    // 煙火定時器：每0.5秒同時放3~5個煙火，持續10秒
                    fireworkTimer = setInterval(() => {
                        const count = Math.floor(Math.random() * 5) + 3; // 3~5發
                        for (let i = 0; i < count; i++) {
                            particles.push(new Firework());
                        }
                    }, 500);

                    setTimeout(() => {
                        clearInterval(fireworkTimer);
                    }, 10000);
                }
            } else {
                // VIP 2~4: 彩帶
                for (let i = 0; i < confettiCount; i++) {
                    particles.push(new Confetto(confettiColors));
                }
            }

            // ------------ 顯示視窗與自動關閉 --------------
            function showModal() {
                modalClosed = false;
                modal.style.opacity = "1";
                modal.style.pointerEvents = "auto";

                autoCloseTimer = setTimeout(() => {
                    hideModal();
                }, 10000);

                document.addEventListener("click", onUserClick, {
                    once: true,
                    capture: true
                });
            }

            function hideModal() {
                if (modalClosed) return;
                modalClosed = true;
                modal.style.opacity = "0";
                modal.style.pointerEvents = "none";

                if (animationFrameId) cancelAnimationFrame(animationFrameId);
                if (fireworkTimer) clearInterval(fireworkTimer);

                ctx.clearRect(0, 0, W, H);
                canvas.style.display = "none";
                blackOverlay.style.display = "none";
            }

            function onUserClick() {
                clearTimeout(autoCloseTimer);
                hideModal();
            }

            closeBtn.addEventListener("click", () => {
                clearTimeout(autoCloseTimer);
                hideModal();
            });

            window.addEventListener("load", () => {
                setTimeout(showModal, 500);
            });

            // ------------ 主動畫迴圈 ----------------
            function loop() {
                ctx.clearRect(0, 0, W, H);
                particles.forEach((p) => {
                    p.update();
                    p.draw(ctx);
                });

                // 清除已結束煙火
                if (effectType === "firework") {
                    particles = particles.filter((p) => p.alive !== false);
                }

                animationFrameId = requestAnimationFrame(loop);
            }
            animationFrameId = requestAnimationFrame(loop);
        })();
    </script>




</body>

</html>