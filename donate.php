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

// 查詢募款進度及建言描述
$sql = "
    SELECT f.Funding_ID, s.Title, s.Description, f.Required_Amount, f.Raised_Amount, f.Status, f.Updated_At
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
            border-radius: 12px;
            padding: 20px;
            transition: transform 0.3s ease;
            background-color:rgb(202, 220, 230);
            border: 1px solid #ddd;
        }

        .donation-card:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }


        .left-card {
            margin-right: 15px;
            flex: 1;
            padding: 20px;
            margin: 10px;
            border-radius: 12px;
            background-color: transparent;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .right-card {
            margin-left: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            padding: 20px;
            margin: 10px;
            border-radius: 12px;
            background-color:rgba(108, 139, 157, 0.29);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h3 {
            font-weight: 600;
            color: #333;
            text-align: left;
        }

        p {
            text-align: left;
            font-size: 1rem;
            color: #555;
            line-height: 1.6;
        }

        .donate-label {
            font-weight: bold;
            margin-top: 10px;
            color: #d63384;
            font-size: 1.1rem;
            cursor: pointer;
        }

        canvas {
            max-width: 100%;
            margin-top: 20px;
        }

        .description {
            font-size: 1rem;
            color: #555;
            margin-top: 25px;
            overflow-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            flex-grow: 1;
        }

        .amount-section {
            text-align: left;
            margin-top: 20px;
        }

        .amount-section p {
            margin: 5px 0;
            font-size: 1.1rem;
        }

        .amount-section .status {
            font-weight: bold;
            color: rgb(55, 78, 116);
        }

        .status {
            font-size: 1rem;
            color: #888;
        }

        .piggy-bank {
            width: 90px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .amount-section .text-muted {
            font-size: 0.9rem;
            color: #777;
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
                        <!-- 左側卡片: 標題和內文 -->
                        <div class="left-card">
                            <h3><?= htmlspecialchars($row["Title"]) ?></h3>
                            <!-- 顯示建言描述 -->
                            <?php
                            $maxLength = 200; // 限制顯示最大 200 字
                            $description = $row["Description"];
                            if (!empty($description)) {
                                $shortDescription = mb_strlen($description) > $maxLength 
                                    ? mb_substr($description, 0, $maxLength) . '...' 
                                    : $description;
                            ?>
                                <p class="description"><?= nl2br(htmlspecialchars($shortDescription)) ?></p>
                            <?php } ?>
                        </div>

                        <!-- 右側卡片: 圓餅圖和募款金額 -->
                        <div class="right-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- 右側金額顯示區 -->
                                <div class="amount-section">
                                    <p>募款目標：<?= number_format($row["Required_Amount"], 0) ?> 元</p>
                                    <p>目前募得：<?= number_format($row["Raised_Amount"], 0) ?> 元</p>
                                    <p class="status">狀態：<?= htmlspecialchars($row["Status"]) ?></p>
                                    <p class="text-muted">更新時間：<?= date("Y-m-d H:i:s", strtotime($row["Updated_At"])) ?></p>
                                </div>

                                <!-- 圓餅圖 -->
                                <div class="chart-container" style="flex: 0 0 auto; margin-left: 20px;">
                                    <canvas id="chart<?= $row["Funding_ID"] ?>" width="150" height="150"></canvas>
                                    <!-- 小豬撲滿捐款按鈕 -->
                                    <div class="text-center mt-3">
                                        <i class="fas fa-piggy-bank donate-label" onclick="alert('即將開啟捐款功能')">點我捐款</i>
                                    </div>
                                </div>

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
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(tooltipItem) {
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