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
    $funding_id = $_POST['funding_id'];
    $required_amount = $_POST['required_amount'];
    $raised_amount = $_POST['raised_amount'];
    $status = $_POST['status'];

    // 更新募款金額資料
    $update_sql = "UPDATE FundingSuggestion SET Required_Amount = ?, Raised_Amount = ?, Status = ? WHERE Funding_ID = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ddsi", $required_amount, $raised_amount, $status, $funding_id);

    if ($stmt->execute()) {
        // 如果募款金額已達目標，更新狀態
        if ($status === '已完成' || $raised_amount >= $required_amount) {
            $updateStatusSql = "UPDATE FundingSuggestion SET Status = '已完成' WHERE Funding_ID = ?";
            $stmtStatus = $conn->prepare($updateStatusSql);
            $stmtStatus->bind_param("i", $funding_id);
            $stmtStatus->execute();
        }
        // 跳轉回募款列表頁面
        header("Location: funding_detail.php");
        exit();
    } else {
        $errorMessage = "更新失敗，請稍後再試！";
    }

    $stmt->close();
}

// 查詢募款資訊
if (isset($_GET['funding_id'])) {
    $funding_id = $_GET['funding_id'];
    $sql = "SELECT * FROM FundingSuggestion WHERE Funding_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $funding_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "找不到該募款資料";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>編輯募款建言</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background-color: transparent;
            font-family: "Noto Serif TC", serif;
        }

        .form-container {
            margin-top: 60px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-card {
            background-color: rgba(255, 255, 255, 0.9);
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

        .btn-custom {
            background-color: rgb(136, 184, 209);
            border-color: rgb(83, 127, 164);
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
            border-radius: 30px;
        }

        .btn-custom:hover {
            background-color: rgb(83, 127, 164);
            border-color: rgb(51, 81, 105);
        }

        .btn-custom:focus,
        .btn-custom:active {
            outline: none;
            box-shadow: none;
        }

        footer {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container form-container">
        <div class="form-card">
            <h2>編輯募款建言</h2>

            <!-- 顯示錯誤訊息 -->
            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="funding_id" value="<?= htmlspecialchars($row['Funding_ID']) ?>">

                <div class="mb-3">
                    <label for="required_amount" class="form-label">募款目標金額</label>
                    <input type="number" class="form-control" id="required_amount" name="required_amount" value="<?= htmlspecialchars($row['Required_Amount']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="raised_amount" class="form-label">已募得金額</label>
                    <input type="number" class="form-control" id="raised_amount" name="raised_amount" value="<?= htmlspecialchars($row['Raised_Amount']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">募款狀態</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="進行中" <?= $row['Status'] === '進行中' ? 'selected' : '' ?>>進行中</option>
                        <option value="已完成" <?= $row['Status'] === '已完成' ? 'selected' : '' ?>>已完成</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-custom btn-block">保存更改</button>
            </form>
        </div>
    </div>

    <?php $conn->close(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>