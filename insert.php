<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    // 取得表單提交的資料
    $User_Name = $_POST['User_Name'];
    $Email = $_POST['Email'];
    $Password = $_POST['Password'];
    $Nickname = empty($_POST['Nickname']) ? $User_Name : $_POST['Nickname']; // 如果沒有填暱稱，預設為帳號

    // 1. 連接到資料庫
    $link = mysqli_connect('localhost', 'root', '', 'SA');
    if (!$link) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // 檢查電子郵件是否已經存在
    $checkEmailQuery = "SELECT * FROM useraccount WHERE Email = ?";
    $stmt = mysqli_prepare($link, $checkEmailQuery);
    mysqli_stmt_bind_param($stmt, 's', $Email); // 綁定變數
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // 如果資料庫中有該電子郵件，顯示錯誤訊息
    if (mysqli_num_rows($result) > 0) {
        echo "這個電子郵件已經註冊過，請使用其他電子郵件。", "<br>";
        echo "<meta http-equiv='refresh' content='3;url=register.php'>";
    } else {
        // 2. 如果沒有重複的電子郵件，則插入新用戶
        // 不進行密碼加密處理，直接存儲原始密碼
        $insertQuery = "INSERT INTO useraccount(User_Name, Nickname, Email, Password, User_Type) 
                        VALUES(?, ?, ?, ?, 'user')";
        $stmt = mysqli_prepare($link, $insertQuery);
        mysqli_stmt_bind_param($stmt, 'ssss', $User_Name, $Nickname, $Email, $Password);

        if (mysqli_stmt_execute($stmt)) {
            echo "註冊成功", "<br>";
            echo "<meta http-equiv='refresh' content='2;url=login.php'>";
        } else {
            echo "註冊失敗", "<br>";
        }
    }

    // 關閉連接
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    ?>
</body>

</html>
