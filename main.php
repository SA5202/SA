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
    <title>輔仁大學愛校建言捐款系統</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@550&display=swap">
</head>
<style>
    body {
        max-width: 1000px;
        margin: 0 auto;
        padding: 30px;
        font-size: 1.1rem;
        line-height: 1.8;
        background-color: transparent;
        overflow-x: hidden;
        /* 防止 iframe 出現左右捲軸 */
    }

    h3 {
        margin-top: 30px;
        font-weight: bold;
    }

    .card {
        margin-bottom: 30px;
    }

    .honor-wrapper {
        max-width: 1000px;
        margin: 0 auto;
        background-color: rgba(255, 255, 255, 0.9);
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    h3 {
        margin-bottom: 25px;
        font-weight: bold;
    }

    .honor-item {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
    }

    .honor-item h5 {
        font-weight: bold;
        margin-bottom: 10px;
    }

    .honor-icon {
        color: #ffc107;
        margin-right: 10px;
    }

    /*跑馬燈*/
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

<body>
    <h3>重要資訊</h3>
    <div class="marquee-wrapper">
        <marquee id="mqmain" scrollamount="8">7/8系統將進行年度保養，請使用者留意。</marquee>
    </div>
    <h3>建言一覽</h3>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">📜<b> 最新建言</b></div>
                <div class="card-body">
                    <p>學生希望改善校內飲水機品質...</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">📊<b> 熱門建言</b></div>
                <div class="card-body">
                    <p>學生希望改善校內飲水機品質...</p>
                </div>
            </div>
        </div>
        <div class="honor-wrapper">
            <h3><i class="fas fa-medal"></i> 榮譽榜</h3>

            <div class="honor-item">
                <h5><i class="fas fa-trophy honor-icon"></i> 卓越貢獻獎</h5>
                <p>感謝李珍校友捐贈百萬 為輔大永續發展注入愛與希望</p>
            </div>

            <div class="honor-item">
                <h5><i class="fas fa-trophy honor-icon"></i> 卓越貢獻獎</h5>
                <p>感謝張氏家庭百萬美金捐贈化學系及民生學院 助力輔大教育永續發展</p>
            </div>

        </div>

    </div>

</body>

</html>