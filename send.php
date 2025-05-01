<?php

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
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // 產生 token
    // 設定時區為台灣時區（或根據需要更改）
    date_default_timezone_set('Asia/Taipei');

    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));


    // 儲存 token 到資料庫
    $update = "UPDATE useraccount SET reset_token = ?, reset_token_expires = ? WHERE Email = ?";
    $stmt = $link->prepare($update);
    $stmt->bind_param("sss", $token, $expires, $email);
    $stmt->execute();


    // 建立重設連結
    $reset_link = "http://localhost/Web/SA/reset.php?token=$token"; // 修改為指向 reset.php

    // 然後再寄信
    $mail = new PHPMailer(true); // 確保這一行存在
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ziqin030@gmail.com';         // 你的 Gmail
        $mail->Password   = 'dgbd hcrk javu wxdr';        // 你的 Gmail 應用程式密碼
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('ziqin030@gmail.com', 'FJU I-Money');
        $mail->addAddress($email);  // 使用者的 email 地址

        $mail->Subject = '密碼重設連結';
        $mail->Body    = "您好，請點擊以下連結重設您的密碼（1小時內有效）：\n\n$reset_link";

        $mail->send();  // 發送郵件

        echo "重設連結已寄出，請至信箱查看。";
        echo '<meta http-equiv="refresh" content="5;url=login.php">'; // 5秒後重定向到登入頁面
    } catch (Exception $e) {
        echo "寄信失敗: {$mail->ErrorInfo}";
    }
} else {
    echo "查無此 Email。";
}

$link->close();
