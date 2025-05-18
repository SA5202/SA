<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>使用者登入</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Noto Serif TC", serif;
        }

        .wrapper {
            background: #fff;
            width: 400px;
            max-width: 700px;
            margin-top: 120px;
            margin-left: auto;
            /* 水平居中 */
            margin-right: auto;
            /* 水平居中 */
            display: block;
            /* 讓按鈕成為塊級元素，才能使用 margin: 0 auto */
            padding: 40px;
            border-radius: 40px;
            border: 1px solid #ccc;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s ease-in-out;
        }

        .wrapper:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .wrapper h2 {
            margin-bottom: 20px;
            font-weight: 750;
            color: #333;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box input {
            width: 100%;
            padding: 12px 40px 12px 40px;
            /* Adjust padding to make space for icon */
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .input-box input:focus {
            border-color: rgb(173, 231, 248);
            box-shadow: 0 0 8px rgba(70, 117, 141, 0.88);
        }

        .input-box .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #666;
        }

        .show-password-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            cursor: pointer;
            color: #666;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: rgb(147, 188, 205);
            color: #fff;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 750;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: rgb(108, 141, 173);
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
        
        .login-register {
            margin-top: 30px;
        }

        .login-register p {
            margin: 0.5rem;
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
        <h2>登入</h2>
        <form action="logincheck.php" method="POST">
            <div class="input-box">
                <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                <input type="text" name="User_Name" placeholder="請輸入帳號" required>
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                <input type="password" id="password" name="Password" placeholder="請輸入密碼" required>
                <span class="show-password-btn" onclick="togglePassword()">
                    <ion-icon id="eye-icon" name="eye-off-outline"></ion-icon>
                </span>
            </div>
            <button type="submit" class="btn">登入</button>
            <div class="login-register">
                <p>忘記密碼 ? <a href="password.php">點擊復原</a></p>
                <p>沒有帳號 ? <a href="register.php">點擊註冊</a></p>
            </div>
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">

        </form>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var eyeIcon = document.getElementById("eye-icon");
            var type = passwordField.type === "password" ? "text" : "password";
            passwordField.type = type;
            // Toggle the eye icon
            eyeIcon.name = type === "password" ? "eye-off-outline" : "eye-outline";
        }
    </script>
</body>

</html>