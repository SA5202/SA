<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

// 確保是管理員登入
if (!isset($_SESSION['User_Name']) || $_SESSION['User_Type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

// 撈取捐款項目（FundingSuggestion + Suggestion）
$fundingOptions = [];
$query = "
    SELECT f.Funding_ID, s.Title 
    FROM FundingSuggestion f
    JOIN Suggestion s ON f.Suggestion_ID = s.Suggestion_ID
    WHERE f.Status = '進行中'
";
$result = $link->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $fundingOptions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>手動新增捐款紀錄</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center fw-bold">管理員新增捐款紀錄</h2>

    <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>成功新增！</strong> 已建立一筆手動捐款紀錄。
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>錯誤：</strong> <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="donation_admin_process.php">
        <div class="mb-3">
            <label for="donor_name" class="form-label">捐款者姓名（選填）</label>
            <input type="text" class="form-control" name="donor_name" id="donor_name" maxlength="50">
        </div>

        <div class="mb-3">
            <label for="funding_id" class="form-label">捐款項目</label>
            <select class="form-select" name="funding_id" id="funding_id" required>
                <option value="">請選擇項目</option>
                <?php foreach ($fundingOptions as $item): ?>
                    <option value="<?= $item['Funding_ID'] ?>"><?= htmlspecialchars($item['Title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="method_id" class="form-label">付款方式</label>
            <select class="form-select" name="method_id" id="method_id" required>
                <option value="7" selected>現金</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">金額（NTD）</label>
            <input type="number" class="form-control" name="amount" id="amount" required min="1">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="anonymous" id="anonymous">
            <label class="form-check-label" for="anonymous">匿名</label>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="receipt" id="receipt">
            <label class="form-check-label" for="receipt">需要收據</label>
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">備註（可輸入管理員留言）</label>
            <textarea class="form-control" name="note" id="note" rows="2" maxlength="100"></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">新增捐款紀錄</button>
    </form>
</div>

<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) alert.classList.remove('show');
    }, 3000);
</script>
</body>
</html>