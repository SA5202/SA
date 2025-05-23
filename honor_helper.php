<?php
// honor_helper.php

function getVipLevel($link, $User_ID) {
    // 確保用戶 ID 為字串且安全
    $User_ID = mysqli_real_escape_string($link, $User_ID);

    // 計算目前用戶總捐款金額
    $sql = "SELECT SUM(Donation_Amount) AS total FROM Donation WHERE User_ID = '$User_ID'";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    $total = $row ? floatval($row['total']) : 0;

    // 預設等級與樣式
    $class = '';
    $label = '';
    $to_next = null;
    $next_level = null;

    // 根據捐款金額判斷等級
    if ($total > 0) {
        if ($total >= 10000) {
            $class = 'vip4';
            $label = 'IV'; // VIP4
            $to_next = 0; // 無需升級
            $next_level = 'VIP5'; // 下一等級 VIP5
        } elseif ($total >= 5000) {
            $class = 'vip3';
            $label = 'III'; // VIP3
            $to_next = 10000 - $total; // 距離 VIP4 還差多少
            $next_level = 'VIP4'; // 下一等級 VIP4
        } elseif ($total >= 1000) {
            $class = 'vip2';
            $label = 'II'; // VIP2
            $to_next = 5000 - $total; // 距離 VIP3 還差多少
            $next_level = 'VIP3'; // 下一等級 VIP3
        } else {
            $class = 'vip1';
            $label = 'I'; // VIP1
            $to_next = 1000 - $total; // 距離 VIP2 還差多少
            $next_level = 'VIP2'; // 下一等級 VIP2
        }

        // 查詢前 10 名用戶的 ID
        $top_sql = "SELECT User_ID, SUM(Donation_Amount) AS total FROM Donation 
                    GROUP BY User_ID ORDER BY total DESC LIMIT 10";
        $top_result = mysqli_query($link, $top_sql);

        // 檢查用戶是否是前 10 名之一，且捐款金額大於等於 10,000
        $isTop10 = false;
        while ($top_row = mysqli_fetch_assoc($top_result)) {
            if ($top_row['User_ID'] === $User_ID && $total >= 10000) {
                $isTop10 = true;
                break;
            }
        }

        // 如果是前 10 名且捐款金額 >= 10,000，升為 VIP5
        if ($isTop10) {
            $class = 'vip5';
            $label = 'V'; // VIP5
            $to_next = 0; // 最高等級，無需升級
            $next_level = 'VIP5'; // 無下一等級
        }
    }

    // 用 if / else 來處理 tooltip 顯示
    if ($label === 'V') {
        $tooltip = 'VIP5 已為最高等級';
    } elseif ($label === 'IV') {
        $tooltip = '若您成為本系統總捐款金額數"前10名"即可晉升為 VIP5（隱藏等級）';
    } else {
        $tooltip = '再 ' . $to_next . ' 元可升級到 ' . $next_level;
    }

    return [
        'class' => $class,
        'label' => $label,
        'amount' => $total,
        'to_next' => $to_next,
        'tooltip' => $tooltip
    ];
}
?>
