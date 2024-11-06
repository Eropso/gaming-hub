<?php
session_start();
include("database.php"); // Ensure you have your database connection here

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(["status" => "error", "message" => "User  not logged in"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $score = $data['score'];
    $user_id = $_SESSION['user_id']; // Assuming you store user ID in session on login

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO leaderboard (user_id, score) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $score);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save score"]);
    }

    $stmt->close();
}
?>