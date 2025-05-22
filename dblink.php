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
            $ext = strtolower(pathinfo($_FILES['Avatar']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid('avatar_') . '.' . $ext;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpName, $destination)) {
                $avatarPath = $destination;
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
                $_SESSION['Avatar'] = $avatarPath;
            }

            // ✅ 根據使用者身分決定跳轉頁面
            $redirectPage = (isset($_SESSION['User_Name']) && $_SESSION['User_Name'] === 'admin') ? 'record_admin.php' : 'record.php';

            echo "<script>
                alert('更新成功');
                if (window.top && window.top.document) {
                    const avatarImg = window.top.document.querySelector('.avatar');
                    if (avatarImg) {
                        avatarImg.src = '" . $avatarPath . "?t=' + new Date().getTime();
                    }
                }
                window.location.href='" . $redirectPage . "';
            </script>";
            exit;
        } else {
            die("更新失敗: " . mysqli_error($link));
        }
    }
}

mysqli_close($link);
