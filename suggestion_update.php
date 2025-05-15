<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改建言</title>
    <style>
        body {
            font-family: "Noto Serif TC", serif;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 40px;
        }

        .card {
            max-width: 500px;
            background-color: #fff;
            margin: 40px auto;
            padding: 30px 40px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 12px 10px;
            font-size: 16px;
            color: #444;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .button-row {
            text-align: center;
            padding-top: 10px;
        }

        
        .btn {
            font-family: "Noto Serif TC", serif;
            font-size: 16px;
            padding: 10px 25px;
            border: none;
            border-radius: 100px;
            cursor: pointer;
            margin: 5px;
            color: white;
            transition: opacity 0.3s ease;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-primary {
            background-color: #84c684;
        }

        .btn-reset {
            background-color: rgb(189, 84, 76);
        }

        .btn-danger {
            background-color: rgb(189, 84, 76);
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

    if ($method === 'update') {
        if (isset($_POST['suggestion_id'], $_POST['title'], $_POST['facility'], $_POST['building'], $_POST['description'])) {
            $suggestion_id = $_POST['suggestion_id'];
            $title = $_POST['title'];
            $facility = $_POST['facility'];
            $building = $_POST['building'];
            $description = $_POST['description'];

            // 使用 prepared statement 更安全
            $stmt = $link->prepare("UPDATE suggestions SET title=?, facility=?, building=?, description=? WHERE suggestion_id=?");
            $stmt->bind_param("ssssi", $title, $facility, $building, $description, $suggestion_id);

            if ($stmt->execute()) {
                echo "建言更新成功！";
                // 根據身份轉跳
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
            echo "缺少建言 ID 或其他欄位。";
        }
    }

    mysqli_close($link);
    ?>


    <h2>修改建言</h2>
    <div class="card">
        <form action="dblink2.php?method=update" method="post">
            <table>
                <tr>
                    <td>建言標題</td>
                    <td><input type="text" name="title" value="<?= htmlspecialchars($Title) ?>"></td>
                </tr>
                <tr>
                    <td>關聯設施</td>
                    <td><input type="text" name="facility" value="<?= htmlspecialchars($Facility) ?>"></td>
                </tr>
                <tr>
                    <td>關聯建築物</td>
                    <td><input type="text" name="building" value="<?= htmlspecialchars($Building) ?>"></td>
                </tr>
                <tr>
                    <td>建言內容</td>
                    <td><textarea name="description" rows="4"><?= htmlspecialchars($Description) ?></textarea></td>
                </tr>
                <tr>
                    <td colspan="2" class="button-row">
                        <input type="hidden" name="suggestion_id" value="<?= htmlspecialchars($suggestion_id) ?>">
                        <input type="submit" value="更新建言" class="btn btn-primary">
                        <input type="reset" value="重設" class="btn btn-reset">
                    </td>
                </tr>
            </table>
        </form>

        <!-- 刪除按鈕獨立出來 -->
        <form action="dblink2.php?method=delete" method="post" onsubmit="return confirm('確定要刪除這個建言嗎？');">
            <input type="hidden" name="suggestion_id" value="<?= htmlspecialchars($suggestion_id) ?>">
            <div class="button-row">
                <input type="submit" value="刪除建言" class="btn btn-danger">
            </div>
        </form>

        </table>
        </form>
    </div>
</body>

</html>