<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>登入失敗</title>
    <style>
        body {
            font-family: 'Noto Serif TC', serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: transparent;
            animation: fadeIn 0.8s ease-in;
         }

        .alert-card {
            width: 100%;
            max-width: 700px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 25px;
            box-sizing: border-box;
            text-align: center;
            position: relative;
            border-left: 12px solid rgb(205, 89, 87);
        }

        .alert-title {
            font-size: 20px;
            font-weight: bold;
            color: rgb(205, 89, 87);
            margin-bottom: 15px;
        }

        .alert-message {
            font-size: 18px;
            font-weight: bold;
            color: #555;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            background: none;
            border: none;
            color: #aaa;
            cursor: pointer;
        }

        .close-btn:hover {
            color: #666;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="alert-card error">
        <button class="close-btn" onclick="closeAlert()">×</button>
        <div class="alert-title">登入失敗！</div>
        <div class="alert-message">帳號或密碼有誤，請重新登入。</div>
    </div>

    <script>
        function closeAlert() {
            document.querySelector('.alert-card').style.display = 'none';
        }

        // 自動跳轉至 login.php
        setTimeout(function () {
            window.location.href = 'login.php';
        }, 3000);
    </script>
</body>

</html>