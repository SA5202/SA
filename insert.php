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

    //step1
    $link = mysqli_connect('localhost', 'root', '', 'SA');
    //step3
    $sql = "insert into useraccount(User_Name,Email,Password,User_Type) values('$User_Name','$Email','$Password','user')";
    if (mysqli_query($link, $sql)) {
        echo "新增成功", "<br>";
    } else {
        echo "新增失敗", "<br>";
    }
    ?>
</body>

</html>