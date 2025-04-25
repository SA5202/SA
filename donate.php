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

    <!-- 引入Bootstrap樣式 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <style>
        .donation-progress {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        
        .donation-card {
            margin-bottom: 20px;
            background-color:transparent;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .donation-card:hover {
            transform: scale(1.03);
        }

        .progress-bar-container {
            position: relative;
            height: 30px;
            margin-bottom: 15px;
        }

        .progress-bar {
            position: absolute;
            height: 100%;
            width: 0;
            background-color: #28a745;
            transition: width 0.5s ease;
        }

        .progress-text {
            position: absolute;
            width: 100%;
            text-align: center;
            font-weight: bold;
            color: #fff;
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
                    // 計算進度百分比，避免目標金額為 0 時出現錯誤
                    if ($row["Required_Amount"] > 0) {
                        $progress = ($row["Raised_Amount"] / $row["Required_Amount"]) * 100;
                    } else {
                        $progress = 0; // 目標金額為 0 時進度為 0
                    }

                    // 確保進度條至少顯示 1%（避免 0% 時完全隱藏進度條）
                    $progress = max($progress, 1); // 防止進度條為 0%

                    ?>
                    <div class="donation-card">
                        <h3><?= htmlspecialchars($row["Title"]) ?></h3>
                        <p>募款目標：<?= number_format($row["Required_Amount"], 2) ?> 元</p>
                        <p>目前募得：<?= number_format($row["Raised_Amount"], 2) ?> 元</p>
                        <p>狀態：<?= htmlspecialchars($row["Status"]) ?></p>
                        
                        <!-- 進度條 -->
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: <?= $progress ?>%;"></div>
                            <div class="progress-text"><?= number_format($progress, 2) ?>%</div>
                        </div>

                        <p class="text-muted">更新時間：<?= date("Y-m-d H:i:s", strtotime($row["Updated_At"])) ?></p>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-center'>目前沒有募款建議。</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <script>
        // 檢查返回頂部的按鈕顯示與隱藏
        const backToTopBtn = document.getElementById('backToTopBtn');
        const iframe = document.querySelector('iframe[name="contentFrame"]');

        backToTopBtn.addEventListener('click', () => {
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });

        iframe.addEventListener('load', () => {
            const iframeWindow = iframe.contentWindow;

            function toggleBackToTop() {
                const scrollTop = iframeWindow.scrollY || iframeWindow.pageYOffset;
                backToTopBtn.style.display = scrollTop > 200 ? 'block' : 'none';
            }
            toggleBackToTop();
            iframeWindow.addEventListener('scroll', toggleBackToTop);
        });
    </script>
</body>

</html>
