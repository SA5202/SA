<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改密碼</title>
    <style>
        body {
            font-family: "Noto Serif TC", serif;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 40px;
        }

        .card {
            max-width: 500px;
            background-color: #fff;
            margin: 40px auto;
            padding: 30px 40px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            background-color: rgb(126, 189, 84);
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            font-size: 16px;
            font-family: "Noto Serif TC", serif;
        }

        input[type="reset"] {
            background-color: rgb(189, 84, 76);
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

    $link = mysqli_connect('localhost', 'root', '', 'SA');
    if (!$link) {
        die("資料庫連線失敗：" . mysqli_connect_error());
    }

    // 管理員查看他人資料，否則是自己
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true && isset($_GET['User_Name'])) {
        $User_Name = $_GET['User_Name'];
    } else {
        $User_Name = $_SESSION['User_Name'];
    }

    $sql = "SELECT * FROM useraccount WHERE User_Name = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("s", $User_Name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $Email = $row['Email'];
    } else {
        die("找不到使用者資料！");
    }

    $stmt->close();
    $link->close();
    ?>

    <h2>修改密碼</h2>
    <div class="card">
        <form action="dblink.php?method=update" method="post">
            <table>
                <tr>
                    <td>帳號</td>
                    <td>
                        <input type="text" name="User_Name" value="<?= htmlspecialchars($User_Name) ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>
                        <input type="email" name="Email" value="<?= htmlspecialchars($Email) ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>新密碼</td>
                    <td>
                        <input type="password" name="Password" required>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="button-row">
                        <input type="hidden" name="Old_User_Name" value="<?= htmlspecialchars($User_Name) ?>">
                        <input type="submit" value="更新密碼">
                        <input type="reset" value="重設">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>

</html>