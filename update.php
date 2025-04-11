<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改帳號資訊</title>
    <style>
        body {
            font-family: "Microsoft JhengHei", sans-serif;
            background-color: #f2f4f8;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 40px;
        }

        .form-container {
            max-width: 500px;
            background-color: #fff;
            margin: 40px auto;
            padding: 30px 40px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 12px 10px;
            font-size: 16px;
            color: #444;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[readonly] {
            background-color: #eee;
        }

        .button-row {
            text-align: center;
            padding-top: 10px;
        }

        input[type="submit"],
        input[type="reset"] {
            background-color:rgb(126, 189, 84);
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            font-size: 16px;
        }

        input[type="reset"] {
            background-color:rgb(189, 84, 76);
        }

        input[type="submit"]:hover,
        input[type="reset"]:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['User_Name'])) {
        die("請先登入！");
    }

    $User_Name = $_SESSION['User_Name'];

    $link = mysqli_connect('localhost', 'root', '', 'SA');
    if (!$link) {
        die("資料庫連線失敗：" . mysqli_connect_error());
    }

    $sql = "SELECT * FROM useraccount WHERE User_Name='$User_Name'";
    $result = mysqli_query($link, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        $Email = $row['Email'];
    } else {
        die("找不到使用者資料！");
    }
    mysqli_close($link);
    ?>

    <h2>修改帳號資訊</h2>
    <div class="form-container">
        <form action="dblink.php?method=update" method="post">
            <table>
                <tr>
                    <td>帳號</td>
                    <td><input type="text" name="User_Name" value="<?php echo $User_Name; ?>" readonly></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input type="email" name="Email" value="<?php echo $Email; ?>" required></td>
                </tr>
                <tr>
                    <td>新密碼</td>
                    <td><input type="password" id="Password" name="Password" required></td>
                </tr>
                <tr>
                    <td colspan="2" class="button-row">
                        <input type="submit" value="更新資料">
                        <input type="reset" value="重設">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>
