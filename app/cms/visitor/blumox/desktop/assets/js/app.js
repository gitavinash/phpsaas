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
});
