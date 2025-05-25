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
    if ($total >= 10000) {
        $class = 'vip4';
        $label = 'IV';
        $to_next = 0;
        $next_level = 'VIP5';
    } elseif ($total >= 5000) {
        $class = 'vip3';
        $label = 'III';
        $to_next = 10000 - $total;
        $next_level = 'VIP4';
    } elseif ($total >= 1000) {
        $class = 'vip2';
        $label = 'II';
        $to_next = 5000 - $total;
        $next_level = 'VIP3';
    } else {
        $class = 'vip1';
        $label = 'I';
        $to_next = 1000 - $total;
        $next_level = 'VIP2';
    }

    // 查詢前 10 名用戶
    $top_sql = "SELECT User_ID, SUM(Donation_Amount) AS total FROM Donation 
                GROUP BY User_ID ORDER BY total DESC LIMIT 10";
    $top_result = mysqli_query($link, $top_sql);

    $isTop10 = false;
    while ($top_row = mysqli_fetch_assoc($top_result)) {
        if ($top_row['User_ID'] === $User_ID && $total >= 10000) {
            $isTop10 = true;
            break;
        }
    }

    // 如果是前10名，提升為VIP5
    if ($isTop10) {
        $class = 'vip5';
        $label = 'V';
        $to_next = 0;
        $next_level = null;
    }

    // 設定 tooltip 顯示內容
    if ($label === 'V') {
        $tooltip = 'VIP5 已為最高等級';
    } elseif ($label === 'IV') {
        $tooltip = '若您成為本系統總捐款金額「前10名」（目前為 NT$ ' . number_format($total) . '）即可晉升為 VIP5';
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