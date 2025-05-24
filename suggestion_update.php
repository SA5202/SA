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
            max-width: 75%;
            background-color: #fff;
            margin: 100px auto;
            padding: 40px;
            border: 2px solid #dee2e6;
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
            color: #555;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px 15px;
            font-size: 16px;
            font-family: "Noto Serif TC", serif;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: rgb(173, 231, 248);
            box-shadow: 0 0 8px rgba(70, 117, 141, 0.88);
        }

        input[type="text"] {
            background-color: #eee;
        }

        textarea {
            resize: vertical;
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
            margin: 0 5px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            color: white;
            transition: opacity 0.3s ease;
        }

        .btn:hover {
            opacity: 0.6;
        }

        .btn-primary {
            background-color: #84c684;
        }

        .btn-danger {
            background-color:rgb(224, 107, 107);
        }

        .message {
            width: 75%;
            margin: 280px auto;
            font-size: 18px;
            font-weight: 600;
            background-color: #fff;
            color: red;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 25px;
            box-sizing: border-box;
            text-align: center;
            position: relative;
            border-left: 12px solid rgb(205, 89, 87);
            animation: fadeIn 0.8s ease-in;
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

    // 撈此建言最新狀態
    $status = '';
    $stmt2 = $link->prepare("SELECT Status FROM Progress WHERE Suggestion_ID = ? ORDER BY Progress_ID DESC LIMIT 1");
    $stmt2->bind_param("i", $Suggestion_ID);
    $stmt2->execute();
    $stmt2->bind_result($status);
    $stmt2->fetch();
    $stmt2->close();

    // 是否鎖定
    $is_locked = in_array($status, ['處理中', '已完成']);


    $stmt->close();
    $link->close();
    ?>

    <?php if (!$is_locked): ?>
        <div class="card">
            <form action="dblink2.php?method=update" method="post">
                <table>
                    <tr>
                        <td>建言標題</td>
                        <td><input type="text" name="title" value="<?= htmlspecialchars($Title) ?>" readonly></td>
                    </tr>
                    
                    <tr>
                        <td>關聯設施</td>
                        <td>
                            <?php
                            // 找出當前設施的顯示名稱
                            mysqli_data_seek($facility_result, 0); // 重置資料指標
                            $facility_name = '';
                            while ($fac = mysqli_fetch_assoc($facility_result)) {
                                if ($fac['Facility_ID'] == $Facility_ID) {
                                    $facility_name = $fac['Facility_Type'];
                                    break;
                                }
                            }
                            ?>
                            <input type="text" value="<?= htmlspecialchars($facility_name) ?>" readonly>
                            <input type="hidden" name="facility" value="<?= htmlspecialchars($Facility_ID) ?>">
                        </td>
                    </tr>

                    <tr>
                        <td>關聯建築物</td>
                        <td>
                            <?php
                            // 找出當前建築的顯示名稱
                            mysqli_data_seek($building_result, 0); // 重置資料指標
                            $building_name = '';
                            while ($bld = mysqli_fetch_assoc($building_result)) {
                                if ($bld['Building_ID'] == $Building_ID) {
                                    $building_name = $bld['Building_Name'];
                                    break;
                                }
                            }
                            ?>
                            <input type="text" value="<?= htmlspecialchars($building_name) ?>" readonly>
                            <input type="hidden" name="building" value="<?= htmlspecialchars($Building_ID) ?>">
                        </td>
                    </tr>

                    <tr>
                        <td>建言內容</td>
                        <td><textarea name="description" rows="4"><?= htmlspecialchars($Description) ?></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="button-row">
                            <div class="button-row">
                                <form action="dblink2.php?method=update" method="post" style="display: inline;">
                                    <input type="hidden" name="suggestion_id" value="<?= htmlspecialchars($Suggestion_ID) ?>">
                                    <input type="submit" value="儲存變更" class="btn btn-primary">
                                </form>

                                <form action="dblink2.php?method=delete" method="post" style="display: inline;" onsubmit="return confirm('確定要刪除這個建言嗎？');">
                                    <input type="hidden" name="suggestion_id" value="<?= htmlspecialchars($Suggestion_ID) ?>">
                                    <input type="submit" value="刪除建言" class="btn btn-danger">
                                </form>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

    <?php else: ?>
        <p class="message">此建言已進入處理階段：「<?= htmlspecialchars($status) ?>」，無法修改或刪除。</p>
        <script>
            setTimeout(function () {
                window.history.back();
            }, 3000);
        </script>
    <?php endif; ?>
</body>

</html>