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
    <title>聯絡資訊 | 輔仁大學愛校建言捐款系統</title>

    <!-- 字體 & 樣式 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: transparent; /* 關鍵：讓 iframe 透出背景 */
            font-family: 'Poppins', 'Noto Serif TC', sans-serif;
            font-size: 1.05rem;
            line-height: 1.8;
            margin: 0;
            padding: 30px;
            color: #333;
        }

        .contact-card {
            max-width: 1000px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.9); /* 白色半透明背景 */
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .contact-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #6c713d;
            margin-bottom: 30px;
        }

        .contact-section i {
            color: #6c713d;
            margin-right: 8px;
        }

        .contact-section p {
            margin-bottom: 10px;
        }

        .divider {
            border-top: 1px solid #ddd;
            margin: 25px 0;
        }

        a {
            color: #3366cc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .office-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .note {
            font-size: 0.95rem;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="contact-card">
        <div class="contact-title">
            <i class="fas fa-envelope"></i> 聯絡資訊
        </div>

        <div class="contact-section">
            <p><i class="fas fa-map-marker-alt"></i> 地址：242062 新北市新莊區中正路 510 號 天主教輔仁大學</p>
            <p><i class="fas fa-phone"></i> 生輔組電話：<a href="tel:(02)2905-2264">(02) 2905-2264</a></p>
            <p><i class="fas fa-envelope-open-text"></i> 生輔組服務信箱：<a href="mailto:sld@mail.fju.edu.tw">sld@mail.fju.edu.tw</a></p>
        </div>

        <div class="divider"></div>

        <div class="contact-section">
            <p><b>承辦人：</b>邱小姐</p>
            <p><b>電話：</b><a href="tel:(02)2905-3031">(02) 2905-3031</a></p>
        </div>

        <div class="divider"></div>

        <div class="contact-section">
            <p class="office-title">日間部辦公室（于斌樓一樓 YP104）</p>
            <p>學期中：週一至週五 08:00–16:30</p>
            <p>寒暑假：週一至週四 08:00–16:30</p>
        </div>

        <div class="divider"></div>

        <div class="contact-section">
            <p class="office-title">進修部辦公室（進修部大樓二樓 ES201）</p>
            <p>學期中：週一至週五 15:00–22:00</p>
            <p>寒暑假：週一至週四 08:00–16:30</p>
            <p class="note">（寒暑假作息請參照本校行事曆）</p>
        </div>
    </div>
</body>
</html>