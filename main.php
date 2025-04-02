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
    /*跑馬燈*/
    #mqmain {
        background: linear-gradient(45deg, rgb(189, 182, 117), rgb(153, 151, 104));
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
</style>

<body>
    <h3>重要資訊</h3>
    <div class="item">
        <marquee id="mqmain" scrollamount="10">4/5將進行系統維護，請使用者注意。</marquee>
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
        <h3>榮譽榜</h3>
    </div>

</body>

</html>