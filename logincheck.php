<?php
session_start();
$username = $_POST["username"];
$password = $_POST["password"];
$link = mysqli_connect("localhost", "root", "12345678", "users");
$sql = "select * from accounts where username='$username' and password ='$password'";
$result = mysqli_query($link, $sql);

if ($username == "admin") {
    $_SESSION['username'] = $username;
    $_SESSION['is_admin'] = true; // 設置為管理員
    // 登入成功，刷新父頁面並關閉當前 iframe 頁面
    echo '<script>
            parent.location.reload();
          </script>';
    exit();
}
if ($record = mysqli_fetch_assoc($result)) {
    $_SESSION['username'] = $record['username'];
    $_SESSION['level'] = $record['level'];
    $_SESSION['is_admin'] = false; // 非管理員
    echo '<script>
                parent.location.reload();
        </script>';
    exit();
} else {
    echo "登入失敗";
}
