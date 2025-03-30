<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0;
    }

    .container {

        margin: 20px auto;
    }

    .search-container {
        text-align: left;
        margin-bottom: 20px;
    }

    .search-container input[type="text"] {
        padding: 10px;
        font-size: 16px;
        width: 400px;
        border: 2px solid #ddd;
        border-radius: 30px;
        /* 圓角邊框 */
        outline: none;
        transition: all 0.3s ease;
    }

    .search-container input[type="text"]:focus {
        border-color: rgb(157, 209, 45);
        /* 當聚焦時改變邊框顏色 */
        box-shadow: 0 0 8px rgba(58, 67, 5, 0.7);
        /* 聚焦時增加陰影效果 */
    }

    .search-container button {
        padding: 10px 16px;
        font-size: 16px;
        background-color: rgb(119, 125, 35);
        color: white;
        border: none;
        border-radius: 30px;
        /* 圓角按鈕 */
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .search-container button:hover {
        background-color: rgb(75, 99, 21);
    }

    .search-container button:focus {
        outline: none;
    }


    .card {
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
        padding: 16px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        flex-direction: row;
    }

    .card-content {
        flex: 1;
        margin-right: 20px;
    }

    .card-header {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 8px;
        color: #333;
    }

    .card-description {
        font-size: 14px;
        margin-bottom: 8px;
        color: #555;
    }

    .card-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .tag {
        font-size: 12px;
        color: white;
        background-color: rgb(153, 134, 50);
        padding: 4px 8px;
        border-radius: 4px;
    }

    .card-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn {
        text-decoration: none;
        font-size: 14px;
        padding: 8px 12px;
        border-radius: 4px;
        color: white;
        text-align: center;
        transition: background-color 0.3s;
    }

    .btn-favorite {
        background-color: rgb(248, 177, 96);
        font-weight: bold;
        padding: 8px 16px;
        border-radius: 4px;
        text-align: center;
        display: inline-block;
        transition: background-color 0.3s;
    }

    .btn-favorite:hover {
        background-color: rgb(196, 130, 31);
    }

    .btn-disabled {
        background-color: rgb(159, 129, 64);
        cursor: not-allowed;
    }
</style>
<div class="filter-section">
    <h1 class="h3 mb-2 text-gray-800">公告</h1>
    <br>
    <div class="container">
        <!-- 搜尋表單 -->
        <div class="search-container">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="搜尋公告"
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
                <button type="submit">搜尋</button>
            </form>
        </div>

        <?php
        // Step 1: 連接資料庫
        $link = mysqli_connect('localhost', 'root');
        mysqli_select_db($link, "announcement");

        // Step 2: 處理搜尋功能
        $search = isset($_GET['search']) ? mysqli_real_escape_string($link, $_GET['search']) : '';

        // Step 3: 查詢公告資料，根據搜尋關鍵字來篩選標題或內容
        $sql = "SELECT * FROM announce";
        if ($search) {
            $sql .= " WHERE title LIKE '%$search%' OR content LIKE '%$search%'";
        }
        $result = mysqli_query($link, $sql);

        // Step 4: 顯示公告
        while ($row = mysqli_fetch_assoc($result)) {
            // 檢查是否已收藏
            $favorite_button_text = $row['favorite'] == 1 ? '已收藏 !' : '收藏';
            $favorite_button_class = $row['favorite'] == 1 ? 'btn-favorite btn-disabled' : 'btn-favorite';

            echo '<div class="card">';
            echo '<div class="card-content">';
            echo '<div class="card-header">' . $row['title'] . '</div>';
            echo '<div class="card-description">' . $row['content'] . '</div>';
            echo '<div class="card-tags">';

            // 顯示標籤
            $tags = explode(' ', $row['tag']);
            foreach ($tags as $tag) {
                if (!empty($tag)) {
                    echo '<span class="tag">' . $tag . '</span>';
                }
            }
            echo '</div>';
            echo '</div>';

            // 顯示收藏按鈕
            echo '<div class="card-buttons">';
            if ($row['favorite'] == 0) {
                echo "<a href='favorite.php?id=" . $row['id'] . "' class='btn $favorite_button_class'>$favorite_button_text</a>";
            } else {
                echo "<span class='btn $favorite_button_class'>$favorite_button_text</span>";
            }
            echo '</div>';

            echo '</div>';
        }
        ?>
    </div>
</div>
</div>