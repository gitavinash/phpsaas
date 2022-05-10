// let accounts_cart = document.querySelector('.leftsidebar');
const nav_close = document.querySelector('.top-level-link .close');
const nav_open = document.querySelector('nav > .menu');
const menu = document.querySelector('.main-nav');
nav_open.addEventListener('click' , ()=>{
    menu.style.display= 'block';
})
nav_close.addEventListener('click' ,()=>{
    menu.style.display = 'none';
})
function remove_all() {
    menu.style.display = 'none';

}
