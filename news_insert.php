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

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $news_title = $_POST['news_title'];
    $news_content = $_POST['news_content'];
    $suggestion_id = $_POST['suggestion_id'];

    if ($suggestion_id == "0") {
        $suggestion_id = NULL;
    }

    // 插入資料到 News 表（假設 News 表有 Title 和 Suggestion_ID 欄位）
    $sql = "INSERT INTO News (News_Title, News_Content, Suggestion_ID, Update_At) 
            VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $news_title, $news_content, $suggestion_id);

    if ($stmt->execute()) {
        header("Location: news.php");
        exit();
    } else {
        $errorMessage = "新增公告失敗，請稍後再試。";
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
    <title>發布公告 | 輔仁大學愛校建言捐款系統</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background-color: transparent;
            font-family: "Noto Serif TC", serif;
        }

        .form-container {
            max-width: 70%;
            margin: 100px auto;
        }

        .form-card {
            background-color: rgba(255, 255, 255, 0.9);
            /* 淡透明背景 */
            padding: 50px;
            border-radius: 30px;
            transition: transform 0.3s;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
        }

        .form-card:hover {
            transform: scale(1.02);
        }

        label {
            color: #555;
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

            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form action="news_insert.php" method="POST">
                <div class="mb-3">
                    <label for="news_title" class="form-label">公告標題：</label>
                    <input type="text" name="news_title" id="news_title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="news_content" class="form-label">公告內容：</label>
                    <textarea name="news_content" id="news_content" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-4">
                    <label for="suggestion_id" class="form-label">公告相關建言：</label>
                    <select name="suggestion_id" id="suggestion_id" class="form-select" required>
                        <option value="">選擇建言</option>
                        <option value="0">本公告無相關建言</option>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['Suggestion_ID'] . "'>" . htmlspecialchars($row['Title']) . "</option>";
                            }
                        } else {
                            echo "<option disabled>目前沒有建言可以選擇</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-custom btn-block">確認發布</button>
            </form>
        </div>
    </div>

    <?php $conn->close(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>