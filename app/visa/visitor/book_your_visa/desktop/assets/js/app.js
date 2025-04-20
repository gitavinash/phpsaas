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

  // Select JS

  if ($(".selectJS").length) {
    function formatCountry(country) {
      if (!country.id) {
        return country.text;
      }
      var flagUrl = $(country.element).data("flag");
      var $country = $(
        `<div style="display: flex; align-items: center; padding: 5px;">
          <img src="${flagUrl}" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px; border: 1px solid #bcbcbc;" />
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

  // Visa service page

  if ($("#countryScroll").length) {
    const $scrollContainer = $("#countryScroll");
    $("#scroll-left").on("click", function () {
      $scrollContainer.animate({ scrollLeft: "-=300" }, 400);
    });
    $("#scroll-right").on("click", function () {
      $scrollContainer.animate({ scrollLeft: "+=300" }, 400);
    });
  }

  // Visa details page

  if ($("#visaMainContent")) {
    // Toggle between Business and Standard buttons
    const $buttons = $("button");
    const $businessBtn = $buttons.eq(2);
    const $standardBtn = $buttons.eq(3);

    $businessBtn.on("click", function () {
      $businessBtn
        .removeClass("bg-gray-100 text-gray-500")
        .addClass("bg-white text-gray-800 shadow-sm");
      $standardBtn
        .removeClass("bg-white text-gray-800 shadow-sm")
        .addClass("bg-gray-100 text-gray-500");
    });

    $standardBtn.on("click", function () {
      $standardBtn
        .removeClass("bg-gray-100 text-gray-500")
        .addClass("bg-white text-gray-800 shadow-sm");
      $businessBtn
        .removeClass("bg-white text-gray-800 shadow-sm")
        .addClass("bg-gray-100 text-gray-500");
    });

    // Quick menu navigation
    const $menuItems = $("nav div");
    const $mainContent = $("#visaMainContent");

    $menuItems.on("click", function () {
      // Remove active class from all menu items
      $menuItems
        .removeClass("text-primary border-l-4 border-primary")
        .addClass("text-gray-700 hover:text-primary");

      // Add active class to clicked item
      $(this)
        .removeClass("text-gray-700 hover:text-primary")
        .addClass("text-primary border-l-4 border-primary");

      // Get target section text
      const targetSection = $(this).find("span").text();

      // Scroll to corresponding section
      $mainContent.children().each(function () {
        if ($(this).text().includes(targetSection)) {
          $(this)[0].scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
          $(this).addClass("bg-blue-50");
          setTimeout(() => {
            $(this).removeClass("bg-blue-50");
          }, 1000);
          return false; // break loop
        }
      });
    });

    // Bottom sticky bar
  }

  // Homepage from for start application

  if ($("#startApplication").length) {
    console.log(`startApplication found`);

    $("#startApplication").click(function (e) {
      e.preventDefault();
      let selectedCountry = $("#selectedCountry").val();
      if (selectedCountry) {
        console.log("selectedCountry", selectedCountry);
        selectedCountry = selectedCountry.toLowerCase().replace(/\s+/g, "-");
        window.location.href = `/start-application/?country=${selectedCountry}`;
      }
    });
  }

  if ($("#startApplicationDropDown").length) {
    console.log(`startApplicationDropDown found`);
    $("#startApplicationDropDown").change(function () {
      let selectedCountry = $(this).val();
      if (selectedCountry) {
        selectedCountry = selectedCountry.toLowerCase().replace(/\s+/g, "-");
        window.location.href = `/start-application/?country=${selectedCountry}`;
      }
    });
  }

  const urlParams = new URLSearchParams(window.location.search);
  const country = urlParams.get("country");
  const purpose = urlParams.get("purpose");
  const type = urlParams.get("type"); // Optional, in case needed later

  // Hide all by default
  $("#selectCountry").hide();
  $("#purposeOfVisa").hide();
  $("#visaPackages").hide();

  // Case: No country parameter
  if (!country) {
    $("#selectCountry").show();
    return;
  }

  // Case: Country is present
  $("#purposeOfVisa").show();

  // Optional: fetch and populate purposeOfVisa data from API
  $.ajax({
    url: "/api/company/?id=5",
    method: "GET",
    success: function (data) {
      $("#purposeOfVisa").html(data);
    },
    error: function () {
      console.error("Error fetching purposeOfVisa data");
    },
  });

  // Case: Country and Purpose are present and not empty
  if (purpose && purpose.trim() !== "") {
    $("#visaPackages").show();

    // Optional: fetch visaPackages data from API
    $.ajax({
      url: "/api/visa/purpose/",
      method: "GET",
      data: {
        country: country,
        purpose: purpose,
      },
      success: function (data) {
        $("#visaPackages").html(data);
      },
      error: function () {
        console.error("Error fetching visaPackages");
      },
    });
  }
});
