<?php
session_start();
include("database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Hub</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Erozone</h1>

        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <a href="logout.php" class="logout-button">Logout</a>
        <?php endif; ?>

        <div class="game-cards">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <a href="./spill/index.php" class="game-card">
                    <img src="./spill/punch.png" alt="Punch">
                    <h3>Punch Game</h3>
                </a>
                <a href="./minesweeper/index.php" class="game-card">
                    <p>💣</p>
                    <h3>Minesweeper</h3>
                </a>
            <?php else: ?>
                <a href="login.php?redirect=./spill/index.php" class="game-card">
                    <img src="./spill/punch.png" alt="Punch">
                    <h3>Punch Game</h3>
                </a>
                <a href="login.php?redirect=./minesweeper/index.php" class="game-card">
                <p>💣</p>
                <h3>Minesweeper</h3>
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
