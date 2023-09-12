const menu_btn = document.querySelector('.menu_icon');
const close_btn = document.querySelector('.close_icon');
const header_bg = document.querySelector('.links_nav');
menu_btn.addEventListener('click',()=>{
    header_bg.classList.add('links_nav_active')
})
close_btn.addEventListener('click',()=>{
    header_bg.classList.remove('links_nav_active') 
})