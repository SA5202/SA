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
$paused = [];
$completed = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // 判斷是否為已完成
        $isCompleted = ($row["Raised_Amount"] >= $row["Required_Amount"]) || $row["Status"] === "已結束";

        if ($isCompleted) {
            // 自動更新為已完成
            $updateSql = "UPDATE FundingSuggestion SET Status = '已完成' WHERE Funding_ID = " . $row["Funding_ID"];
            $conn->query($updateSql);

            $row["Status"] = "已完成"; // 確保畫面顯示正確
            $completed[] = $row;

        } elseif ($row["Status"] === "暫停") {
            $paused[] = $row;

        } else {
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
    </style>
</head>

<body>
    <script>
        const isLoggedIn = <?= isset($_SESSION['User_ID']) ? 'true' : 'false' ?>;
    </script>

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
                                <div class="text-center mt-3">
                                    <i class="fas fa-piggy-bank donate-label" onclick="handleDonate(<?= $row['Funding_ID'] ?>)">點我捐款</i>

                                </div>
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
                    </script>
            <?php }
            } else {
                echo "<p class='text-center'>目前沒有進行中的募款建言。</p>";
            } ?>
        </div>
        <!-- 暫停中的募款建言 -->
        <h3><i class="icon fas fa-pause-circle me-2 text-warning"></i> 暫停中的募款建言</h3>
        <div class="donation-progress">
            <?php if (empty($paused)) : ?>
                <p>目前沒有暫停中的募款建言。</p>
            <?php else : ?>
                <?php foreach ($paused as $row) :
                    $progress = ($row["Required_Amount"] > 0)
                        ? ($row["Raised_Amount"] / $row["Required_Amount"]) * 100 : 0;
                    $progress = round($progress, 2);
                ?>
                    <div class="donation-card">
                        <div class="left-card">
                            <h4><?= htmlspecialchars($row["Title"]) ?></h4>
                            <?php
                            $desc = $row["Description"];
                            $short = mb_strlen($desc) > 120 ? mb_substr($desc, 0, 120) . '...' : $desc;
                            ?>
                            <p class="description"><?= nl2br(htmlspecialchars($short)) ?></p>
                        </div>
                        <div class="right-card d-flex justify-content-between">
                            <div class="amount-section">
                                <p>目標金額： NT$ <?= number_format($row["Required_Amount"]) ?></p>
                                <p>當前募得： NT$ <?= number_format($row["Raised_Amount"]) ?></p>
                                <p class="status">狀態：<?= htmlspecialchars($row["Status"]) ?></p>
                                <p class="text-muted">更新時間：<?= date("Y-m-d H:i", strtotime($row["Updated_At"])) ?></p>
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
                                    legend: { display: false },
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
                    </script>
                <?php endforeach; ?>
            <?php endif; ?>
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
                        </div>
                        <div class="right-card d-flex justify-content-between">
                            <div class="amount-section">
                                <p>募款目標：<?= number_format($row["Required_Amount"]) ?> 元</p>
                                <p>目前募得：<?= number_format($row["Raised_Amount"]) ?> 元</p>
                                <p class="status">狀態：已完成</p>
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
                    </script>
            <?php }
            } else {
                echo "<p class='text-center'>目前沒有已結束的建議。</p>";
            } ?>
        </div>
    </div>
    <script>
        function handleDonate(fundingID) {
            if (!isLoggedIn) {
                // 顯示自訂警告框（Bootstrap Modal or confirm）
                if (confirm("請先登入才能捐款。\n是否前往登入頁面？")) {
                    window.location.href = "login.php"; // 更換成你的登入頁面路徑
                }
            } else {
                window.location.href = "donation_page.php?funding_id=" + fundingID;
            }
        }
    </script>

</body>

</html>