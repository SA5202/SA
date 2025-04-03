<?php
$success = null; // 預設設為null, 表示尚未提交過表單

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 確保表單資料已經送出
    $title = $_POST['title'];
    $facility = $_POST['facility'];
    $building = $_POST['building'];
    $description = $_POST['description'];

    // 建立資料庫連接
    $link = mysqli_connect('localhost', 'root', '', 'sa');

    // 檢查連接是否成功
    if (!$link) {
        die('資料庫連接失敗: ' . mysqli_connect_error());
    }

    // 使用準備好的語句來防止 SQL 注入
    $stmt = $link->prepare("INSERT INTO suggestion (title, facility, building, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $facility, $building, $description);

    // 執行插入操作
    if ($stmt->execute()) {
        $success = true;
    } else {
        $success = false;
    }

    // 關閉資料庫連接
    $stmt->close();
    $link->close();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>輔仁大學愛校建言捐款系統</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@550&display=swap">
    <style>
        /* 建言表格 */
        .suggestion-form {
            max-width: 700px;
            margin-top: 15px;
            margin-left: 180px;
            padding: 30px;
            background-color: #fff;
            border-radius: 25px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .suggestion-form:hover {
            transform: scale(1.02);
        }

        .form-group {
            margin-bottom: 15px;
            padding-left: 25px;
            padding-right: 25px;
            position: relative;
        }

        .form-control,
        .form-select,
        textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus,
        textarea:focus {
            outline: none;
            border-color: rgb(109, 140, 60);
            box-shadow: 0 0 5px rgba(111, 140, 60, 0.5);
        }

        .btn-submit {
            background-color: #4C85B1;
            color: white;
            padding: 4px 30px;
            border: none;
            border-radius: 10px;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 16px;
        }

        .btn-submit:hover {
            background-color: #0047AB;
            color: white;
            transform: translateY(-3px);
        }

        .btn-reset {
            background-color: #FF4C4C;
            color: white;
            padding: 4px 30px;
            border: none;
            border-radius: 10px;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 16px;
        }

        .btn-reset:hover {
            background-color: #e60000;
            color: white;
            transform: translateY(-3px);
        }

        .form-group:hover {
            transform: scale(1.02);
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .message {
            text-align: center;
            margin: 10px;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- 只有當表單提交後才顯示成功或失敗訊息 -->
    <?php if ($success !== null): ?>
        <div class="message <?php echo ($success ? 'success' : 'error'); ?>">
            <?php
            if ($success) {
                echo "發送成功";
            } else {
                echo "發送失敗";
            }
            ?>
        </div>
    <?php endif; ?>

    <div class="main-content">
        <form action="submit_suggestion.php" method="POST" class="suggestion-form">
            <div class="form-group">
                <label for="suggestion-title">建言標題：</label>
                <input type="text" id="suggestion-title" name="title" placeholder="請輸入建言標題" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="facility">關聯設施：</label>
                <input type="text" id="facility" name="facility" placeholder="請輸入設施" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="building">請選擇關聯樓棟：</label>
                <select id="building" name="building" class="form-select">
                    <option value="">選擇大樓</option>
                    <option value="大樓A">利瑪竇</option>
                    <option value="大樓B">進修部</option>
                    <option value="大樓C">羅耀拉</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">建言內容：</label>
                <textarea id="description" name="description" rows="5" placeholder="請具體描述您的建言內容" class="form-control" required></textarea>
            </div>

            <div class="button-container">
                <button type="submit" class="btn btn-submit">確認發佈</button>
                <button type="reset" class="btn btn-reset">一鍵清空</button>
            </div>
        </form>
    </div>
</body>

</html>
