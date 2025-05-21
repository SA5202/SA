<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    /* 清除 Bootstrap 預設樣式 */
    .custom-pagination .page-link {
        background-color: transparent;
        border: none;
        color: #333;
        padding: 8px 14px;
        margin: 0 4px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: bold;
        transition: all 0.3s;
    }

    /* 滑鼠滑過頁碼 */
    .custom-pagination .page-link:hover {
        background-color: #f5f5f5;
        color: rgb(83, 132, 186);
    }

    /* 當前頁碼樣式 */
    .custom-pagination .page-item.active .page-link {
        background-color: rgb(104, 139, 176);
        color: white;
        border-radius: 10px;
    }

    /* 禁用按鈕（上一頁/下一頁） */
    .custom-pagination .page-item.disabled .page-link {
        color: #ccc;
        pointer-events: none;
    }

    /* 分頁列容器居中 */
    .pagination.custom-pagination {
        justify-content: center;
    }

    title {
        color: #003153;
        font-weight: bold;
        text-decoration: none;
    }
</style>

<body>

    <?php

    session_start();

    $sessionUserID = $_SESSION['User_ID'] ?? null;      // 注意大小寫
    $sessionUserType = $_SESSION['User_Type'] ?? null;  // 注意大小寫

    require_once 'db_connect.php'; // 或你的連線檔名稱

    // 每頁顯示筆數
    $limit = 5;

    // 取得目前頁數，預設為第1頁
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1); // 避免頁數小於1

    // 計算起始資料索引
    $offset = ($page - 1) * $limit;

    // 計算總筆數
    $count_sql = "SELECT COUNT(*) AS total FROM Suggestion";
    $count_result = $link->query($count_sql);
    $total_rows = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_rows / $limit);

    // 查詢目前頁的資料
    $sql = "SELECT s.Suggestion_ID, s.Title, s.Updated_At, s.User_ID,
               (SELECT COUNT(*) FROM Upvote u WHERE u.Suggestion_ID = s.Suggestion_ID AND u.Is_Upvoted = 1) AS LikeCount
        FROM Suggestion s
        ORDER BY s.Updated_At DESC
        LIMIT $limit OFFSET $offset";
    $result = $link->query($sql);
    ?>

    <table class="table">
        <thead class="table-primary">
            <tr>
                <th class="fw-bold">建言標題</th>
                <th class="fw-bold text-center">更新時間</th>
                <th class="fw-bold text-center">獲得愛心數</th>
                <th class="fw-bold text-center">編輯建言</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <a class="title" href="suggestion_detail.php?id=<?= $row['Suggestion_ID'] ?>">
                                <?= htmlspecialchars($row['Title']) ?>
                            </a>
                        </td>
                        <td class="text-center update-time">
                            <span class="update"><?= date('Y-m-d', strtotime($row['Updated_At'])) ?></span>
                        </td>
                        <td class="text-center like-count">
                            <span class="custom-badge">
                                <?= ($row['LikeCount'] >= 10000)
                                    ? number_format($row['LikeCount'] / 10000, 1) . ' 萬 ❤️'
                                    : $row['LikeCount'] . ' ❤️'; ?>
                            </span>
                        </td>
                        <td class="text-center edit-action">
                            <?php if ($row['User_ID'] == $sessionUserID): ?>
                                <!-- 本人：只顯示編輯按鈕（編輯頁中包含刪除功能） -->
                                <form action="suggestion_update.php" method="get" style="display:inline;">
                                    <input type="hidden" name="Suggestion_ID" value="<?= $row['Suggestion_ID'] ?>">
                                    <button type="submit" class="pretty-btn">
                                        <i class="fas fa-pen-to-square"></i> 修改
                                    </button>
                                </form>
                            <?php elseif ($sessionUserType === 'admin'): ?>
                                <!-- 管理員：只能刪除 -->
                                <form action="dblink2.php?method=delete" method="post" onsubmit="return confirm('管理員確定要刪除這個建言嗎？');" style="display:inline;">
                                    <input type="hidden" name="suggestion_id" value="<?= $row['Suggestion_ID'] ?>">
                                    <button type="submit" class="pretty-btn">
                                        <i class="fas fa-pen-to-square"></i> 刪除
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-muted">目前沒有建言紀錄。</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php
    $pagesPerGroup = 5;

    // 計算目前頁碼的群組 index（從0開始）
    $currentGroup = intdiv($page - 1, $pagesPerGroup);

    // 計算總群組數量
    $totalGroups = intdiv($total_pages - 1, $pagesPerGroup) + 1;

    // 這組的起始與結束頁碼
    $startPage = $currentGroup * $pagesPerGroup + 1;
    $endPage = min($startPage + $pagesPerGroup - 1, $total_pages);
    ?>

    <div class="d-flex justify-content-center mt-4">
        <nav>
            <ul class="pagination custom-pagination">

                <!-- 上一頁 -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">上一頁</a>
                </li>

                <!-- 上一組頁碼 -->
                <li class="page-item <?= ($currentGroup == 0) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= max(1, $startPage - $pagesPerGroup) ?>">
                        &laquo; 前 <?= $pagesPerGroup ?> 頁
                    </a>
                </li>

                <!-- 頁碼 -->
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a href="#" class="donation-page-link page-link" data-page="<?= $i ?>"><?= $i ?></a>

                    </li>
                <?php endfor; ?>

                <!-- 下一組頁碼 -->
                <li class="page-item <?= ($currentGroup >= $totalGroups - 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= min($total_pages, $endPage + 1) ?>">
                        後 <?= $pagesPerGroup ?> 頁 &raquo;
                    </a>
                </li>

                <!-- 下一頁 -->
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= min($total_pages, $page + 1) ?>">下一頁</a>
                </li>
            </ul>
        </nav>
    </div>
</body>

</html>