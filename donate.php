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

// 查詢募款進度
$sql = "
    SELECT f.Funding_ID, s.Title, f.Required_Amount, f.Raised_Amount, f.Status, f.Updated_At
    FROM FundingSuggestion f
    JOIN Suggestion s ON f.Suggestion_ID = s.Suggestion_ID
    ORDER BY f.Updated_At DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>募款進度</title>

    <!-- Bootstrap、Font Awesome、Chart.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: transparent !important;
        }

        .donation-progress {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .donation-card {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: stretch;
            margin-bottom: 20px;
            background-color: rgb(202, 221, 225);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .donation-card:hover {
            transform: scale(1.03);
        }

        .left-section {
            flex: 2;
        }

        .right-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        .piggy-bank {
            width: 80px;
            cursor: pointer;
            margin-top: 10px;
        }

        .donate-label {
            font-weight: bold;
            margin-top: 5px;
            color: #d63384;
        }

        canvas {
            max-width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">募款進度</h2>

        <div class="donation-progress">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // 計算進度百分比
                    $progress = ($row["Required_Amount"] > 0)
                        ? ($row["Raised_Amount"] / $row["Required_Amount"]) * 100
                        : 0;
                    $progress = round($progress, 2);
                    ?>
                    <div class="donation-card">
                        <!-- 左側區塊 -->
                        <div class="left-section">
                            <h3><?= htmlspecialchars($row["Title"]) ?></h3>
                            <p>募款目標：<?= number_format($row["Required_Amount"], 2) ?> 元</p>
                            <p>目前募得：<?= number_format($row["Raised_Amount"], 2) ?> 元</p>
                            <p>狀態：<?= htmlspecialchars($row["Status"]) ?></p>
                            <p class="text-muted">更新時間：<?= date("Y-m-d H:i:s", strtotime($row["Updated_At"])) ?></p>
                        </div>

                        <!-- 右側區塊 -->
                        <div class="right-section">
                            <canvas id="chart<?= $row["Funding_ID"] ?>" width="150" height="150"></canvas>
                            <div class="text-center">
                                <img src="piggy-bank.png" class="piggy-bank" alt="Piggy Bank" onclick="alert('即將開啟捐款功能')" />
                                <div class="donate-label">點我捐款</div>
                            </div>
                        </div>
                    </div>

                    <script>
                        const ctx<?= $row["Funding_ID"] ?> = document.getElementById('chart<?= $row["Funding_ID"] ?>').getContext('2d');
                        let progress<?= $row["Funding_ID"] ?> = <?= $progress ?>;

                        let color<?= $row["Funding_ID"] ?> = '#28a745';
                        if (progress<?= $row["Funding_ID"] ?> >= 75) {
                            color<?= $row["Funding_ID"] ?> = '#e60000';
                        } else if (progress<?= $row["Funding_ID"] ?> >= 50) {
                            color<?= $row["Funding_ID"] ?> = '#ff6600';
                        } else if (progress<?= $row["Funding_ID"] ?> >= 25) {
                            color<?= $row["Funding_ID"] ?> = '#ffcc00';
                        }

                        new Chart(ctx<?= $row["Funding_ID"] ?>, {
                            type: 'doughnut',
                            data: {
                                labels: ['已募得', '剩餘'],
                                datasets: [{
                                    data: [progress<?= $row["Funding_ID"] ?>, 100 - progress<?= $row["Funding_ID"] ?>],
                                    backgroundColor: [color<?= $row["Funding_ID"] ?>, '#e0e0e0'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: false,
                                cutout: '70%',
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function (tooltipItem) {
                                                return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    </script>
                    <?php
                }
            } else {
                echo "<p class='text-center'>目前沒有募款建議。</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>

</html>
