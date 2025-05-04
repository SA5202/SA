<?php
session_start();

// 僅限管理員
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo "未授權進入";
    exit;
}

// 資料庫連接
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SA";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}

$errorMessage = "";
$shouldRedirect = false;

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $suggestion_id = $_POST['suggestion_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    if ($title === '' || $description === '' || $status === '') {
        $errorMessage = "所有欄位皆為必填";
    } else {
        $update_sql = "UPDATE Suggestion SET Title = ?, Description = ?, Updated_At = NOW() WHERE Suggestion_ID = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $title, $description, $suggestion_id);

        if ($stmt->execute()) {
            // 寫入 Progress 表
            $admin_id = $_SESSION['User_ID'];
            $progress_sql = "INSERT INTO Progress (Suggestion_ID, Status, Updated_At, Updated_By)
                             VALUES (?, ?, NOW(), ?)
                             ON DUPLICATE KEY UPDATE Status = VALUES(Status), Updated_At = NOW(), Updated_By = VALUES(Updated_By)";
            $progress_stmt = $conn->prepare($progress_sql);
            $progress_stmt->bind_param("isi", $suggestion_id, $status, $admin_id);
            $progress_stmt->execute();

            $shouldRedirect = true; // ✅ 成功儲存，稍後觸發跳轉
        } else {
            $errorMessage = "更新失敗，請稍後再試。";
        }

        $stmt->close();
    }
}

// 載入原始建言資料
if (isset($_GET['suggestion_id'])) {
    $suggestion_id = intval($_GET['suggestion_id']);
    $sql = "SELECT * FROM Suggestion WHERE Suggestion_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $suggestion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "找不到該建言";
        exit;
    }
} else {
    echo "缺少建言 ID";
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>編輯建言 | 輔仁大學愛校建言捐款系統</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background-color: transparent;
            font-family: "Noto Serif TC", serif;
        }

        .form-container {
            max-width: 50%;
            margin: 80px auto;
        }

        .form-card {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--bs-card-border-color);
        }

        .form-card h3 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            color: #2c3e50;
        }

        label {
            font-weight: bold;
        }

        .btn-block {
            width: 100%;
        }

        .alert {
            margin-top: 20px;
        }

        .btn-custom {
            background-color: rgb(136, 184, 209);
            padding: 10px 30px;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
            border-radius: 30px;
        }

        .btn-custom:hover {
            background-color: rgb(83, 127, 164);
        }

        .btn-custom:focus,
        .btn-custom:active {
            outline: none;
            box-shadow: none;
        }

        footer {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container form-container">
        <div class="form-card">
            <h3>編輯建言內容</h3>

            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="suggestion_id" value="<?= htmlspecialchars($row['Suggestion_ID']) ?>">

                <div class="mb-3">
                    <label for="title" class="form-label">建言標題：</label>
                    <input type="text" class="form-control" id="title" name="title"
                        value="<?= htmlspecialchars($row['Title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">建言內容：</label>
                    <textarea class="form-control" id="description" name="description" rows="5"
                        required><?= htmlspecialchars($row['Description']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">處理進度：</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="">-- 請選擇 --</option>
                        <option value="建言已受理" <?= ($row['Status'] ?? '') === '建言已受理' ? 'selected' : '' ?>>建言已受理</option>
                        <option value="處理中" <?= ($row['Status'] ?? '') === '處理中' ? 'selected' : '' ?>>處理中</option>
                        <option value="處理完成" <?= ($row['Status'] ?? '') === '處理完成' ? 'selected' : '' ?>>處理完成</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-custom btn-block">儲存變更</button>
            </form>
        </div>
    </div>

    <?php $conn->close(); ?>

    <?php if ($shouldRedirect): ?>
    <script>
        window.location.href = "suggestion_detail.php?id=<?= $suggestion_id ?>";
    </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>