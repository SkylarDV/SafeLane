<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $user_id = intval($_POST['user_id']);
    $points = intval($_POST['points']);
    $game = isset($_POST['game']) ? $_POST['game'] : '';
    $correct = isset($_POST['correct']) ? intval($_POST['correct']) : 0;

    // Update total score in user-group for all groups this user is in
    $sql = "UPDATE `user-group` SET Score = Score + ? WHERE User_ID = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $points, $user_id);
    $stmt->execute();
    $stmt->close();

    // Update Q1-Q10 in user-results if question_number is set
    if (isset($_POST['question_number'])) {
        $question_number = intval($_POST['question_number']);
        if ($question_number >= 1 && $question_number <= 10) {
            $monday = date('Y-m-d', strtotime('monday this week'));
            $column = "Q" . $question_number;
            $is_correct = $correct ? 1 : 0;
            $sql = "UPDATE `user-results` SET `$column` = ? WHERE User_ID = ? AND Date = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("iis", $is_correct, $user_id, $monday);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo "OK";
}
?>
