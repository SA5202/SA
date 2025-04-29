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

// 查詢所有募款建議
$sql = "
    SELECT f.Funding_ID, s.Title, s.Description, f.Required_Amount, f.Raised_Amount, f.Status, f.Updated_At
    FROM FundingSuggestion f
    JOIN Suggestion s ON f.Suggestion_ID = s.Suggestion_ID
    ORDER BY f.Updated_At DESC
";
$result = $conn->query($sql);

$ongoing = [];
$completed = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // 判斷是否達標或已結束
        $isCompleted = ($row["Raised_Amount"] >= $row["Required_Amount"]) || $row["Status"] === "已結束";

        // 如果已達標，更新資料庫中的狀態為 '已完成'
        if ($isCompleted) {
            // 更新狀態為已完成
            $updateSql = "UPDATE FundingSuggestion SET Status = '已完成' WHERE Funding_ID = " . $row["Funding_ID"];
            $conn->query($updateSql);

            // 將已完成的建議加入 completed 陣列
            $completed[] = $row;
        } else {
            // 進行中的募款
            $ongoing[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>募款進度 | 輔仁大學愛校建言捐款系統</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: transparent !important;
            font-family: "Noto Serif TC", serif;
        }

        h2 {
            font-weight: 750;
            text-align: left;
            margin-top: 30px;
        }

        .donation-card {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: stretch;
            margin-bottom: 20px;
            border-radius: 12px;
            padding: 20px;
            background-color: rgb(224, 235, 241);
            border: 1px solid #ddd;
            transition: transform 0.3s ease;
        }

        .donation-card:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .left-card,
        .right-card {
            flex: 1;
            padding: 20px;
            margin: 10px;
            border-radius: 12px;
            background-color: transparent;
        }

        .right-card {
            background-color: rgba(108, 139, 157, 0.29);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            align-items: center;
        }

        .description {
            font-size: 1rem;
            color: #555;
            margin-top: 25px;
            max-height: 180px;
            word-break: break-word;
            flex-grow: 1;
        }

        .amount-section p {
            margin: 5px 0;
            font-size: 1.1rem;
        }

        .amount-section .status {
            font-weight: bold;
            color: rgb(55, 78, 116);
        }

        .text-muted {
            font-size: 0.9rem;
            color: #777;
        }

        .donate-label {
            font-weight: bold;
            color: #d63384;
            font-size: 1.1rem;
            cursor: pointer;
        }

        .action-btn {
            margin-top: 10px;
        }

        /* 自定義按鈕樣式 */
        .action-btn button {
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 100px;
            transition: all 0.3s ease;
        }

        .action-btn button.btn-warning {
            background-color:rgb(173, 209, 117);
            color: white;
        }

        .action-btn button.btn-warning:hover {
            background-color:rgb(139, 173, 76);
            transform: translateY(-2px);
        }

        .action-btn button.btn-danger {
            background-color:rgb(219, 101, 101);
            color: white;
        }

        .action-btn button.btn-danger:hover {
            background-color:rgb(209, 57, 72);
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- 正在進行 -->
        <h2><i class="fas fa-coins me-2"></i>募款進行中</h2>

        <div class="donation-progress">
            <?php if (!empty($ongoing)) {
                foreach ($ongoing as $row) {
                    $progress = ($row["Required_Amount"] > 0)
                        ? ($row["Raised_Amount"] / $row["Required_Amount"]) * 100 : 0;
                    $progress = round($progress, 2);
            ?>
                    <div class="donation-card">
                        <div class="left-card">
                            <h3><?= htmlspecialchars($row["Title"]) ?></h3>
                            <?php
                            $desc = $row["Description"];
                            $short = mb_strlen($desc) > 120 ? mb_substr($desc, 0, 120) . '...' : $desc;
                            ?>
                            <p class="description"><?= nl2br(htmlspecialchars($short)) ?></p>
                            <div class="action-btn">
                                <button class="btn btn-warning" onclick="window.location.href='edit_funding.php?funding_id=<?= $row['Funding_ID'] ?>'">編輯金額進度</button>
                                <button class="btn btn-danger" onclick="deleteFunding(<?= $row['Funding_ID'] ?>)">刪除</button>
                            </div>
                        </div>
                        <div class="right-card d-flex justify-content-between">
                            <div class="amount-section">
                                <p>募款目標：<?= number_format($row["Required_Amount"]) ?> 元</p>
                                <p>目前募得：<?= number_format($row["Raised_Amount"]) ?> 元</p>
                                <p class="status">狀態：<?= htmlspecialchars($row["Status"]) ?></p>
                                <p class="text-muted">更新時間：<?= date("Y-m-d H:i:s", strtotime($row["Updated_At"])) ?></p>
                            </div>
                            <div class="chart-container" style="flex: 0 0 auto; margin-left: 20px;">
                                <canvas id="chart<?= $row["Funding_ID"] ?>" width="150" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                    <script>
                        const ctx<?= $row["Funding_ID"] ?> = document.getElementById('chart<?= $row["Funding_ID"] ?>').getContext('2d');
                        let progress<?= $row["Funding_ID"] ?> = <?= $progress ?>;
                        let color<?= $row["Funding_ID"] ?> = '#28a745';
                        if (progress<?= $row["Funding_ID"] ?> >= 75) color<?= $row["Funding_ID"] ?> = '#e60000';
                        else if (progress<?= $row["Funding_ID"] ?> >= 50) color<?= $row["Funding_ID"] ?> = '#ff6600';
                        else if (progress<?= $row["Funding_ID"] ?> >= 25) color<?= $row["Funding_ID"] ?> = '#ffcc00';

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
                                            label: function(t) {
                                                return t.label + ': ' + t.raw + '%';
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        function deleteFunding(fundingID) {
                            if (confirm('確定要刪除這筆募款建言嗎？')) {
                                window.location.href = 'delete_funding.php?funding_id=' + fundingID;
                            }
                        }
                    </script>
            <?php }
            } else {
                echo "<p class='text-center'>目前沒有進行中的募款建言。</p>";
            } ?>
        </div>

        <!-- 已結束 -->
        <h2><i class="fas fa-check-circle me-2 text-success"></i>募款已完成</h2>

        <div class="donation-progress">
            <?php if (!empty($completed)) {
                foreach ($completed as $row) {
                    $progress = ($row["Required_Amount"] > 0)
                        ? ($row["Raised_Amount"] / $row["Required_Amount"]) * 100 : 0;
                    $progress = round($progress, 2);
            ?>
                    <div class="donation-card">
                        <div class="left-card">
                            <h3><?= htmlspecialchars($row["Title"]) ?></h3>
                            <?php
                            $desc = $row["Description"];
                            $short = mb_strlen($desc) > 120 ? mb_substr($desc, 0, 120) . '...' : $desc;
                            ?>
                            <p class="description"><?= nl2br(htmlspecialchars($short)) ?></p>
                            <div class="action-btn">
                                <button class="btn btn-warning" onclick="window.location.href='edit_funding.php?funding_id=<?= $row['Funding_ID'] ?>'">編輯募款進度</button>
                                <button class="btn btn-danger" onclick="deleteFunding(<?= $row['Funding_ID'] ?>)">刪除</button>
                            </div>
                        </div>
                        <div class="right-card d-flex justify-content-between">
                            <div class="amount-section">
                                <p>募款目標：<?= number_format($row["Required_Amount"]) ?> 元</p>
                                <p>目前募得：<?= number_format($row["Raised_Amount"]) ?> 元</p>
                                <p class="status">狀態：已完成</p>
                                <p class="text-muted">更新時間：<?= date("Y-m-d H:i:s", strtotime($row["Updated_At"])) ?></
                                        p>
                            </div>
                            <div class="chart-container" style="flex: 0 0 auto; margin-left: 20px;"> <canvas id="chart<?= $row["Funding_ID"] ?>" width="150" height="150"></canvas> </div>
                        </div>
                    </div> <?php }
                    } else {
                        echo "<p class='text-center'>目前沒有已完成的募款建言。</p>";
                    } ?>
        </div>
    </div>

</body>

</html>