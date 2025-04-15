<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta http-equiv="refresh" content="2;url=login.php">
</head>

<body>
    <?php
    $User_Name = $_POST['User_Name'];
    $Email = $_POST['Email'];
    $Password = $_POST['Password'];

    // 1. 連接到資料庫
    $link = mysqli_connect('localhost', 'root', '', 'SA');

    // 檢查電子郵件是否已經存在
    $checkEmailQuery = "SELECT * FROM useraccount WHERE Email = '$Email'";
    $result = mysqli_query($link, $checkEmailQuery);

    // 如果資料庫中有該電子郵件，顯示錯誤訊息
    if (mysqli_num_rows($result) > 0) {
        echo "這個電子郵件已經註冊過，請使用其他電子郵件。", "<br>";
    } else {
        // 2. 如果沒有重複的電子郵件，則插入新用戶
        $sql = "INSERT INTO useraccount(User_Name, Email, Password, User_Type) VALUES('$User_Name', '$Email', '$Password', 'user')";
        if (mysqli_query($link, $sql)) {
            echo "註冊成功", "<br>";
        } else {
            echo "註冊失敗", "<br>";
        }
    }
    ?>
</body>

</html>
