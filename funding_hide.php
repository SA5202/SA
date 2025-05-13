<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SA";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}

if (isset($_GET['funding_id']) && is_numeric($_GET['funding_id'])) {
    $funding_id = $_GET['funding_id'];

    // 更新募款建議的狀態為“已隱藏”
    $updateSql = "UPDATE FundingSuggestion SET Status = '已隱藏' WHERE Funding_ID = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("i", $funding_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // 成功隱藏卡片
        echo "
        <!DOCTYPE html>
        <html lang='zh-Hant'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>刪除提示</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    background-color: transparent !important;
                }

                .alert-card {
                    width: 90%;
                    max-width: 500px;
                    background-color: #fff;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    padding: 25px;
                    box-sizing: border-box;
                    text-align: center;
                    position: relative;
                }

                .alert-card.success {
                    border-left: 8px solid rgb(147, 194, 90);
                }

                .alert-card.error {
                    border-left: 8px solid rgb(205, 89, 87);
                }

                .alert-title {
                    font-size: 20px;
                    font-weight: bold;
                    margin-bottom: 10px;
                }

                .alert-message {
                    font-size: 16px;
                    color: #555;
                }

                .alert-card.success .alert-title {
                    color: rgb(147, 194, 90);
                }

                .alert-card.error .alert-title {
                    color: rgb(205, 89, 87);
                }

                .close-btn {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    font-size: 20px;
                    background: none;
                    border: none;
                    color: #aaa;
                    cursor: pointer;
                }

                .close-btn:hover {
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class='alert-card success'>
                <button class='close-btn' onclick='closeAlert()'>×</button>
                <div class='alert-title'>成功！</div>
                <div class='alert-message'>募款建議已成功刪除。</div>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'funding_detail.php?status=hidden';
                }, 3000);

                function closeAlert() {
                    document.querySelector('.alert-card').style.display = 'none';
                }
            </script>
        </body>
        </html>
        ";
    } else {
        // 失敗卡片
        echo "
        <!DOCTYPE html>
        <html lang='zh-Hant'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>隱藏提示</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    background-color: #f4f4f4;
                }

                .alert-card {
                    width: 90%;
                    max-width: 500px;
                    background-color: #fff;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    padding: 25px;
                    box-sizing: border-box;
                    text-align: center;
                    position: relative;
                }

                .alert-card.success {
                    border-left: 8px solid rgb(147, 194, 90);
                }

                .alert-card.error {
                    border-left: 8px solid rgb(205, 89, 87);
                }

                .alert-title {
                    font-size: 20px;
                    font-weight: bold;
                    margin-bottom: 10px;
                }

                .alert-message {
                    font-size: 16px;
                    color: #555;
                }

                .alert-card.success .alert-title {
                    color: rgb(147, 194, 90);
                }

                .alert-card.error .alert-title {
                    color: rgb(205, 89, 87);
                }

                .close-btn {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    font-size: 20px;
                    background: none;
                    border: none;
                    color: #aaa;
                    cursor: pointer;
                }

                .close-btn:hover {
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class='alert-card error'>
                <button class='close-btn' onclick='closeAlert()'>×</button>
                <div class='alert-title'>錯誤！</div>
                <div class='alert-message'>刪除失敗，請稍後再試。</div>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'funding_detail.php?status=error';
                }, 3000);

                function closeAlert() {
                    document.querySelector('.alert-card').style.display = 'none';
                }
            </script>
        </body>
        </html>
        ";
    }

    $stmt->close();
} else {
    header("Location: funding_detail.php?status=error");
    exit;
}

$conn->close();
?>
