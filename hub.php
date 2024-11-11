<?php
session_start();
include("database.php");

// Fetch game popularity data
$sql = "SELECT game_name, COUNT(*) as play_count FROM game_plays GROUP BY game_name ORDER BY play_count DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
$gameData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $gameData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Hub</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1 class="title">Erozone</h1>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <a href="logout.php" class="logout-button">Logout</a>
            <?php endif; ?>
        </header>

        <section class="game-section">
            <div class="game-cards">
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <a href="./snake/index.php" class="game-card">
                        <img src="./snake/snake.jpg" alt="Snake Game">
                        <h3>Snake</h3>
                    </a>
                    <a href="./minesweeper/index.php" class="game-card">
                        <img src="./minesweeper/northkorea.png" alt="Minesweeper Game">
                        <h3>Minesweeper</h3>
                    </a>
                <?php else: ?>
                    <a href="login.php?redirect=./snake/index.php" class="game-card">
                        <img src="./snake/snake.jpg" alt="Snake Game">
                        <h3>Snake</h3>
                    </a>
                    <a href="login.php?redirect=./minesweeper/index.php" class="game-card">
                        <img src="./minesweeper/northkorea.png" alt="Minesweeper Game">
                        <h3>Minesweeper</h3>
                    </a>
                <?php endif; ?>
            </div>

            <div class="chart-container">
                <canvas id="popularityChart"></canvas>
            </div>
        </section>
    </div>

    <script>
        const gameData = <?php echo json_encode($gameData); ?>;
        
        const ctx = document.getElementById('popularityChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: gameData.map(game => game.game_name),
                datasets: [{
                    data: gameData.map(game => game.play_count),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Most Popular Games'
                    }
                }
            }
        });
    </script>
</body>
</html>
