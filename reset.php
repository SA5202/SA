<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>輔仁大學愛校建言捐款系統</title>

    <!-- 字體與樣式 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: transparent;
            font-family: "Noto Serif TC", serif;
            font-size: 1.1rem;
            line-height: 1.8;
            margin: 0;
            padding: 30px;
            color: #333;
        }
    </style>
</head>

<body>
    <?php
    $link = new mysqli('localhost', 'root', '', 'sa');
    if ($link->connect_error) {
        die('資料庫連接失敗: ' . $link->connect_error);
    }

    // 處理使用者提交的新密碼
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_password = $_POST['new_password'] ?? '';
        $token = $_POST['token'] ?? '';

        if (!empty($new_password) && !empty($token)) {
            // 可選：加密密碼（建議使用 password_hash）
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // 更新密碼並清除 token
            $sql = "UPDATE useraccount SET Password = ?, reset_token = NULL, reset_token_expires = NULL 
                WHERE reset_token = ? AND reset_token_expires > NOW()";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("ss", $hashed_password, $token);
            $stmt->execute();

            if ($stmt->affected_rows === 1) {
                echo "密碼已成功更新，5 秒後跳轉回登入頁面。";
                echo '<meta http-equiv="refresh" content="5;url=login.php">';
            } else {
                echo "無效或過期的重設連結。";
            }
            $stmt->close();
        } else {
            echo "請輸入新密碼。";
        }

        $link->close();
        exit;
    }

    // 顯示密碼重設表單（使用 token 驗證）
    $token = $_GET['token'] ?? '';

    if ($token) {
        $sql = "SELECT * FROM useraccount WHERE reset_token = ? AND reset_token_expires > NOW()";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // 顯示表單
    ?>
            <!DOCTYPE html>
            <html lang="zh-Hant">

            <head>
                <meta charset="UTF-8">
                <title>重設密碼</title>
            </head>

            <body>
                <h2>請輸入新密碼</h2>
                <form method="POST">
                    <input type="password" name="new_password" placeholder="新密碼" required>
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <button type="submit">重設密碼</button>
                </form>
            </body>

            </html>
    <?php
        } else {
            echo "此連結無效或已過期。";
        }
        $stmt->close();
    } else {
        echo "缺少驗證 token。";
    }

    $link->close();
    ?>


</body>

</html>