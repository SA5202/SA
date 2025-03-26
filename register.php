<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>使用者註冊</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="wrapper">
        <div class="form-box register">
            <h2>註冊</h2>
            <form action="insert.php" method="POST">
                <div class="input-box">
                    <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                    <input type="text" name='username' required>
                    <label>Username</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                    <input type="email" name='email' required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                    <input type="password" name='password' required>
                    <label>Password</label>
                </div>
                <button type="submit" class="btn">Register</button>

                <div class="login-register">
                    <p>已經擁有帳號?<a href="login.php" class="regist-link">
                            登入
                        </a></p>
                </div>
            </form>
        </div>
    </div>
    <style>
        .wrapper {
            height: 480px;
        }
    </style>
    <!--ionicons-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>