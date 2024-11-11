const canvas = document.getElementById('gameCanvas');
const context = canvas.getContext('2d');
const playAgainButton = document.getElementById('playAgain');
const highScoreDiv = document.getElementById('highScore');

const box = 20;
let snake, direction, food, score, game;
let highScore = JSON.parse(localStorage.getItem('highScore')) || 0;

function initializeGame() {
    snake = [{ x: 9 * box, y: 10 * box }];
    direction = 'RIGHT';
    score = 0;
    generateFood();
    updateHighScore();
    if (game) clearInterval(game);
    game = setInterval(gameLoop, 150);
}

function generateFood() {
    do {
        food = {
            x: Math.floor(Math.random() * 18 + 1) * box, 
            y: Math.floor(Math.random() * 18 + 1) * box  
        };
    } while (snake.some(segment => segment.x === food.x && segment.y === food.y));
}

function updateHighScore() {
    highScoreDiv.innerHTML = `<strong>High Score:</strong> ${highScore}`;
}

document.addEventListener('keydown', event => {
    if (event.key === 'ArrowLeft' && direction !== 'RIGHT') direction = 'LEFT';
    else if (event.key === 'ArrowUp' && direction !== 'DOWN') direction = 'UP';
    else if (event.key === 'ArrowRight' && direction !== 'LEFT') direction = 'RIGHT';
    else if (event.key === 'ArrowDown' && direction !== 'UP') direction = 'DOWN';
});

playAgainButton.addEventListener('click', initializeGame);

function gameLoop() {
    let head = { ...snake[0] };
    if (direction === 'LEFT') head.x -= box;
    if (direction === 'UP') head.y -= box;
    if (direction === 'RIGHT') head.x += box;
    if (direction === 'DOWN') head.y += box;

    if (head.x < 0 || head.x >= canvas.width || head.y < 0 || head.y >= canvas.height || snake.some(segment => segment.x === head.x && segment.y === head.y)) {
        clearInterval(game);
        if (score > highScore) {
            highScore = score;
            localStorage.setItem('highScore', JSON.stringify(highScore));
            saveScore(score); 
        }
        alert('Game Over! Your score: ' + score);
        updateHighScore();
        return;
    }

    if (head.x === food.x && head.y === food.y) {
        score++;
        generateFood();
    } else {
        snake.pop();
    }

    snake.unshift(head);

    context.clearRect(0, 0, canvas.width, canvas.height);

    snake.forEach(segment => {
        context.fillStyle = 'green';
        context.fillRect(segment.x, segment.y, box, box);
        context.strokeStyle = 'black';
        context.strokeRect(segment.x, segment.y, box, box);
    });

    context.fillStyle = 'red';
    context.fillRect(food.x, food.y, box, box);

    context.fillStyle = 'black';
    context.font = '20px Arial';
    context.fillText('Score: ' + score, 10, 20);
}

function saveScore(score) {
    fetch('save_snake_score.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'score=' + score
    })
    .then(response => response.text())
    .then(data => console.log(data))
    .catch((error) => console.error('Error:', error));
}

initializeGame();