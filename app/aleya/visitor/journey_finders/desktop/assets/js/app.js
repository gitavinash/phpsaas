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

  // Quick Filter Functionality

  $(".quick-filter").on("click", function (e) {
    e.preventDefault();

    const url = new URL(window.location.href);
    const filter = $(this).data("filter");
    const value = $(this).data("value");

    // Set or update the filter and value parameters
    url.searchParams.set("filter", filter);
    url.searchParams.set("value", value);

    // Redirect with the updated URL
    window.location.href = url.toString();
  });

  // Highlight the active filter based on current URL
  const currentUrl = new URL(window.location.href);
  const activeFilter = currentUrl.searchParams.get("filter");
  const activeValue = currentUrl.searchParams.get("value");

  $(".quick-filter").each(function () {
    const filter = $(this).data("filter");
    const value = $(this).data("value");

    if (filter == activeFilter && value == activeValue) {
      // Add active styling
      $(this)
        .removeClass("bg-gray-100 text-gray-700 hover:bg-gray-200")
        .addClass("bg-primary text-white");
    } else {
      // Reset others to default
      $(this)
        .removeClass("bg-primary text-white")
        .addClass("bg-gray-100 text-gray-700 hover:bg-gray-200");
    }
  });
});
