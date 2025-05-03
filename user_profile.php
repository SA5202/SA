<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 僅限管理員可使用此頁面
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

// 檢查 GET 傳入的使用者 ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "無效的使用者 ID";
    exit();
}
$userID = intval($_GET['id']);

// 查詢該使用者資料
$user_sql = "SELECT * FROM useraccount WHERE User_ID = ?";
$user_stmt = $link->prepare($user_sql);
$user_stmt->bind_param("i", $userID);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$row = $user_result->fetch_assoc();

if (!$row) {
    echo "找不到使用者資料";
    exit();
}

$password = htmlspecialchars($row['Password']);

// 查詢該使用者的建言紀錄
$sql = "
    SELECT s.title, s.description, s.updated_at, s.upvoted_amount,
           f.facility_type, b.building_name
    FROM suggestion s
    JOIN facility f ON s.facility_id = f.facility_id
    JOIN building b ON s.building_id = b.building_id
    WHERE s.user_id = ?
    ORDER BY s.updated_at DESC
";

$stmt = $link->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>使用者個人檔案</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <style>
        body {
            max-width: 85%;
            margin: 0 auto;
            padding: 30px;
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            font-family: "Noto Serif TC", serif;
            line-height: 1.8;
            background-color: transparent;
            overflow-x: hidden;
            color: #333;
        }

        .icon { font-size: 1.5rem; width: 1.5rem; height: 1.5rem; margin-right: 10px; display: inline-block; }

        h3 { margin: 20px 0; font-weight: bold; }

        .table-responsive, .table-container {
            background-color: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #ddd;
        }

        .table th { background-color: #f1f3f5; }

        .table-striped tbody tr:nth-of-type(odd) { background-color: #f9f9f9; }

        .left { text-align: left; }

        .custom-btn {
            display: inline-block;
            padding: 4px 40px;
            font-size: 1rem;
            font-weight: 750;
            background: linear-gradient(to right, #84c684, #6fb36f);
            color: #fff;
            border-radius: 15px;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .custom-btn:hover {
            background: linear-gradient(to right, #84c684, #6fb36f, 0.5);
            box-shadow: 0 0 10px rgba(111, 179, 111, 0.4);
            transform: translateY(-2px);
        }

        .pretty-btn {
            background: linear-gradient(to right, #84c684, #6fb36f);
            border: none;
            color: white;
            padding: 4px 20px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 750;
        }

        .pretty-btn:hover {
            background: linear-gradient(to right, #84c684, #6fb36f, 0.5);
            box-shadow: 0 0 10px rgba(111, 179, 111, 0.4);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<h3><i class="icon fas fa-user"></i> 使用者基本資訊</h3>
<div class="table-responsive">
    <table>
        <tbody>
            <tr>
                <td rowspan='5'>
                    <img src='https://th.bing.com/th/id/OIP.sL-PTY6gaFaZu6VVwZgqaQHaHQ?w=178&h=180&c=7&r=0&o=5&dpr=1.5&pid=1.7' style='border-radius: 30px; width: 250px; height: 250px; margin: 10px 40px;'>
                </td>
                <td class='left'>用戶名稱： <?= htmlspecialchars($row['User_Name']) ?></td>
            </tr>
            <tr>
                <td class='left'>使用者ID： <?= str_pad($row['User_ID'], 10, '0', STR_PAD_LEFT) ?></td>
            </tr>
            <tr>
                <td class='left'>Email： <?= htmlspecialchars($row['Email']) ?></td>
            </tr>
            <tr>
                <td class='left'>
                    密碼：
                    <span id='password' style='font-weight: bold;'>••••••••••</span>
                    <button id='togglePassword' onclick='togglePassword()' style='border: none; background: none; cursor: pointer;'>
                        <i id='eyeIcon' class='fa fa-eye'></i>
                    </button>
                    <span id='realPassword' style='display: none;'><?= $password ?></span>
                </td>
            </tr>
            <tr>
                <td colspan='2' class='left'>
                    <a href='update.php?method=update&User_Name=<?= htmlspecialchars($row['User_Name']) ?>' class='custom-btn'>
                        <i class='fas fa-pen-to-square'></i> 修改資料
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<br>
<h3><i class="icon fas fa-clipboard-list"></i> 該使用者的建言紀錄</h3>
<div class="table-container">
    <table class="table table-striped">
        <thead class="table-primary">
            <tr>
                <th>建言標題</th>
                <th>發佈時間</th>
                <th>按讚數</th>
                <th>編輯建言</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($r = $result->fetch_assoc()): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($r['title']) ?></b></td>
                        <td><?= date('Y-m-d', strtotime($r['updated_at'])) ?></td>
                        <td><span class="badge bg-success fs-6"><?= htmlspecialchars($r['upvoted_amount']) ?> ❤️</span></td>
                        <td><button class="pretty-btn" disabled><i class="fas fa-pen-to-square"></i> 修改</button></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center text-muted">尚未有建言紀錄。</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function togglePassword() {
        let passwordField = document.getElementById("password");
        let realPasswordField = document.getElementById("realPassword");
        let eyeIcon = document.getElementById("eyeIcon");

        if (passwordField.style.display === "none") {
            passwordField.style.display = "inline";
            realPasswordField.style.display = "none";
            eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
        } else {
            passwordField.style.display = "none";
            realPasswordField.style.display = "inline";
            eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
        }
    }
</script>

</body>
</html>