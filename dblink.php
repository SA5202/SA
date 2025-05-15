<?php
session_start();
$method = $_GET['method'] ?? null;

// 連接資料庫
$link = mysqli_connect('localhost', 'root', '', 'SA');
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

// === 同時處理暱稱與密碼 ===
if ($method === 'update') {
    if (isset($_POST['Nickname'], $_POST['Old_User_Name'])) {
        $Nickname = mysqli_real_escape_string($link, $_POST['Nickname']);
        $Old_User_Name = mysqli_real_escape_string($link, $_POST['Old_User_Name']);
        $Password = $_POST['Password']; // 可能為空

        // 更新 SQL 動態處理
        if (!empty($Password)) {
            $Password = mysqli_real_escape_string($link, $Password);
            $sql = "UPDATE useraccount SET Nickname = '$Nickname', Password = '$Password' WHERE User_Name = '$Old_User_Name'";
        } else {
            $sql = "UPDATE useraccount SET Nickname = '$Nickname' WHERE User_Name = '$Old_User_Name'";
        }

        if (mysqli_query($link, $sql)) {
            $_SESSION['Nickname'] = $Nickname; // 同步更新 session

            $query = "SELECT User_ID FROM useraccount WHERE User_Name = '$Old_User_Name'";
            $result = mysqli_query($link, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                $User_ID = $row['User_ID'];
                echo "<script>
                    alert('更新成功!!!');
                    if(window.parent) {
                        window.parent.location.reload();
                    }
                </script>";
            } else {
                echo "更新成功但找不到使用者 ID。";
            }
        } else {
            echo "更新失敗...: " . mysqli_error($link);
        }
    } else {
        echo "缺少必要欄位";
    }
}

mysqli_close($link);
