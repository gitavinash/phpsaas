var myFilter = document.querySelector('.myFilter');
var filterContent = document.querySelector('.filter_overlay');
var filterCancel = document.querySelector('.filter_cancel');

myFilter.addEventListener("click", () => {
    filterContent.classList.toggle('show');
});

filterCancel.addEventListener("click", () => {
    filterContent.classList.toggle('show');
});