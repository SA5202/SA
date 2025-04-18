/*管理員*/

<?php
$host = 'localhost';
$db = 'SA';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("連線失敗：" . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $suggestion_id = $_POST['suggestion_id'];
    $status = $_POST['status'];
    $now = date('Y-m-d H:i:s');

    // 判斷是否為無需募款
    $no_donation = isset($_POST['no_donation']) ? 1 : 0;
    $required_amount = $no_donation ? 0 : $_POST['required_amount'];

    $stmt = $conn->prepare("INSERT INTO FundingSuggestion (Suggestion_ID, Required_Amount, Raised_Amount, Status, Updated_At) VALUES (?, ?, 0, ?, ?)");
    $stmt->bind_param("idss", $suggestion_id, $required_amount, $status, $now);

    if ($stmt->execute()) {
        $message = "募資建言已成功新增！";
    } else {
        $message = "新增失敗：" . $conn->error;
    }
}

$sql = "SELECT s.Suggestion_ID, s.Title 
        FROM Suggestion s 
        LEFT JOIN FundingSuggestion f ON s.Suggestion_ID = f.Suggestion_ID 
        WHERE f.Suggestion_ID IS NULL";

$suggestions = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>設置募款建言</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            max-width: 85%;
            margin: 0 auto;
            padding: 30px;
            font-size: 1.1rem;
            line-height: 1.8;
            background-image: url('https://www.transparenttextures.com/patterns/brick-wall.png');
            background-repeat: repeat;
            background-color: #fefefe;
        }
    </style>
    <script>
        function toggleAmountField() {
            const checkbox = document.getElementById("no_donation");
            const amountField = document.getElementById("amount_group");
            const amountInput = document.getElementById("required_amount");

            if (checkbox.checked) {
                amountField.style.display = "none";
                amountInput.value = 0;
            } else {
                amountField.style.display = "block";
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <h2 class="my-4">管理員：設置募款建言</h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" class="border p-4 rounded bg-light">
            <div class="mb-3">
                <label for="suggestion_id" class="form-label">選擇建言項目</label>
                <select name="suggestion_id" id="suggestion_id" class="form-select" required>
                    <option value="">-- 請選擇建言 --</option>
                    <?php while ($row = $suggestions->fetch_assoc()): ?>
                        <option value="<?php echo $row['Suggestion_ID']; ?>">
                            <?php echo htmlspecialchars($row['Title']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" name="no_donation" id="no_donation" onchange="toggleAmountField()">
                <label class="form-check-label" for="no_donation">
                    此建言無需捐款
                </label>
            </div>

            <div class="mb-3" id="amount_group">
                <label for="required_amount" class="form-label">所需金額 (NT$)</label>
                <input type="number" step="100" name="required_amount" id="required_amount" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">狀態</label>
                <select name="status" id="status" class="form-select">
                    <option value="進行中">進行中</option>
                    <option value="已完成">已完成</option>
                    <option value="暫停">暫停</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">新增募資建言</button>
        </form>
    </div>
</body>

</html>