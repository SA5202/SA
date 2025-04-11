<?php
session_start();
$User_Name = $_POST["User_Name"];
$Password = $_POST["Password"];
$link = mysqli_connect("localhost", "root", "", "SA");
$sql = "select * from useraccount where User_Name='$User_Name' and Password ='$Password'";
$result = mysqli_query($link, $sql);

if ($User_Name == "admin") {
    $_SESSION['User_Name'] = $User_Name;
    $_SESSION['is_admin'] = true; // 設置為管理員
    // 登入成功，刷新父頁面並關閉當前 iframe 頁面
    echo '<script>
            parent.location.reload();
          </script>';
    exit();
}
if ($record = mysqli_fetch_assoc($result)) {
    $_SESSION['User_Name'] = $record['User_Name'];
    $_SESSION['User_Type'] = $record['User_Type'];
    $_SESSION['User_ID'] = $record['User_ID'];
    $_SESSION['is_admin'] = false; // 非管理員
    echo '<script>
                parent.location.reload();
        </script>';
    exit();
} else {
    echo "登入失敗";
}
