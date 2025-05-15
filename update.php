<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改密碼及暱稱</title>
    <style>
        body {
            font-family: "Noto Serif TC", serif;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        .card {
            max-width: 600px;
            background-color: #fff;
            margin: 80px auto;
            padding: 15px 50px;
            border-radius: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 12px 10px;
            font-size: 16px;
            font-weight: bold;
            color: #444;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[readonly] {
            background-color: #eee;
        }

        input:focus {
            border-color: rgb(173, 231, 248);
            box-shadow: 0 0 8px rgba(70, 117, 141, 0.88);
        }

        .button-row {
            text-align: center;
            padding-top: 10px;
        }

        input[type="submit"],
        input[type="reset"] {
            background-color: #84c684;
            color: white;
            padding: 10px 40px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            margin: 15px 5px;
            font-size: 16px;
            font-weight: bold;
            font-family: "Noto Serif TC", serif;
        }

        input[type="reset"] {
            background-color: rgb(189, 84, 76);
        }

        input[type="submit"]:hover,
        input[type="reset"]:hover {
            opacity: 0.7;
        }

        .custom-file-btn {
            display: inline-block;
            background-color: #999;
            color: white;
            padding: 4px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
        }

        .custom-file-btn:hover {
            opacity: 0.7;
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
        $Nickname = $row['Nickname']; // 取得暱稱
    } else {
        die("找不到使用者資料！");
    }

    $stmt->close();
    $link->close();
    ?>

    <div class="card">
        <form action="dblink.php?method=update_avatar" method="post" enctype="multipart/form-data">
        <h2>編輯個人檔案</h2>
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
                    <td>暱稱</td>
                    <td>
                        <input type="text" name="Nickname" value="<?= htmlspecialchars($Nickname) ?>">
                    </td>
                </tr>
                <tr>
                    <td>新密碼</td>
                    <td>
                        <input type="password" name="Password">
                    </td>
                </tr>
                <tr>
                    <td>頭像圖片</td>
                    <td>
                        <input type="file" name="Avatar" id="avatarInput" accept="image/*" style="display: none;">
                        <label for="avatarInput" class="custom-file-btn">選擇圖片</label>
                        <span id="fileNameDisplay" style="margin-left:10px; color:#555;"></span>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="button-row">
                        <input type="hidden" name="Old_User_Name" value="<?= htmlspecialchars($User_Name) ?>">
                        <input type="submit" value="儲存變更">
                        <input type="reset" value="重設">
                    </td>
                </tr>
            </table>
        </form>

    </div>
</body>
<script>
    const input = document.getElementById('avatarInput');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    input.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileNameDisplay.textContent = this.files[0].name;
        } else {
            fileNameDisplay.textContent = '';
        }
    });
</script>

</html>