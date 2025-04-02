<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>使用者登入</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .wrapper {
            background: #fff;
            margin-left: 40%;
            max-width: 800px;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }

        .wrapper h2 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box input {
            width: 100%;
            padding: 12px 40px;
            border: 1px solid #ddd;
            border-radius: 25px;
            outline: none;
            font-size: 16px;
            transition: 0.3s;
        }

        .input-box input:focus {
            border-color:rgb(162, 198, 105);
            box-shadow: 0 0 8px rgba(93, 101, 72, 0.88);
        }

        .input-box .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #666;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background:rgb(205, 204, 147);
            color: #fff;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background:rgb(180, 181, 134);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .remember-forgot a {
            color:rgb(146, 49, 22);
            text-decoration: none;
        }

        .login-register p {
            margin-top: 15px;
            font-size: 14px;
        }

        .login-register a {
            color:rgb(47, 158, 46);
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>登入</h2>
        <form action="logincheck.php" method="POST">
            <div class="input-box">
                <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                <input type="text" name="username" placeholder="使用者名稱" required>
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                <input type="password" name="password" placeholder="密碼" required>
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox" name="remember_me"> 記住我</label>
                <a href="#">忘記密碼?</a>
            </div>
            <button type="submit" class="btn">登入</button>
            <div class="login-register">
                <p>還沒有帳號? <a href="register.php">註冊</a></p>
                <p>(請使用LDAP登入)</p>
            </div>
        </form>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>
