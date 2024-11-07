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
        const safeZone = new Set([centerIndex]);
        const isLeftEdge = (centerIndex % width === 0);
        const isRightEdge = (centerIndex % width === width - 1);
    
        // All possible directions (including diagonals)
        const directions = [
            -width-1, -width, -width+1,  // top left, top, top right
            -1, 1,                       // left, right
            width-1, width, width+1      // bottom left, bottom, bottom right
        ];
    
        directions.forEach(direction => {
            const newIndex = centerIndex + direction;
            
            // Check if the new index is within bounds
            if (newIndex >= 0 && newIndex < width * width) {
                // Check edge cases
                if ((isLeftEdge && [direction-1, -width-1, width-1].includes(direction)) ||
                    (isRightEdge && [direction+1, -width+1, width+1].includes(direction))) {
                    return;
                }
                safeZone.add(newIndex);
            }
        });
    
        return Array.from(safeZone);
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
            revealArea(parseInt(square.id));
            return;
        }
    
        if (square.classList.contains('bomb')) {
            gameOver();
            return;
        }
    
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
    
        revealArea(parseInt(square.id));
    }

    function revealArea(centerIndex) {
        const checked = new Set();
        const toCheck = [centerIndex];
    
        while (toCheck.length > 0) {
            const currentIndex = toCheck.pop();
            if (checked.has(currentIndex)) continue;
            
            checked.add(currentIndex);
            const currentSquare = squares[currentIndex];
            
            // Skip if it's already checked or has a flag
            if (currentSquare.classList.contains('checked') || 
                currentSquare.classList.contains('flag')) continue;
    
            // Reveal the current square
            currentSquare.classList.add('checked');
            
            // If it's a number, show it
            const total = currentSquare.getAttribute('data');
            if (total !== null && total !== '0') {
                currentSquare.innerHTML = total;
                if (total == 1) currentSquare.classList.add('one');
                if (total == 2) currentSquare.classList.add('two');
                if (total == 3) currentSquare.classList.add('three');
                if (total == 4) currentSquare.classList.add('four');
                continue; // Don't expand from numbers
            }
    
            // If it's an empty square, add neighbors to check
            const isLeftEdge = (currentIndex % width === 0);
            const isRightEdge = (currentIndex % width === width - 1);
    
            // Check all 8 surrounding squares
            if (currentIndex > 0 && !isLeftEdge) toCheck.push(currentIndex - 1);
            if (currentIndex > width - 1) toCheck.push(currentIndex - width);
            if (currentIndex > width && !isRightEdge) toCheck.push(currentIndex - width + 1);
            if (currentIndex < width * width - 1 && !isRightEdge) toCheck.push(currentIndex + 1);
            if (currentIndex < width * (width - 1)) toCheck.push(currentIndex + width);
            if (currentIndex < width * (width - 1) - 1 && !isRightEdge) toCheck.push(currentIndex + width + 1);
            if (currentIndex < width * (width - 1) && !isLeftEdge) toCheck.push(currentIndex + width - 1);
            if (currentIndex > width && !isLeftEdge) toCheck.push(currentIndex - width - 1);
        }
    }

    function checkSquare(square) {
        const currentId = parseInt(square.id);
        const isLeftEdge = (currentId % width === 0);
        const isRightEdge = (currentId % width === width - 1);
    
        setTimeout(() => {
            // Check neighboring squares while avoiding out-of-bounds indices
            if (currentId > 0 && !isLeftEdge) {
                const newId = currentId - 1;
                const newSquare = document.getElementById(newId);
                if (newSquare) click(newSquare);
            }
            if (currentId > width - 1) {
                const newId = currentId - width;
                const newSquare = document.getElementById(newId);
                if (newSquare) click(newSquare);
            }
            if (currentId > width && !isRightEdge) {
                const newId = currentId - width + 1;
                const newSquare = document.getElementById(newId);
                if (newSquare) click(newSquare);
            }
            if (currentId < width * (width - 1)) {
                const newId = currentId + width;
                const newSquare = document.getElementById(newId);
                if (newSquare) click(newSquare);
            }
            if (currentId < width * (width - 1) - 1 && !isRightEdge) {
                const newId = currentId + width + 1;
                const newSquare = document.getElementById(newId);
                if (newSquare) click(newSquare);
            }
            if (currentId < width * (width - 1) && !isLeftEdge) {
                const newId = currentId + width - 1;
                const newSquare = document.getElementById(newId);
                if (newSquare) click(newSquare);
            }
            if (currentId < width - 1 && !isRightEdge) {
                const newId = currentId + 1;
                const newSquare = document.getElementById(newId);
                if (newSquare) click(newSquare);
            }
            if (currentId > width && !isLeftEdge) {
                const newId = currentId - width - 1;
                const newSquare = document.getElementById(newId);
                if (newSquare) click(newSquare);
            }
        }, 10);
    }

    function saveScore(time) {
        fetch('save_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'time=' + time
        })
        .then(response => response.text())
        .then(data => console.log(data))
        .catch((error) => console.error('Error:', error));
    }
    
    function gameOver() {
        isGameOver = true;
        result.innerHTML = 'Game Over!';
        clearInterval(timerInterval);  // Stop the timer
    
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
            clearInterval(timerInterval);  // Stop the timer
            const finalTime = parseInt(timerDisplay.innerHTML);
            saveScore(finalTime);  // Save the score
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