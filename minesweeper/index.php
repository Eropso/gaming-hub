<?php
session_start();
include("../database.php"); 



if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php?redirect=/minesweeper/index.php");
    exit();
}


$sql = "SELECT username, best_time FROM minesweeper_scores JOIN users ON minesweeper_scores.user_id = users.id ORDER BY best_time ASC LIMIT 10";
$result = mysqli_query($conn, $sql);
$leaderboard = mysqli_fetch_all($result, MYSQLI_ASSOC);




if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $game_name = "Minesweeper";
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
    <title>Minesweeper</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="game-container">
        <div class="game-area">
            <div class="grid"></div>
            <div>Flags left: <span id="flags-left"></span></div>
            <div>Time: <span id="timer">0</span></div>
            <div id="result"></div>
        </div>
        <div class="leaderboard">
            <h2>Leaderboard</h2>
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Username</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaderboard as $index => $score): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($score['username']); ?></td>
                            <td><?php echo $score['best_time']; ?> seconds</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>