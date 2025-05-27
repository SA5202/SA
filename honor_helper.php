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

    // 如果沒有捐款，直接返回 level 為 0
    if ($total == 0) {
        return [
            'level' => 0,
            'class' => '',
            'label' => '',
            'amount' => 0,
            'to_next' => 0,
            'tooltip' => '您尚未捐款'
        ];
    }

    // 預設等級與樣式
    $class = '';
    $label = '';
    $to_next = null;
    $next_level = null;

    // 根據捐款金額決定等級
    if ($total >= 50000) {
        $class = 'vip4';
        $label = 'IV';
        $to_next = 0;
        $next_level = 'VIP5';
    } elseif ($total >= 10000) {
        $class = 'vip3';
        $label = 'III';
        $to_next = 50000 - $total;
        $next_level = 'VIP4';
    } elseif ($total >= 5000) {
        $class = 'vip2';
        $label = 'II';
        $to_next = 10000 - $total;
        $next_level = 'VIP3';
    } else {
        $class = 'vip1';
        $label = 'I';
        $to_next = 5000 - $total;
        $next_level = 'VIP2';
    }

    // 先查出用戶總數
    $count_sql = "SELECT COUNT(DISTINCT User_ID) AS user_count FROM Donation";
    $count_result = mysqli_query($link, $count_sql);
    $count_row = mysqli_fetch_assoc($count_result);
    $user_count = (int)$count_row['user_count'];

    // 計算前 10% 名單數量（至少 1 名）
    $top_percentage = 0.1;
    $top_limit = max(1, ceil($user_count * $top_percentage));

    // 查詢前 10% 用戶
    $top_sql = "SELECT User_ID, SUM(Donation_Amount) AS total FROM Donation 
                GROUP BY User_ID 
                ORDER BY total DESC 
                LIMIT $top_limit";
    $top_result = mysqli_query($link, $top_sql);

    $isTopPercentage = false;
    while ($top_row = mysqli_fetch_assoc($top_result)) {
        if ($top_row['User_ID'] === $User_ID && $top_row['total'] >= 50000) {
            $isTopPercentage = true;
            break;
        }
    }

    // 如果是前 10%，提升為 VIP 5
    if ($isTopPercentage) {
        $class = 'vip5';
        $label = 'V';
        $to_next = 0;
        $next_level = null;
    }

    // 設定 tooltip 顯示內容
    if ($label === 'V') {
        $tooltip = '當前已為最高等級';
    } elseif ($label === 'IV') {
        $tooltip = '累計總捐款金額排名前 10%（目前為 NT$ ' . number_format($total) . '）即可晉升為 VIP 5';
    } else {
        $tooltip = 'NT$ '. number_format($total) . ' / NT$ ' . number_format($total + $to_next);
    }

    return [
        'level' => 1,  // 在這裡返回 level 為 1 表示有 VIP 等級
        'class' => $class,
        'label' => $label,
        'amount' => $total,
        'to_next' => $to_next,
        'tooltip' => $tooltip
    ];
}

?>