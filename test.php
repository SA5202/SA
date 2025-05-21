<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>VIP 錦旗</title>
  <style>
   

    .pennant {
      width: 280px;
      height: 400px;
      background: linear-gradient(to bottom, #ffe600, #ff6600);
      clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 50px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      color: white;
    }

    .title {
      font-size: 48px;
      font-weight: bold;
      color: red;
      margin-top: 120px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .fringe {
      position: absolute;
      bottom: 0;
      width: 100%;
      height: 12px;
      background: repeating-linear-gradient(
        to right,
        gold 0 6px,
        orange 6px 12px
      );
      clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 40%, 0 100%);
    }

    .ribbon-top {
      position: absolute;
      top: 0;
      width: 100%;
      height: 10px;
      background: orange;
    }

  </style>
</head>
<body>
  <div class="pennant">
    <div class="ribbon-top"></div>
    <div class="title">VIP1</div>
    <div class="fringe"></div>
  </div>
</body>
</html>
