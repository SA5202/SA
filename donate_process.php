<?php
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

// 建立資料庫連線
$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

// 查詢使用者 ID 與 Email
$user_query = $link->prepare("SELECT User_ID, Email FROM UserAccount WHERE User_Name = ?");
$user_query->bind_param("s", $user_name);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_result->num_rows === 0) {
    echo "<script>alert('找不到使用者帳號'); window.location.href='donation_make.php?error=找不到使用者帳號';</script>";
    exit();
}
$user_data = $user_result->fetch_assoc();
$user_id = $user_data['User_ID'];
$user_email = $user_data['Email'];

// 查詢項目名稱
$title_query = $link->prepare("
    SELECT s.Title 
    FROM FundingSuggestion f 
    JOIN Suggestion s ON f.Suggestion_ID = s.Suggestion_ID 
    WHERE f.Funding_ID = ?
");
$title_query->bind_param("i", $funding_id);
$title_query->execute();
$title_result = $title_query->get_result();
$project_title = $title_result->fetch_assoc()['Title'] ?? '未知項目';

// 狀態備註
$notes = [];
if ($is_anonymous) $notes[] = '匿名';
if ($needs_receipt) $notes[] = '收據';
if (!empty($message)) $notes[] = '留言: ' . $message;
if (!empty($notes)) {
    $status .= '（' . implode('，', $notes) . '）';
}

// 寫入資料
$insert = $link->prepare("
    INSERT INTO Donation (
        User_ID, Funding_ID, Method_ID, Donation_Amount, 
        Status, Donation_Date, Is_Anonymous, Needs_Receipt
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$insert->bind_param("iiiissii", $user_id, $funding_id, $method_id, $amount, $status, $date, $is_anonymous, $needs_receipt);

if ($insert->execute()) {
    // 若勾選收據，發送 email
    if ($needs_receipt && filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        require 'send_receipt.php';
        send_receipt_email($user_email, [
            'name' => $user_name,
            'amount' => $amount,
            'date' => $date,
            'project' => $project_title
        ]);
    }

    echo "<script>alert('捐款成功！'); window.location.href='donation_make.php?success=1';</script>";
    exit();
} else {
    $error = $link->error;
    echo "<script>alert('資料庫錯誤：$error'); window.location.href='donation_make.php?error=資料庫錯誤';</script>";
    exit();
}
?>
