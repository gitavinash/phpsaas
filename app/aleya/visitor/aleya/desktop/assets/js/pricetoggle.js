let priceToggler = document.querySelector(".price-toggler");
let price1 = document.querySelectorAll(".myPrice1");
let price2 = document.querySelectorAll(".myPrice2");
let del1 = document.querySelectorAll(".del1");
let del2 = document.querySelectorAll(".del2");
let duration = document.querySelectorAll(".duration");

const setAnnualPricing = () => {
  duration.forEach(
    (el) => (el.innerText = "/month, billed annually · up to 10 users"),
  );
  price1.forEach((el) => (el.innerText = "999"));
  del1.forEach((el) => {
    el.innerText = "₹1,249 /mo";
    el.style.display = "";
  });
  price2.forEach((el) => (el.innerText = "2,499"));
  del2.forEach((el) => {
    el.innerText = "₹3,124 /mo";
    el.style.display = "";
  });
};

const setMonthlyPricing = () => {
  duration.forEach((el) => (el.innerText = "/month · up to 10 users"));
  price1.forEach((el) => (el.innerText = "1,249"));
  del1.forEach((el) => {
    el.innerText = "";
    el.style.display = "none";
  });
  price2.forEach((el) => (el.innerText = "3,124"));
  del2.forEach((el) => {
    el.innerText = "";
    el.style.display = "none";
  });
};

if (priceToggler) {
  if (priceToggler.classList.contains("active")) {
    setAnnualPricing();
  } else {
    setMonthlyPricing();
  }

  priceToggler.onclick = () => {
    priceToggler.classList.toggle("active");

    if (priceToggler.classList.contains("active")) {
      setAnnualPricing();
    } else {
      setMonthlyPricing();
    }
  };
}
