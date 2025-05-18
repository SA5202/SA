<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊提示</title>
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
            width: 70%;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 25px;
            box-sizing: border-box;
            text-align: center;
            position: relative;
        }

        .alert-card.success {
            border-left: 12px solid rgb(147, 194, 90);
        }

        .alert-card.error {
            border-left: 12px solid rgb(205, 89, 87);
        }

        .alert-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .alert-message {
            font-size: 18px;
            font-weight: bold;
            color: #555;
        }

        .alert-card.success .alert-title {
            color: rgb(147, 194, 90);
        }

        .alert-card.error .alert-title {
            color: rgb(205, 89, 87);
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
    <?php
        // 取得表單提交的資料
        $User_Name = $_POST['User_Name'];
        $Email = $_POST['Email'];
        $Password = $_POST['Password'];
        $Confirm_Password = $_POST['Confirm_Password'];
        $Nickname = $User_Name; // 預設暱稱為帳號

        // 檢查密碼是否一致
        if ($Password !== $Confirm_Password) {
            echo "
            <div class='alert-card error'>
                <button class='close-btn' onclick='closeAlert()'>×</button>
                <div class='alert-title'>註冊失敗！</div>
                <div class='alert-message'>輸入的密碼不一致，請重新嘗試。</div>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'register.php';
                }, 3000);
                function closeAlert() {
                    document.querySelector('.alert-card').style.display = 'none';
                }
            </script>";
            exit;
        }

        // 連接到資料庫
        $link = mysqli_connect('localhost', 'root', '', 'SA');
        if (!$link) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // 檢查電子郵件是否已存在
        $checkEmailQuery = "SELECT * FROM useraccount WHERE Email = ?";
        $stmt = mysqli_prepare($link, $checkEmailQuery);
        mysqli_stmt_bind_param($stmt, 's', $Email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            // 如果電子郵件已存在
            echo "
            <div class='alert-card error'>
                <button class='close-btn' onclick='closeAlert()'>×</button>
                <div class='alert-title'>註冊失敗！</div>
                <div class='alert-message'>此電子郵件已經被註冊過，請使用其他電子郵件。</div>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'register.php';
                }, 3000);
                function closeAlert() {
                    document.querySelector('.alert-card').style.display = 'none';
                }
            </script>";
        } else {
            // 插入新帳號
            $insertQuery = "INSERT INTO useraccount(User_Name, Nickname, Email, Password, User_Type) VALUES(?, ?, ?, ?, 'user')";
            $stmt = mysqli_prepare($link, $insertQuery);
            mysqli_stmt_bind_param($stmt, 'ssss', $User_Name, $Nickname, $Email, $Password);

            if (mysqli_stmt_execute($stmt)) {
                // 成功註冊
                echo "
                <div class='alert-card success'>
                    <button class='close-btn' onclick='closeAlert()'>×</button>
                    <div class='alert-title'>註冊成功！</div>
                    <div class='alert-message'>帳戶註冊成功！即將跳轉至登入頁面。</div>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
                    function closeAlert() {
                        document.querySelector('.alert-card').style.display = 'none';
                    }
                </script>";
            } else {
                // 寫入失敗
                echo "
                <div class='alert-card error'>
                    <button class='close-btn' onclick='closeAlert()'>×</button>
                    <div class='alert-title'>註冊失敗！</div>
                    <div class='alert-message'>帳戶註冊過程中發生錯誤，請稍後再試。</div>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'register.php';
                    }, 3000);
                    function closeAlert() {
                        document.querySelector('.alert-card').style.display = 'none';
                    }
                </script>";
            }
        }

        // 關閉連線
        mysqli_stmt_close($stmt);
        mysqli_close($link);
    ?>
</body>

</html>