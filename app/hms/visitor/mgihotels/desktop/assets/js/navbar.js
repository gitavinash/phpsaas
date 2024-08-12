var menu = document.querySelector('.menu');
var menuSLide = document.querySelector('.menu_container');
var menuCancel = document.querySelector('.cut');

menu.addEventListener('click', () => {
    menuSLide.classList.toggle('show');
});
menuCancel.addEventListener('click', () => {
    menuSLide.classList.toggle('show');
});