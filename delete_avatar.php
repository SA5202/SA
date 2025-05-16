<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => '請先登入']);
    exit;
}

$link = new mysqli('localhost', 'root', '', 'SA');
if ($link->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '資料庫連線失敗']);
    exit;
}

$User_Name = $_SESSION['User_Name'];

// 將 Avatar 欄位設為 NULL 或空字串
$stmt = $link->prepare("UPDATE useraccount SET Avatar = NULL WHERE User_Name = ?");
$stmt->bind_param("s", $User_Name);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '更新失敗']);
}
$stmt->close();
$link->close();
