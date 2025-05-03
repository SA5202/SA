<?php
session_start();

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
$news_title = "";
$news_content = "";
$suggestion_id = "";
$news_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($news_id <= 0) {
    die("無效的公告 ID");
}

// 取得原始公告資料
$sql = "SELECT News_Title, News_Content, Suggestion_ID FROM News WHERE News_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$stmt->bind_result($news_title, $news_content, $suggestion_id);
if (!$stmt->fetch()) {
    die("找不到該公告");
}
$stmt->close();

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $news_title = $_POST['news_title'];
    $news_content = $_POST['news_content'];
    $suggestion_id = $_POST['suggestion_id'];

    if ($suggestion_id == "0") {
        $suggestion_id = NULL;
    }

    $sql = "UPDATE News SET News_Title = ?, News_Content = ?, Suggestion_ID = ?, Update_At = NOW() WHERE News_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $news_title, $news_content, $suggestion_id, $news_id);

    if ($stmt->execute()) {
        header("Location: news.php");
        exit();
    } else {
        $errorMessage = "更新公告失敗，請稍後再試。";
    }

    $stmt->close();
}

// 查詢建言列表
$sql = "SELECT Suggestion_ID, Title FROM Suggestion";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>編輯公告 | 輔仁大學愛校建言捐款系統</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background-color: transparent;
            font-family: "Noto Serif TC", serif;
        }

        .form-container {
            max-width: 55%;
            margin: 80px auto;
        }

        .form-card {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 30px;
            transition: transform 0.3s;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
        }

        .form-card:hover {
            transform: scale(1.02);
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

        footer {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container form-container">
        <div class="form-card">
            <h3>編輯公告</h3>

            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form action="news_edit.php?id=<?= $news_id ?>" method="POST">
                <div class="mb-3">
                    <label for="news_title" class="form-label">公告標題：</label>
                    <input type="text" name="news_title" id="news_title" class="form-control" required value="<?= htmlspecialchars($news_title) ?>">
                </div>

                <div class="mb-3">
                    <label for="news_content" class="form-label">公告內容：</label>
                    <textarea name="news_content" id="news_content" class="form-control" rows="3" required><?= htmlspecialchars($news_content) ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="suggestion_id" class="form-label">公告關聯建言：</label>
                    <select name="suggestion_id" id="suggestion_id" class="form-select" required>
                        <option value="">選擇建言</option>
                        <option value="0" <?= is_null($suggestion_id) ? 'selected' : '' ?>>無關聯建言</option>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $selected = ($row['Suggestion_ID'] == $suggestion_id) ? "selected" : "";
                                echo "<option value='" . $row['Suggestion_ID'] . "' $selected>" . htmlspecialchars($row['Title']) . "</option>";
                            }
                        } else {
                            echo "<option disabled>無可選建言</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-custom btn-block">儲存變更</button>
            </form>
        </div>
    </div>

    <?php $conn->close(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>