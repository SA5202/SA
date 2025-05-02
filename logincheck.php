<?php
session_start();
$User_Name = $_POST["User_Name"];
$Password = $_POST["Password"];

$link = mysqli_connect("localhost", "root", "", "SA");
if (!$link) {
    die("連線失敗: " . mysqli_connect_error());
}

// 使用 prepared statement 防止 SQL injection
$sql = "SELECT * FROM useraccount WHERE User_Name = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $User_Name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($record = mysqli_fetch_assoc($result)) {
    // 驗證密碼
    if (password_verify($Password, $record['Password'])) {
        $_SESSION['User_Name'] = $record['User_Name'];
        $_SESSION['User_ID'] = $record['User_ID'];
        $_SESSION['User_Type'] = $record['User_Type'];
        $_SESSION['is_admin'] = ($record['User_Type'] === 'admin');

        // 登入成功，刷新父頁面
        echo '<script>parent.location.reload();</script>';
        exit();
    } else {
        // 密碼錯誤
        header("Location: login_failed.php");
        exit();
    }
} else {
    // 查無此帳號
    header("Location: login_failed.php");
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($link);
?>

