<?php
session_start();
include("../database.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo "User  not logged in";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['score'])) {
    $user_id = $_SESSION['user_id'];
    $score = intval($_POST['score']);

    // Check if user already has a score
    $check_sql = "SELECT highest_score FROM snake_scores WHERE user_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $user_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $current_best = $row['highest_score'];

        if ($score > $current_best) {
            // Update the score if the new score is higher
            $update_sql = "UPDATE snake_scores SET highest_score = ? WHERE user_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "ii", $score, $user_id);
            mysqli_stmt_execute($update_stmt);
            echo "Score updated";
        } else {
            echo "Not a new high score";
        }
    } else {
        // Insert new score
        $insert_sql = "INSERT INTO snake_scores (user_id, highest_score) VALUES (?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $score);
        mysqli_stmt_execute($insert_stmt);
        echo "New score saved";
    }
} else {
    echo "Invalid request";
}
?>