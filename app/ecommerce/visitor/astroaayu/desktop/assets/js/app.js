const nav = document.querySelector('.nav');
window.addEventListener('scroll',function(){
    if(scrollY > 100){
        nav.classList.add('nav_active');
    }else{
        nav.classList.remove('nav_active')
    }
})
const toggle = document.querySelector('.toggler');
const nav_res = document.querySelector('.nav_res');
const close = document.querySelector('.close_icon_res');
toggle.addEventListener('click',function(){
  nav_res.classList.toggle('nav_res_active');
})
close.addEventListener('click',function(){
    nav_res.classList.remove('nav_res_active');
})
window.addEventListener('click',function(e){
    if(e.target == nav_res){
        nav_res.classList.remove('nav_res_active');
    }
});
