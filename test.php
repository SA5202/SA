<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>輔大愛校建言捐款系統</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Noto Sans TC', sans-serif;
    }
    .sidebar {
      height: 100vh;
      background-color: #2e3b55;
      color: white;
      position: fixed;
      width: 240px;
      padding-top: 20px;
    }
    .sidebar h4 {
      text-align: center;
      font-weight: bold;
      margin-bottom: 30px;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 12px 20px;
    }
    .sidebar a:hover {
      background-color: #1d273a;
    }
    .main {
      margin-left: 240px;
      padding: 30px;
    }
    .card-title {
      font-weight: 600;
    }
    .topbar {
      background-color: white;
      padding: 10px 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    .topbar .user-info {
      text-align: right;
    }
  </style>
</head>
<body>

  <!-- 側邊欄 -->
  <div class="sidebar">
    <h4>輔大 I-Money</h4>
    <a href="#">首頁</a>
    <a href="#">建言總覽</a>
    <a href="#">提出建言</a>
    <a href="#">捐款進度</a>
    <a href="#">榮譽制度</a>
    <a href="#">捐款報表</a>
    <a href="#">聯絡我們</a>
  </div>

  <!-- 主內容區 -->
  <div class="main">
    <!-- 上方欄 -->
    <div class="topbar d-flex justify-content-between align-items-center">
      <h5 class="mb-0">輔大愛校建言捐款系統</h5>
      <div class="user-info">
        <span>您好，使用者</span>
        <button class="btn btn-outline-primary btn-sm ms-2">登出</button>
      </div>
    </div>

    <!-- 系統公告 -->
    <div class="alert alert-info">
      系統公告：4/10 將進行系統維護，請提前完成捐款與建言。
    </div>

    <!-- 建言內容 -->
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">最新建言</h5>
        <p class="card-text">學生建議改善校內飲水機水質與維護頻率。</p>
      </div>
    </div>

    <!-- 捐款進度 -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">捐款進度</h5>
        <div class="progress">
          <div class="progress-bar bg-success" style="width: 65%;">65%</div>
        </div>
        <p class="mt-2">目前已募得 NT$ 65,000 / 100,000</p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
