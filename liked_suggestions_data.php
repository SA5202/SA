<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    session_start();
    require_once 'db_connect.php';

    $viewUserID = $_SESSION['User_ID'];
    $limit = 5;
    $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
    $offset = ($page - 1) * $limit;

    // 頁碼分組設定
    $pagesPerGroup = 5;
    $count_stmt = $link->prepare("
    SELECT COUNT(*) AS total
    FROM Upvote u
    JOIN Suggestion s ON u.Suggestion_ID = s.Suggestion_ID
    WHERE u.User_ID = ? AND u.Is_Upvoted = 1
");
    $count_stmt->bind_param("i", $viewUserID);
    $count_stmt->execute();
    $total_rows = $count_stmt->get_result()->fetch_assoc()['total'];
    $total_pages = ceil($total_rows / $limit);

    $currentGroup = floor(($page - 1) / $pagesPerGroup);
    $startPage = $currentGroup * $pagesPerGroup + 1;
    $endPage = min($startPage + $pagesPerGroup - 1, $total_pages);

    // 撈資料
    $sql = "
    SELECT s.Suggestion_ID, s.Title, s.Updated_At,
        (SELECT COUNT(*) FROM Upvote u2 WHERE u2.Suggestion_ID = s.Suggestion_ID AND u2.Is_Upvoted = 1) AS LikeCount
    FROM Upvote u
    JOIN Suggestion s ON u.Suggestion_ID = s.Suggestion_ID
    WHERE u.User_ID = ? AND u.Is_Upvoted = 1
    ORDER BY s.Updated_At DESC
    LIMIT ? OFFSET ?
";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("iii", $viewUserID, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <table class="table">
        <thead class="table-primary">
            <tr>
                <th class="fw-bold">建言標題</th>
                <th class="fw-bold text-center">更新時間</th>
                <th class="fw-bold text-center">獲得愛心數</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <a class="title" href="suggestion_detail.php?id=<?= $row['Suggestion_ID'] ?>">
                                <?= htmlspecialchars($row["Title"]) ?>
                            </a>
                        </td>
                        <td class="text-center"><?= date('Y-m-d', strtotime($row['Updated_At'])) ?></td>
                        <td class="text-center">
                            <?= ($row['LikeCount'] >= 10000)
                                ? number_format($row['LikeCount'] / 10000, 1) . ' 萬 ❤️'
                                : $row['LikeCount'] . ' ❤️'; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-muted">目前沒有按讚紀錄。</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- 分頁按鈕 -->
    <div class="d-flex justify-content-center mt-4">
        <nav>
            <ul class="pagination custom-pagination">
                <!-- 上一頁 -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link like-page-link" href="#" data-page="<?= max(1, $page - 1) ?>">上一頁</a>
                </li>

                <!-- 上一組頁碼 -->
                <li class="page-item <?= ($currentGroup == 0) ? 'disabled' : '' ?>">
                    <a class="page-link like-page-link" href="#" data-page="<?= max(1, $startPage - $pagesPerGroup) ?>">&laquo; 前 <?= $pagesPerGroup ?> 頁</a>
                </li>

                <!-- 頁碼列表 -->
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link like-page-link" href="#" data-page="<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- 下一組頁碼 -->
                <li class="page-item <?= ($currentGroup >= ceil($total_pages / $pagesPerGroup) - 1) ? 'disabled' : '' ?>">
                    <a class="page-link like-page-link" href="#" data-page="<?= min($total_pages, $endPage + 1) ?>">後 <?= $pagesPerGroup ?> 頁 &raquo;</a>
                </li>

                <!-- 下一頁 -->
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link like-page-link" href="#" data-page="<?= min($total_pages, $page + 1) ?>">下一頁</a>
                </li>
            </ul>
        </nav>
    </div>

</body>

</html>