$(document).ready(function () {
  $("#menuToggle").on("click", function () {
    $("#mobileMenu").removeClass("-translate-x-full").addClass("translate-x-0");
  });

  $("#menuClose").on("click", function () {
    $("#mobileMenu").removeClass("translate-x-0").addClass("-translate-x-full");
  });
  const $faqItems = $(".faq-item");

  if ($faqItems.length > 0) {
    $faqItems.each(function () {
      const $question = $(this).find(".faq-question");

      $question.on("click", () => {
        $(this).toggleClass("active");
      });
    });
  }
});
