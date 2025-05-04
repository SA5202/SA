<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo "未授權";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST["suggestion_id"]);
    $title = $_POST["title"];
    $description = $_POST["description"];
    $status = $_POST["status"];
    $admin_id = $_SESSION['User_ID'];

    // 更新建言內容
    $stmt1 = $link->prepare("UPDATE Suggestion SET Title = ?, Description = ?, Updated_At = NOW() WHERE Suggestion_ID = ?");
    $stmt1->bind_param("ssi", $title, $description, $id);
    $stmt1->execute();

    // 更新處理進度
    $stmt2 = $link->prepare("INSERT INTO Progress (Suggestion_ID, Status, Updated_At, Updated_By)
                             VALUES (?, ?, NOW(), ?)
                             ON DUPLICATE KEY UPDATE Status = VALUES(Status), Updated_At = NOW(), Updated_By = VALUES(Updated_By)");
    $stmt2->bind_param("isi", $id, $status, $admin_id);
    $stmt2->execute();

    header("Location: suggestion_detail.php?id=$id");
    exit;
}
?>