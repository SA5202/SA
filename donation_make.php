<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}

// 建立資料庫連線
$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

// 取得 FundingSuggestion 資料
$fundingOptions = [];
$result = $link->query("SELECT Funding_ID, Funding_Title FROM FundingSuggestion WHERE Status = '公開'");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $fundingOptions[] = $row;
    }
}
?>

<div class="container mt-5">
    <h2 class="mb-4">愛校捐款</h2>

    <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
        <div class="alert alert-success">捐款成功，感謝您的支持！</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form method="POST" action="donate_process.php">
        <div class="mb-3">
            <label for="funding_id" class="form-label">捐款項目</label>
            <select class="form-select" id="funding_id" name="funding_id" required>
                <option value="">請選擇項目</option>
                <?php foreach ($fundingOptions as $item): ?>
                    <option value="<?= $item['Funding_ID'] ?>"><?= htmlspecialchars($item['Funding_Title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">捐款金額 (NTD)</label>
            <input type="number" class="form-control" id="amount" name="amount" required min="1">
        </div>

        <div class="mb-3">
            <label for="method" class="form-label">付款方式</label>
            <select class="form-select" id="method" name="method_id" required>
                <option value="1">信用卡</option>
                <option value="2">ATM轉帳</option>
                <option value="3">Line Pay</option>
            </select>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="anonymous" name="anonymous">
            <label class="form-check-label" for="anonymous">匿名捐款</label>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="receipt" name="receipt">
            <label class="form-check-label" for="receipt">需要收據</label>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">留言 (可留下一句話)</label>
            <textarea class="form-control" id="message" name="message" rows="2" maxlength="100"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">提交捐款</button>
    </form>
</div>