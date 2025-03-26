$(document).ready(function (event) {
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

  $(".getInTouchBtn").click(function (e) {
    // $(".popUpModal").show();
    // e.preventDefault();
  });

  $(".popUpModal").click(function (event) {
    if (!$(event.target).closest(".popUpModal").length) {
      // Check if the clicked element is not a child of .popUpModal
      $(".popUpModal").hide();
    }
  });

  // Prevent clicks within .popUpModal from triggering the document click event
  $(".popUpModal").click(function (event) {
    event.stopPropagation();
  });
});
