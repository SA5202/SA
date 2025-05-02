<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>捐款頁面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color:rgba(248, 249, 250, 0);
        }

        .donation-card {
            max-width: 600px;
            margin: 50px auto;
            border-radius: 1rem;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card donation-card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">捐款頁面(建置中)</h4>
            </div>
            <div class="card-body">

                <form>
                    <!-- 建言種類 -->
                    <div class="mb-3">
                        <label for="suggestion_type" class="form-label">建言種類</label>
                        <select class="form-select" id="suggestion_type" name="suggestion_type" required>
                            <option value="">請選擇建言種類</option>
                            <option value="校園環境">校園環境</option>
                            <option value="設施改善">設施改善</option>
                            <option value="教學設備">教學設備</option>
                            <option value="活動支持">活動支持</option>
                            <option value="其他">其他</option>
                        </select>
                    </div>

                    <!-- 支付方式 -->
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">支付方式</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">請選擇支付方式</option>
                            <option value="信用卡">信用卡</option>
                            <option value="ATM轉帳">ATM轉帳</option>
                            <option value="超商代碼">超商代碼</option>
                        </select>
                    </div>

                    <!-- 金額 -->
                    <div class="mb-3">
                        <label for="amount" class="form-label">捐款金額（NT$）</label>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="請輸入金額" required min="1">
                    </div>

                    <!-- 送出按鈕 -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">確認捐款</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</body>

</html>