<?php
// 資料庫連線
$host = 'localhost';
$db = 'SA';
$user = 'root';
$pass = ''; // 根據你自己的設定修改

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("連線失敗：" . $conn->connect_error);
}

// 查詢所有募資建言的總目標金額
$sql = "SELECT SUM(Required_Amount) AS TotalRequired FROM FundingSuggestion";
$result = $conn->query($sql);

// 結果處理
$totalRequired = 0;
if ($result && $row = $result->fetch_assoc()) {
    $totalRequired = $row['TotalRequired'];
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>總目標募款金額</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            max-width: 85%;
            margin: 0 auto;
            padding: 30px;
            font-size: 1.1rem;
            line-height: 1.8;
            background-image: url('https://www.transparenttextures.com/patterns/brick-wall.png');
            background-repeat: repeat;
            background-color: #fefefe;
        }
    </style>
</head>

<body>
    <div class="container text-center mt-5">
        <h2 class="mb-4">目前所有建言的總目標募款金額</h2>
        <div class="alert alert-primary fs-4" role="alert">
            NT$ <?php echo number_format($totalRequired, 0); ?> 元
        </div>
    </div>
</body>

</html>