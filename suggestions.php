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
    <title>建言總覽 | 輔仁大學愛校建言捐款系統</title>

    <!-- 外部樣式與字體 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>

    <!-- 版面樣式調整 -->
    <style>
        body {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            line-height: 1.8;
            background-color: transparent;
            overflow-x: hidden;
        }

        h3 {
            margin-top: 30px;
            font-weight: bold;
        }

        .card {
            margin-bottom: 25px;
        }

        .marquee-wrapper {
            max-width: 1000px;
            margin: 0 auto 30px auto;
        }

        #mqmain {
            background: linear-gradient(45deg, rgb(189, 182, 117), rgb(153, 151, 104));
            color: white;
            font-size: 1.1rem;
            padding: 10px;
            border-radius: 10px;
            font-weight: bold;
            overflow: hidden;
            width: 100%;
        }
    </style>
</head>

<body>

    <!-- 如有公告或跑馬燈，可放這裡 -->
    <div class="marquee-wrapper">
        <marquee id="mqmain" scrollamount="8">這是建言總覽公告，請參閱最新資訊。</marquee>
    </div>

    <h3>建言總覽</h3>

    <!-- 示例建言列表 -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header"><i class="fas fa-lightbulb"></i> 建言標題 A</div>
                <div class="card-body">
                    學生建議提升自習室冷氣開放時間，特別是考前期間。
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header"><i class="fas fa-lightbulb"></i> 建言標題 B</div>
                <div class="card-body">
                    校園 Wi-Fi 在部分區域訊號不穩定，建議優化覆蓋。
                </div>
            </div>
        </div>

        <!-- 若未來有更多建言，可以持續新增 -->
    </div>

</body>
</html>