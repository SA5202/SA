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

    if ($total > 0) {
        if ($total >= 10000) {
            $class = 'vip4';
            $label = 'IV';
            $to_next = 0; // 預設為 VIP4，等下可能升 VIP5
        } elseif ($total >= 5000) {
            $class = 'vip3';
            $label = 'III';
            $to_next = 10000 - $total;
        } elseif ($total >= 1000) {
            $class = 'vip2';
            $label = 'II';
            $to_next = 5000 - $total;
        } else {
            $class = 'vip1';
            $label = 'I';
            $to_next = 1000 - $total;
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
            $label = 'V';
            $to_next = 0; // 最高等級，無需升級
        }
    }

    return [
        'class' => $class,
        'label' => $label,
        'amount' => $total,
        'to_next' => $to_next,
        'tooltip' => match ($label) {
            'VIP5' => '已經到達最高等級',
            'VIP4' => '若你為本系統捐款前10名即可晉升為 VIP5（隱藏等級）',
            default => '再 ' . $to_next . ' 元可升級'
        }
    ];
}
?>