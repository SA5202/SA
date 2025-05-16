<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>頒獎台現場</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #e0e0e0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow: hidden;
    }

    .stage {
      width: 80%;
      height: 70%;
      background: #333;
      border-radius: 20px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.7);
    }

    .stage::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('stage-background.jpg') no-repeat center center/cover;
      opacity: 0.6;
    }

    .podium {
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 300px;
      height: 100px;
      background: gold;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    }

    .award {
      position: absolute;
      bottom: 110px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 200px;
      background-color: transparent;
      display: flex;
      justify-content: center;
      align-items: center;
      opacity: 0;
      transition: opacity 1s;
    }

    .award img {
      width: 50%;
      height: auto;
    }

    .winner {
      position: absolute;
      bottom: 100px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 180px;
      background-color: #fff;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      opacity: 0;
      transition: opacity 1s;
    }

    .winner img {
      width: 80%;
      height: auto;
      border-radius: 50%;
    }

    /* 動畫效果：人物走上台 */
    @keyframes walkOnStage {
      0% {
        bottom: -150px;
        opacity: 0;
      }
      100% {
        bottom: 100px;
        opacity: 1;
      }
    }

    /* 顯示獎杯 */
    .award.active, .winner.active {
      opacity: 1;
      animation: walkOnStage 2s ease-out forwards;
    }
  </style>
</head>
<body>

  <div class="stage">
    <!-- 背景圖 -->
    <div class="podium"></div>

    <!-- 獎杯 -->
    <div class="award">
      <img src="trophy.png" alt="獎杯">
    </div>

    <!-- 獲獎者 -->
    <div class="winner">
      <img src="winner.jpg" alt="獲獎者">
    </div>
  </div>

  <script>
    // 讓獲獎者和獎杯出現動畫效果
    window.onload = () => {
      setTimeout(() => {
        document.querySelector('.award').classList.add('active');
      }, 1000); // 延遲獎杯顯示

      setTimeout(() => {
        document.querySelector('.winner').classList.add('active');
      }, 2000); // 延遲獲獎者顯示
    }
  </script>

<div class="award-item">
  <div class="award-content">
    <!-- 內容 -->
  </div>
  <div class="award-base gold">
    <div class="base-layer base-front"></div>
    <div class="base-layer base-top"></div>
  </div>
</div>
<style>
  /* 加厚效果模擬立體層 */
.base-layer.base-front {
    background-color: rgba(0,0,0,0.1);
    height: 10px;
    margin-top: -10px;
    border-radius: 0 0 5px 5px;
}

.base-layer.base-top {
    height: 10px;
    background: rgba(255,255,255,0.2);
    border-radius: 8px 8px 0 0;
}
</style>
</body>
</html>
