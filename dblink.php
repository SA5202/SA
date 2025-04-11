<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta http-equiv="refresh" content="1;url=record.php">
</head>

<body>
    <?php
    $method = $_GET['method'] ?? null;

    // 連接資料庫
    $link = mysqli_connect('localhost', 'root', '', 'SA');

    if (!$link) {
        die("Connection failed: " . mysqli_connect_error());
    }
    if ($method == 'update') {
        // 更新操作
        if (isset($_POST['User_Name'], $_POST['Email'], $_POST['Password'])) {
            // 確保所有資料都已經提交
            $User_Name = mysqli_real_escape_string($link, $_POST['User_Name']);
            $Email = mysqli_real_escape_string($link, $_POST['Email']);
            $Password = mysqli_real_escape_string($link, $_POST['Password']);

            // 執行更新 SQL 查詢
            $sql = "UPDATE useraccount SET 
                        Password='$Password', Email='$Email'
                        WHERE User_Name='$User_Name'";

            if (mysqli_query($link, $sql)) {
                echo "更新成功";
            } else {
                echo "錯誤: " . mysqli_error($link);
            }
        }
    }
    // 原來的帳號修改和刪除處理
    elseif ($method == 'delete') {
        $User_Name = $_GET['User_Name'];
        $link = mysqli_connect('localhost', 'root', '', 'SA');
        $sql = "DELETE FROM useraccount WHERE User_Name='$User_Name'";

        if (mysqli_query($link, $sql)) {
            echo "刪除成功", "<br>";
        } else {
            echo "刪除失敗", "<br>";
        }


        // 關閉資料庫連接
        mysqli_close($link);
    }
    ?>
</body>

</html>