<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}

// 資料驗證與準備
$user_name = $_SESSION['User_Name'];
$funding_id = $_POST['funding_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$method_id = $_POST['method_id'] ?? '';
$is_anonymous = isset($_POST['anonymous']) ? 1 : 0;
$needs_receipt = isset($_POST['receipt']) ? 1 : 0;
$message = $_POST['message'] ?? '';
$status = '已捐款';
$date = date('Y-m-d');

// 建立資料庫連線
$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

// 查詢使用者 ID
$user_query = $link->prepare("SELECT User_ID FROM UserAccount WHERE User_Name = ?");
$user_query->bind_param("s", $user_name);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_result->num_rows === 0) {
    header("Location: donation_make.php?error=找不到使用者帳號");
    exit();
}
$user_id = $user_result->fetch_assoc()['User_ID'];

// 狀態附加備註
$notes = [];
if ($is_anonymous) $notes[] = '匿名';
if ($needs_receipt) $notes[] = '收據';
if (!empty($message)) $notes[] = '留言: ' . $message;
if (!empty($notes)) {
    $status .= '（' . implode('，', $notes) . '）';
}

// 寫入資料
$insert = $link->prepare("INSERT INTO Donation (User_ID, Funding_ID, Method_ID, Donation_Amount, Status, Donation_Date) VALUES (?, ?, ?, ?, ?, ?)");
$insert->bind_param("iiiiss", $user_id, $funding_id, $method_id, $amount, $status, $date);

if ($insert->execute()) {
    header("Location: donation_make.php?success=1");
    exit();
} else {
    $error = $link->error;
    header("Location: donation_make.php?error=" . urlencode("資料庫錯誤: $error"));
    exit();
}
?>