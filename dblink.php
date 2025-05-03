<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>帳號處理</title>
</head>

<body>
<?php
session_start();
$method = $_GET['method'] ?? null;

// 連接資料庫
$link = mysqli_connect('localhost', 'root', '', 'SA');
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($method == 'update') {
    if (isset($_POST['User_Name'], $_POST['Email'], $_POST['Password'], $_POST['Old_User_Name'])) {
        $New_User_Name = mysqli_real_escape_string($link, $_POST['User_Name']);
        $Email = mysqli_real_escape_string($link, $_POST['Email']);
        $Password = mysqli_real_escape_string($link, $_POST['Password']);
        $Old_User_Name = mysqli_real_escape_string($link, $_POST['Old_User_Name']);

        $sql = "UPDATE useraccount SET 
                    User_Name = '$New_User_Name',
                    Password = '$Password',
                    Email = '$Email'
                WHERE User_Name = '$Old_User_Name'";

        if (mysqli_query($link, $sql)) {
            // 查詢更新後的 User_ID
            $query = "SELECT User_ID FROM useraccount WHERE User_Name = '$New_User_Name'";
            $result = mysqli_query($link, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                $User_ID = $row['User_ID'];
                echo "更新成功";

                // 根據身分跳轉
                if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
                    echo "<meta http-equiv='refresh' content='1;url=user_profile.php?id=$User_ID'>";
                } else {
                    echo "<meta http-equiv='refresh' content='1;url=record.php'>";
                }

            } else {
                echo "更新成功但找不到使用者 ID。";
            }

        } else {
            echo "錯誤: " . mysqli_error($link);
        }

    } else {
        echo "缺少必要欄位。";
    }
}

// 刪除操作
elseif ($method == 'delete') {
    if (isset($_GET['User_Name'])) {
        $User_Name = mysqli_real_escape_string($link, $_GET['User_Name']);
        $sql = "DELETE FROM useraccount WHERE User_Name='$User_Name'";

        if (mysqli_query($link, $sql)) {
            echo "刪除成功", "<br>";
        } else {
            echo "刪除失敗", "<br>";
        }
    } else {
        echo "缺少使用者名稱。";
    }
}

mysqli_close($link);
?>
</body>

</html>