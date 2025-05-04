<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $suggestion_id = intval($_POST['suggestion_id']);
    $status = $_POST['status'];
    $admin_id = $_SESSION['User_ID'];

    $stmt = $link->prepare("INSERT INTO Progress (Suggestion_ID, Status, Updated_At, Updated_By)
                            VALUES (?, ?, NOW(), ?)
                            ON DUPLICATE KEY UPDATE Status = VALUES(Status), Updated_At = NOW(), Updated_By = VALUES(Updated_By)");
    $stmt->bind_param("isi", $suggestion_id, $status, $admin_id);
    $stmt->execute();

    header("Location: suggestion_detail.php?id=$suggestion_id");
    exit;
}
?>