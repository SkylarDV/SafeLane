<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $points = intval($_POST['points']);

    // Update all user-group rows for this user
    $sql = "UPDATE `user-group` SET Score = Score + ? WHERE User_ID = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $points, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows >= 0) {
        echo "OK";
    } else {
        http_response_code(500);
        echo "Failed to update score";
    }
    $stmt->close();
}
?>