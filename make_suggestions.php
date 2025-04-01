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

        
        /*建言表格 */
        .suggestion-form {
            max-width: 600px;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .suggestion-form:hover {
            transform: scale(1.02);
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        .form-control, .form-select, textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus, textarea:focus {
            outline: none;
            border-color: #6f8c3c;
            box-shadow: 0 0 8px rgba(111, 140, 60, 0.5);
        }

        .btn-custom {
            background-color: #6f8c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 50px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-custom:hover {
            background-color: #56752a;
            transform: translateY(-3px);
        }

        .form-group:hover {
            transform: scale(1.02);
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
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
        <a href="make_suggestions.php"><i class="icon fas fa-comment-dots"></i> 提出建言</a>
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
        <div class="main-content">
            <h2 class="content-title">提出建言</h2>
            <form action="submit_suggestion.php" method="POST" class="suggestion-form">
                <div class="form-group">
                    <label for="suggestion-title">建言標題：</label>
                    <input type="text" id="suggestion-title" name="title" placeholder="請輸入建言標題" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="facility">相關設施：</label>
                    <input type="text" id="facility" name="facility" placeholder="請輸入設施" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="building">大樓：</label>
                    <select id="building" name="building" class="form-select">
                        <option value="">選擇大樓</option>
                        <option value="大樓A">利瑪竇</option>
                        <option value="大樓B">進修部</option>
                        <option value="大樓C">羅耀拉</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">建言內容：</label>
                    <textarea id="description" name="description" rows="5" placeholder="請具體描述您的建言內容" class="form-control" required></textarea>
                </div>

                <button type="submit" class="btn btn-custom">提交建言</button>
            </form>
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