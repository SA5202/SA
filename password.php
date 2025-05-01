<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>忘記密碼</title>
    <style>
        body {
            font-family: "Noto Serif TC", serif;
        }

        .wrapper {
            background: #fff;
            width: 400px;
            margin: 100px auto;
            padding: 40px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
            border-radius: 25px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 14px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: rgb(147, 188, 205);
            color: #fff;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn:hover {
            background: rgb(108, 141, 173);
        }

        .back-link {
            margin-top: 15px;
            display: block;
            font-size: 14px;
        }

        .back-link a {
            color: rgb(47, 158, 46);
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>忘記密碼</h2>
        <p>請輸入您的 Email，我們將寄送重設密碼連結給您</p>
        <form action="send.php" method="POST">
            <input type="email" name="email" placeholder="請輸入您的 Email" required>
            <button type="submit" class="btn">寄送重設連結</button>
        </form>
        <div class="back-link">
            <a href="login.php">← 回到登入頁</a>
        </div>
    </div>
</body>

</html>
