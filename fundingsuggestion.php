<?php
session_start();

// 檢查是否已登入
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");  // 重新導向到登入頁面
    exit();
}

// 用戶資訊
$User_Name = $_SESSION['User_Name'];
$User_Type = $_SESSION['User_Type'];
$admin_type = $_SESSION['admin_type']; // 用戶角色類型
$Nickname = $_SESSION['Nickname'];
$is_admin = $_SESSION['is_admin'];

// 資料庫連接
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SA";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}

$errorMessage = ""; // 初始化錯誤訊息

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $suggestion_id = $_POST['suggestion_id'];
    $required_amount = intval($_POST['required_amount']);  // 轉整數
    $status = "進行中";  // 固定設定為進行中

    // 插入募款建議
    $sql = "INSERT INTO FundingSuggestion (Suggestion_ID, Required_Amount, Raised_Amount, Status, Updated_At) 
            VALUES (?, ?, 0, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $suggestion_id, $required_amount, $status);

    if ($stmt->execute()) {
        header("Location: funding_detail.php");
        exit();
    } else {
        $errorMessage = "此建言找不到或已在募款中，請重新選擇。";
    }

    $stmt->close();
}

// 查詢尚未募款的建言列表
$sql = "
    SELECT s.Suggestion_ID, s.Title
    FROM Suggestion s
    WHERE NOT EXISTS (
        SELECT 1
        FROM FundingSuggestion f
        WHERE f.Suggestion_ID = s.Suggestion_ID
    )
";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>新增募款建言 | 輔仁大學愛校建言捐款系統</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background-color: transparent;
            font-family: "Noto Serif TC", serif;
        }

        .form-container {
            max-width: 60%;
            margin: 160px auto;
        }

        .form-card {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 50px;
            border-radius: 30px;
            box-shadow: 0 0px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
            border: 1px solid var(--bs-border-color-translucent);
        }

        .form-card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        label {
            color: #555;
            font-weight: bold;
        }

        .btn-block {
            width: 100%;
        }

        .alert {
            margin-top: 20px;
        }

        .btn-custom {
            background-color: rgb(136, 184, 209);
            padding: 10px 30px;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
            border-radius: 30px;
        }

        .btn-custom:hover {
            background-color: rgb(83, 127, 164);
        }

        footer {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container form-container">
        <div class="form-card">

            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form action="fundingsuggestion.php" method="POST" id="fundingForm">
                <div class="mb-3">
                    <label for="suggestion_id" class="form-label">選擇待募款建言：</label>
                    <select name="suggestion_id" id="suggestion_id" class="form-select" required>
                        <option value="">選擇建言</option>
                        <?php
                        // 根據用戶角色來查詢建議
                        if ($_SESSION['admin_type'] === 'super') {
                            // 超級管理員：顯示所有未募款的建議
                            $sql = "
                                SELECT Suggestion_ID, Title 
                                FROM Suggestion s
                                WHERE NOT EXISTS (
                                    SELECT 1 
                                    FROM FundingSuggestion f
                                    WHERE f.Suggestion_ID = s.Suggestion_ID
                                )
                            ";
                        } elseif ($_SESSION['admin_type'] === 'department') {
                            // 部門管理員：僅顯示該學院的未募款建議
                            $user_id = $_SESSION['User_ID'];  // 當前登入用戶的 ID
                            
                            // 查詢該部門管理員所屬的學院 ID
                            $scope_sql = "SELECT College_ID FROM DepartmentAdminScope WHERE User_ID = '$user_id'";
                            $scope_result = $conn->query($scope_sql);
                            if ($scope_result && $scope_result->num_rows > 0) {
                                $scope_row = $scope_result->fetch_assoc();
                                $college_id = $scope_row['College_ID'];  // 取得學院 ID

                                // 查詢該學院的未募款建議
                                $sql = "
                                    SELECT Suggestion_ID, Title
                                    FROM Suggestion s
                                    WHERE s.Building_ID IN (SELECT Building_ID FROM Building WHERE College_ID = '$college_id')
                                    AND NOT EXISTS (
                                        SELECT 1
                                        FROM FundingSuggestion f
                                        WHERE f.Suggestion_ID = s.Suggestion_ID
                                    )
                                ";
                            } else {
                                // 如果部門管理員沒有設定學院範圍，則不顯示任何建議
                                $sql = "SELECT Suggestion_ID, Title FROM Suggestion WHERE 1 = 0";  // 沒有符合條件的建議
                            }
                        } else {
                            // 其他角色：不顯示任何建議
                            $sql = "SELECT Suggestion_ID, Title FROM Suggestion WHERE 1 = 0";  // 沒有符合條件的建議
                        }

                        // 執行查詢
                        $result = $conn->query($sql);

                        // 顯示查詢結果
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['Suggestion_ID'] . "'>" . htmlspecialchars($row['Title']) . "</option>";
                            }
                        } else {
                            echo "<option disabled>沒有可選的建言</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="required_amount" class="form-label">預期目標金額 (最低金額為 NT$1000)：</label>
                    <input type="number" name="required_amount" id="required_amount" class="form-control" required min="1000" step="1">
                </div>

                <!-- 隱藏欄位，直接寫死進行中 -->
                <input type="hidden" name="status" value="進行中">

                <button type="submit" class="btn btn-custom btn-block">確認新增</button>
            </form>
        </div>

    </div>

    <script>
        document.getElementById('fundingForm').addEventListener('submit', function(e) {
            const requiredAmount = document.getElementById('required_amount').value;

            if (!/^\d+$/.test(requiredAmount) || requiredAmount < 1000) {
                alert("請輸入大於或等於 1000 的整數！");
                e.preventDefault();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
