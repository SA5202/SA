<?php
session_start();

$User_Name = $_POST["User_Name"];
$Password = $_POST["Password"];

$link = mysqli_connect("localhost", "root", "", "SA");

$User_Name = mysqli_real_escape_string($link, $User_Name);
$Password = mysqli_real_escape_string($link, $Password);

$sql = "SELECT * FROM useraccount WHERE User_Name='$User_Name' AND Password='$Password'";
$result = mysqli_query($link, $sql);

if ($record = mysqli_fetch_assoc($result)) {
    $_SESSION['User_Name'] = $record['User_Name'];
    $_SESSION['User_ID'] = $record['User_ID'];
    $_SESSION['User_Type'] = $record['User_Type'];  // 原始角色名稱
    $_SESSION['Nickname'] = $record['Nickname'];

    $user_type = $record['User_Type'];

    if ($user_type === 'admin') {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_type'] = 'super';
    } elseif ($user_type === 'General Admin') {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_type'] = 'general';
    } elseif ($user_type === 'Department Admin') {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_type'] = 'department';
    } else {
        $_SESSION['is_admin'] = false;
        $_SESSION['admin_type'] = '';
    }

    echo '<script>parent.location.reload();</script>';
    exit();
} else {
    header("Location: login_failed.php");
    exit();
}
?>
