<?php
session_start();

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
    $required_amount = $_POST['required_amount'];
    $status = $_POST['status'];

    // 插入募款建議
    $sql = "INSERT INTO FundingSuggestion (Suggestion_ID, Required_Amount, Raised_Amount, Status, Updated_At) 
            VALUES (?, ?, 0, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $suggestion_id, $required_amount, $status);

    if ($stmt->execute()) {
        header("Location: donate.php");
        exit();
    } else {
        // 美化的錯誤訊息
        $errorMessage = "此建言找不到或已在募款中，請重新選擇。";
    }

    $stmt->close();
}

// 查詢建言列表
$sql = "SELECT Suggestion_ID, Title FROM Suggestion";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>新增募款建議</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background-color: #eef3f7;
        }

        .form-container {
            margin-top: 60px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-card {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .form-card h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            color: #2c3e50;
        }

        .btn-block {
            width: 100%;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container form-container">
        <div class="form-card">
            <h2>新增募款建議</h2>

            <!-- 錯誤提示訊息 -->
            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form action="fundingsuggestion.php" method="POST">
                <div class="mb-3">
                    <label for="suggestion_id" class="form-label">選擇建言：</label>
                    <select name="suggestion_id" id="suggestion_id" class="form-select" required>
                        <option value="">-- 請選擇建言 --</option>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['Suggestion_ID'] . "'>" . htmlspecialchars($row['Title']) . "</option>";
                            }
                        } else {
                            echo "<option disabled>無可選建言</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="required_amount" class="form-label">募款目標金額：</label>
                    <input type="number" name="required_amount" id="required_amount" class="form-control" required min="0" step="0.01">
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label">募款狀態：</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="募款中">募款中</option>
                        <option value="已達標">已達標</option>
                        <option value="募款結束">募款結束</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">提交募款建議</button>
            </form>
        </div>
    </div>

    <?php $conn->close(); ?>
</body>

</html>
