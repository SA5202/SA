<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

// 撈取進行中的募款建言（確認 Suggestion_ID 有對應建言）
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

// 撈取付款方式
$paymentMethods = [];
$result = $link->query("SELECT Method_ID, Method_Name FROM PaymentMethod");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $paymentMethods[] = $row;
    }
}

// 撈取使用者 Email
$email = '';
$user_name = $_SESSION['User_Name'];
$email_result = $link->query("SELECT Email FROM UserAccount WHERE User_Name = '" . $link->real_escape_string($user_name) . "'");
if ($email_result && $email_result->num_rows > 0) {
    $email = $email_result->fetch_assoc()['Email'];
}

// 根據 URL 中的 funding_id 取得對應的標題
$selectedFundingID = isset($_GET['funding_id']) ? $_GET['funding_id'] : null;
$selectedTitle = '';
if ($selectedFundingID) {
    $query = "SELECT Title FROM FundingSuggestion f JOIN Suggestion s ON f.Suggestion_ID = s.Suggestion_ID WHERE f.Funding_ID = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param("i", $selectedFundingID);
    $stmt->execute();
    $stmt->bind_result($selectedTitle);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>捐款頁面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: transparent;
            font-family: "Noto Serif TC", serif;
            font-size: 1.1rem;
            line-height: 1.8;
            margin: 0;
            padding: 30px 20px;
            color: #333;
        }

        .donation-form {
            border: 2px solid #ccc;
            border-radius: 25px;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 0px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            max-width: 500px;
            margin: 0 auto;
        }

        .donation-form:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .donation-form .form-control,
        .donation-form .form-select,
        .donation-form textarea {
            border-radius: 10px;
            max-width: 100%;
        }

        .form-label {
            font-weight: bold;
            color: #555;
        }

        .btn-success {
            background-color: rgb(99, 160, 101);
            font-weight: bold;
            font-size: 1.1rem;
            border-radius: 50px;
        }

        .btn-success:hover {
            background-color: rgb(66, 107, 70);
        }
    </style>
</head>

<body>

    <div class="donation-form">
        <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>捐款成功！</strong> 感謝您的支持，我們已收到您的捐款。
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>錯誤：</strong> <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($fundingOptions)): ?>
            <div class="alert alert-warning text-center">⚠ 目前沒有「進行中」的募款建言可供捐款。</div>
        <?php else: ?>
            <form method="POST" action="donate_process.php">
                <div class="mb-3">
                    <label for="funding_id" class="form-label">捐款項目</label>
                    <input type="text" class="form-control" id="funding_id_display" value="<?= htmlspecialchars($selectedTitle) ?>" readonly>
                    <input type="hidden" name="funding_id" id="funding_id" value="<?= htmlspecialchars($selectedFundingID) ?>">
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">捐款金額 (NTD)</label>
                    <input type="number" class="form-control" id="amount" name="amount" required min="1">
                </div>

                <div class="mb-3">
                    <label for="method" class="form-label">付款方式</label>
                    <select class="form-select" id="method" name="method_id" required>
                        <option value="">請選擇付款方式</option>
                        <?php foreach ($paymentMethods as $method): ?>
                            <?php if ($method['Method_ID'] != 7): ?>
                                <option value="<?= $method['Method_ID'] ?>"><?= htmlspecialchars($method['Method_Name']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="anonymous" name="anonymous">
                    <label class="form-check-label" for="anonymous">匿名捐款</label>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="receipt" name="receipt">
                    <label class="form-check-label" for="receipt">需要收據（電子）</label>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">留言 (100 字以內)</label>
                    <textarea class="form-control" id="message" name="message" rows="2" maxlength="100"></textarea>
                </div>

                <input type="hidden" name="donor_email" value="<?= htmlspecialchars($email) ?>">

                <button type="submit" class="btn btn-success w-100">立即捐款</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        setTimeout(function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>

</body>

</html>
