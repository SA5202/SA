<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php'; // 確保 Composer 的自動載入生效

function send_receipt_email($to_email, $info) {
    $mail = new PHPMailer(true);

    try {
        // SMTP 設定
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'alex2531566666@gmail.com';  // ← 請換成你的學校信箱
        $mail->Password = 'hmgjmudwpnmxikvm';        // ← 請換成你的 Gmail 應用程式密碼
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // 寄件人與收件人
        $mail->setFrom(
            'alex2531566666@gmail.com',
            '=?UTF-8?B?' . base64_encode('輔大愛校捐款系統') . '?='
        );
        $mail->addAddress($to_email, $info['name']);

        // 信件內容
        $mail->isHTML(true);
        $mail->Subject = '=?UTF-8?B?' . base64_encode('【輔大愛校捐款系統】捐款收據') . '?=';
        $mail->Body = "
            <h2>感謝您的捐款</h2>
            <p><strong>捐款者：</strong> {$info['name']}</p>
            <p><strong>金額：</strong> NT\$ " . number_format($info['amount']) . "</p>
            <p><strong>捐款項目：</strong> {$info['project']}</p>
            <p><strong>日期：</strong> {$info['date']}</p>
            <br><p>請妥善保存此信件作為電子收據依據。</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("收據 Email 寄送失敗: {$mail->ErrorInfo}");
        return false;
    }
}
