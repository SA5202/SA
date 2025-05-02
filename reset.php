<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>重設密碼</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>

<body>
    <div class="wrapper">
        <?php
        $link = new mysqli('localhost', 'root', '', 'sa');
        if ($link->connect_error) {
            die('資料庫連接失敗: ' . $link->connect_error);
        }

        // 處理新密碼提交
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = $_POST['new_password'] ?? '';
            $token = $_POST['token'] ?? '';

            if (!empty($new_password) && !empty($token)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // ✅ 不檢查過期時間
                $sql = "UPDATE useraccount SET Password = ?, reset_token = NULL, reset_token_expires = NULL WHERE reset_token = ?";
                $stmt = $link->prepare($sql);
                $stmt->bind_param("ss", $hashed_password, $token);
                $stmt->execute();

                if ($stmt->affected_rows === 1) {
                    echo "密碼已成功更新，3 秒後跳轉回首頁。";
                    echo '<meta http-equiv="refresh" content="3;url=index.php">';
                } else {
                    echo "無效的重設連結或密碼更新失敗。";
                }
                $stmt->close();
            } else {
                echo "請輸入新密碼。";
            }

            $link->close();
            exit;
        }

        // 顯示表單
        $token = $_GET['token'] ?? '';

        if ($token) {
            $sql = "SELECT * FROM useraccount WHERE reset_token = ?";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
        ?>
                <h2>請輸入新密碼</h2>
                <form method="POST">
                    <div class="mb-3">
                        <input type="password" class="form-control" name="new_password" placeholder="新密碼" required>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">重設密碼</button>
                </form>
        <?php
            } else {
                echo "此連結無效或已使用。";
            }
            $stmt->close();
        } else {
            echo "缺少驗證 token。";
        }

        $link->close();
        ?>
    </div>
</body>

</html>