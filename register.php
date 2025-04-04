<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>使用者註冊</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }


        .wrapper {
            background: #fff;
            width: 400px;
            max-width: 700px;
            margin-top: 60px;
            margin-left: 30%;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
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
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .input-box input:focus {
            border-color: rgb(160, 205, 86);
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
            background: rgb(205, 204, 147);
            color: #fff;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: rgb(180, 181, 134);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .remember-forgot a {
            color: rgb(146, 49, 22);
            text-decoration: none;
        }

        .login-register p {
            margin-top: 15px;
            font-size: 14px;
        }

        .login-register a {
            color: rgb(47, 158, 46);
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="form-box register">
            <h2>註冊</h2>
            <form action="insert.php" method="POST">
                <div class="input-box">
                    <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                    <input type="text" name='User_Name' placeholder="請輸入帳號" required>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                    <input type="email" name='Email' placeholder="請輸入 EMAIL" required>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                    <input type="password" name='Password' placeholder="請輸入密碼" required>
                </div>
                <button type="submit" class="btn">註冊</button>

                <div class="login-register">
                    <p>已經擁有帳號 ?<a href="login.php" class="regist-link">
                            登入
                        </a></p>
                </div>
            </form>
        </div>
    </div>
    <!--ionicons-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>