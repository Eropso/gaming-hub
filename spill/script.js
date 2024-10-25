let score = 0;

const punch = document.getElementById('punch');
const scoreDisplay = document.getElementById('score');
const animationContainer = document.getElementById('click-animation-container');

punch.addEventListener('click', (event) => {
  score++;
  
  scoreDisplay.textContent = score;

  const plusOne = document.createElement('span');
  plusOne.textContent = '+1';
  plusOne.classList.add('click-animation');
  
  const rect = punch.getBoundingClientRect();
  const randomX = Math.random() * rect.width - rect.width / 2; 
  const randomY = Math.random() * rect.height - rect.height / 2;
  
  plusOne.style.left = `${event.offsetX + randomX}px`;
  plusOne.style.top = `${event.offsetY + randomY}px`;
  
  animationContainer.appendChild(plusOne);
  
  setTimeout(() => {
    plusOne.remove();
  }, 1000);
});
