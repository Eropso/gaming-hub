<?php
session_start();
include("../database.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php?redirect=./index.php");
    exit();
}

$sql = "SELECT users.username, snake_scores.highest_score FROM snake_scores 
        JOIN users ON snake_scores.user_id = users.id 
        ORDER BY highest_score DESC LIMIT 10";
$result = mysqli_query($conn, $sql);
$leaderboard = mysqli_fetch_all($result, MYSQLI_ASSOC);


if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $game_name = "Snake";
    $sql = "INSERT INTO game_plays (user_id, game_name) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $game_name);
    mysqli_stmt_execute($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Snake Game</h1>
        <div class="leaderboard">
            <h2>Leaderboard</h2>
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Username</th>
                        <th>Highest Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaderboard as $index => $score): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($score['username']); ?></td>
                            <td><?php echo htmlspecialchars($score['highest_score']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="game-area">
            <canvas id="gameCanvas" width="400" height="400"></canvas>
            <button id="playAgain">Play Again</button>
        </div>
        <div id="highScore"></div>
        <script src="script.js"></script>
    </div>
</body>
</html>