$(document).ready(function (e) {
  /** SweetAlert Pop Up Box */

  function popUpMessage(msgPostion, msgTxt, msgType) {
    Swal.fire({
      position: msgPostion,
      type: msgType,
      title: msgTxt,
      showConfirmButton: false,
      timer: 1500,
    });
  }

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
    $("#startApplication").click(function (e) {
      e.preventDefault();
      let selectedCountry = $("#selectedCountry").val();
      if (selectedCountry) {
        console.log("selectedCountry", selectedCountry);
        selectedCountry = selectedCountry.toLowerCase().replace(/\s+/g, "_");
        window.location.href = `/start-application/?country=${selectedCountry}`;
      } else {
        popUpMessage(
          "center",
          "Please select country to start application",
          "warning"
        );
      }
    });
  }

  if ($("#startApplicationDropDown").length) {
    console.log(`startApplicationDropDown found`);
    $("#startApplicationDropDown").change(function () {
      let selectedCountry = $(this).val();
      if (selectedCountry) {
        selectedCountry = selectedCountry.toLowerCase().replace(/\s+/g, "_");
        window.location.href = `/start-application/?country=${selectedCountry}`;
      }
    });
  }

  // Start application process page

  const urlParams = new URLSearchParams(window.location.search);
  const country = urlParams.get("country");
  const purpose = urlParams.get("purpose");
  const type = urlParams.get("type"); // Optional, in case needed later

  $("#selectCountry").hide();
  $("#purposeOfVisa").hide();
  $("#visaPackages").hide();

  // Case: No country parameter
  if (!country) {
    $("#selectCountry").show();
    return;
  }

  // Show visa purpose

  if (country && !purpose) {
    $("#selectCountry").hide();
    console.log(`Show visa purpose`);
    const $visaContainer = $("#visaPurposeListBox");
    if ($("#visaPurposeListBox").length) {
      const urlParams = new URLSearchParams(window.location.search);
      const countryName = urlParams.get("country");
      if (!countryName) {
        $visaContainer.html(
          '<p class="text-red-500">Missing country ID in the URL.</p>'
        );
        return;
      }
      $visaContainer.html('<p class="text-gray-500">Loading visa types...</p>');
      $.ajax({
        url: `/api/country/visas/?country=${countryName}`,
        method: "GET",
        success: function (res) {
          console.log(`Success data found`, res);
          $visaContainer.empty();
          console.log(`Country data:`, res.countryList);

          if (Array.isArray(res.countryList)) {
            res.countryList.forEach((type) => {
              const formattedType =
                type.charAt(0).toUpperCase() + type.slice(1); // Capitalize
              const purposeUrl = formattedType.toLowerCase().replace(" ", "_");
              console.log(`formattedType: `, formattedType);

              const visaCard = `
                            <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl mb-4">
                               <a href='/start-application/?country=${countryName}&purpose=${purposeUrl}'>
                                 <div class="flex items-center mb-3">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-gray-600/5">
                                        <i class="ri-more-line text-gray-600 ri-xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-semibold text-gray-900">${formattedType}</h3>
                                </div>
                                <p class="text-gray-600 mb-4">Visa information for ${formattedType.toLowerCase()} travel purposes.</p>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="ri-time-line mr-2 w-4 h-4 flex items-center justify-center"></i>
                                    <span>Processing time may vary</span>
                                </div>
                               </a>
                            </div>
                        `;
              $visaContainer.append(visaCard);
            });
          } else {
            $visaContainer.html(
              '<p class="text-gray-500">No visa information found for this country.</p>'
            );
          }
        },
        error: function (error) {
          console.log(`error: `, error);
          popUpMessage("center", error.responseJSON.msg, "error");
        },
      });
    }
    $("#purposeOfVisa").show();
    return;
  }

  // Show visa package

  if (country && purpose) {
    $("#purposeOfVisa").hide();
    console.log(`Show visa package`);
    const $visaContainer = $("#visaPackagesListBox");
    if ($("#visaPackagesListBox").length) {
      const urlParams = new URLSearchParams(window.location.search);
      const countryName = urlParams.get("country");
      if (!countryName) {
        $visaContainer.html(
          '<p class="text-red-500">Missing country ID in the URL.</p>'
        );
        return;
      }
      $visaContainer.html(
        '<p class="text-gray-500">Loading visa packages...</p>'
      );
      $.ajax({
        url: `/api/visa_packages/list/?country=${countryName}&purpose=${purpose}`,
        method: "GET",
        success: function (res) {
          $visaContainer.empty();
          console.log(`Package data:`, res.packageList);

          if (Array.isArray(res.packageList)) {
            res.packageList.forEach((type) => {
              const formattedType =
                type.charAt(0).toUpperCase() + type.slice(1); // Capitalize
              const purposeUrl = formattedType.toLowerCase().replace(" ", "_");
              console.log(`formattedType: `, formattedType);

              const visaCard = `
                            <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl mb-4">
                               <a href='/start-application/?country=${countryName}&purpose=${purposeUrl}'>
                                 <div class="flex items-center mb-3">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-gray-600/5">
                                        <i class="ri-more-line text-gray-600 ri-xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-semibold text-gray-900">${formattedType}</h3>
                                </div>
                                <p class="text-gray-600 mb-4">Visa information for ${formattedType.toLowerCase()} travel purposes.</p>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="ri-time-line mr-2 w-4 h-4 flex items-center justify-center"></i>
                                    <span>Processing time may vary</span>
                                </div>
                               </a>
                            </div>
                        `;
              $visaContainer.append(visaCard);
            });
          } else {
            $visaContainer.html(
              '<p class="text-gray-500">No visa information found for this country.</p>'
            );
          }
        },
        error: function (error) {
          console.log(`error: `, error);
          popUpMessage("center", error.responseJSON.msg, "error");
        },
      });
    }
    $("#visaPackages").show();
    return;
  }
});
