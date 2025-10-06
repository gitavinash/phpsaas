$(document).ready(function () {
  // Scroll Header Class Toggle
  $(window).on("scroll", function () {
    if ($(this).scrollTop() > 50) {
      $("header").removeClass("header-transparent").addClass("header-solid");
    } else {
      $("header").addClass("header-transparent").removeClass("header-solid");
    }
  });

  // Mobile Menu Open/Close
  $("#menuButton").on("click", function () {
    $("#mobileMenu").removeClass("translate-x-full");
    $("#mobileMenuOverlay").removeClass("hidden");
    $("body").css("overflow", "hidden");
  });

  $("#closeMenu, #mobileMenuOverlay").on("click", function () {
    $("#mobileMenu").addClass("translate-x-full");
    $("#mobileMenuOverlay").addClass("hidden");
    $("body").css("overflow", "");
  });

  // Mobile Shop Toggle
  $("#mobileShopButton").on("click", function () {
    $("#mobileShopContent").toggleClass("hidden");
    var icon = $(this).find("i");
    var currentTransform = icon.css("transform");
    icon.css(
      "transform",
      currentTransform.includes("180") ? "" : "rotate(180deg)"
    );
  });

  // Custom Checkbox Styling
  $(".custom-checkbox input").on("change", function () {
    var checkmark = $(this).next();
    if ($(this).is(":checked")) {
      checkmark.addClass("bg-primary");
    } else {
      checkmark.removeClass("bg-primary");
    }
  });

  // Mobile Menu Toggle Button
  $("#mobile-menu-button").on("click", function () {
    $("#mobile-menu").toggleClass("hidden");
  });

  // Mobile Mega Menu Toggle
  window.toggleMobileMega = function () {
    $("#mobile-mega").toggleClass("hidden");
  };

  // Quantity Control
  $("#decrease-quantity").on("click", function () {
    var qty = parseInt($("#quantity").val());
    if (qty > 1) $("#quantity").val(qty - 1);
  });

  $("#increase-quantity").on("click", function () {
    var qty = parseInt($("#quantity").val());
    $("#quantity").val(qty + 1);
  });

  $("#quantity").on("change", function () {
    var val = parseInt($(this).val());
    if (isNaN(val) || val < 1) $(this).val(1);
  });

  // Tab Switcher
  $(".tab-button").on("click", function () {
    var tabName = $(this).data("tab");
    $(".tab-button")
      .removeClass("border-primary text-primary")
      .addClass("border-transparent text-gray-500");
    $(this)
      .removeClass("border-transparent text-gray-500")
      .addClass("border-primary text-primary");
    $(".tab-content").removeClass("active");
    $("#" + tabName + "-content").addClass("active");
  });

  // Checkbox Toggle for chocolate, card, vase
  $('label[for="chocolate"], label[for="card"], label[for="vase"]').on(
    "click",
    function (e) {
      e.preventDefault();
      var checkboxId = $(this).attr("for");
      var $checkbox = $("#" + checkboxId);
      var $icon = $(this).find(".ri-check-line");
      var $box = $(this).find(".checkbox-custom");

      $checkbox.prop("checked", !$checkbox.prop("checked"));

      if ($checkbox.prop("checked")) {
        $box.addClass("bg-primary border-primary");
        $icon.removeClass("hidden");
      } else {
        $box.removeClass("bg-primary border-primary");
        $icon.addClass("hidden");
      }
    }
  );

  // Product Thumbnail Image Zoom
  $(".thumbnail img").on("click", function () {
    var newSrc = $(this).attr("src");
    $("#mainProductImage").attr("src", newSrc);
    $("#zoomOverlay").css("background-image", "url(" + newSrc + ")");
    $(".thumbnail img").parent().removeClass("border-primary");
    $(this).parent().addClass("border-primary");
  });

  // Initial Zoom Overlay Setup
  $("#zoomOverlay").css(
    "background-image",
    "url(" + $("#mainProductImage").attr("src") + ")"
  );

  // Zoom Effect on Hover
  $(".product-image-container")
    .on("mouseenter", function () {
      $("#zoomOverlay").addClass("active");
    })
    .on("mouseleave", function () {
      $("#zoomOverlay").removeClass("active");
    })
    .on("mousemove", function (e) {
      var offset = $(this).offset();
      var x = e.pageX - offset.left;
      var y = e.pageY - offset.top;
      var width = $(this).width();
      var height = $(this).height();
      var xPercent = (x / width) * 100;
      var yPercent = (y / height) * 100;
      $("#zoomOverlay").css(
        "background-position",
        xPercent + "% " + yPercent + "%"
      );
    });
});
