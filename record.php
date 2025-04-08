<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>捐款報表 | 輔仁大學愛校建言捐款系統</title>

    <!-- 樣式與字體 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>

    <style>
        body {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            line-height: 1.8;
            background-color: transparent;
            overflow-x: hidden;
            color: #333;
        }

        h3 {
            margin-bottom: 25px;
            font-weight: bold;
        }

        .table-responsive {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            text-align: center;
            vertical-align: middle;
        }

        .table th {
            background-color: #f1f3f5;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>

    <h3><i class="fas fa-chart-pie"></i> 捐款報表</h3>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>項目名稱</th>
                    <th>捐款人數</th>
                    <th>已募金額</th>
                    <th>目標金額</th>
                    <th>達成率</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>圖書館翻新</td>
                    <td>58</td>
                    <td>NT$320,000</td>
                    <td>NT$500,000</td>
                    <td>64%</td>
                </tr>
                <tr>
                    <td>校園綠化</td>
                    <td>42</td>
                    <td>NT$85,000</td>
                    <td>NT$200,000</td>
                    <td>42.5%</td>
                </tr>
                <tr>
                    <td>學生餐廳改善</td>
                    <td>33</td>
                    <td>NT$115,000</td>
                    <td>NT$300,000</td>
                    <td>38.3%</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>