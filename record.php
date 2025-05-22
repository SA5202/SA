<?php
session_start();
if (!isset($_SESSION['User_Name'])) {
    header("Location: login.php");
    exit();
}

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die('資料庫連接失敗: ' . $link->connect_error);
}

$sessionUserID = $_SESSION['User_ID'];
$sessionUserType = $_SESSION['User_Type'];

if (isset($_GET['id'])) {
    $viewUserID = intval($_GET['id']);
    if ($viewUserID !== $sessionUserID && $sessionUserType !== 'admin') {
        die('無權限查看此使用者資訊。');
    }
} else {
    $viewUserID = $sessionUserID;
}



$sql = "
SELECT s.Suggestion_ID, s.Title, s.Description, s.Updated_At, s.User_ID, u.User_Name,
       f.Facility_Type, b.Building_Name,
       (SELECT COUNT(*) FROM Upvote u2 WHERE u2.Suggestion_ID = s.Suggestion_ID AND u2.Is_Upvoted = 1) AS LikeCount,
       (SELECT Status FROM Progress p WHERE p.Suggestion_ID = s.Suggestion_ID ORDER BY Updated_At DESC LIMIT 1) AS LatestStatus
FROM Suggestion s
JOIN Facility f ON s.Facility_ID = f.Facility_ID
JOIN Building b ON s.Building_ID = b.Building_ID
JOIN Useraccount u ON s.User_ID = u.User_ID
WHERE s.User_ID = ?
ORDER BY s.Updated_At DESC
";


$stmt = $link->prepare($sql);
$stmt->bind_param("i", $viewUserID);
$stmt->execute();
$result = $stmt->get_result();

$sql_user = "SELECT * FROM useraccount WHERE User_ID = ?";
$stmt_user = $link->prepare($sql_user);
$stmt_user->bind_param("i", $viewUserID);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$row_user = $result_user->fetch_assoc();
?>

<!DOCTYPE html>

<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>個人檔案 | 輔仁大學愛校建言捐款系統</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/e19963bd49.js" crossorigin="anonymous"></script>
    <style>
        body {
            max-width: 85%;
            margin: 0 auto;
            padding: 30px;
            font-size: 1.1rem;
            font-family: "Noto Serif TC", serif;
            line-height: 1.8;
            background-color: transparent;
            overflow-x: hidden;
            color: #333;
        }

        .icon {
            font-size: 1.5rem;
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 10px;
            display: inline-block;
        }

        h3 {
            margin: 30px 0;
            font-weight: bold;
        }

        .left {
            text-align: left;
            font-size: 1.05rem;
            font-weight: 600;
            color: #555;
        }

        .password-display-wrapper {
            position: relative;
            width: 200px;
        }

        #passwordDisplay {
            width: 100%;
            padding-right: 35px;
            font-weight: bold;
            border: none;
            background: transparent;
            font-size: 16px;
            color: #333;
            pointer-events: none;
        }

        #togglePassword {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
        }

        .custom-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 40px;
            font-size: 1rem;
            font-weight: 750;
            background: linear-gradient(to right, rgb(139, 186, 224), rgb(69, 109, 133));
            color: #fff;
            border-radius: 15px;
            text-decoration: none;
        }

        .custom-btn:hover {
            opacity: 0.6;
        }

        .pretty-btn {
            background: linear-gradient(to right, rgb(139, 186, 224), rgb(69, 109, 133));
            text-decoration: none;
            border: none;
            color: white;
            padding: 0.2rem 20px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 750;
        }

        .pretty-btn:hover {
            opacity: 0.6;
        }

        .pretty-btn i {
            margin-right: 6px;
        }

        .custom-badge {
            background: linear-gradient(to right, rgb(218, 240, 249), rgb(197, 226, 239));
            color: #2a4d69;
            font-size: 0.9rem;
            padding: 0.3rem 1.2rem;
            border-radius: 12px;
            font-weight: bold;
        }

        .table-responsive {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px 20px;
            border-radius: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .table-responsive:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }


        .table th {
            background-color: #f1f3f5;
            color: #555;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .table {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 20px;
            overflow: hidden;
            width: 100%;
            border-radius: 25px;
            border: 2px solid #dee2e6;
            border-collapse: separate;
            overflow: hidden;
            border-spacing: 0;
        }

        .table:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }


        .table th,
        .table td {
            padding: 10px 40px;
            border-radius: 0px;
            border-bottom: none;
        }

        .table-primary {
            background-color: #e9f5ff;
        }

        .title {
            color: #003153;
            font-weight: bold;
            text-decoration: none;
        }

        .table .update-time,
        .table .like-count,
        .table .edit-action {
            text-align: center;
            vertical-align: middle;
        }

        .update {
            font-size: 0.9rem;
            font-weight: bold;
            color: gray;
        }

        .text-muted {
            text-align: center;
            font-size: 1.05rem;
            font-weight: 600;
        }

        .funding-status-label {
            display: inline-block;
            padding: 0.5em 1.3em;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 4px;
            color: white;
        }

        .funding-status-進行中 {
            background: linear-gradient(90deg, rgb(192, 222, 132), rgb(139, 173, 79));
        }

        .funding-status-已暫停 {
            background: linear-gradient(90deg, rgb(255, 237, 150), rgb(244, 212, 54));
        }

        .funding-status-已完成 {
            background: linear-gradient(90deg, rgb(240, 165, 165), rgb(210, 82, 82));
        }

        .table td.highlight-title {
            color: #003153;
            font-weight: bold;
        }
    </style>


</head>

<body>
    <?php if ($sessionUserType == 'admin'): ?>
        <h3><i class="icon fas fa-user"></i> <?= htmlspecialchars($row_user['User_Name']) ?> 的基本資訊</h3>
    <?php else: ?>
        <h3><i class="icon fas fa-user"></i> 帳戶基本資訊</h3>
    <?php endif; ?>
    <div class="table-responsive">
        <table>
            <tbody>
                <?php if ($row_user): ?>
                    <tr>
                        <?php
                        $default_avatar = 'https://i.pinimg.com/736x/15/46/d1/1546d15ce5dd2946573b3506df109d00.jpg';

                        $avatar_url = !empty($row_user['Avatar'])
                            ? htmlspecialchars($row_user['Avatar'])
                            : $default_avatar;

                        // 加上時間戳防止快取
                        $avatar_url .= '?t=' . time();
                        ?>
                        <td rowspan='6'>
                            <img src="<?= $avatar_url ?>"
                                onerror="this.src='<?= $default_avatar ?>'"
                                style='border-radius: 50%; width: 200px; height: 200px; margin: 20px 50px;'>
                        </td>

                        <td class='left'>帳號： <?= htmlspecialchars($row_user['User_Name']) ?></td>
                    </tr>
                    <tr>
                        <td class='left'>暱稱： <?= htmlspecialchars($row_user['Nickname']) ?></td>
                    </tr>
                    <tr>
                        <td class='left'>使用者 ID： 0000000000<?= $row_user['User_ID'] ?></td>
                    </tr>
                    <tr>
                        <td class='left'>Email： <?= htmlspecialchars($row_user['Email']) ?></td>
                    </tr>
                    <tr>
                        <td class="left">
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <span>密碼： </span>
                                <div class="password-display-wrapper">
                                    <input type="text" id="passwordDisplay" value="••••••••••" readonly>
                                    <button type="button" id="togglePassword" onclick="togglePassword()">
                                        <i id="eyeIcon" class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php if ($viewUserID === $sessionUserID): ?>
                        <tr>
                            <td colspan='2' class='left'>
                                <a href='update.php?method=update&User_Name=<?= urlencode($row_user['User_Name']) ?>' class='custom-btn'>
                                    <i class='fas fa-pen-to-square'></i> 編輯個人檔案
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php else: ?>
                    <tr>
                        <td colspan='2' align='center'>找不到使用者資料</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($sessionUserType == 'admin'): ?>
        <h3><i class="icon fas fa-clipboard-list"></i> <?= htmlspecialchars($row_user['User_Name']) ?> 的建言記錄</h3>
    <?php else: ?>
        <h3><i class="icon fas fa-clipboard-list"></i> 我的建言紀錄</h3>
    <?php endif; ?>

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
                                <?= htmlspecialchars($row["Title"]) ?>
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
    // 撈取使用者按讚過的建言
    $sql_likes = "
    SELECT s.Suggestion_ID, s.Title, s.Updated_At,
       (SELECT COUNT(*) FROM Upvote u2 WHERE u2.Suggestion_ID = s.Suggestion_ID AND u2.Is_Upvoted = 1) AS LikeCount
FROM Upvote u
JOIN Suggestion s ON u.Suggestion_ID = s.Suggestion_ID
WHERE u.User_ID = ? AND u.Is_Upvoted = 1
ORDER BY s.Updated_At DESC
";

    $stmt_likes = $link->prepare($sql_likes);
    if (!$stmt_likes) {
        die("按讚查詢準備失敗: " . $link->error);
    }

    $stmt_likes->bind_param("i", $viewUserID);
    $stmt_likes->execute();
    $likes_result = $stmt_likes->get_result();
    ?>

    <?php if ($sessionUserType == 'admin'): ?>
        <h3><i class="icon fas fa-clipboard-list"></i> <?= htmlspecialchars($row_user['User_Name']) ?> 的按讚記錄</h3>
    <?php else: ?>
        <h3><i class="icon fas fa-clipboard-list"></i> 我的按讚紀錄</h3>
    <?php endif; ?>

    <table class="table">
        <thead class="table-primary">
            <tr>
                <th class="fw-bold">建言標題</th>
                <th class="fw-bold text-center">更新時間</th>
                <th class="fw-bold text-center">獲得愛心數</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($likes_result->num_rows > 0): ?>
                <?php while ($row = $likes_result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <a class="title" href="suggestion_detail.php?id=<?= $row['Suggestion_ID'] ?>">
                                <?= htmlspecialchars($row["Title"]) ?>
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
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-muted">目前沒有按讚紀錄。</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php
    $stmt_likes->close();
    ?>


    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/db_connect.php'; // 使用 $link

    if (!isset($viewUserID)) {
        echo "找不到要查看的使用者。";
        exit;
    }

    $user_id = $viewUserID;


    $sql = "SELECT 
    d.Donation_Amount,
    d.Status AS Donation_Status,
    d.Donation_Date,
    s.Suggestion_ID,
    s.Title AS Funding_Title,
    pm.Method_Name AS Payment_Method,
    fs.Required_Amount,
    fs.Raised_Amount,
    fs.Status AS Funding_Status,
    fs.Updated_At
FROM Donation d
LEFT JOIN FundingSuggestion fs ON d.Funding_ID = fs.Funding_ID
LEFT JOIN Suggestion s ON fs.Suggestion_ID = s.Suggestion_ID
LEFT JOIN PaymentMethod pm ON d.Method_ID = pm.Method_ID
WHERE d.User_ID = ?
ORDER BY d.Donation_Date DESC";

    $stmt = $link->prepare($sql);
    if (!$stmt) {
        die("SQL 準備失敗: " . $link->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <?php if ($sessionUserType == 'admin'): ?>
        <h3><i class="icon fas fa-donate"></i> <?= htmlspecialchars($row_user['User_Name']) ?> 的捐款紀錄</h3>
    <?php else: ?>
        <h3><i class="icon fas fa-donate"></i> 我的捐款紀錄</h3>
    <?php endif; ?>

    <table class="table">
        <thead class="table-primary">
            <tr>
                <th class="fw-bold">捐款項目</th>
                <th class="fw-bold">金額</th>
                <th class="fw-bold">付款方式</th>
                <th class="fw-bold">狀態</th>
                <th class="fw-bold">日期</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()) :
                    $funding_status_class = 'funding-status-' . preg_replace('/\s+/', '', $row['Funding_Status']);
                ?>
                    <tr>
                        <td class="highlight-title">
                            <a class="title" href="suggestion_detail.php?id=<?= htmlspecialchars($row['Suggestion_ID']) ?>">
                                <?= htmlspecialchars($row['Funding_Title'] ?? '無標題') ?>
                            </a>
                        </td>

                        <td>$<?= number_format($row['Donation_Amount'], 0) ?></td>
                        <td><?= htmlspecialchars($row['Payment_Method'] ?? '未知') ?></td>
                        <td>
                            <small class="funding-status-label <?= htmlspecialchars($funding_status_class) ?>">
                                <?= htmlspecialchars($row['Funding_Status'] ?? '') ?>
                            </small>
                        </td>
                        <td>
                            <?= date('Y-m-d', strtotime($row['Donation_Date'])) ?><br>
                            <small class="update-date">更新於：<?= date('Y-m-d', strtotime($row['Updated_At'])) ?></small>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-muted">目前沒有捐款紀錄。</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php
    $stmt->close();
    $link->close();
    ?>

    <script>
        let isVisible = false;

        function togglePassword() {
            const passwordInput = document.getElementById('passwordDisplay');
            const eyeIcon = document.getElementById('eyeIcon');
            const realPassword = "<?= htmlspecialchars($row_user['Password']) ?>";

            if (isVisible) {
                passwordInput.value = "••••••••••";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            } else {
                passwordInput.value = realPassword;
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            }
            isVisible = !isVisible;

            passwordDisplay.style.color = '#555';
        }
    </script>


</body>

</html>