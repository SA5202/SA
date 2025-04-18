<?php
require_once "db_connect.php";

$sql = "
SELECT f.Funding_ID, f.Required_Amount, f.Raised_Amount, f.Status, s.Title, s.Suggestion_ID
FROM FundingSuggestion f
JOIN Suggestion s ON f.Suggestion_ID = s.Suggestion_ID
WHERE f.Status != '已完成'
ORDER BY f.Updated_At DESC
";

$result = $link->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>待捐款建言列表</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="p-4">
    <h3 class="mb-4">待捐款建言</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>建言標題</th>
                <th>目標金額</th>
                <th>已募金額</th>
                <th>狀態</th>
                <th>前往捐款</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Title']) ?></td>
                    <td>$<?= number_format($row['Required_Amount'], 2) ?></td>
                    <td>$<?= number_format($row['Raised_Amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['Status']) ?></td>
                    <td><a href="donate.php?funding_id=<?= $row['Funding_ID'] ?>" class="btn btn-success btn-sm">捐款</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>

</html>