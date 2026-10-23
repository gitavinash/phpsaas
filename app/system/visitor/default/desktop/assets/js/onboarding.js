/**
 * Onboarding & Authentication UI Enhancements
 * Path: /app/system/visitor/default/desktop/assets/js/onboarding.js
 */
$(document).ready(function () {
  // Password visibility toggle
  $(document).on("click", ".password-toggle-btn", function (e) {
    e.preventDefault();
    var $btn = $(this);
    var $input = $btn.siblings("input");
    if (!$input.length) {
      $input = $btn.closest(".input-password-wrapper").find("input");
    }
    if ($input.length && $input.attr("type") === "password") {
      $input.attr("type", "text");
      $btn.attr("aria-label", "Hide password");
      $btn.html(
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>'
      );
    } else if ($input.length) {
      $input.attr("type", "password");
      $btn.attr("aria-label", "Show password");
      $btn.html(
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>'
      );
    }
  });

  // Prevent scroll wheel accidental increment on number inputs
  $(document).on("wheel", "input[type=number]", function () {
    $(this).blur();
  });

  // Check URL query parameters for auto-continue (e.g. mobile login deep link)
  var params = new URLSearchParams(window.location.search);
  if (params.has("mobile")) {
    var mobileParam = (params.get("mobile") || "").trim();
    var $mobileInput = $("#mobile");
    var $continueBtn = $("#mobileLoginContinueBtn");

    if ($continueBtn.length && $mobileInput.length && mobileParam !== "") {
      $mobileInput.val(mobileParam);
      setTimeout(function () {
        $continueBtn.trigger("click");
      }, 200);
    }
  }
});
