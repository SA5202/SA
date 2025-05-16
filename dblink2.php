<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>建言修改處理</title>
    <style>
        body {
            font-family: 'Noto Serif TC', serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: transparent;
            animation: fadeIn 0.8s ease-in;
        }

        .message {
            width: 75%;
            font-size: 18px;
            font-weight: 600;
            background-color: #fff;
            color: rgb(205, 89, 87);
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 25px;
            box-sizing: border-box;
            text-align: center;
            position: relative;
            border-left: 12px solid rgb(205, 89, 87);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <?php
    session_start();
    $method = $_GET['method'] ?? null;

    $link = mysqli_connect('localhost', 'root', '', 'SA');
    if (!$link) {
        die("資料庫連線失敗: " . mysqli_connect_error());
    }

    function is_locked($link, $Suggestion_ID)
    {
        // 抓最新的一筆進度資料（Progress_ID 最大）
        $sql = "SELECT Status FROM progress 
            WHERE Suggestion_ID = ? 
            ORDER BY Progress_ID DESC 
            LIMIT 1";

        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $Suggestion_ID);
        $stmt->execute();
        $stmt->bind_result($status);

        if ($stmt->fetch()) {
            $stmt->close();
            return in_array($status, ['審核中', '處理中', '已完成']);
        }

        $stmt->close();
        return false; // 沒有進度紀錄，代表可以修改
    }


    if ($method === 'update') {
        if (isset($_POST['suggestion_id'], $_POST['title'], $_POST['facility'], $_POST['building'], $_POST['description'])) {
            $Suggestion_ID = $_POST['suggestion_id'];

            // ✅ 鎖定檢查放這裡
            if (is_locked($link, $Suggestion_ID)) {
                echo "<div class='message'>此建言已進入處理階段，無法修改。</div>";
                exit;
            }

            $Title = $_POST['title'];
            $Facility_ID = $_POST['facility'];
            $Building_ID = $_POST['building'];
            $Description = $_POST['description'];

            // 設置時區為 Asia/Taipei (UTC +8)
            date_default_timezone_set('Asia/Taipei');
            $updatedAt = date("Y-m-d H:i:s");

            $stmt = $link->prepare("UPDATE suggestion SET Title = ?, Facility_ID = ?, Building_ID = ?, Description = ?, Updated_At = ? WHERE Suggestion_ID = ?");
            $stmt->bind_param("siisss", $Title, $Facility_ID, $Building_ID, $Description, $updatedAt, $Suggestion_ID);

            if ($stmt->execute()) {
                echo "<div class='message'>建言已成功更新！</div>";
                echo "<meta http-equiv='refresh' content='2;url=record.php'>";
            } else {
                echo "<div class='message'>更新失敗：</div>" . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "<div class='message'>缺少必要欄位。</div>";
        }
    } elseif ($method === 'delete') {
        if (isset($_POST['suggestion_id'])) {
            $Suggestion_ID = intval($_POST['suggestion_id']);
            $sessionUserID = $_SESSION['User_ID'] ?? null;
            $sessionUserType = $_SESSION['User_Type'] ?? null;

            // 取得該建言的擁有者 User_ID
            $stmt_check = $link->prepare("SELECT User_ID FROM suggestion WHERE Suggestion_ID = ?");
            $stmt_check->bind_param("i", $Suggestion_ID);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $row = $result->fetch_assoc();
            $stmt_check->close();

            if (!$row) {
                echo "<div class='message'>查無此筆建言。</div>";
                echo "<meta http-equiv='refresh' content='2;url=record.php'>";
            }

            $ownerUserID = $row['User_ID'];

            // ✅ 權限檢查：只有本人或管理員能刪除
            if ($ownerUserID != $sessionUserID && $sessionUserType !== 'admin') {
                echo "<div class='message'>您沒有權限刪除此筆建言。</div>";
                echo "<meta http-equiv='refresh' content='2;url=record.php'>";
            }

            // ✅ 鎖定狀態檢查
            if (is_locked($link, $Suggestion_ID)) {
                echo "<div class='message'>此建言已進入處理階段，無法刪除。</div>";
                echo "<meta http-equiv='refresh' content='2;url=record.php'>";
            }

            // 刪除 progress 表中相關資料
            $stmt1 = $link->prepare("DELETE FROM progress WHERE Suggestion_ID = ?");
            $stmt1->bind_param("i", $Suggestion_ID);
            $stmt1->execute();
            $stmt1->close();

            // 刪除 upvote 表中相關資料
            $stmt2 = $link->prepare("DELETE FROM upvote WHERE Suggestion_ID = ?");
            $stmt2->bind_param("i", $Suggestion_ID);
            $stmt2->execute();
            $stmt2->close();

            // 刪除 fundingsuggestion 表中相關資料
            $stmt3 = $link->prepare("DELETE FROM fundingsuggestion WHERE Suggestion_ID = ?");
            $stmt3->bind_param("i", $Suggestion_ID);
            $stmt3->execute();
            $stmt3->close();

            // 最後刪除 suggestion 表資料
            $stmt4 = $link->prepare("DELETE FROM suggestion WHERE Suggestion_ID = ?");
            $stmt4->bind_param("i", $Suggestion_ID);

            if ($stmt4->execute()) {
                echo "<div class='message'>建言已成功刪除！</div>";
                echo "<meta http-equiv='refresh' content='2;url=record.php'>";
            } else {
                echo "<div class='message'>刪除失敗：</div>" . $stmt4->error;
            }

            $stmt4->close();
        } else {
            echo "<div class='message'>缺少建言 ID。</div>";
        }
    }


    mysqli_close($link);
    ?>
</body>

</html>