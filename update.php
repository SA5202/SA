<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    $User_Name = $_GET['User_Name'];
    //step1
    $link = mysqli_connect('localhost', 'root', '', 'SA');
    //step3
    $sql = "select distinct * from useraccount where User_Name='$User_Name'";
    $result = mysqli_query($link, $sql);
    //step4
    if ($row = mysqli_fetch_assoc($result)) {
        $Email = $row['Email'];
        $Password = $row['Password'];
    }
    ?>
    <form action="dblink.php" method="post">
        <table class="RedList" width="500" align="center">
            <tr align="center">
                <td>帳號</td>
                <td><input type="text" name="User_Name" value="<?php echo $User_Name ?>"required></td>
            </tr>
            <tr align="center">
                <td>Email</td>
                <td><input type="text" name="Email" value="<?php echo $Email ?>" required></td>
            </tr>
            <tr align="center">
                <td>密碼</td>
                <td><input type="text" name="Password" value="<?php echo $Password ?>" required></td>
            </tr>
            <tr align="center">
                <td colspan="2"><input type="submit"><input type="reset"></td>
            </tr>
        </table>
    </form>
</body>

</html>