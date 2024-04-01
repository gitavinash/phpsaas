var cart = document.querySelector(".cart");
var cartContent = document.querySelector(".cart_wrapper");
var cartCancel = document.querySelector(".cart_cancel");

cart.addEventListener("click", () => {
    cartContent.classList.toggle('active');
});
cartCancel.addEventListener("click", () => {
    cartContent.classList.toggle('active');
});


// search poppup
var search = document.querySelector(".search");
var searchContent = document.querySelector(".serach_overlay");
var searchCancel = document.querySelector(".search_cancel");

search.addEventListener("click", () => {
    searchContent.classList.toggle('active');
});
searchCancel.addEventListener("click", () => {
    searchContent.classList.toggle('active');
});