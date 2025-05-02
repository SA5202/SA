<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

// 取得使用者輸入的 Email
$email = $_POST['email'] ?? '';
$email = trim($email);

// 驗證 Email 是否存在
$sql = "SELECT * FROM useraccount WHERE Email = ?";
$stmt = $link->prepare($sql);
if (!$stmt) {
    die('SQL 語句準備失敗: ' . $link->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // 產生 token
    date_default_timezone_set('Asia/Taipei');

    $token = bin2hex(random_bytes(32));
    $expires = "9999-12-31 23:59:59";

    // 儲存 token 到資料庫
    $update = "UPDATE useraccount SET reset_token = ?, reset_token_expires = ? WHERE Email = ?";
    $stmt = $link->prepare($update);
    if (!$stmt) {
        die('SQL 更新失敗: ' . $link->error);
    }

    $stmt->bind_param("sss", $token, $expires, $email);
    if (!$stmt->execute()) {
        die('資料庫更新失敗: ' . $stmt->error);
    }

    // 建立重設連結
    $reset_link = "http://localhost/Web/SA/reset.php?token=$token";

    // 寄送電子郵件
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ziqin030@gmail.com';  // 你的 Gmail
        $mail->Password   = 'dgbd hcrk javu wxdr'; // 你的 Gmail 應用程式密碼
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('ziqin030@gmail.com', 'FJU I-Money');
        $mail->addAddress($email);  // 使用者的 email 地址

        $mail->Subject = '密碼重設連結';
        $mail->Body    = "您好，請點擊以下連結重設您的密碼：\n\n$reset_link";

        if ($mail->send()) {
            echo "<script>
                    alert('重設連結已寄出，請至信箱查看。');
                    window.location.href = 'login.php';
                  </script>";
        } else {
            echo "<script>
                    alert('寄送郵件失敗，請稍後再試。');
                    window.location.href = 'forgot_password.php';
                  </script>";
        }
    } catch (Exception $e) {
        echo "<script>
                alert('寄送重設信件時發生錯誤: {$mail->ErrorInfo}');
                window.location.href = 'forgot_password.php'; // 回到忘記密碼頁面
              </script>";
    }
} else {
    // 查無此 Email，顯示錯誤並回到忘記密碼頁面
    echo "<script>
            alert('查無此 Email，請檢查後再試。');
            window.location.href = 'forgot_password.php';  // 回到忘記密碼頁面
          </script>";
}

$link->close();
