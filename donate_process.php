<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

// 設定時區為台北時間，避免時差問題
date_default_timezone_set('Asia/Taipei');

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
$date = date('Y-m-d H:i');

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
    echo "<script>alert('找不到使用者帳號'); window.location.href='donation_page.php?error=找不到使用者帳號';</script>";
    exit();
}
$user_data = $user_result->fetch_assoc();
$user_id = $user_data['User_ID'];
$user_email = $user_data['Email'];

// 查詢 Funding_ID 是否存在
$checkFundingQuery = $link->prepare("SELECT 1 FROM FundingSuggestion WHERE Funding_ID = ?");
$checkFundingQuery->bind_param("i", $funding_id);
$checkFundingQuery->execute();
$checkFundingResult = $checkFundingQuery->get_result();

if ($checkFundingResult->num_rows === 0) {
    echo "<script>alert('錯誤：該募款項目不存在。'); window.location.href='donation_page.php?error=該募款項目不存在';</script>";
    exit();
}
// 查詢募款項目的總金額與已募金額
$fundingQuery = $link->prepare("SELECT Required_Amount, IFNULL(Raised_Amount, 0) AS Raised_Amount FROM FundingSuggestion WHERE Funding_ID = ?");
$fundingQuery->bind_param("i", $funding_id);
$fundingQuery->execute();
$fundingResult = $fundingQuery->get_result();

if ($fundingResult->num_rows === 0) {
    echo "<script>alert('錯誤：無法取得募款項目資料。'); window.location.href='donation_page.php?error=無法取得募款資料';</script>";
    exit();
}

$fundingData = $fundingResult->fetch_assoc();
$requiredAmount = (int)$fundingData['Required_Amount'];
$raisedAmount = (int)$fundingData['Raised_Amount'];
$remainingAmount = $requiredAmount - $raisedAmount;

// 確認捐款金額不得超過剩餘金額
if ((int)$amount > $remainingAmount) {
    echo "<script>alert('捐款金額超過剩餘所需金額（剩餘：$remainingAmount 元），請重新輸入。'); window.location.href='donation_page.php?error=金額超過剩餘金額';</script>";
    exit();
}

// 查詢項目名稱
$title_query = $link->prepare(
    "
    SELECT s.Title 
    FROM FundingSuggestion f 
    JOIN Suggestion s ON f.Suggestion_ID = s.Suggestion_ID 
    WHERE f.Funding_ID = ?"
);
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

// 寫入捐款資料
$sql = "
    INSERT INTO Donation (
        User_ID, Funding_ID, Method_ID, Donation_Amount, 
        Status, Donation_Date, Is_Anonymous, Needs_Receipt, Donor_Email
    ) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)
";
$insert = $link->prepare($sql);
$insert->bind_param("iiiisiis", $user_id, $funding_id, $method_id, $amount, $status, $is_anonymous, $needs_receipt, $user_email);

if ($insert->execute()) {
    // 更新募款進度
    $update = $link->prepare("
        UPDATE FundingSuggestion 
        SET Raised_Amount = IFNULL(Raised_Amount, 0) + ?, Updated_At = NOW()
        WHERE Funding_ID = ?");
    $update->bind_param("di", $amount, $funding_id);
    $update->execute();
    $update->close();

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

    echo "<script>alert('捐款成功！'); window.location.href='donate.php?success=1';</script>";
    exit();
} else {
    $error = $link->error;
    echo "<script>alert('資料庫錯誤：$error'); window.location.href='donation_page.php?error=資料庫錯誤';</script>";
    exit();
}
