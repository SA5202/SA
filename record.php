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
            font-family: 'Poppins', sans-serif;
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

        .table-responsive {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px 20px;
            border-radius: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
        }

        .table th {
            background-color: #f1f3f5;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .left {
            text-align: left;
        }

        .password-display-wrapper {
            position: relative;
            width: 200px;
            /* 或改成 auto 看實際需求 */
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
            padding: 4px 40px;
            font-size: 1rem;
            font-weight: 750;
            background: linear-gradient(to right, rgb(139, 186, 224), rgb(69, 109, 133));
            color: #fff;
            border-radius: 15px;
            text-decoration: none;
        }

        .custom-btn:hover {
            opacity: 0.7;
        }

        .table-container {
            background-color: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --bs-card-border-color: var(--bs-border-color-translucent);
            border: 1px solid var(--bs-card-border-color);
        }

        .table {
            width: 100%;
            border-radius: 25px;
            border: 2px solid #ddd;
            border-collapse: separate;
            overflow: hidden;
            border-spacing: 0;
        }

        .table th,
        .table td {
            padding: 10px 40px;
            border-radius: 0px;
        }

        .table-primary {
            background-color: #e9f5ff;
        }

        .update {
            font-size: 1rem;
            font-weight: bold;
            color: gray;
        }

        .pretty-btn {
            background: linear-gradient(to right, rgb(139, 186, 224), rgb(69, 109, 133));
            border: none;
            color: white;
            padding: 5px 20px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 750;
        }

        .pretty-btn:hover {
            opacity: 0.7;
        }

        .pretty-btn i {
            margin-right: 6px;
        }

        .badge {
            padding: 0.3em 1em;
            font-size: 0.5rem;
        }

        .custom-badge {
            background: linear-gradient(to right, rgb(218, 240, 249), rgb(197, 226, 239));
            color: #2a4d69;
            font-size: 0.9rem;
            padding: 0.5em 1em;
            border-radius: 12px;
            font-weight: 600;
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
                                style='border-radius: 30px; width: 250px; height: 250px; margin: 10px 40px;'>
                        </td>

                        <td class='left'><b>帳號： </b><?= htmlspecialchars($row_user['User_Name']) ?></td>
                    </tr>
                    <tr>
                        <td class='left'><b>暱稱： </b><?= htmlspecialchars($row_user['Nickname']) ?></td>
                    </tr>
                    <tr>
                        <td class='left'><b>使用者 ID： </b>0000000000<?= $row_user['User_ID'] ?></td>
                    </tr>
                    <tr>
                        <td class='left'><b>Email： </b><?= htmlspecialchars($row_user['Email']) ?></td>
                    </tr>
                    <tr>
                        <td class="left">
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <span><b>密碼： </b></span>
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
        <h3><i class="icon fas fa-clipboard-list"></i> 建言紀錄</h3>
    <?php endif; ?>

    <table class="table">
        <thead class="table-primary">
            <tr>
                <th class="fw-bold">建言標題</th>
                <th class="fw-bold">發佈時間</th>
                <th class="fw-bold">獲得讚數</th>
                <th class="fw-bold">編輯建言</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($row['Title']) ?></b></td>
                        <td><span class="update"><?= date('Y-m-d', strtotime($row['Updated_At'])) ?></span></td>
                        <td>
                            <span class="badge custom-badge fs-6"> <?= $row['LikeCount'] ?> ❤️ </span>
                        </td>

                        <td>
                            <?php if ($row['User_ID'] == $sessionUserID): ?>
                                <!-- 本人：只顯示編輯按鈕（編輯頁中包含刪除功能） -->
                                <a href="suggestion_update.php?Suggestion_ID=<?= $row['Suggestion_ID'] ?>" class="pretty-btn">
                                    <i class="fas fa-pen-to-square"></i> 修改
                                </a>
                            <?php elseif ($sessionUserType === 'admin'): ?>
                                <!-- 管理員：只能刪除 -->
                                <form action="dblink2.php?method=delete" method="post" onsubmit="return confirm('管理員確定要刪除這個建言嗎？');" style="display:inline;">
                                    <input type="hidden" name="suggestion_id" value="<?= $row['Suggestion_ID'] ?>">
                                    <input type="submit" value="刪除" class="pretty-btn">
                                </form>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center text-muted">尚未有建言紀錄。</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <h3><i class="icon fas fa-donate"></i> 捐款紀錄</h3>

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
        }
    </script>

</body>

</html>