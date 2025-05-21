<?php
// 必須放在最頂部，不能有空格或換行
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['User_Name'];
$funding_id = $_POST['funding_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$method_id = $_POST['method_id'] ?? '';
$is_anonymous = isset($_POST['anonymous']) ? 1 : 0;
$needs_receipt = isset($_POST['receipt']) ? 1 : 0;
$message = $_POST['message'] ?? '';
$status = '已捐款';
$date = date('Y-m-d');

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

$user_query = $link->prepare("SELECT User_ID FROM UserAccount WHERE User_Name = ?");
$user_query->bind_param("s", $user_name);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_result->num_rows === 0) {
    echo "<script>alert('找不到使用者帳號'); window.location.href='donation_make.php?error=找不到使用者帳號';</script>";
    exit();
}
$user_id = $user_result->fetch_assoc()['User_ID'];

$notes = [];
if ($is_anonymous) $notes[] = '匿名';
if ($needs_receipt) $notes[] = '收據';
if (!empty($message)) $notes[] = '留言: ' . $message;
if (!empty($notes)) {
    $status .= '（' . implode('，', $notes) . '）';
}

$insert = $link->prepare("INSERT INTO Donation (User_ID, Funding_ID, Method_ID, Donation_Amount, Status, Donation_Date) VALUES (?, ?, ?, ?, ?, ?)");
$insert->bind_param("iiiiss", $user_id, $funding_id, $method_id, $amount, $status, $date);

if ($insert->execute()) {
    // 使用 JavaScript 導回避免 header() 錯誤
    echo "<script>alert('捐款成功！'); window.location.href='donation_make.php?success=1';</script>";
    exit();
} else {
    $error = $link->error;
    echo "<script>alert('資料庫錯誤：$error'); window.location.href='donation_make.php?error=資料庫錯誤';</script>";
    exit();
}
?>