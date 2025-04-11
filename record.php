<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>捐款報表 | 輔仁大學愛校建言捐款系統</title>

    <!-- 樣式與字體 -->
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

        table {
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            text-align: center;
            vertical-align: middle;
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
            color:rgb(123, 163, 23);
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
    </style>
</head>

<body>

    <h3><i class="fas fa-user"></i> 個人資訊</h3>
    <div class="table-responsive">
        <table>
            <tbody>
                <?php
                if (isset($_SESSION['User_Name'])) {
                    $current_User_Name = $_SESSION['User_Name']; // 取得當前使用者的 username

                    // 連接資料庫
                    $link = mysqli_connect('localhost', 'root', '', 'SA');
                    if (!$link) {
                        die("資料庫連線失敗：" . mysqli_connect_error());
                    }

                    // 查詢當前使用者
                    $sql = "SELECT * FROM useraccount WHERE User_Name = '$current_User_Name'";
                    $result = mysqli_query($link, $sql);

                    if ($row = mysqli_fetch_assoc($result)) {
                        $password = htmlspecialchars($row['Password']); // 防止 XSS 攻擊

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
</tr>

                          ";
                    } else {
                        echo "<tr><td colspan='2' align='center'>找不到使用者資料</td></tr>";
                    }
                    mysqli_close($link); // 關閉資料庫連線
                } else {
                    echo "<tr><td colspan='2' align='center'>未登入，請先登入</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <br>
    <h3><i class="fas fa-donate"></i> 我的捐款紀錄</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>項目名稱</th>
                    <th>捐款人數</th>
                    <th>已募金額</th>
                    <th>目標金額</th>
                    <th>達成率</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>圖書館翻新</td>
                    <td>58</td>
                    <td>NT$320,000</td>
                    <td>NT$500,000</td>
                    <td>64%</td>
                </tr>
                <tr>
                    <td>校園綠化</td>
                    <td>42</td>
                    <td>NT$85,000</td>
                    <td>NT$200,000</td>
                    <td>42.5%</td>
                </tr>
                <tr>
                    <td>學生餐廳改善</td>
                    <td>33</td>
                    <td>NT$115,000</td>
                    <td>NT$300,000</td>
                    <td>38.3%</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
<!-- 引入 Font Awesome 圖標 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<!-- JavaScript 控制眼睛按鈕 -->
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

</html>