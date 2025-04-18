<?php
require_once "db_connect.php";

$suggestion_id = $_GET['id'] ?? 0;

$suggestion_sql = "
SELECT s.Suggestion_ID, s.Title, s.Description, s.Updated_At, 
       f.Facility_Type, b.Building_Name, 
       (SELECT COUNT(*) FROM Upvote u WHERE u.Suggestion_ID = s.Suggestion_ID AND u.Is_Upvoted = 1) AS LikeCount
FROM Suggestion s
JOIN Facility f ON s.Facility_ID = f.Facility_ID
JOIN Building b ON s.Building_ID = b.Building_ID
WHERE s.Suggestion_ID = $suggestion_id
";

$suggestion_result = $link->query($suggestion_sql);
$suggestion = $suggestion_result->fetch_assoc();

$funding_sql = "
SELECT f.Funding_ID, f.Required_Amount, f.Raised_Amount, f.Status
FROM FundingSuggestion f
WHERE f.Suggestion_ID = $suggestion_id
";

$funding_result = $link->query($funding_sql);
$funding = $funding_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>建言詳細資料 | 輔仁大學愛校建言捐款系統</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            max-width: 85%;
            margin: 0 auto;
            padding: 30px;
            font-size: 1.1rem;
            line-height: 1.8;
            background-image: url('https://www.transparenttextures.com/patterns/brick-wall.png');
            /* 花紋背景 */
            background-repeat: repeat;
            background-color: #fefefe;
            /* 淡背景底色搭配花紋 */
            overflow-x: hidden;
        }

        .card {
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        h3,
        h4 {
            color: #343a40;
        }

        .btn-back {
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="p-4">

    <div class="container">
        <!-- 返回按鈕 -->
        <a href="suggestions.php" class="btn btn-secondary btn-back">&larr; 返回建言總覽</a>

        <!-- 標題 -->
        <h3 class="mb-4">建言詳細資料</h3>

        <!-- 建言卡片 -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><?= htmlspecialchars($suggestion['Title']) ?></h5>
            </div>
            <div class="card-body">
                <p><strong>建議描述:</strong></p>
                <p><?= nl2br(htmlspecialchars($suggestion['Description'])) ?></p>

                <p><strong>關聯設施:</strong> <?= htmlspecialchars($suggestion['Facility_Type']) ?></p>
                <p><strong>關聯建築物:</strong> <?= htmlspecialchars($suggestion['Building_Name']) ?></p>
                <p><strong>更新時間:</strong> <?= $suggestion['Updated_At'] ?></p>
                <p><strong>喜歡數:</strong> <?= $suggestion['LikeCount'] ?></p>
            </div>
        </div>

        <!-- 捐款資訊 -->
        <h4 class="mb-3">捐款資訊</h4>
        <?php if ($funding): ?>
            <?php if ($funding['Required_Amount'] == 0): ?>
                <div class="alert alert-info">
                    此建言<strong>無須捐款</strong>，但仍會依照流程進行處理。
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>目前狀態</th>
                        <td><?= htmlspecialchars($funding['Status']) ?></td>
                    </tr>
                </table>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>目標金額</th>
                                <th>已募金額</th>
                                <th>狀態</th>
                                <th>前往捐款</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>$<?= number_format($funding['Required_Amount'], 2) ?></td>
                                <td>$<?= number_format($funding['Raised_Amount'], 2) ?></td>
                                <td><?= htmlspecialchars($funding['Status']) ?></td>
                                <td>
                                    <a href="donate.php?funding_id=<?= $funding['Funding_ID'] ?>" class="btn btn-success btn-sm">捐款</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-muted">目前沒有進行中的捐款。</p>
        <?php endif; ?>
    </div>

</body>

</html>