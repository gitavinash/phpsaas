$(document).ready(function (e) {
  // Header, Menu and Scroll

  const $header = $(".sticky-header");
  const $backToTop = $(".back-to-top");
  const $mobileMenuBtn = $("#mobileMenuBtn");
  const $mobileMenu = $("#mobileMenu");

  // Mobile menu toggle
  $mobileMenuBtn.on("click", function () {
    $mobileMenu.toggleClass("hidden");
    $mobileMenuBtn.html(
      $mobileMenu.hasClass("hidden")
        ? '<i class="ri-menu-line ri-2x"></i>'
        : '<i class="ri-close-line ri-2x"></i>'
    );
  });

  $(window).on("scroll", function () {
    if ($(window).scrollTop() > 100) {
      $header.addClass("shadow");
      $backToTop.addClass("visible");
    } else {
      $header.removeClass("shadow");
      $backToTop.removeClass("visible");
    }
  });

  $backToTop.on("click", function () {
    $("html, body").animate({ scrollTop: 0 }, "smooth");
  });

  const $chatButton = $("#chatButton");
  let $chatWindow = null;

  $chatButton.on("click", function () {
    if (!$chatWindow) {
      $chatWindow = $(`
                <div class="fixed bottom-24 right-6 w-80 bg-white rounded-lg shadow-xl z-50">
                    <div class="p-4 bg-primary rounded-t-lg flex justify-between items-center">
                        <h3 class="text-white font-medium">Chat Support</h3>
                        <button class="text-white hover:text-gray-200" id="closeChat">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <div class="h-96 p-4 overflow-y-auto">
                        <div class="text-center text-gray-500 text-sm mb-4">How can we help you today?</div>
                    </div>
                    <div class="p-4 border-t">
                        <div class="flex space-x-2">
                            <input type="text" placeholder="Type your message..." class="flex-1 px-3 py-2 border rounded-button text-sm">
                            <button class="bg-primary text-white px-4 !rounded-button hover:bg-primary/90">
                                <i class="ri-send-plane-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
      $("body").append($chatWindow);

      $("#closeChat").on("click", function () {
        $chatWindow.remove();
        $chatWindow = null;
      });
    }
  });

  // Select JS

  if ($(".selectJS").length) {
    function formatCountry(country) {
      if (!country.id) {
        return country.text;
      }
      var flagUrl = $(country.element).data("flag");
      var $country = $(
        `<div style="display: flex; align-items: center; padding: 5px;">
          <img src="${flagUrl}" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;" />
          <span>${country.text}</span>
        </div>`
      );
      return $country;
    }

    $(".selectJS").select2({
      placeholder: "Search for country",
      templateResult: formatCountry,
      tag: true,
      //   allowClear: true,
      templateSelection: formatCountry,
      width: "100%", // Optional: Adjust width
    });
  }
});
