<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>建言修改處理</title>
</head>

<body>
    <?php
    session_start();
    $method = $_GET['method'] ?? null;

    // 連接資料庫
    $link = mysqli_connect('localhost', 'root', '', 'SA');
    if (!$link) {
        die("資料庫連線失敗: " . mysqli_connect_error());
    }

    if ($method === 'update') {
        // 確認資料都有傳入
        if (isset($_POST['suggestion_id'], $_POST['title'], $_POST['facility'], $_POST['building'], $_POST['description'])) {
            $suggestion_id = $_POST['suggestion_id'];
            $title = $_POST['title'];
            $facility = $_POST['facility'];
            $building = $_POST['building'];
            $description = $_POST['description'];

            // 使用 prepared statement 安全更新
            $stmt = $link->prepare("UPDATE suggestions SET title = ?, facility = ?, building = ?, description = ? WHERE suggestion_id = ?");
            $stmt->bind_param("ssssi", $title, $facility, $building, $description, $suggestion_id);

            if ($stmt->execute()) {
                echo "建言更新成功！";

                // 身分判斷跳轉
                if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
                    echo "<meta http-equiv='refresh' content='1;url=admin_suggestions.php'>";
                } else {
                    echo "<meta http-equiv='refresh' content='1;url=record.php'>";
                }
            } else {
                echo "更新失敗：" . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "缺少必要欄位。";
        }
    }

    // 刪除建言
    elseif ($method === 'delete') {
        if (isset($_GET['Suggestion_ID'])) {
            $Suggestion_ID = mysqli_real_escape_string($link, $_GET['Suggestion_ID']);
            $sql = "DELETE FROM suggestions WHERE Suggestion_ID='$Suggestion_ID'";

            if ($stmt->execute()) {
                echo "建言刪除成功！";
                echo "<meta http-equiv='refresh' content='1;url=record.php'>";
            } else {
                echo "刪除失敗：" . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "缺少建言 ID。";
        }
    }

    mysqli_close($link);
    ?>
</body>

</html>