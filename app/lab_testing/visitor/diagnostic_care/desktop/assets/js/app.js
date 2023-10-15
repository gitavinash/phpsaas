try {
  const humburger = document.querySelector(".humburger");
  const mbNav = document.querySelector(".mobile-nav");
  const opacity = document.querySelector(".opacity");
  const close = document.querySelector(".close-icon");

  humburger.addEventListener("click", () => {
    mbNav.classList.add("active");
    opacity.classList.add("active");
  });

  close.addEventListener("click", () => {
    mbNav.classList.remove("active");
    opacity.classList.removec("active");
  });
} catch (error) {}
