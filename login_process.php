<?php
session_start();
$username = $_POST['username'];
$password = $_POST['password'];
/*$link=mysqli_connect("localhost","root","12345678","users");
$sql="select * from accounts "; //where username='$username' and password ='$password'
$result=mysqli_query($link, $sql);*/


if (isset($_POST['username']) && isset($_POST['password'])) {

    // 假設 admin 是管理員帳號，密碼為 admin_password
    if ($username == "admin" && $password == "admin") {
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = true; // 設置為管理員
        // 登入成功，刷新父頁面並關閉當前 iframe 頁面
        echo '<script>
                parent.location.reload();
              </script>';
        exit();
    }
    // 查詢資料庫，找出用戶資料

    // 普通用戶登入
    elseif ($username == "root" && $password == "password") {
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = false; // 非管理員
        // 登入成功，刷新父頁面並關閉當前 iframe 頁面
        echo '<script>
                parent.location.reload();
              </script>';
        exit();
    } else {
        echo "登入失敗";
    }
} else {
    echo "請填寫帳號和密碼";
}
