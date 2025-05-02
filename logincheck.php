<?php
session_start();
$User_Name = $_POST["User_Name"];
$Password = $_POST["Password"];

$link = mysqli_connect("localhost", "root", "", "SA");
// 查詢使用者資料
$sql = "SELECT * FROM useraccount WHERE User_Name='$User_Name' AND Password='$Password'";
$result = mysqli_query($link, $sql);
if ($record = mysqli_fetch_assoc($result)) {
    $_SESSION['User_Name'] = $record['User_Name'];
    $_SESSION['User_ID'] = $record['User_ID'];
    $_SESSION['User_Type'] = $record['User_Type'];

    // 根據使用者類型判斷是否為管理員
    $_SESSION['is_admin'] = ($record['User_Type'] === 'admin');
    // 登入成功，刷新父頁面
    echo '<script>
            parent.location.reload();
          </script>';
    exit();
} else {
    // 登入失敗，導向失敗頁面
    header("Location: login_failed.php");
    exit();
}
?>

