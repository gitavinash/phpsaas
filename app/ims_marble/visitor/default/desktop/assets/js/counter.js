let minusBtn = document.getElementById("minus-btn");
let count = document.getElementById("count");
let plusBtn = document.getElementById("plus-btn");

let countNum = 1;
count.innerHTML = countNum;

minusBtn.addEventListener("click", () => {
    if (countNum > 1) {
        countNum -= 1;
        count.innerHTML = countNum;
    }
});

plusBtn.addEventListener("click", () => {
    countNum += 1;
    count.innerHTML = countNum;
});


// otp number

var counter = 30;
var countdownElement = document.getElementById('countdown');
var newYearCountdown = setInterval(function(){
  countdownElement.textContent = counter;
  counter--;
  if (counter === 0) {
    countdownElement.textContent = "HAPPY NEW YEAR!!";
    clearInterval(newYearCountdown);
  }
}, 1000);