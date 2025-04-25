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

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $suggestion_id = $_POST['suggestion_id'];
    $required_amount = $_POST['required_amount'];
    $status = $_POST['status'];

    // 插入募款建議到 FundingSuggestion
    $sql = "INSERT INTO FundingSuggestion (Suggestion_ID, Required_Amount, Raised_Amount, Status, Updated_At) 
            VALUES (?, ?, 0, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $suggestion_id, $required_amount, $status);
    
    if ($stmt->execute()) {
        // 插入成功後跳轉到 test1.php 顯示進度
        header("Location: test1.php");
        exit();
    } else {
        echo "<p>新增募款建議失敗：" . $conn->error . "</p>";
    }

    $stmt->close();
}

// 查詢已有的建言
$sql = "SELECT Suggestion_ID, Title FROM Suggestion";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>新增募款建議</title>

    <!-- 引入Bootstrap樣式 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        .form-container {
            margin-top: 50px;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-container form {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container form .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container form-container">
        <h2>新增募款建議</h2>
        <form action="test.php" method="POST">
            <div class="form-group">
                <label for="suggestion_id">選擇建言：</label>
                <select name="suggestion_id" id="suggestion_id" class="form-control" required>
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

            <div class="form-group">
                <label for="required_amount">募款目標金額：</label>
                <input type="number" name="required_amount" id="required_amount" class="form-control" required min="0" step="0.01">
            </div>

            <div class="form-group">
                <label for="status">募款狀態：</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="募款中">募款中</option>
                    <option value="已達標">已達標</option>
                    <option value="募款結束">募款結束</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-block">提交募款建議</button>
        </form>
    </div>

    <?php $conn->close(); ?>

</body>

</html>
