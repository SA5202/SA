<?php
session_start();

// 判斷是否登入和是否為管理員
$is_logged_in = isset($_SESSION['username']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>
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
            background-color: rgb(198, 202, 92);
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

        /*公告欄*/
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {

            margin: 20px auto;
        }

        .search-container {
            text-align: left;
            margin-bottom: 20px;
        }

        .search-container input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 400px;
            border: 2px solid #ddd;
            border-radius: 30px;
            /* 圓角邊框 */
            outline: none;
            transition: all 0.3s ease;
        }

        .search-container input[type="text"]:focus {
            border-color: rgb(157, 209, 45);
            /* 當聚焦時改變邊框顏色 */
            box-shadow: 0 0 8px rgba(58, 67, 5, 0.7);
            /* 聚焦時增加陰影效果 */
        }

        .search-container button {
            padding: 10px 16px;
            font-size: 16px;
            background-color: rgb(119, 125, 35);
            color: white;
            border: none;
            border-radius: 30px;
            /* 圓角按鈕 */
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-container button:hover {
            background-color: rgb(79, 99, 21);
        }

        .search-container button:focus {
            outline: none;
        }


        .card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            flex-direction: row;
        }

        .card-content {
            flex: 1;
            margin-right: 20px;
        }

        .card-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .card-description {
            font-size: 14px;
            margin-bottom: 8px;
            color: #555;
        }

        .card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tag {
            font-size: 12px;
            color: white;
            background-color: rgb(142, 208, 173);
            padding: 4px 8px;
            border-radius: 4px;
        }

        .card-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            text-decoration: none;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 4px;
            color: white;
            text-align: center;
            transition: background-color 0.3s;
        }

        .btn-favorite {
            background-color: rgb(209, 186, 35);
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 4px;
            text-align: center;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn-favorite:hover {
            background-color: rgb(159, 139, 75);
        }

        .btn-disabled {
            background-color: rgb(159, 129, 64);
            cursor: not-allowed;
        }

        /*跑馬燈*/
        #mqmain {
            background: linear-gradient(45deg, rgb(210, 172, 57), rgb(154, 126, 36));
            /* 渐变背景 */
            color: white;
            /* 白色文字 */
            font-size: 1.2rem;
            /* 字体大小 */
            padding: 10px;
            /* 内边距 */
            border-radius: 10px;
            /* 圆角 */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            /* 阴影效果 */
            font-weight: bold;
            /* 加粗文字 */
            overflow: hidden;
            /* 确保内容不会溢出容器 */
            position: relative;
            /* 相对定位，为了调整子元素位置 */
            width: 100%;
            /* 确保容器宽度自适应 */
        }

        #mqmain span {
            position: absolute;
            /* 绝对定位文本 */
            white-space: nowrap;
            /* 防止文本换行 */
            animation: scroll-left 10s linear infinite;
            /* 创建滚动效果 */
        }

        @keyframes scroll-left {
            0% {
                left: 100%;
                /* 起始位置在屏幕右侧 */
            }

            100% {
                left: -100%;
                /* 结束位置在屏幕左侧，完全移出视野 */
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3>輔大愛校建言捐款系統</h3>
        <a href="1.php"><i class="icon fas fa-home"></i> 首頁</a>
        <a href="suggestions.php"><i class="icon fas fa-heart"></i></i> 我的收藏</a>
        <a href="donate.php"><i class="icon fas fa-money-bill-wave"></i> 捐款進度</a>
        <a href="statement.php"><i class="icon fas fa-chart-pie"></i>捐款報表</a>
        <a href="honor.php"><i class="icon fas fa-medal"></i>榮譽機制</a>
        <a href="contact.php"><i class="icon fas fa-phone-alt"></i> 聯絡我們</a>
    </div>
    <div class="main-content">

        <!-- 顯示「設定」選項 -->
        <?php if ($is_logged_in): ?>
            <a href="<?= $is_admin ? '管理者設定.php' : '使用者設定.php' ?>" target="contentFrame"
                style="text-decoration: none;">設定</a>
        <?php endif; ?>
        <!-- 顯示登入或登出按鈕 -->
        <?php if ($is_logged_in): ?>
            <a href="logout.php" target="contentFrame">
                <button type="button" class="btn btn-custom btn-custom-logout btn-position">
                    登出
                </button>
            </a>


        <?php endif; ?>
        <h1 class="h3 mb-2 text-gray-800">重要資訊</h1>
        <div class="item">
            <marquee id="mqmain" scrollamount="10">4/5將進行系統維護，請使用者注意。</marquee>
        </div>

        </p>
        <h1 class="h3 mb-2 text-gray-800">建言一覽</h1>

        <div class="filter-section">

            <br>
            <div class="container">
                <!-- 搜尋表單 -->
                <div class="search-container">
                    <form method="GET" action="">
                        <input type="text" name="search" placeholder="搜尋公告"
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
                        <button type="submit">搜尋</button>
                    </form>
                </div>

                <?php
                // Step 1: 連接資料庫
                $link = mysqli_connect('localhost', 'root');
                mysqli_select_db($link, "announcement");

                // Step 2: 處理搜尋功能
                $search = isset($_GET['search']) ? mysqli_real_escape_string($link, $_GET['search']) : '';

                // Step 3: 查詢公告資料，根據搜尋關鍵字來篩選標題或內容
                $sql = "SELECT * FROM announce";
                if ($search) {
                    $sql .= " WHERE title LIKE '%$search%' OR content LIKE '%$search%'";
                }
                $result = mysqli_query($link, $sql);

                // Step 4: 顯示公告
                while ($row = mysqli_fetch_assoc($result)) {
                    // 檢查是否已收藏
                    $favorite_button_text = $row['favorite'] == 1 ? '已收藏 !' : '收藏';
                    $favorite_button_class = $row['favorite'] == 1 ? 'btn-favorite btn-disabled' : 'btn-favorite';

                    echo '<div class="card">';
                    echo '<div class="card-content">';
                    echo '<div class="card-header">' . $row['title'] . '</div>';
                    echo '<div class="card-description">' . $row['content'] . '</div>';
                    echo '<div class="card-tags">';

                    // 顯示標籤
                    $tags = explode(' ', $row['tag']);
                    foreach ($tags as $tag) {
                        if (!empty($tag)) {
                            echo '<span class="tag">' . $tag . '</span>';
                        }
                    }
                    echo '</div>';
                    echo '</div>';

                    // 顯示收藏按鈕
                    echo '<div class="card-buttons">';
                    if ($row['favorite'] == 0) {
                        echo "<a href='favorite.php?id=" . $row['id'] . "' class='btn $favorite_button_class'>$favorite_button_text</a>";
                    } else {
                        echo "<span class='btn $favorite_button_class'>$favorite_button_text</span>";
                    }
                    echo '</div>';

                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="footer">
        2025 © 輔仁大學 愛校建言系統
    </div>

    <!-- Back to Top Button -->
    <div class="back-to-top" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
        ↑
    </div>
</body>

</html>