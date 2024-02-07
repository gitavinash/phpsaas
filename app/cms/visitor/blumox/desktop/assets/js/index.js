const slider = document.querySelector(".slide");
const mobileNav = document.querySelector(".mobile-nav")
const crose = document.querySelector(".crose")
slider.addEventListener("click", () => {
    console.log(mobileNav);
    mobileNav.classList.add('add')
});
crose.addEventListener("click", () => { mobileNav.classList.remove('add') });