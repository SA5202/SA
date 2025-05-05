<?php
session_start();
require_once "db_connect.php";

// 僅允許管理員存取
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 安全轉型與檢查
    $suggestion_id = isset($_POST['suggestion_id']) ? intval($_POST['suggestion_id']) : 0;
    $status = isset($_POST['new_status']) ? trim($_POST['new_status']) : '';
    $admin_id = $_SESSION['User_ID'];

    // 合法狀態清單（更新為包含所有狀態）
    $allowed_statuses = ['未受理', '審核中', '未處理', '處理中', '已完成'];
    
    // 檢查 suggestion_id 和狀態是否有效
    if ($suggestion_id <= 0 || !in_array($status, $allowed_statuses)) {
        echo "無效請求";
        exit;
    }

    // 新增進度紀錄
    $stmt = $link->prepare("
        INSERT INTO Progress (Suggestion_ID, Status, Updated_At, Updated_By)
        VALUES (?, ?, NOW(), ?)
    ");
    if (!$stmt) {
        echo "資料庫錯誤：" . $link->error;
        exit;
    }
    $stmt->bind_param("isi", $suggestion_id, $status, $admin_id);
    $stmt->execute();
    $stmt->close();

    // 導回詳細頁
    header("Location: suggestion_detail.php?id=$suggestion_id");
    exit;
}
?>
