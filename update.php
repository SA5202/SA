<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改帳號資訊</title>
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['User_Name'])) {
        die("請先登入！");
    }

    $User_Name = $_SESSION['User_Name']; // 取得當前登入的使用者名稱

    // 連接資料庫
    $link = mysqli_connect('localhost', 'root', '', 'SA');
    if (!$link) {
        die("資料庫連線失敗：" . mysqli_connect_error());
    }

    // 查詢使用者資訊
    $sql = "SELECT * FROM useraccount WHERE User_Name='$User_Name'";
    $result = mysqli_query($link, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        $Email = $row['Email']; // 取出 Email
    } else {
        die("找不到使用者資料！");
    }
    mysqli_close($link);
    ?>

    <h2 align="center">修改帳號資訊</h2>
    <form action="dblink.php?method=update" method="post">
        <table class="RedList" width="500" align="center">
            <tr align="center">
                <td>帳號</td>
                <td><input type="text" name="User_Name" value="<?php echo $User_Name; ?>" readonly></td>
            </tr>
            <tr align="center">
                <td>Email</td>
                <td><input type="email" name="Email" value="<?php echo $Email; ?>" required></td>
            </tr>
            <tr align="center">
                <td>新密碼</td>
                <td>
                    <input type="password" id="Password" name="Password" required>
                </td>
            </tr>
            <tr align="center">
                <td colspan="2">
                    <input type="submit" value="更新資料">
                    <input type="reset" value="重設">
                </td>
            </tr>
        </table>
    </form>
</body>

</html>