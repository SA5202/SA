<?php
session_start();
$User_Name = $_POST['User_Name'];
$Password = $_POST['Password'];


if (isset($_POST['User_Name']) && isset($_POST['Password'])) {

    // 假設 admin 是管理員帳號，密碼為 admin_password
    if ($User_Name == "admin" && $Password == "admin_password") {
        $_SESSION['User_Name'] = $User_Name;
        $_SESSION['is_admin'] = true; // 設置為管理員
        // 登入成功，刷新父頁面並關閉當前 iframe 頁面
        echo '<script>
                parent.location.reload();
              </script>';
        exit();
    }
    // 查詢資料庫，找出用戶資料

    // 普通用戶登入
    elseif ($User_Name == "root" && $Password == "password") {
        $_SESSION['User_Name'] = $User_Name;
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
