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
        header("Location: funding_detail.php");
        exit();
    } else {
        // 美化的錯誤訊息
        $errorMessage = "此建言找不到或已在募款中，請重新選擇。";
    }

    $stmt->close();
}

// 查詢建言列表
// 僅顯示「從未設置過募款」的建言
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>新增募款建言 | 輔仁大學愛校建言捐款系統</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background-color: transparent;
            /* 設置透明背景 */
            font-family: "Noto Serif TC", serif;
        }

        .form-container {
            max-width: 60%;
            margin: 160px auto;
        }

        .form-card {
            background-color: rgba(255, 255, 255, 0.9);
            /* 淡透明背景 */
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

        /* 自訂按鈕顏色 */
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

        /* 完全移除頁腳區塊 */
        footer {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container form-container">
        <div class="form-card">

            <!-- 錯誤提示訊息 -->
            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>
            <form action="fundingsuggestion.php" method="POST">
                <div class="mb-3">
                    <label for="suggestion_id" class="form-label">選擇待募款建言：</label>
                    <select name="suggestion_id" id="suggestion_id" class="form-select" required>
                        <option value="">選擇建言</option>
                        <?php
                        if ($result->num_rows > 0) {
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
                    <input type="text" name="required_amount" id="required_amount" class="form-control" required>
                </div>
                <input type="hidden" name="status" value="募款中">
                <!--如果要顯示募款中但不能選擇，就把上面那行改成這個就好
                <div class="mb-4">
                    <label for="status" class="form-label">設置建言募款狀態：</label>
                    <input type="text" name="status" id="status" class="form-control" value="募款中" readonly>
                </div> -->
                <button type="submit" class="btn btn-custom btn-block">確認新增</button>
            </form>
        </div>
    </div>
    <script>
        document.querySelector("form").addEventListener("submit", function(e) {
            const requiredAmount = document.querySelector("#required_amount").value;

            // 檢查輸入是否為有效的整數，並且大於等於 1000
            if (!/^\d+$/.test(requiredAmount) || requiredAmount < 1000) {
                alert("請輸入大於或等於 1000 的整數！");
                e.preventDefault();  // 阻止表單提交
            }
        });
    </script>
    <?php $conn->close(); ?>

    <!-- 載入 Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>