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
            font-family: "Noto Serif TC", serif;
        }

        .wrapper {
            background: #fff;
            width: 500px;
            max-width: 700px;
            margin-top: 90px;
            margin-left: auto;
            /* 水平居中 */
            margin-right: auto;
            /* 水平居中 */
            display: block;
            /* 讓按鈕成為塊級元素，才能使用 margin: 0 auto */
            padding: 40px;
            border-radius: 40px;
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
                    <input type="password" id='password' name='Password' placeholder="請輸入密碼" required>
                    <span class="show-password-btn" onclick="togglePassword('password', 'eye-icon')">
                        <ion-icon id="eye-icon" name="eye-off-outline"></ion-icon>
                    </span>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                    <input type="password" id='confirm_password' name='Confirm_Password' placeholder="請再次輸入密碼" required>
                    <span class="show-password-btn" onclick="togglePassword('confirm_password', 'confirm-eye-icon')">
                        <ion-icon id="confirm-eye-icon" name="eye-off-outline"></ion-icon>
                    </span>
                </div>
                <button type="submit" class="btn">註冊</button>

                <div class="login-register">
                    <p>已經擁有帳號 ?<a href="login.php" class="regist-link">登入</a></p>
                </div>
            </form>
        </div>
    </div>
    <!--ionicons-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    
    <script>
        function togglePassword() {
            // 取得密碼欄位
            var passwordField = document.getElementById("password");
            var confirmPasswordField = document.getElementById("confirm_password");

            // 取得眼睛圖示
            var eyeIcon = document.getElementById("eye-icon");
            var confirmEyeIcon = document.getElementById("confirm-eye-icon");

            // 切換顯示/隱藏密碼
            var type = passwordField.type === "password" ? "text" : "password";
            passwordField.type = type;
            confirmPasswordField.type = type;

            // 切換眼睛圖示
            eyeIcon.name = type === "password" ? "eye-off-outline" : "eye-outline";
            confirmEyeIcon.name = type === "password" ? "eye-off-outline" : "eye-outline";
        }
    </script>

</body>

</html>