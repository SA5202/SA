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

    $link = mysqli_connect('localhost', 'root', '', 'SA');
    if (!$link) {
        die("資料庫連線失敗: " . mysqli_connect_error());
    }

    if ($method === 'update') {
        // 對應 HTML 表單欄位名稱
        if (isset($_POST['suggestion_id'], $_POST['title'], $_POST['facility'], $_POST['building'], $_POST['description'])) {
            $Suggestion_ID = $_POST['suggestion_id'];
            $Title = $_POST['title'];
            $Facility_ID = $_POST['facility'];
            $Building_ID = $_POST['building'];
            $Description = $_POST['description'];

            $stmt = $link->prepare("UPDATE suggestion SET Title = ?, Facility_ID = ?, Building_ID = ?, Description = ? WHERE Suggestion_ID = ?");
            $stmt->bind_param("siisi", $Title, $Facility_ID, $Building_ID, $Description, $Suggestion_ID);

            if ($stmt->execute()) {
                echo "建言更新成功！";
                echo "<meta http-equiv='refresh' content='1;url=record.php'>";
            } else {
                echo "更新失敗：" . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "缺少必要欄位。";
        }
    }
    elseif ($method === 'delete') {
        if (isset($_POST['suggestion_id'])) {
            $Suggestion_ID = $_POST['suggestion_id'];

            // 先刪除 progress 表中相關資料
            $stmt1 = $link->prepare("DELETE FROM progress WHERE Suggestion_ID = ?");
            $stmt1->bind_param("i", $Suggestion_ID);
            $stmt1->execute();
            $stmt1->close();

            // 再刪除 upvote 表中相關資料
            $stmt2 = $link->prepare("DELETE FROM upvote WHERE Suggestion_ID = ?");
            $stmt2->bind_param("i", $Suggestion_ID);
            $stmt2->execute();
            $stmt2->close();

            // 最後刪除 suggestion 表資料
            $stmt3 = $link->prepare("DELETE FROM suggestion WHERE Suggestion_ID = ?");
            $stmt3->bind_param("i", $Suggestion_ID);

            if ($stmt3->execute()) {
                echo "建言及相關資料刪除成功！";
                echo "<meta http-equiv='refresh' content='1;url=record.php'>";
            } else {
                echo "刪除失敗：" . $stmt3->error;
            }

            $stmt3->close();
        } else {
            echo "缺少建言 ID。";
        }
    }


    mysqli_close($link);
    ?>
</body>

</html>