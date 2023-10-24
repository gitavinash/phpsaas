$(document).ready(function () {
  $(".menu").click(function () {
    $(".menuBox").height("100%");
    $(".closeMenu").show();
  });
  $(".closeMenu").click(function () {
    $(".menuBox").height("0%");
    $(".closeMenu").hide();
  });
  $(".testimonials").slick({
    dots: false,
    autoplay: true,
    arrows: false,
    infinite: true,
  });

  // Contact Us Pop Up

  // $(".getInTouchBtn").click(function (e) {
  //   alert("test");
  //   e.preventDefault();
  // });
});
