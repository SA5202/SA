<?php
header('Content-Type: application/json');

if (!isset($_GET['username']) || empty($_GET['username'])) {
    echo json_encode(['success' => false, 'message' => '缺少帳號名稱']);
    exit();
}

$username = $_GET['username'];

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    echo json_encode(['success' => false, 'message' => '資料庫連線失敗']);
    exit();
}

$stmt = $link->prepare("SELECT Email FROM UserAccount WHERE User_Name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'email' => $row['Email']]);
} else {
    echo json_encode(['success' => false, 'message' => '找不到對應帳號']);
}
exit();