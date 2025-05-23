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

    if ($total > 0) {
        if ($total >= 10000) {
            // 先預設為 VIP4，因為金額已經 >= 10,000
            $class = 'vip4';
            $label = 'VIP4';
        } elseif ($total >= 5000) {
            $class = 'vip3';
            $label = 'VIP3';
        } elseif ($total >= 1000) {
            $class = 'vip2';
            $label = 'VIP2';
        } else {
            $class = 'vip1';
            $label = 'VIP1';
        }

        // 查詢前 10 名用戶的 ID
        $top_sql = "SELECT User_ID, SUM(Donation_Amount) AS total FROM Donation 
                    GROUP BY User_ID ORDER BY total DESC LIMIT 10";
        $top_result = mysqli_query($link, $top_sql);

        // 檢查用戶是否是前 10 名之一，且捐款金額大於等於 10,000
        $isTop10 = false; // 假設用戶不是前 10 名
        while ($top_row = mysqli_fetch_assoc($top_result)) {
            if ($top_row['User_ID'] === $User_ID && $total >= 10000) {
                $isTop10 = true;
                break;
            }
        }

        // 如果是前 10 名且捐款金額 >= 10,000，將等級設為 VIP5
        if ($isTop10) {
            $class = 'vip5';
            $label = 'VIP5';
        }
    }


    return [
        'class' => $class,
        'label' => $label,
        'amount' => $total
    ];
}
?>