<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}

$success = null;
$errorMessages = [];

// 建立資料庫連線
$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

// 取得設施與建築物資料
$facilities = [];
$buildings = [];

$facilityResult = $link->query("SELECT facility_id, facility_type FROM facility");
if ($facilityResult) {
    while ($row = $facilityResult->fetch_assoc()) {
        $facilities[] = $row;
    }
}

$buildingResult = $link->query("SELECT building_id, building_name FROM building");
if ($buildingResult) {
    while ($row = $buildingResult->fetch_assoc()) {
        $buildings[] = $row;
    }
}

// 表單送出時處理資料
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $facility = isset($_POST['facility']) ? (int) $_POST['facility'] : 0;
    $building = isset($_POST['building']) ? (int) $_POST['building'] : 0;
    $description = $_POST['description'] ?? '';
    $userID = $_SESSION['User_ID'] ?? null;

    // 檢查必填欄位，並收集錯誤訊息
    if (empty($title)) {
        $errorMessages[] = "請輸入標題";
    }
    if ($facility === 0) {
        $errorMessages[] = "請選擇設施";
    }
    if ($building === 0) {
        $errorMessages[] = "請選擇建築物";
    }
    if (empty($description)) {
        $errorMessages[] = "請輸入建議內容";
    }
    if (empty($userID)) {
        $errorMessages[] = "使用者未登入";
    }

    if (!empty($errorMessages)) {
        $success = false;

        // 只有錯誤時才輸出 POST 資料
        echo "<pre>";
        var_dump($_POST);
        echo "</pre>";

        // 顯示錯誤訊息
        echo "<div style='color: red;'><ul>";
        foreach ($errorMessages as $msg) {
            echo "<li>$msg</li>";
        }
        echo "</ul></div>";
    } else {
        $updatedAt = date('Y-m-d H:i:s');
        $upvotedAmount = 0;

        $stmt = $link->prepare("INSERT INTO suggestion (title, facility_id, building_id, description, updated_at, upvoted_amount, User_ID) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die('準備語句失敗: ' . $link->error);
        }

        $stmt->bind_param("siisssi", $title, $facility, $building, $description, $updatedAt, $upvotedAmount, $userID);

        if (!$stmt->execute()) {
            die('資料插入失敗: ' . $stmt->error);
        }

        $success = true;
        $stmt->close();
    }

    $link->close();
}
?>


<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>提出建言 | 輔仁大學愛校建言捐款系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: transparent;
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            line-height: 1.8;
            margin: 0;
            padding: 30px;
            color: #333;
        }

        .suggestion-form {
            max-width: 75%;
            margin: 0 auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 25px;
            box-shadow: 0 0px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
        }

        .suggestion-form:hover {
            transform: scale(1.02);
        }

        .form-group {
            margin-bottom: 15px;
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
            padding: 6px 30px;
            border: none;
            border-radius: 10px;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 16px;
        }

        .btn-submit:hover {
            background-color: #0047AB;
            transform: translateY(-3px);
        }

        .btn-reset {
            background-color: #FF4C4C;
            color: white;
            padding: 6px 30px;
            border: none;
            border-radius: 10px;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 16px;
        }

        .btn-reset:hover {
            background-color: #e60000;
            transform: translateY(-3px);
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
        }

        label {
            font-weight: bold;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>

    <?php if ($success !== null): ?>
        <div class="message <?php echo ($success ? 'success' : 'error'); ?>">
            <?php echo $success ? "建言發送成功！" : "發送失敗，請確認所有欄位已填寫"; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="suggestion-form">
        <div class="form-group">
            <label for="suggestion-title">建言標題：</label>
            <input type="text" id="suggestion-title" name="title" placeholder="請輸入建言標題" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="facility">選擇關聯設施：</label>
            <select id="facility" name="facility" class="form-select" required>
                <option value="">請選擇設施</option>
                <?php foreach ($facilities as $f): ?>
                    <option value="<?= $f['facility_id'] ?>"><?= htmlspecialchars($f['facility_type']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="building">選擇關聯建築物：</label>
            <select id="building" name="building" class="form-select" required>
                <option value="">請選擇建築物</option>
                <?php foreach ($buildings as $b): ?>
                    <option value="<?= $b['building_id'] ?>"><?= htmlspecialchars($b['building_name']) ?></option>
                <?php endforeach; ?>
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

</body>
</html>
