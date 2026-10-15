window.addEventListener("scroll", function () {
    var header = this.document.querySelector(".myNav");
    header.classList.toggle("sticky", this.window.scrollY > 0);
});

// navbar menu
const menu = document.querySelector('.menu_icon');
const mobileNav = document.querySelector('.mobile_nav');
const cancelMenu = document.querySelector('.cancel_icon');

menu.addEventListener('click', () => {
    mobileNav.classList.toggle('active');
});
cancelMenu.addEventListener('click', () => {
    mobileNav.classList.toggle('active');
});
