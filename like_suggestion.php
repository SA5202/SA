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

// 先檢查使用者是否已經按讚
$sql = "SELECT Is_Upvoted FROM Upvote WHERE Suggestion_ID = ? AND User_ID = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("ii", $suggestionId, $userID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    if ($row['Is_Upvoted'] == 1) {
        // 已按讚 → 取消按讚
        $updateSql = "UPDATE Upvote SET Is_Upvoted = 0 WHERE Suggestion_ID = ? AND User_ID = ?";
        $stmt = $link->prepare($updateSql);
        $stmt->bind_param("ii", $suggestionId, $userID);
        if (!$stmt->execute()) {
            echo json_encode(["success" => false, "message" => "取消讚失敗"]);
            exit;
        }
        echo json_encode(["success" => true, "liked" => false]);
    } else {
        // 原本沒按讚 → 加回按讚
        $updateSql = "UPDATE Upvote SET Is_Upvoted = 1 WHERE Suggestion_ID = ? AND User_ID = ?";
        $stmt = $link->prepare($updateSql);
        $stmt->bind_param("ii", $suggestionId, $userID);
        if (!$stmt->execute()) {
            echo json_encode(["success" => false, "message" => "按讚失敗"]);
            exit;
        }
        echo json_encode(["success" => true, "liked" => true]);
    }
} else {
    // 沒紀錄 → 新增一筆按讚
    $insertSql = "INSERT INTO Upvote (Suggestion_ID, User_ID, Is_Upvoted) VALUES (?, ?, 1)";
    $stmt = $link->prepare($insertSql);
    $stmt->bind_param("ii", $suggestionId, $userID);
    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "新增按讚失敗"]);
        exit;
    }
    echo json_encode(["success" => true, "liked" => true]);
}
