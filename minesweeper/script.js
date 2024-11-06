document.addEventListener('DOMContentLoaded', function() {
    const grid = document.querySelector('.grid');
    const flagsLeft = document.querySelector('#flags-left');
    const result = document.querySelector('#result');
    const timerDisplay = document.querySelector('#timer');
    const width = 10;
    let bombAmount = 20;
    let squares = [];
    let isGameOver = false;
    let flags = 0;
    let startTime;
    let timerInterval;
    let firstClick = true;

    function createBoard() {
        flagsLeft.innerHTML = bombAmount;

        // Create empty board first, without bombs
        for (let i = 0; i < width * width; i++) {
            const square = document.createElement('div');
            square.id = i;
            square.classList.add('valid'); // All squares start as valid
            grid.appendChild(square);
            squares.push(square);

            //normal click
            square.addEventListener('click', function() {
                click(square);
            });

            //right click
            square.addEventListener('contextmenu', function(event) {
                event.preventDefault();
                addFlag(square);
            });
        }
    }

    function generateBombs(firstClickId) {
        // Create safe zone around first click
        const safeZone = getSafeZoneIndices(parseInt(firstClickId));
        
        // Create array of possible bomb positions (excluding safe zone)
        let possibleBombPositions = [];
        for (let i = 0; i < width * width; i++) {
            if (!safeZone.includes(i)) {
                possibleBombPositions.push(i);
            }
        }

        // Randomly select bomb positions
        for (let i = 0; i < bombAmount; i++) {
            const randomIndex = Math.floor(Math.random() * possibleBombPositions.length);
            const bombPosition = possibleBombPositions[randomIndex];
            squares[bombPosition].classList.remove('valid');
            squares[bombPosition].classList.add('bomb');
            possibleBombPositions.splice(randomIndex, 1);
        }

        // Add numbers
        for (let i = 0; i < squares.length; i++) {
            if (squares[i].classList.contains('valid')) {
                let total = calculateTotal(i);
                squares[i].setAttribute('data', total);
            }
        }
    }

    function getSafeZoneIndices(centerIndex) {
        const safeZone = [centerIndex];
        const isLeftEdge = (centerIndex % width === 0);
        const isRightEdge = (centerIndex % width === width - 1);

        // Add all surrounding squares to safe zone
        if (centerIndex > 0 && !isLeftEdge) safeZone.push(centerIndex - 1);
        if (centerIndex > 9 && !isRightEdge) safeZone.push(centerIndex + 1 - width);
        if (centerIndex > 10) safeZone.push(centerIndex - width);
        if (centerIndex > 11 && !isLeftEdge) safeZone.push(centerIndex - 1 - width);
        if (centerIndex < 98 && !isRightEdge) safeZone.push(centerIndex + 1);
        if (centerIndex < 90 && !isLeftEdge) safeZone.push(centerIndex - 1 + width);
        if (centerIndex < 88 && !isRightEdge) safeZone.push(centerIndex + 1 + width);
        if (centerIndex < 89) safeZone.push(centerIndex + width);

        return safeZone;
    }

    function calculateTotal(index) {
        let total = 0;
        const isLeftEdge = (index % width === 0);
        const isRightEdge = (index % width === width - 1);

        if (index > 0 && !isLeftEdge && squares[index - 1].classList.contains('bomb')) total++;
        if (index > 9 && !isRightEdge && squares[index + 1 - width].classList.contains('bomb')) total++;
        if (index > 10 && squares[index - width].classList.contains('bomb')) total++;
        if (index > 11 && !isLeftEdge && squares[index - 1 - width].classList.contains('bomb')) total++;
        if (index < 98 && !isRightEdge && squares[index + 1].classList.contains('bomb')) total++;
        if (index < 90 && !isLeftEdge && squares[index - 1 + width].classList.contains('bomb')) total++;
        if (index < 88 && !isRightEdge && squares[index + 1 + width].classList.contains('bomb')) total++;
        if (index < 89 && squares[index + width].classList.contains('bomb')) total++;

        return total;
    }

    createBoard();

    function addFlag(square) {
        if (isGameOver) return;
        if (!square.classList.contains('checked') && (flags < bombAmount)) {
            if (!square.classList.contains('flag')) {
                square.classList.add('flag');
                square.innerHTML = '🚩';
                flags++;
                flagsLeft.innerHTML = bombAmount - flags;
                checkForWin();
            } else {
                square.classList.remove('flag');
                square.innerHTML = '';
                flags--;
                flagsLeft.innerHTML = bombAmount - flags;
            }
        }
    }

    function click(square) {
        if (!startTime) {
            startTimer();
        }

        if (isGameOver || square.classList.contains('checked') || square.classList.contains('flag')) return;

        // Handle first click
        if (firstClick) {
            firstClick = false;
            generateBombs(square.id);
            // Automatically reveal area around first click
            checkSquare(square);
            return;
        }

        if (square.classList.contains('bomb')) {
            gameOver();
        } else {
            let total = square.getAttribute('data');
            if (total != 0) {
                square.classList.add('checked');
                if (total == 1) square.classList.add('one');
                if (total == 2) square.classList.add('two');
                if (total == 3) square.classList.add('three');
                if (total == 4) square.classList.add('four');
                square.innerHTML = total;
                return;
            }
            checkSquare(square);
        }
        square.classList.add('checked');
    }

    function checkSquare(square) {
        const currentId = parseInt(square.id);
        const isLeftEdge = (currentId % width === 0);
        const isRightEdge = (currentId % width === width - 1);

        setTimeout(() => {
            if (currentId > 0 && !isLeftEdge) {
                const newId = currentId - 1;
                const newSquare = document.getElementById(newId);
                click(newSquare);
            }
            if (currentId > 9 && !isRightEdge) {
                const newId = currentId + 1 - width;
                const newSquare = document.getElementById(newId);
                click(newSquare);
            }
            if (currentId > 10) {
                const newId = currentId - width;
                const newSquare = document.getElementById(newId);
                click(newSquare);
            }
            if (currentId > 11 && !isLeftEdge) {
                const newId = currentId - 1 - width;
                const newSquare = document.getElementById(newId);
                click(newSquare);
            }
            if (currentId < 98 && !isRightEdge) {
                const newId = currentId + 1;
                const newSquare = document.getElementById(newId);
                click(newSquare);
            }
            if (currentId <  90 && !isLeftEdge) {
                const newId = currentId - 1 + width;
                const newSquare = document.getElementById(newId);
                click(newSquare);
            }
            if (currentId < 88 && !isRightEdge) {
                const newId = currentId + 1 + width;
                const newSquare = document.getElementById(newId);
                click(newSquare);
            }
            if (currentId < 89) {
                const newId = currentId + width;
                const newSquare = document.getElementById(newId);
                click(newSquare);
            }
        }, 10);
    }

    function gameOver() {
        isGameOver = true;
        result.innerHTML = 'Game Over!';
        // Reveal all bombs
        squares.forEach(square => {
            if (square.classList.contains('bomb')) {
                square.classList.remove('valid');
                square.classList.add('bomb');
                square.innerHTML = '💣';
            }
        });
    }

    function checkForWin() {
        let matchedFlags = 0;
        squares.forEach(square => {
            if (square.classList.contains('flag') && square.classList.contains('bomb')) {
                matchedFlags++;
            }
        });
        if (matchedFlags === bombAmount) {
            isGameOver = true;
            result.innerHTML = 'You Win!';
        }
    }

    function startTimer() {
        startTime = Date.now();
        timerInterval = setInterval(() => {
            const elapsedTime = Math.floor((Date.now() - startTime) / 1000);
            timerDisplay.innerHTML = elapsedTime;
        }, 1000);
    }
});