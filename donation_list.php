<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

// 僅限 super 和 general 存取
if (!isset($_SESSION['User_Name']) || ($_SESSION['admin_type'] !== 'super' && $_SESSION['admin_type'] !== 'general')) {
    header("Location: login.php");
    exit();
}

$link = new mysqli('localhost', 'root', '', 'sa');
if ($link->connect_error) {
    die("資料庫連接失敗：" . $link->connect_error);
}

$keyword = $_GET['keyword'] ?? '';
$method_id = $_GET['method_id'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';

// 撈取付款方式
$methods = [];
$m_result = $link->query("SELECT Method_ID, Method_Name FROM PaymentMethod");
while ($row = $m_result->fetch_assoc()) {
    $methods[] = $row;
}

// 撈取捐款資料
$sql = "
    SELECT d.Donation_ID, d.Donation_Amount, d.Status, d.Donation_Date, 
           d.Is_Manual, d.User_ID, ua.User_Name,
           pm.Method_Name,
           s.Title AS Suggestion_Title
    FROM Donation d
    LEFT JOIN UserAccount ua ON d.User_ID = ua.User_ID
    LEFT JOIN PaymentMethod pm ON d.Method_ID = pm.Method_ID
    LEFT JOIN FundingSuggestion fs ON d.Funding_ID = fs.Funding_ID
    LEFT JOIN Suggestion s ON fs.Suggestion_ID = s.Suggestion_ID
    WHERE 1=1
";

if (!empty($keyword)) {
    $escaped = $link->real_escape_string($keyword);
    $sql .= " AND (ua.User_Name LIKE '%$escaped%' OR d.Status LIKE '%$escaped%')";
}

if (!empty($method_id)) {
    $sql .= " AND d.Method_ID = " . intval($method_id);
}

switch ($sort) {
    case 'amount_asc':
        $sql .= " ORDER BY d.Donation_Amount ASC";
        break;
    case 'amount_desc':
        $sql .= " ORDER BY d.Donation_Amount DESC";
        break;
    case 'date_asc':
        $sql .= " ORDER BY d.Donation_Date ASC";
        break;
    default:
        $sql .= " ORDER BY d.Donation_Date DESC";
        break;
}

$result = $link->query($sql);
?>


<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>捐款紀錄列表</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            padding: 30px;
            font-family: "Noto Serif TC", serif;
            background-color: transparent;
            color: #333;
            max-width: 85%;
            margin: 0 auto;
        }

        h2 {
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.75rem;
            padding-bottom: 10px;
            color: #003153;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 25px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            
        }

        .custom-table:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .custom-table th,
        .custom-table td {
            padding: 20px 3px;
            border-bottom: 1px solid rgb(255, 255, 255);
            /* 只加底線 */
            border-left: none !important;
            /* 取消左邊線 */
            border-right: none !important;
            /* 取消右邊線 */
            text-align: center;
        }

        .custom-table thead th {
            background-color: #f1f3f5;
            color: #555;

        }

        .custom-table tbody tr:last-child td {
            border-bottom: none;
            /* 最後一列不要底線 */
        }

        .custom-table tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .form-select,
        .form-control {
            border-radius: 10px;
            border: 1.5px solid #b0c4de;
            font-size: 1rem;
            color: #333;
        }

        .btn-dark {
            background: linear-gradient(to right, rgb(139, 186, 224), rgb(69, 109, 133));
            border: none;
            border-radius: 15px;
            font-weight: 750;
            font-size: 1rem;
            transition: opacity 0.3s ease;
        }

        .btn-dark:hover {
            opacity: 0.6;
        }

        .btn-outline-primary {
            background: linear-gradient(to right, rgb(139, 186, 224), rgb(69, 109, 133));
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 750;
            transition: opacity 0.3s ease;
        }

        .btn-outline-primary:hover {
            opacity: 0.6;
            color: white;
        }

        .btn-secondary {
            background: #a0a0a0;
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
        }

        .tooltip-wrapper {
            display: inline-block;
            cursor: not-allowed;
        }

        .alert-success {
            background-color: #daf1ff;
            color: #004085;
            border-radius: 15px;
            font-weight: 600;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            /* 手機滑順 */
            max-width: 100%;
        }
    </style>

</head>

<body>
    <div class="container">
        <h2>捐款紀錄總覽（管理員）</h2>

        <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                捐款紀錄已成功更新！
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="method_id" class="form-label">付款方式</label>
                <select name="method_id" id="method_id" class="form-select">
                    <option value="">所有方式</option>
                    <?php foreach ($methods as $m): ?>
                        <option value="<?= $m['Method_ID'] ?>" <?= ($method_id == $m['Method_ID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['Method_Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="sort" class="form-label">排序</label>
                <select name="sort" id="sort" class="form-select">
                    <option value="date_desc" <?= ($sort == 'date_desc') ? 'selected' : '' ?>>日期：新 → 舊</option>
                    <option value="date_asc" <?= ($sort == 'date_asc') ? 'selected' : '' ?>>日期：舊 → 新</option>
                    <option value="amount_desc" <?= ($sort == 'amount_desc') ? 'selected' : '' ?>>金額：高 → 低</option>
                    <option value="amount_asc" <?= ($sort == 'amount_asc') ? 'selected' : '' ?>>金額：低 → 高</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="keyword" class="form-label">關鍵字（帳號或留言/備註）</label>
                <input type="text" name="keyword" id="keyword" class="form-control" value="<?= htmlspecialchars($keyword) ?>" placeholder="輸入帳號或備註關鍵字">
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-dark w-100">查詢</button>
            </div>
        </form>
        
            <table class="custom-table table-hover align-middle text-center">

                <thead>
                    <tr>
                        <th>捐款者帳號</th>
                        <th>項目名稱</th>
                        <th>金額</th>
                        <th>付款方式</th>
                        <th>是否手動</th>
                        <th>日期</th>
                        <th>備註 / 狀態說明</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['User_Name'] ?? '匿名' ?></td>
                            <td><?= htmlspecialchars($row['Suggestion_Title']) ?></td>
                            <td><?= number_format($row['Donation_Amount']) ?> 元</td>
                            <td><?= htmlspecialchars($row['Method_Name']) ?></td>
                            <td><?= $row['Is_Manual'] ? '是' : '否' ?></td>
                            <td><?= date('Y-m-d', strtotime($row['Donation_Date'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['Status'])) ?></td>
                            <td>
                                <?php if ($row['Is_Manual']): ?>
                                    <a href="donation_admin_edit.php?id=<?= $row['Donation_ID'] ?>" class="btn btn-sm btn-outline-primary">編輯</a>
                                <?php else: ?>
                                    <span class="tooltip-wrapper" data-bs-toggle="tooltip" title="此筆為使用者透過網站捐款紀錄，為確保資料真實性，不可修改">
                                        <button class="btn btn-sm btn-secondary" disabled>無法編輯</button>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(el => new bootstrap.Tooltip(el));
        });

        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
</body>

</html>