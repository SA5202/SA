<?php
session_start();
require_once "db_connect.php";

// 僅允許管理員操作
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 安全處理
    $suggestion_id = isset($_POST['suggestion_id']) ? intval($_POST['suggestion_id']) : 0;
    $status = isset($_POST['new_status']) ? trim($_POST['new_status']) : '';
    $admin_id = $_SESSION['User_ID'];

    // 檢查狀態是否合法
    $allowed_statuses = ['未受理', '審核中', '處理中', '已完成'];
    if ($suggestion_id <= 0 || !in_array($status, $allowed_statuses)) {
        echo "無效請求";
        exit;
    }

    // 1. 刪除該建議ID的所有超過目標狀態的進度紀錄
    $status_order = ['未受理', '審核中', '處理中', '已完成'];
    $status_index = array_search($status, $status_order);

    // 刪除當前狀態及之後的所有紀錄
    $delete_stmt = $link->prepare("
        DELETE FROM Progress
        WHERE Suggestion_ID = ? 
        AND (Status = ? OR FIELD(Status, '未受理', '審核中', '處理中', '已完成') > ?)
    ");
    $delete_stmt->bind_param("isi", $suggestion_id, $status, $status_index);
    $delete_stmt->execute();
    $delete_stmt->close();

    // 2. 插入當前狀態為最新進度
    $stmt = $link->prepare("
        INSERT INTO Progress (Suggestion_ID, Status, Updated_At, Updated_By)
        VALUES (?, ?, NOW(), ?)
    ");
    $stmt->bind_param("isi", $suggestion_id, $status, $admin_id);
    if ($stmt->execute()) {
        // 更新成功後重定向回建議詳情頁面
        header("Location: suggestion_detail.php?id=$suggestion_id");
    } else {
        echo "更新失敗，請重試。";
    }
    $stmt->close();
}
?>
