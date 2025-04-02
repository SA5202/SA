<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@550&display=swap">
    
</head>

<body>

    <div class="main-content">
        <h2 class="content-title">提出建言</h2>
        <form action="submit_suggestion.php" method="POST" class="suggestion-form">
            <div class="form-group">
                <label for="suggestion-title">建言標題：</label>
                <input type="text" id="suggestion-title" name="title" placeholder="請輸入建言標題" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="facility">相關設施：</label>
                <input type="text" id="facility" name="facility" placeholder="請輸入設施" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="building">大樓：</label>
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

            <button type="submit" class="btn btn-custom">提交建言</button>
        </form>
    </div>
</body>

</html>