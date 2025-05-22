<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $points = intval($_POST['points']);
    $game = isset($_POST['game']) ? $_POST['game'] : '';
    $correct = isset($_POST['correct']) ? intval($_POST['correct']) : 0;

    // Update total score as before
    $sql = "UPDATE users SET Score = Score + ? WHERE ID = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $points, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "OK";
}
?>