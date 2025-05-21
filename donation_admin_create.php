<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

// 僅限管理員存取
if (!isset($_SESSION['User_Name']) || $_SESSION['User_Type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

// 撈取捐款項目
$fundingOptions = [];
$query = "SELECT f.Funding_ID, s.Title 
          FROM FundingSuggestion f
          JOIN Suggestion s ON f.Suggestion_ID = s.Suggestion_ID
          WHERE f.Status = '進行中'";
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
    <style>
        body {
            background-color: transparent;
            font-family: "Noto Serif TC", serif;
            font-size: 1.1rem;
            line-height: 1.8;
            padding: 30px;
            color: #333;
        }

        .donation-form {
            max-width: 650px;
            margin: 30px auto;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            box-shadow: 0 0 18px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease-in-out;
        }

        .donation-form:hover {
            transform: scale(1.015);
        }

        .form-control,
        .form-select,
        textarea {
            border-radius: 10px;
        }

        .form-label {
            font-weight: bold;
            color: #555;
        }

        .btn-primary {
            background-color: #007bff;
            font-weight: bold;
            font-size: 1.1rem;
            border-radius: 25px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="donation-form">
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
            <label for="amount" class="form-label">金額（NTD）</label>
            <input type="number" class="form-control" name="amount" id="amount" required min="1">
        </div>

        <input type="hidden" name="method_id" value="7"> <!-- 固定為現金 -->

        <div class="mb-3">
            <label for="note" class="form-label">備註（可輸入管理員留言）</label>
            <textarea class="form-control" name="note" id="note" rows="2" maxlength="100"></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">新增捐款紀錄</button>
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