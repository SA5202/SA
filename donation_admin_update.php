<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

date_default_timezone_set('Asia/Taipei');

if (!isset($_SESSION['User_Name']) || $_SESSION['User_Type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 驗證必要欄位
$donation_id = $_POST['donation_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$method_id = $_POST['method_id'] ?? null;
$status = $_POST['status'] ?? '';
$donation_date_input = $_POST['donation_date'] ?? '';

if (!$donation_id || !$amount || !$method_id) {
    die("資料不完整，請返回表單檢查");
}

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die("資料庫連線失敗：" . $link->connect_error);
}

// 檢查捐款紀錄是否為手動新增
$check = $link->prepare("SELECT Is_Manual FROM Donation WHERE Donation_ID = ?");
$check->bind_param("i", $donation_id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows === 0) {
    die("找不到該筆捐款紀錄");
}
$is_manual = $res->fetch_assoc()['Is_Manual'];
if (!$is_manual) {
    die("非手動新增紀錄，禁止修改");
}

// 🔸 新增段落開始：檢查金額是否超過剩餘可捐金額
// 先取得原始捐款資訊
$stmt = $link->prepare("SELECT Funding_ID, Donation_Amount FROM Donation WHERE Donation_ID = ?");
$stmt->bind_param("i", $donation_id);
$stmt->execute();
$stmt->bind_result($funding_id, $original_amount);
if (!$stmt->fetch()) {
    die("找不到原始捐款資料");
}
$stmt->close();

// 撈出該募資項目需求與目前總募得金額
$stmt2 = $link->prepare("
    SELECT Required_Amount,
           (SELECT IFNULL(SUM(Donation_Amount), 0) FROM Donation WHERE Funding_ID = ?) AS Raised_Amount
    FROM FundingSuggestion WHERE Funding_ID = ?
");
$stmt2->bind_param("ii", $funding_id, $funding_id);
$stmt2->execute();
$stmt2->bind_result($required_amount, $raised_amount);
if (!$stmt2->fetch()) {
    die("找不到募資資料");
}
$stmt2->close();

// 算剩餘可募資金額（不包含這筆原始金額）
$remaining_amount = $required_amount - ($raised_amount - $original_amount);

if ($amount > $remaining_amount) {
    // 避免 XSS
    $safe_error = urlencode("金額超過剩餘可募金額：{$remaining_amount} 元");
    header("Location: donation_admin_edit.php?id={$donation_id}&error={$safe_error}");
    exit();
}
// 🔸 新增段落結束

// 判斷捐款日期
if (empty($donation_date_input)) {
    $donation_date = date('Y-m-d H:i:s'); // 現在時間
} else {
    // 轉換前端 datetime-local 格式為 MySQL datetime
    $donation_date = str_replace('T', ' ', $donation_date_input) . ':00';
}

// 執行更新 (多加捐款日期欄位)
$update = $link->prepare("UPDATE Donation SET Donation_Amount = ?, Method_ID = ?, Status = ?, Donation_Date = ? WHERE Donation_ID = ?");
$update->bind_param("iissi", $amount, $method_id, $status, $donation_date, $donation_id);

if ($update->execute()) {
    header("Location: donation_list.php?success=1");
    exit();
} else {
    die("更新失敗：" . $link->error);
}
