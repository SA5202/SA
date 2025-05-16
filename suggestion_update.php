<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改建言 | 輔仁大學愛校建言捐款系統</title>
    <style>
        body {
            font-family: "Noto Serif TC", serif;
            margin: 0;
            padding: 0;
        }

        .card {
            max-width: 800px;
            background-color: #fff;
            margin: 80px auto;
            padding: 40px;
            border-radius: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-family: "Noto Serif TC", serif;
        }

        td {
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #444;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 8px 15px;
            font-size: 16px;
            font-family: "Noto Serif TC", serif;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: rgb(173, 231, 248);
            box-shadow: 0 0 8px rgba(70, 117, 141, 0.88);
        }

        .button-row {
            text-align: center;
            padding-top: 10px;
        }

        .btn {
            font-family: "Noto Serif TC", serif;
            font-size: 16px;
            font-weight: bold;
            padding: 8px 50px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            margin: 5px;
            color: white;
            transition: opacity 0.3s ease;
        }

        .btn:hover {
            opacity: 0.6;
        }

        .btn-primary {
            background-color: #84c684;
        }

        .btn-reset {
            background-color: rgb(76, 144, 189);
        }

        .btn-danger {
            background-color:rgb(224, 107, 107);
        }
    </style>
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['User_Name'])) {
        die("請先登入！");
    }

    $link = mysqli_connect('localhost', 'root', '', 'SA');
    if (!$link) {
        die("資料庫連線失敗：" . mysqli_connect_error());
    }

    $Suggestion_ID = $_GET['Suggestion_ID'] ?? null;
    if (!$Suggestion_ID) {
        die("未提供建言 ID");
    }


    $sql = "SELECT * FROM suggestion WHERE Suggestion_ID = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $Suggestion_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $Suggestion_ID = $row['Suggestion_ID'];
        $Title = $row['Title'];
        $Building_ID = $row['Building_ID'];
        $Facility_ID = $row['Facility_ID'];
        $Description = $row['Description'];
    } else {
        die("找不到建言資料！");
    }

    // 撈設施資料
    $facility_result = mysqli_query($link, "SELECT Facility_ID, Facility_Type FROM Facility");

    // 撈建築物資料
    $building_result = mysqli_query($link, "SELECT Building_ID, Building_Name FROM Building");

    //撈此建言最新狀態
    $status = '';
    $stmt2 = $link->prepare("SELECT Status FROM Progress WHERE Suggestion_ID = ? ORDER BY Progress_ID DESC LIMIT 1");
    $stmt2->bind_param("i", $Suggestion_ID);
    $stmt2->execute();
    $stmt2->bind_result($status);
    $stmt2->fetch();
    $stmt2->close();

    //是否鎖定
    $is_locked = in_array($status, ['審核中', '處理中', '已完成']);


    $stmt->close();
    $link->close();
    ?>

    <div class="card">
        <?php if (!$is_locked): ?>
            <form action="dblink2.php?method=update" method="post">
                <table>
                    <tr>
                        <td>建言標題</td>
                        <td><input type="text" name="title" value="<?= htmlspecialchars($Title) ?>" readonly></td>
                    </tr>
                    
                    <tr>
                        <td>相關設施</td>
                        <td>
                            <select id="facility" name="facility" class="form-select" required>
                                <option value="">請選擇設施</option>
                                <?php
                                mysqli_data_seek($facility_result, 0); // 重置資料指標
                                while ($f = mysqli_fetch_assoc($facility_result)):
                                    $selected = ($f['Facility_ID'] == $Facility_ID) ? 'selected' : '';
                                ?>
                                    <option value="<?= htmlspecialchars($f['Facility_ID']) ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($f['Facility_Type']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>相關建築物</td>
                        <td>
                            <select id="building" name="building" class="form-select" required>
                                <option value="">請選擇建築物</option>
                                <?php
                                mysqli_data_seek($building_result, 0); // 重置資料指標
                                while ($b = mysqli_fetch_assoc($building_result)):
                                    $selected = ($b['Building_ID'] == $Building_ID) ? 'selected' : '';
                                ?>
                                    <option value="<?= htmlspecialchars($b['Building_ID']) ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($b['Building_Name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>建言內容</td>
                        <td><textarea name="description" rows="4"><?= htmlspecialchars($Description) ?></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="button-row">
                            <input type="hidden" name="suggestion_id" value="<?= htmlspecialchars($Suggestion_ID) ?>">
                            <input type="submit" value="儲存變更" class="btn btn-primary">
                            <input type="reset" value="一鍵清空" class="btn btn-reset">
                        </td>
                    </tr>
                </table>
            </form>

            <!-- 刪除按鈕獨立出來 -->
            <form action="dblink2.php?method=delete" method="post" onsubmit="return confirm('確定要刪除這個建言嗎？');">
                <input type="hidden" name="suggestion_id" value="<?= htmlspecialchars($Suggestion_ID) ?>">
                <div class="button-row">
                    <input type="submit" value="刪除建言" class="btn btn-danger">
                </div>
            </form>
        <?php else: ?>
            <p style="color: red; text-align: center;">此建言已進入處理階段「<?= htmlspecialchars($status) ?>」，無法修改或刪除。</p>
        <?php endif; ?>

    </div>
</body>

</html>