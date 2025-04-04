<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta http-equiv="refresh" content="1;url=使用者設定.php">
</head>

<body>
    <?php
    $method = $_GET['method'] ?? null;

    // 連接資料庫
    $link = mysqli_connect('localhost', 'root', '', 'SA');

    if (!$link) {
        die("Connection failed: " . mysqli_connect_error());
    }
    // 原來的帳號修改和刪除處理
    elseif ($method == 'delete') {
        $username = $_GET['username'];
        $link = mysqli_connect('localhost', '', '', 'SA');
        $sql = "DELETE FROM useraccount WHERE username='$username'";

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