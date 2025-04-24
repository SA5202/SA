<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>登入失敗</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Microsoft JhengHei", sans-serif;
            
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            animation: fadeIn 0.8s ease-in;
        }

        .box {
            background-color: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 360px;
        }

        h1 {
            color:rgb(213, 84, 84);
            margin-bottom: 15px;
        }

        p {
            color: #555;
            margin-bottom: 30px;
        }

        a {
            text-decoration: none;
            padding: 12px 24px;
            background-color:rgb(102, 154, 187);
            color: #fff;
            border-radius: 30px;
            font-weight: bold;
            transition: 0.3s;
        }

        a:hover {
            background-color:rgb(65, 96, 129);
            transform: scale(1.05);
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
    <div class="box">
        <h1>登入失敗</h1>
        <p>帳號或密碼錯誤，請重新登入。</p>
        <a href="login.php">返回登入頁</a>
    </div>
</body>

</html>