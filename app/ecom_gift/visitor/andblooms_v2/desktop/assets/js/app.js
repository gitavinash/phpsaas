$(document).ready(function () {
  // Toggle overlay navigation
  $(".top_menu").on("click", function () {
    $(".overlay").toggleClass("show");
  });

  $(".close").on("click", function () {
    $(".overlay").toggleClass("show");
  });

  // Toggle mobile search
  $(".mobile_search").on("click", function () {
    $(".search_overlay").toggleClass("active");
  });

  $(".cancel_search__input").on("click", function () {
    $(".search_overlay").toggleClass("active");
  });

  // Filter toggle
  $(".filter_btn").on("click", function () {
    $(".filter_overlay").toggleClass("myfilter");
  });

  $(".filter_cancel").on("click", function () {
    $(".filter_overlay").toggleClass("myfilter");
  });

  // Image change on click
  window.myFunction = function (smallImg) {
    $("#imgBox").attr("src", $(smallImg).attr("src"));
  };

  // Slick carousel
  if ($(".testimonial_slider").length) {
    $(".testimonial_slider").slick({
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 1,
      autoplay: false,
      autoplaySpeed: 2000,
      arrows: false,
      dots: true,
      responsive: [
        {
          breakpoint: 1200,
          settings: {
            slidesToShow: 2,
          },
        },
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 2,
          },
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1,
          },
        },
        {
          breakpoint: 576,
          settings: {
            slidesToShow: 1,
          },
        },
      ],
    });
  }
});
