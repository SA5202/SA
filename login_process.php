<?php
session_start();

$User_Name = $_POST['User_Name'] ?? null;
$Password = $_POST['Password'] ?? null;

if ($User_Name && $Password) {
    $link = new mysqli('localhost', 'root', '', 'SA');
    if ($link->connect_error) {
        die("資料庫連線失敗: " . $link->connect_error);
    }

    // 查詢使用者
    $stmt = $link->prepare("SELECT * FROM useraccount WHERE User_Name = ? AND Password = ?");
    $stmt->bind_param("ss", $User_Name, $Password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['User_Name'] = $user['User_Name'];
        $_SESSION['Nickname'] = $user['Nickname'] ?? $user['User_Name'];
        $_SESSION['Avatar'] = !empty($user['Avatar']) ? $user['Avatar'] : null;

        // ✅ 登入成功後刷新父頁面
        echo '<script>parent.location.reload();</script>';
        exit();
    } else {
        echo "登入失敗，帳號或密碼錯誤";
    }

    $stmt->close();
    $link->close();
} else {
    echo "請填寫帳號與密碼";
}
