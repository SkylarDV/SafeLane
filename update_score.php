<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $points = intval($_POST['points']);

    $conn = new mysqli("localhost", "root", "root", "safelane");
    if ($conn->connect_error) {
        http_response_code(500);
        echo "Connection failed: " . $conn->connect_error;
        exit;
    }

    // Update all user-group rows for this user
    $sql = "UPDATE `user-group` SET Score = Score + ? WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $points, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows >= 0) {
        echo "OK";
    } else {
        http_response_code(500);
        echo "Failed to update score";
    }
    $stmt->close();
    $conn->close();
}
?>