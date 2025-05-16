<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯個人檔案 | 輔仁大學愛校建言捐款系統</title>
    <style>
        body {
            font-family: "Noto Serif TC", serif;
            margin: 0;
            padding: 0;
        }

        .card {
            max-width: 750px;
            background-color: #fff;
            margin: 80px auto;
            padding: 40px 50px;
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
        }

        td {
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #444;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px 15px;
            font-size: 16px;
            font-family: "Noto Serif TC", serif;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        input[readonly] {
            background-color: #eee;
        }

        input:focus {
            outline: none;
            border-color: rgb(173, 231, 248);
            box-shadow: 0 0 8px rgba(70, 117, 141, 0.88);
        }

        .input-wrapper {
            position: relative;
        }

        #nicknameCounter {
            position: absolute;
            bottom: 8px;
            right: 12px;
            font-size: 14px;
            font-weight: bold;
            color: #888;
            pointer-events: none;
        }

        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .show-password-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            cursor: pointer;
            color: #666;
        }

        .button-row {
            text-align: center;
            padding-top: 10px;
        }

        input[type="submit"],
        input[type="reset"] {
            background-color: #84c684;
            color: white;
            padding: 8px 50px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            margin: 10px 5px 0 5px;
            font-size: 16px;
            font-weight: bold;
            font-family: "Noto Serif TC", serif;
        }

        input[type="reset"] {
            background-color: rgb(224, 107, 107);
        }

        input[type="submit"]:hover,
        input[type="reset"]:hover {
            opacity: 0.6;
        }

        .custom-file-btn {
            display: inline-block;
            background-color: #bbb;
            color: white;
            padding: 5px 40px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            border: none;
            /* 新增：移除邊框 */
        }


        .custom-file-btn:hover {
            opacity: 0.7;
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

    // 管理員查看他人資料，否則是自己
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true && isset($_GET['User_Name'])) {
        $User_Name = $_GET['User_Name'];
    } else {
        $User_Name = $_SESSION['User_Name'];
    }

    $sql = "SELECT * FROM useraccount WHERE User_Name = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("s", $User_Name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $Email = $row['Email'];
        $Nickname = $row['Nickname']; // 取得暱稱
    } else {
        die("找不到使用者資料！");
    }

    $stmt->close();
    $link->close();
    ?>

    <div class="card">
        <form id="profileForm" action="dblink.php?method=update_avatar" method="post" enctype="multipart/form-data">
            <table>
                <tr>
                    <td>帳號</td>
                    <td>
                        <input type="text" name="User_Name" value="<?= htmlspecialchars($User_Name) ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>
                        <input type="email" name="Email" value="<?= htmlspecialchars($Email) ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>暱稱</td>
                    <td>
                        <div class="input-wrapper">
                            <input type="text" name="Nickname" id="nicknameInput" value="<?= htmlspecialchars($Nickname) ?>" maxlength="10">
                            <span id="nicknameCounter">0 / 10</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>設置新密碼</td>
                    <td>
                        <div class="password-wrapper">
                            <input type="password" name="Password" id="password">
                            <span class="show-password-btn" onclick="togglePassword('password', 'eye-icon')">
                                <ion-icon id="eye-icon" name="eye-off-outline"></ion-icon>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>確認新密碼</td>
                    <td>
                        <div class="password-wrapper">
                            <input type="password" name="Confirm_Password" id="confirm_password">
                            <span class="show-password-btn" onclick="togglePassword('confirm_password', 'confirm-eye-icon')">
                                <ion-icon id="confirm-eye-icon" name="eye-off-outline"></ion-icon>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                <tr>
                    <td>設置頭像</td>
                    <td>
                        <input type="file" name="Avatar" id="avatarInput" accept="image/*" style="display: none;">
                        <label for="avatarInput" class="custom-file-btn">選擇圖片</label>
                        <button type="button" class="custom-file-btn" onclick="deleteAvatar()">恢復預設</button>

                        <span id="fileNameDisplay" style="margin-left:10px; color:#555;"></span>
                    </td>
                </tr>


                <tr>
                    <td colspan="2" class="button-row">
                        <input type="hidden" name="Old_User_Name" value="<?= htmlspecialchars($User_Name) ?>">
                        <input type="submit" value="儲存變更">
                        <input type="reset" value="重設">
                    </td>
                </tr>
            </table>
        </form>

    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        const nicknameInput = document.getElementById('nicknameInput');
        const nicknameCounter = document.getElementById('nicknameCounter');

        nicknameInput.addEventListener('input', function() {
            nicknameCounter.textContent = `${this.value.length} / 10`;
        });

        function togglePassword() {
            // 取得密碼欄位
            var passwordField = document.getElementById("password");
            var confirmPasswordField = document.getElementById("confirm_password");

            // 取得眼睛圖示
            var eyeIcon = document.getElementById("eye-icon");
            var confirmEyeIcon = document.getElementById("confirm-eye-icon");

            // 切換顯示/隱藏密碼
            var type = passwordField.type === "password" ? "text" : "password";
            passwordField.type = type;
            confirmPasswordField.type = type;

            // 切換眼睛圖示
            eyeIcon.name = type === "password" ? "eye-off-outline" : "eye-outline";
            confirmEyeIcon.name = type === "password" ? "eye-off-outline" : "eye-outline";
        }

        document.getElementById('profileForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('confirm_password').value.trim();

            // 如果其中一個欄位有填，但另一個沒填
            if ((password && !confirmPassword) || (!password && confirmPassword)) {
                event.preventDefault();
                alert("請完整填寫密碼與確認密碼欄位！");
                return;
            }

            // 如果兩個欄位都有填，但不一致
            if (password && confirmPassword && password !== confirmPassword) {
                event.preventDefault();
                alert("兩次輸入的密碼不一致！");
                return;
            }
        });

        const input = document.getElementById('avatarInput');
        const fileNameDisplay = document.getElementById('fileNameDisplay');

        input.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileNameDisplay.textContent = this.files[0].name;
            } else {
                fileNameDisplay.textContent = '';
            }
        });
    </script>
    <script>
        function deleteAvatar() {
            if (confirm("確定要刪除頭像並恢復為預設圖片嗎？")) {
                fetch("delete_avatar.php", {
                        method: "POST"
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const defaultAvatar = "https://i.pinimg.com/736x/15/46/d1/1546d15ce5dd2946573b3506df109d00.jpg";

                            // 更新畫面上所有頭像為預設圖片，加時間戳避免快取
                            document.querySelectorAll(".avatar").forEach(el => {
                                el.src = defaultAvatar + "?t=" + new Date().getTime();
                            });

                            // 清除檔案選擇與檔名顯示
                            document.getElementById("avatarInput").value = "";
                            document.getElementById("fileNameDisplay").textContent = "";

                            alert("頭像已刪除並恢復為預設圖片");
                        } else {
                            alert("刪除失敗：" + (data.message || "請稍後再試"));
                        }
                    })
                    .catch(error => {
                        console.error("錯誤：", error);
                        alert("發生錯誤，請稍後再試");
                    });
            }
        }
    </script>
    <script>
        function updateAvatarToDefault() {
            const defaultAvatar = "https://i.pinimg.com/736x/15/46/d1/1546d15ce5dd2946573b3506df109d00.jpg";
            document.querySelectorAll("img[style*='width: 250px'][style*='height: 250px']").forEach(img => {
                img.src = defaultAvatar + "?t=" + new Date().getTime();
            });
        }
    </script>
</body>

</html>