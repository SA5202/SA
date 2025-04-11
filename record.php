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
    SELECT s.title, s.description, s.updated_at, s.upvoted_amount,
           f.facility_type, b.building_name
    FROM suggestion s
    JOIN facility f ON s.facility_id = f.facility_id
    JOIN building b ON s.building_id = b.building_id
    WHERE s.User_ID = ?
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>捐款報表 | 輔仁大學愛校建言捐款系統</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>

    <style>
        body {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            line-height: 1.8;
            background-color: transparent;
            overflow-x: hidden;
            color: #333;
        }

        h3 {
            margin-bottom: 25px;
            font-weight: bold;
        }

        .table-responsive {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
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
            padding: 8px 18px;
            font-size: 1rem;
            color: rgb(123, 163, 23);
            border: 2px solid rgb(123, 163, 23);
            border-radius: 30px;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
            font-weight: 500;
        }

        .custom-btn i {
            margin-right: 6px;
        }

        .custom-btn:hover {
            background-color: rgb(123, 163, 23);
            color: white;
        }

        .table-container {
            background-color: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .badge {
            padding: 0.6em 1em;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <h3><i class="fas fa-user"></i> 個人資訊</h3>
    <div class="table-responsive">
        <table>
            <tbody>
                <?php
                $current_User_Name = $_SESSION['User_Name'];

                $link = mysqli_connect('localhost', 'root', '', 'SA');
                if (!$link) {
                    die("資料庫連線失敗：" . mysqli_connect_error());
                }

                $sql = "SELECT * FROM useraccount WHERE User_Name = '$current_User_Name'";
                $result_user = mysqli_query($link, $sql);

                if ($row = mysqli_fetch_assoc($result_user)) {
                    $password = htmlspecialchars($row['Password']);

                    echo "<tr>
                        <td rowspan='5'>
                            <img src='https://th.bing.com/th/id/OIP.sL-PTY6gaFaZu6VVwZgqaQHaHQ?w=178&h=180&c=7&r=0&o=5&dpr=1.5&pid=1.7' style='border-radius: 5%;'>
                        </td>
                        <td class='left'>帳號：{$row['User_Name']}</td>
                      </tr>
                      <tr>
                        <td class='left'>使用者編號：{$row['User_ID']}</td>
                      </tr>
                      <tr>
                        <td class='left'>Email：{$row['Email']}</td>
                      </tr>
                      <tr>
                        <td class='left'>
                            密碼：
                            <span id='password' style='font-weight: bold;'>••••••</span>
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

                mysqli_close($link);
                ?>
            </tbody>
        </table>
    </div>

    <br>
    <h3><i class="fas fa-donate"></i> 我的建言紀錄</h3>
    <div class="container">
        <div class="table-container">
            <table class="table table-bordered table-striped align-middle text-center shadow-sm rounded">
                <thead class="table-primary">
                    <tr>
                        <th class="fw-bold">標題</th>
                        <th class="fw-bold">設施</th>
                        <th class="fw-bold">建築物</th>
                        <th class="fw-bold">內容</th>
                        <th class="fw-bold">發佈時間</th>
                        <th class="fw-bold">按讚數</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars($row['facility_type']) ?></td>
                                <td><?= htmlspecialchars($row['building_name']) ?></td>
                                <td class="text-start"><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                                <td><?= date('Y-m-d H:i', strtotime($row['updated_at'])) ?></td>
                                <td>
                                    <span class="badge bg-success fs-6"><?= htmlspecialchars($row['upvoted_amount']) ?> 👍</span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">尚未有建言紀錄。</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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

