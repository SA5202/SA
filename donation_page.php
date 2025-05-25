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
            border: 1px solid #ccc;
            border-radius: 40px;
            padding: 40px;
            background-color: #fff;
            box-shadow: 0 0px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            max-width: 70%;
            margin: 30px auto;
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

        input[readonly] {
            background-color: #eee;
        }

        input:focus {
            outline: none;
            border-color: rgb(173, 231, 248);
            box-shadow: 0 0 8px rgba(70, 117, 141, 0.88);
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

        .modal-backdrop.show {
            background-color: transparent !important;
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
            <form id="donationForm" method="POST" action="donate_process.php">
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





    <!-- 信用卡付款彈跳視窗 -->
    <!-- Modal -->
    <div class="modal fade" id="creditCardModal" tabindex="-1" aria-labelledby="creditCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="creditCardModalLabel">信用卡付款資訊</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cardNumber" class="form-label">卡號</label>
                        <input type="text" class="form-control" id="cardNumber" maxlength="16" placeholder="XXXX XXXX XXXX XXXX">
                    </div>
                    <div class="mb-3">
                        <label for="expiry" class="form-label">有效期限 (MM/YY)</label>
                        <input type="text" class="form-control" id="expiry" placeholder="MM/YY">
                    </div>
                    <div class="mb-3">
                        <label for="cvv" class="form-label">CVV</label>
                        <input type="text" class="form-control" id="cvv" maxlength="3" placeholder="123">
                    </div>
                    <div class="mb-3">
                        <label for="cardName" class="form-label">持卡人姓名</label>
                        <input type="text" class="form-control" id="cardName" placeholder="姓名">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" id="fakeSubmitBtn">送出付款</button>
                </div>
            </div>
        </div>
    </div>
    <!-- LINE Pay / 街口支付圖片 Modal -->
    <div class="modal fade" id="ewalletImageModal" tabindex="-1" aria-labelledby="ewalletImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ewalletImageModalLabel">行動支付說明</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="fake_linepay_qr.png" alt="行動支付 QR Code" class="img-fluid rounded shadow">
                    <p class="mt-2">請使用 LINE Pay 或 街口支付 掃描上方 QR Code 完成付款後再提交表單。</p>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.getElementById('method').addEventListener('change', function() {
            const selectedValue = parseInt(this.value);

            if (selectedValue === 1) {
                const creditCardModal = new bootstrap.Modal(document.getElementById('creditCardModal'));
                creditCardModal.show();
            } else if (selectedValue === 2 || selectedValue === 6) {
                alert(
                    "📌 匯款資訊：\n" +
                    "銀行名稱：台灣銀行（004）\n" +
                    "分行名稱：輔大分行\n" +
                    "帳戶名稱：愛校無限公司\n" +
                    "銀行帳號：123-456-789012\n" +
                    "⚠️ 請先完成匯款再提交捐款表單\n" +
                    "⚠️ 提交表單前記得在留言區回報帳號後五碼"
                );
            } else if (selectedValue === 3 || selectedValue === 4) {
                // 顯示共用的行動支付圖片 modal
                const ewalletModal = new bootstrap.Modal(document.getElementById('ewalletImageModal'));
                ewalletModal.show();
            }
        });



        document.getElementById('fakeSubmitBtn').addEventListener('click', function() {
            const cardNumber = document.getElementById('cardNumber').value.trim();
            const amount = document.getElementById('amount').value.trim();
            const expiry = document.getElementById('expiry').value.trim();
            const cvv = document.getElementById('cvv').value.trim();
            const cardName = document.getElementById('cardName').value.trim();

            if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
                alert("請輸入有效的捐款金額");
                return;
            }

            if (!cardNumber || cardNumber.length !== 16 || isNaN(cardNumber)) {
                alert("請輸入 16 位數字的信用卡號");
                return;
            }

            if (!expiry || !/^\d{2}\/\d{2}$/.test(expiry)) {
                alert("請輸入有效的到期日格式，例如 12/26");
                return;
            }

            if (!cvv || cvv.length !== 3 || isNaN(cvv)) {
                alert("請輸入 3 位數 CVV");
                return;
            }

            if (!cardName) {
                alert("請輸入持卡人姓名");
                return;
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById('creditCardModal'));
            if (modal) modal.hide();

            console.log("提交中...");
            document.getElementById('donationForm').submit();
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>