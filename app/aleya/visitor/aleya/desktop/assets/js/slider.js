// const prevButton = document.querySelector('.custom-prev');
// const nextButton = document.querySelector('.custom-next');

// nextButton.addEventListener('click', function () {
//     prevButton.classList.add('active');
//     nextButton.classList.add('active');
// });

document.addEventListener("DOMContentLoaded", function() {
    const prevButton = document.querySelector('.custom-prev');
    const nextButton = document.querySelector('.custom-next');

    nextButton.addEventListener('click', function () {
      prevButton.classList.add('active');
      nextButton.classList.add('active');
    });
  });