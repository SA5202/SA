<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

date_default_timezone_set('Asia/Taipei');

if (!isset($_SESSION['User_Name']) || $_SESSION['User_Type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("未提供捐款 ID");
}

$donation_id = intval($_GET['id']);

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die("資料庫連線失敗：" . $link->connect_error);
}

// 撈取捐款資料
$sql = "
    SELECT d.*, ua.User_Name, pm.Method_Name,
           s.Title AS Suggestion_Title
    FROM Donation d
    LEFT JOIN UserAccount ua ON d.User_ID = ua.User_ID
    LEFT JOIN PaymentMethod pm ON d.Method_ID = pm.Method_ID
    LEFT JOIN FundingSuggestion fs ON d.Funding_ID = fs.Funding_ID
    LEFT JOIN Suggestion s ON fs.Suggestion_ID = s.Suggestion_ID
    WHERE d.Donation_ID = $donation_id
";
$result = $link->query($sql);
if (!$result || $result->num_rows === 0) {
    die("找不到該筆捐款紀錄");
}
$donation = $result->fetch_assoc();

// 撈取付款方式（如果日後要用到）
$methods = [];
$m_result = $link->query("SELECT Method_ID, Method_Name FROM PaymentMethod");
while ($row = $m_result->fetch_assoc()) {
    $methods[] = $row;
}

// 處理捐款時間說明（for display）
$dt = new DateTime($donation['Donation_Date']);
$donation_date_display = $dt->format("Y-m-d H:i");

// 轉換為 <input type="datetime-local"> 格式，注意要是 "YYYY-MM-DDTHH:MM"
$donation_date_for_input = $dt->format("Y-m-d\TH:i");

?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>編輯捐款紀錄</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 40px;
            font-family: "Noto Serif TC", serif;
            background-color: transparent !important;
        }

        .card {
            max-width: 800px;
            margin: auto;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0px 15px rgba(0, 0, 0, 0.08);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
            /* 加這行才會顯示框線 */

            /* 加入浮動效果所需的過渡設定 */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .card h4 {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            margin-top: 10px;
        }

        .btn-primary {
            margin-top: 20px;
            border-radius: 12px;
            padding: 10px 40px;
            font-weight: bold;
        }

        .btn-submit {
            background-color: rgb(120, 174, 215);
            color: white;
            padding: 5px 40px;
            border: none;
            border-radius: 20px;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-block;
        }

        .btn-submit:hover {
            background-color: rgb(100, 150, 190);
            transform: scale(1.05);
        }

        .btn-submit:active {
            background-color: rgb(80, 130, 170);
            transform: scale(0.95);
        }

        .btn-submit:focus {
            outline: none;
            box-shadow: 0 0 8px 3px rgba(120, 174, 215, 0.6);
        }
    </style>
</head>

<body>
    <div class="card">
        <h4 class="text-center">編輯捐款紀錄</h4>

        <!-- 錯誤訊息顯示區塊 -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <form method="POST" action="donation_admin_update.php">
            <input type="hidden" name="donation_id" value="<?= $donation['Donation_ID'] ?>">

            <div class="mb-3">
                <label class="form-label">捐款者帳號</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($donation['User_Name'] ?? '匿名 / 手動') ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">捐款項目</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($donation['Suggestion_Title']) ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">金額</label>
                <input type="number" class="form-control" id="amount" name="amount" value="<?= htmlspecialchars($donation['Donation_Amount']) ?>" min="1" required>
            </div>

            <div class="mb-3">
                <label for="method_id" class="form-label">付款方式</label>
                <input type="text" class="form-control" value="現金" disabled>
                <input type="hidden" name="method_id" value="7">
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">狀態說明 / 備註</label>
                <textarea name="status" id="status" class="form-control" rows="3"><?= htmlspecialchars($donation['Status']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">是否手動新增</label>
                <input type="text" class="form-control" value="<?= $donation['Is_Manual'] ? '是' : '否' ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="donation_date" class="form-label">捐款日期 (可不填，預設為現在時間)</label>
                <input type="datetime-local" class="form-control" id="donation_date" name="donation_date" value="<?= $donation_date_for_input ?>">
            </div>
            <div class="text-center">
                <button type="submit" class="btn-submit">儲存變更</button>
            </div>


        </form>
    </div>
</body>

</html>