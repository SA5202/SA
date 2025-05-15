<?php
session_start();
$method = $_GET['method'] ?? null;
$link = mysqli_connect('localhost', 'root', '', 'SA');
if (!$link) {
    die("資料庫連線失敗：" . mysqli_connect_error());
}

if ($method === 'update_avatar') {
    if (isset($_POST['Nickname'], $_POST['Old_User_Name'])) {
        $Nickname = mysqli_real_escape_string($link, $_POST['Nickname']);
        $Old_User_Name = mysqli_real_escape_string($link, $_POST['Old_User_Name']);
        $Password = $_POST['Password']; // 可空

        $avatarPath = null;

        // 處理圖片上傳
        if (isset($_FILES['Avatar']) && $_FILES['Avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $tmpName = $_FILES['Avatar']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['Avatar']['name'], PATHINFO_EXTENSION)); // 小寫副檔名
            $newFileName = uniqid('avatar_') . '.' . $ext;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpName, $destination)) {
                $avatarPath = $destination; // 相對於網站根目錄的路徑
            } else {
                die("頭像上傳失敗");
            }
        }

        $sql = "UPDATE useraccount SET Nickname = '$Nickname'";
        if (!empty($Password)) {
            $Password = mysqli_real_escape_string($link, $Password);
            $sql .= ", Password = '$Password'";
        }

        if ($avatarPath !== null) {
            $avatarPathEscaped = mysqli_real_escape_string($link, $avatarPath);
            $sql .= ", Avatar = '$avatarPathEscaped'";
        }
        $sql .= " WHERE User_Name = '$Old_User_Name'";

        if (mysqli_query($link, $sql)) {
            $_SESSION['Nickname'] = $Nickname;
            if ($avatarPath !== null) {
                $_SESSION['Avatar'] = $avatarPath; // 存完整相對路徑，不要用 basename()
            }
            echo "<script>alert('更新成功'); window.location.href='record.php';</script>";
        } else {
            die("更新失敗: " . mysqli_error($link));
        }
    }
}

mysqli_close($link);
