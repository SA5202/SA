<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>捐款頁面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* 設定全局顏色和樣式變數 */
        :root {
            --primary-color:rgb(94, 141, 192);
            /* 主色：藍色 */
            --secondary-color:rgb(140, 193, 97);
            /* 副色：綠色 */
            --card-bg-color: #ffffff;
            /* 卡片背景顏色 */
            --text-color: #333;
            /* 文字顏色 */
            --border-radius: 1rem;
            /* 邊角圓角半徑 */
            --shadow-color: rgba(0, 0, 0, 0.1);
            /* 卡片陰影顏色 */
            --font-family: 'Arial', sans-serif;
            /* 字型 */
        }

        /* 設定背景顏色和全局文字樣式 */
        body {
            background-color: rgba(248, 249, 250, 0);
            /* 透明背景 */
            font-family: var(--font-family);
            /* 設定字體 */
            color: var(--text-color);
            /* 設定文字顏色 */
            margin: 0;
            padding: 0;
        }

        /* 容器樣式 */
        .container {
            padding: 50px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* 卡片樣式 */
        .donation-card {
            width: 100%;
            max-width: 600px;
            background-color: var(--card-bg-color);
            border-radius: var(--border-radius);
            box-shadow: 0 4px 8px var(--shadow-color);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        /* 當卡片懸停時的效果 */
        .donation-card:hover {
            transform: translateY(-10px);
        }

        /* 卡片標題樣式 */
        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border-top-left-radius: var(--border-radius);
            border-top-right-radius: var(--border-radius);
        }

        /* 表單欄位標籤樣式 */
        .form-label {
            font-weight: bold;
            margin-bottom: 8px;
            display: inline-block;
        }

        /* 表單欄位樣式 */
        .form-select,
        .form-control {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 0.5rem;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        /* 當欄位獲得焦點時改變邊框顏色 */
        .form-select:focus,
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        /* 送出按鈕樣式 */
        .btn-success {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px;
            font-size: 18px;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        /* 按鈕懸停效果 */
        .btn-success:hover {
            background-color:rgb(115, 181, 70);
            transform: translateY(-3px);
        }

        /* 按鈕容器樣式 */
        .d-grid {
            display: grid;
            justify-items: center;
            margin-top: 20px;
        }

        /* 彈性盒子調整 */
        .mb-3 {
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card donation-card">
            <div class="card-header">
                <h4 class="mb-0">捐款頁面</h4>
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