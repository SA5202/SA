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

    // ✅ 檢查已募得金額不能大於目標金額
    if ($raised_amount > $required_amount) {
        $errorMessage = "當前已募得金額不可超過募款目標金額！";
    } else {
        // 更新募款金額資料
        $update_sql = "UPDATE FundingSuggestion SET Required_Amount = ?, Raised_Amount = ?, Status = ?, Updated_At = NOW() WHERE Funding_ID = ?";
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
            header("Location: funding_detail.php");
            exit();
        } else {
            $errorMessage = "操作失敗，請稍後再試！";
        }

        $stmt->close();
    }
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
    <title>編輯募款建言 | 輔仁大學愛校建言捐款系統</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background-color: transparent;
            font-family: "Noto Serif TC", serif;
        }

        .form-container {
            max-width: 70%;
            margin: 120px auto;
        }

        .form-card {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 50px;
            border-radius: 30px;
            box-shadow: 0 0px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
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

            <!-- 顯示錯誤訊息 -->
            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="funding_id" value="<?= htmlspecialchars($row['Funding_ID']) ?>">

                <div class="mb-3">
                    <label for="required_amount" class="form-label">預期目標金額：</label>
                    <input type="number" class="form-control" id="required_amount" name="required_amount" value="<?= htmlspecialchars($row['Required_Amount']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="raised_amount" class="form-label">當前已募得金額：</label>
                    <input type="number" class="form-control" id="raised_amount" name="raised_amount" value="<?= htmlspecialchars($row['Raised_Amount']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">募款狀態：</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="進行中" <?= $row['Status'] === '進行中' ? 'selected' : '' ?>>進行中</option>
                        <option value="暫停" <?= $row['Status'] === '暫停' ? 'selected' : '' ?>>暫停</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-custom btn-block">儲存變更</button>
            </form>
        </div>
    </div>

    <?php $conn->close(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ JavaScript 驗證 -->
    <script>
        document.querySelector("form").addEventListener("submit", function(e) {
            const requiredAmount = parseFloat(document.getElementById("required_amount").value);
            const raisedAmount = parseFloat(document.getElementById("raised_amount").value);

            if (raisedAmount > requiredAmount) {
                alert("當前已募得金額不可超過募款目標金額！");
                e.preventDefault();
            }
        });
    </script>
</body>

</html>
