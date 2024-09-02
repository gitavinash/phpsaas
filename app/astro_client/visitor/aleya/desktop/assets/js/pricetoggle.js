let priceToggler = document.querySelector(".price-toggler");
let price1 = document.querySelectorAll(".myPrice1");
let price2 = document.querySelectorAll(".myPrice2");
let del1 = document.querySelectorAll(".del1");
let del2 = document.querySelectorAll(".del2");
let duration = document.querySelectorAll(".duration");

priceToggler.onclick = () => {
  priceToggler.classList.toggle("active");

  if (priceToggler.classList.contains("active")) {
    for (let i = 0; i < duration.length; i++) {
      duration[i].innerText = "/year upto 10 users";

      price1.forEach(function (element) {
        element.innerText = "₹ 9,999";
      });
      del1.forEach(function (element) {
        element.innerText = "₹ 24,999";
      });

      price2.forEach(function (element) {
        element.innerText = "₹ 24,999";
      });
      del2.forEach(function (element) {
        element.innerText = "₹ 49,999";
      });
    }
  } else {
    for (let i = 0; i < duration.length; i++) {
      duration[i].innerText = "/month upto 10 users";

      price1.forEach(function (element) {
        element.innerText = "₹ 999";
      });
      del1.forEach(function (element) {
        element.innerText = "₹ 2,499";
      });

      price2.forEach(function (element) {
        element.innerText = "₹ 2,499";
      });
      del2.forEach(function (element) {
        element.innerText = "₹ 4,999";
      });
    }
  }
};
