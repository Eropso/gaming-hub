<?php
session_start();
include("../database.php");

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $game_name = "PunchGame";
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
  <title>Punch Clicker</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Punch Clicker</h1>
    <p>Strength: <span id="score">0</span></p>
    <div class="punch-container">
      <img id="punch" src="punch.png" alt="Punch">
      <div id="click-animation-container"></div>
    </div>
  </div>
  <script src="script.js"></script>
</body>
</html>
