// counter

// first cart
let minusBtn = document.getElementById("minus-btn");
let count = document.getElementById("count");
let plusBtn = document.getElementById("plus-btn");

let countNum = 0;
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

// second cart
let minusBtn2 = document.getElementById("minus-btn2");
let count2 = document.getElementById("count2");
let plusBtn2 = document.getElementById("plus-btn2");

// third cart







