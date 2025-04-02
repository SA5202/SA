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

<body>
    <h2 class="mb-4 text-primary">首頁</h2>
    <div class="card">
        <div class="card-header"><b>捐款進度</b></div>
        <div class="card-body">
            <div class="progress">
                <div class="progress-bar" style="width: 60%;">60%</div>
            </div>
        </div>
    </div>
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
                <div class="card-header">📊<b> 捐款報表</b></div>
                <div class="card-body">
                    <p><a href="#" class="text-link">下載最新捐款報表</a></p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>