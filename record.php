<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

$userID = $_SESSION['User_ID'];
$sql = "
SELECT s.Suggestion_ID, s.Title, s.Description, s.Updated_At, u.User_Name,
       f.Facility_Type, b.Building_Name,
       (SELECT COUNT(*) FROM Upvote u2 WHERE u2.Suggestion_ID = s.Suggestion_ID AND u2.Is_Upvoted = 1) AS LikeCount,
       (SELECT Status FROM Progress p WHERE p.Suggestion_ID = s.Suggestion_ID ORDER BY Updated_At DESC LIMIT 1) AS LatestStatus
FROM Suggestion s
JOIN Facility f ON s.Facility_ID = f.Facility_ID
JOIN Building b ON s.Building_ID = b.Building_ID
JOIN Useraccount u ON s.User_ID = u.User_ID
WHERE s.User_ID = ?
ORDER BY s.Updated_At DESC
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>個人檔案 | 輔仁大學愛校建言捐款系統</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>

    <style>
        /* 你的 CSS 原樣保留 */
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

        .icon {
            font-size: 1.5rem;
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 10px;
            display: inline-block;
        }

        h3 {
            margin: 20px 0;
            font-weight: bold;
        }

        .table-responsive {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px 20px;
            border-radius: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
        }

        .table th {
            background-color: #f1f3f5;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .left {
            text-align: left;
        }

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

        .table-container {
            background-color: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
        }

        .table {
            width: 100%;
            border-radius: 15px;
            border: 2px solid #ddd;
            border-collapse: separate;
            overflow: hidden;
            border-spacing: 0;
        }

        .table th,
        .table td {
            padding: 10px 30px;
            border-radius: 0px;
        }

        .table-primary {
            background-color: #e9f5ff;
        }

        .pretty-btn {
            background: linear-gradient(to right, #84c684, #6fb36f);
            border: none;
            color: white;
            padding: 4px 20px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 750;
            transition: background 0.3s ease;
        }

        .pretty-btn:hover {
            background: linear-gradient(to right, #84c684, #6fb36f, 0.5);
            box-shadow: 0 0 10px rgba(111, 179, 111, 0.4);
            transform: translateY(-2px);
        }

        .pretty-btn i {
            margin-right: 6px;
        }

        .badge {
            padding: 0.3em 1em;
            font-size: 0.5rem;
        }
    </style>
</head>

<body>

    <h3><i class="icon fas fa-user"></i> 帳戶基本資訊</h3>
    <div class="table-responsive">
        <table>
            <tbody>
                <?php
                $current_User_Name = $_SESSION['User_Name'];

                $link_user = mysqli_connect('localhost', 'root', '', 'sa');
                if (!$link_user) {
                    die("資料庫連線失敗：" . mysqli_connect_error());
                }

                $sql_user = "SELECT * FROM useraccount WHERE User_Name = ?";
                $stmt_user = $link_user->prepare($sql_user);
                $stmt_user->bind_param("s", $current_User_Name);
                $stmt_user->execute();
                $result_user = $stmt_user->get_result();

                if ($row = $result_user->fetch_assoc()) {
                    $password = htmlspecialchars($row['Password']);

                    echo "
                    <tr>
                        <td rowspan='5'>
                            <img src='https://th.bing.com/th/id/OIP.sL-PTY6gaFaZu6VVwZgqaQHaHQ?w=178&h=180&c=7&r=0&o=5&dpr=1.5&pid=1.7'  style='border-radius: 30px; width: 250px; height: 250px; margin: 10px 40px;'>
                        </td>
                        <td class='left'>用戶名稱： {$row['User_Name']}</td>
                    </tr>
                    <tr>
                        <td class='left'>使用者ID： 0000000000{$row['User_ID']}</td>
                    </tr>
                    <tr>
                        <td class='left'>Email： {$row['Email']}</td>
                    </tr>
                    <tr>
                        <td class='left'>
                            密碼：
                            <span id='password' style='font-weight: bold;'>••••••••••</span>
                            <button id='togglePassword' onclick='togglePassword()' style='border: none; background: none; cursor: pointer;'>
                                <i id='eyeIcon' class='fa fa-eye'></i>
                            </button>
                            <span id='realPassword' style='display: none;'>{$password}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' class='left'>
                            <a href='update.php?method=update&User_Name={$row['User_Name']}' class='custom-btn'>
                                <i class='fas fa-pen-to-square'></i> 修改資料
                            </a>
                        </td>
                    </tr>";
                } else {
                    echo "<tr><td colspan='2' align='center'>找不到使用者資料</td></tr>";
                }

                mysqli_close($link_user);
                ?>
            </tbody>
        </table>
    </div>

    <br>
    <h3><i class="icon fas fa-clipboard-list"></i> 我的建言紀錄</h3>
    <div class="table-container">
        <table class="table">
            <thead class="table-primary">
                <tr>
                    <th class="fw-bold">建言標題</th>
                    <th class="fw-bold">發佈時間</th>
                    <th class="fw-bold">按讚數</th>
                    <th class="fw-bold">編輯建言</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><b><?= htmlspecialchars($row['Title']) ?></b></td>
                            <td><?= date('Y-m-d', strtotime($row['Updated_At'])) ?></td>
                            <td>
                                <span class="badge bg-success fs-6">
                                    <?= $row['LikeCount'] ?> ❤️
                                </span>
                            </td>
                            <td>
                                <a href="#update_suggestion.php?Suggestion_ID=<?= $row['Suggestion_ID'] ?>" class="pretty-btn">
                                    <i class="fas fa-pen-to-square"></i> 修改
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">尚未有建言紀錄。</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <br>
    <h3><i class="icon fas fa-medal"></i> 我的榮譽等級</h3>

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