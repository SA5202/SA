<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['User_Name'])) {
    echo json_encode(["success" => false, "redirect" => "login.php", "message" => "未登入，請重新登入"]);
    exit;
}

if (!isset($_POST['Suggestion_ID'])) {
    echo json_encode(["success" => false, "message" => "缺少建言ID"]);
    exit;
}

$suggestionId = intval($_POST['Suggestion_ID']);
$userID = intval($_SESSION['User_ID']);

// 檢查是否已有按讚紀錄
$sql = "SELECT Is_Upvoted FROM Upvote WHERE Suggestion_ID = ? AND User_ID = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("ii", $suggestionId, $userID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row) {
    if ($row['Is_Upvoted'] == 1) {
        // 取消按讚
        $updateSql = "UPDATE Upvote SET Is_Upvoted = 0, Upvote_Date = NOW() WHERE Suggestion_ID = ? AND User_ID = ?";
        $stmt = $link->prepare($updateSql);
        $stmt->bind_param("ii", $suggestionId, $userID);
        if (!$stmt->execute()) {
            echo json_encode(["success" => false, "message" => "取消讚失敗", "error" => $stmt->error]);
            exit;
        }
        $stmt->close();

        // 避免數值變成負數
        $decrementSql = "UPDATE Suggestion SET Upvoted_Amount = GREATEST(Upvoted_Amount - 1, 0) WHERE Suggestion_ID = ?";
        $stmt = $link->prepare($decrementSql);
        $stmt->bind_param("i", $suggestionId);
        if (!$stmt->execute()) {
            echo json_encode(["success" => false, "message" => "更新 Upvoted_Amount 減少失敗", "error" => $stmt->error]);
            exit;
        }
        $stmt->close();

        echo json_encode(["success" => true, "liked" => false]);
    } else {
        // 加回按讚
        $updateSql = "UPDATE Upvote SET Is_Upvoted = 1, Upvote_Date = NOW() WHERE Suggestion_ID = ? AND User_ID = ?";
        $stmt = $link->prepare($updateSql);
        $stmt->bind_param("ii", $suggestionId, $userID);
        if (!$stmt->execute()) {
            echo json_encode(["success" => false, "message" => "按讚失敗", "error" => $stmt->error]);
            exit;
        }
        $stmt->close();

        $incrementSql = "UPDATE Suggestion SET Upvoted_Amount = Upvoted_Amount + 1 WHERE Suggestion_ID = ?";
        $stmt = $link->prepare($incrementSql);
        $stmt->bind_param("i", $suggestionId);
        if (!$stmt->execute()) {
            echo json_encode(["success" => false, "message" => "更新 Upvoted_Amount 增加失敗", "error" => $stmt->error]);
            exit;
        }
        $stmt->close();

        echo json_encode(["success" => true, "liked" => true]);
    }
} else {
    // 新增一筆按讚紀錄
    $insertSql = "INSERT INTO Upvote (Suggestion_ID, User_ID, Is_Upvoted, Upvote_Date) VALUES (?, ?, 1, NOW())";
    $stmt = $link->prepare($insertSql);
    $stmt->bind_param("ii", $suggestionId, $userID);
    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "新增按讚失敗", "error" => $stmt->error]);
        exit;
    }
    $stmt->close();

    // 增加 Upvoted_Amount
    $incrementSql = "UPDATE Suggestion SET Upvoted_Amount = Upvoted_Amount + 1 WHERE Suggestion_ID = ?";
    $stmt = $link->prepare($incrementSql);
    $stmt->bind_param("i", $suggestionId);
    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "更新 Upvoted_Amount 增加失敗", "error" => $stmt->error]);
        exit;
    }
    $stmt->close();

    echo json_encode(["success" => true, "liked" => true]);
}
