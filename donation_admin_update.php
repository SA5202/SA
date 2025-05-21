<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION['User_Name']) || $_SESSION['User_Type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 驗證必要欄位
$donation_id = $_POST['donation_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$method_id = $_POST['method_id'] ?? null;
$status = $_POST['status'] ?? '';

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

// 執行更新
$update = $link->prepare("UPDATE Donation SET Donation_Amount = ?, Method_ID = ?, Status = ? WHERE Donation_ID = ?");
$update->bind_param("iisi", $amount, $method_id, $status, $donation_id);

if ($update->execute()) {
    header("Location: donation_list.php?success=1");
    exit();
} else {
    die("更新失敗：" . $link->error);
}
?>
