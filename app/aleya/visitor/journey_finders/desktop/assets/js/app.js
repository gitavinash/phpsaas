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

  // Show modal
  function openCallbackModal() {
    $("#callbackModal").removeClass("hidden");
  }

  // Hide modal on close button click
  $("#closeCallbackBtn").on("click", function () {
    $("#callbackModal").addClass("hidden");
  });

  // Optional: Close when clicking outside the modal content
  $("#callbackModal").on("click", function (e) {
    if (e.target === this) {
      $(this).addClass("hidden");
    }
  });
});
