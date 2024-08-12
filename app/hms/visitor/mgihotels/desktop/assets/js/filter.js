var myFilter = document.querySelector('.myFilter');
var filterSlide = document.querySelector('.filter_slide');
var cancelBtn = document.querySelector('.cancel');

myFilter.addEventListener('click', () => {
    filterSlide.classList.toggle('show');
});
cancelBtn.addEventListener('click', () => {
    filterSlide.classList.toggle('show');
});