<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

// 設定時區為台北時間，避免時差問題
date_default_timezone_set('Asia/Taipei');

// 僅限 super 和 general 存取
if (!isset($_SESSION['User_Name']) || ($_SESSION['admin_type'] !== 'super' && $_SESSION['admin_type'] !== 'general')) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['User_Name'];

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die("資料庫連接失敗：" . $link->connect_error);
}

// 撈取管理員 ID
$admin_stmt = $link->prepare("SELECT User_ID FROM UserAccount WHERE User_Name = ?");
$admin_stmt->bind_param("s", $admin_name);
$admin_stmt->execute();
$admin_result = $admin_stmt->get_result();

if ($admin_result->num_rows === 0) {
    header("Location: donation_admin_create.php?error=無法識別管理員身份");
    exit();
}
$admin_id = $admin_result->fetch_assoc()['User_ID'];

// 表單資料
$donor_name = trim($_POST['donor_name'] ?? '');
$donor_email = trim($_POST['donor_email'] ?? '');
$funding_id = $_POST['funding_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$method_id = 7; // 現金
$needs_receipt = isset($_POST['receipt']) ? 1 : 0;
$note = trim($_POST['note'] ?? '');
$status = '已捐款';
$date = date('Y-m-d H:i'); // 取得台北時間
$is_manual = 1;
$is_anonymous = 0;

// 如果需要收據但沒填 email，阻擋送出
if ($needs_receipt && empty($donor_email)) {
    header("Location: donation_admin_create.php?error=" . urlencode("需要收據時必須提供電子郵件"));
    exit();
}

// 判斷是否指定使用者（若沒填則為 NULL 且匿名）
$user_id = null;
if (!empty($donor_name)) {
    $user_stmt = $link->prepare("SELECT User_ID, Email FROM UserAccount WHERE User_Name = ?");
    $user_stmt->bind_param("s", $donor_name);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
        $user_id = $user_data['User_ID'];
        if (empty($donor_email)) {
            $donor_email = $user_data['Email'];
        }
    } else {
        $is_anonymous = 1;
    }
} else {
    $is_anonymous = 1;
}

// 狀態描述組合
$notes = [];
if ($is_anonymous) $notes[] = '匿名';
if ($needs_receipt) $notes[] = '收據';
if (!empty($note)) $notes[] = '備註: ' . $note;
if (!empty($notes)) {
    $status .= '（' . implode('，', $notes) . '）';
}

// 撈取項目名稱（寄信用）
$project_stmt = $link->prepare("SELECT s.Title FROM Suggestion s JOIN FundingSuggestion f ON f.Suggestion_ID = s.Suggestion_ID WHERE f.Funding_ID = ?");
$project_stmt->bind_param("i", $funding_id);
$project_stmt->execute();
$project_result = $project_stmt->get_result();
$project_title = $project_result->num_rows > 0 ? $project_result->fetch_assoc()['Title'] : '不明項目';

// 寫入資料庫
$insert = $link->prepare("
    INSERT INTO Donation 
    (User_ID, Funding_ID, Method_ID, Donation_Amount, Status, Donation_Date, 
     Is_Manual, Added_By_Admin_ID, Is_Anonymous, Needs_Receipt, Donor_Email) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$insert->bind_param(
    "iiiissiiiis",
    $user_id,
    $funding_id,
    $method_id,
    $amount,
    $status,
    $date,
    $is_manual,
    $admin_id,
    $is_anonymous,
    $needs_receipt,
    $donor_email
);

// 查詢該募款項目的 Required_Amount 和 Raised_Amount
$funding_stmt = $link->prepare("SELECT Required_Amount, Raised_Amount FROM FundingSuggestion WHERE Funding_ID = ?");
$funding_stmt->bind_param("i", $funding_id);
$funding_stmt->execute();
$funding_result = $funding_stmt->get_result();

if ($funding_result->num_rows > 0) {
    $funding_data = $funding_result->fetch_assoc();
    $required_amount = $funding_data['Required_Amount'];
    $raised_amount = $funding_data['Raised_Amount'];

    // 計算剩餘的募款金額
    $remaining_amount = $required_amount - $raised_amount;

    // 檢查捐款金額是否超過剩餘金額
    if ($amount > $remaining_amount) {
        // 如果超過，顯示錯誤並停止執行
        header("Location: donation_admin_create.php?error=" . urlencode("捐款金額不能超過剩餘金額 ($remaining_amount)"));
        exit();
    }

    // 更新募款金額
    $new_raised_amount = $raised_amount + $amount;
    $update_stmt = $link->prepare("UPDATE FundingSuggestion SET Raised_Amount = ? WHERE Funding_ID = ?");
    $update_stmt->bind_param("di", $new_raised_amount, $funding_id);
    $update_stmt->execute();
} else {
    // 如果未找到對應的 Funding_ID，可以處理錯誤或提醒管理員
    header("Location: donation_admin_create.php?error=" . urlencode("未找到對應的募款項目"));
    exit();
}

// 寫入 Donation 表格
$insert = $link->prepare("
    INSERT INTO Donation 
    (User_ID, Funding_ID, Method_ID, Donation_Amount, Status, Donation_Date, 
     Is_Manual, Added_By_Admin_ID, Is_Anonymous, Needs_Receipt, Donor_Email) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$insert->bind_param(
    "iiiissiiiis",
    $user_id,
    $funding_id,
    $method_id,
    $amount,
    $status,
    $date,
    $is_manual,
    $admin_id,
    $is_anonymous,
    $needs_receipt,
    $donor_email
);

if ($insert->execute()) {
    // 如果需要收據且 email 格式正確，寄發收據信
    if ($needs_receipt && filter_var($donor_email, FILTER_VALIDATE_EMAIL)) {
        require_once __DIR__ . '/send_receipt.php';
        $name_for_email = $is_anonymous ? '匿名' : $donor_name;
        send_receipt_email($donor_email, [
            'name' => $name_for_email,
            'amount' => $amount,
            'date' => $date,
            'project' => $project_title
        ]);
    }
    header("Location: donation_admin_create.php?success=1");
} else {
    $error = $link->error;
    header("Location: donation_admin_create.php?error=" . urlencode("新增失敗: $error"));
}
exit();

