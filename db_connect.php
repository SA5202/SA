<?php
// db_connect.php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'SA'; // 這是你目前在用的資料庫名稱

$link = new mysqli($host, $username, $password, $database);

// 檢查連線是否成功
if ($link->connect_error) {
    die("資料庫連線失敗: " . $link->connect_error);
}
