<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $suggestion_id = intval($_POST['suggestion_id']);
    $status = $_POST['new_status']; // 注意這裡對應 suggestion_detail.php 的 name
    $admin_id = $_SESSION['User_ID'];

    // 檢查狀態是否在允許範圍
    $allowed_statuses = ['未處理', '處理中', '已完成'];
    if (!in_array($status, $allowed_statuses)) {
        echo "無效狀態"; exit;
    }

    $stmt = $link->prepare("INSERT INTO Progress (Suggestion_ID, Status, Updated_At, Updated_By)
                            VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("isi", $suggestion_id, $status, $admin_id);
    $stmt->execute();

    header("Location: suggestion_detail.php?id=$suggestion_id");
    exit;
}
?>