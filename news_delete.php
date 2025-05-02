<?php
session_start();

// 資料庫連接
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SA";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}

$news_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($news_id <= 0) {
    die("無效的公告 ID");
}

// 檢查公告是否存在
$check_sql = "SELECT News_ID FROM News WHERE News_ID = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $news_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows === 0) {
    $check_stmt->close();
    die("找不到該公告，無法刪除。");
}
$check_stmt->close();

// 執行刪除
$delete_sql = "DELETE FROM News WHERE News_ID = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $news_id);

if ($delete_stmt->execute()) {
    $delete_stmt->close();
    $conn->close();
    
    // 設置刪除成功的訊息
    $_SESSION['success_message'] = "公告已成功刪除！";
    
    // 確保 session 被寫入
    session_write_close();
    
    // 重定向回公告頁面
    header("Location: news.php");
    exit();
} else {
    $delete_stmt->close();
    $conn->close();
    die("刪除公告失敗，請稍後再試。");
}
?>