var menu_icon = document.querySelector('.menu_icon');
var menuContent = document.querySelector('.menu_wrapper');
var menuCancel = document.querySelector('.menu_cancel');

menu_icon.addEventListener("click", () => {
    menuContent.classList.toggle('show');
});
menuCancel.addEventListener("click", () => {
    menuContent.classList.toggle('show');
});


// menu bottom
var menu = document.querySelector('.menu');
var menuContent = document.querySelector('.menu_wrapper');

menu.addEventListener("click", () => {
    menuContent.classList.toggle('show');
});
