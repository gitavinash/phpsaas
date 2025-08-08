localStorage.setItem(`newApplicationRequest`, true);
localStorage.removeItem(`visaApplicationId`);

const urlParams = new URLSearchParams(window.location.search);

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

function formatDateToYMD(dateStr) {
  const d = new Date(dateStr);
  const dd = String(d.getDate()).padStart(2, "0");
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const yyyy = d.getFullYear();
  return `${yyyy}-${mm}-${dd}`;
}

function toCapitalize(str) {
  if (!str) return "";
  return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

function formatDateToDDMMYYYY(dateStr) {
  if (!dateStr) return "";
  const date = new Date(dateStr);
  if (isNaN(date)) return ""; // invalid date fallback

  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed
  const year = date.getFullYear();

  return `${day}-${month}-${year}`;
}

function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(";").shift();
}

$(document).ready(function () {
  $(".datepicker").datepicker({
    dateFormat: "yy-mm-dd",
    changeMonth: true,
    changeYear: true,
    yearRange: "1900:2025",
  });

  $(".dateOfBirthDatePicker").datepicker({
    dateFormat: "yy-mm-dd",
    changeMonth: true,
    changeYear: true,
    yearRange: "1900:" + new Date().getFullYear(),
    maxDate: 0, // today
  });

  // 📅 Passport Expiry Picker — minimum date is 6 months from today
  $(".passportExpiryDatePicker").datepicker({
    dateFormat: "yy-mm-dd",
    changeMonth: true,
    changeYear: true,
    minDate: "+6m",
    yearRange: new Date().getFullYear() + ":2099",
  });

  $(".arrivalDatePicker").datepicker({
    dateFormat: "yy-mm-dd",
    changeMonth: true,
    changeYear: true,
    minDate: 0, // today
    yearRange: new Date().getFullYear() + ":2099",
  });

  // 🛫 Departure Date Picker (tomorrow onward)
  $(".departureDatePicker").datepicker({
    dateFormat: "yy-mm-dd",
    changeMonth: true,
    changeYear: true,
    minDate: 1, // tomorrow
    yearRange: new Date().getFullYear() + ":2099",
  });

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
    $("html, body").animate({ scrollTop: 0 }, 400);
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

  // Other page code

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

  if ($("#visaMainContent").length) {
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
    $("#startApplicationDropDown").change(function () {
      let selectedCountry = $(this).val();
      if (selectedCountry) {
        selectedCountry = selectedCountry.toLowerCase().replace(/\s+/g, "_");
        window.location.href = `/start-application/?country=${selectedCountry}`;
      }
    });
  }

  // Travel Ageny Registration

  $("#travelAgentRegistrationBtn").on("click", function (e) {
    e.preventDefault();
    let isValid = true;
    let msg = "";

    const fields = [
      { id: "companyName", name: "Company Name" },
      { id: "gstNumber", name: "GST Number" },
      { id: "yearsOperation", name: "Years in Operation" },
      { id: "contactName", name: "Primary Contact Name" },
      { id: "contactEmail", name: "Email Address" },
      { id: "contactPhone", name: "Mobile Number" },
      { id: "businessAddress", name: "Business Address" },
      { id: "city", name: "City" },
      { id: "state", name: "State" },
    ];

    fields.forEach(function (field) {
      const value = $(`#${field.id}`).val().trim();
      if (value === "") {
        msg += `${field.name} is required.\n`;
        isValid = false;
      }
    });

    const email = $("#contactEmail").val().trim();
    const phone = $("#contactPhone").val().trim();
    const url = $("#websiteUrl").val().trim();

    // Email validation
    if (email && !/^\S+@\S+\.\S+$/.test(email)) {
      msg += `Invalid Email Address format.\n`;
      isValid = false;
    }

    // Mobile validation (Indian format)
    const cleanedPhone = phone.replace(/^\+91|^0/, ""); // Remove +91 or 0 prefix

    if (!/^[6-9]\d{9}$/.test(cleanedPhone)) {
      msg += `Invalid Indian Mobile Number. Must start with 6-9 and have 10 digits.\n`;
      isValid = false;
    }

    // Optional website URL validation
    if (url && !/^https?:\/\/[\w.-]+\.[a-z]{2,}$/.test(url)) {
      msg += `Invalid Website URL.\n`;
      isValid = false;
    }

    if (isValid) {
      $("#agentRegistrationForm").submit();
    } else {
      popUpMessage("center", msg.trim(), "warning");
    }
  });

  // Get Application Status

  $("#getApplicationStatus").click(function (e) {
    e.preventDefault();
    $("#applicationStatus").addClass("hidden");
    const $btn = $(this);
    const referenceNumber = $("#referenceNumber").val().trim();
    const contactInfo = $("#contactInfo").val().trim();
    const submitForm = true;

    if (!referenceNumber) {
      popUpMessage("center", "Please enter reference number.", "warning");
      submitForm = false;
      return;
    }

    if (!contactInfo) {
      popUpMessage("center", "Please enter your mobile number.", "warning");
      submitForm = false;
      return;
    }

    $btn
      .prop("disabled", true)
      .addClass("opacity-70 cursor-not-allowed")
      .html(
        `<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Tracking...`
      );

    if (submitForm === true) {
      $.ajax({
        url: "/api/application/status/",
        method: "GET",
        data: {
          referenceNumber: referenceNumber,
          mobileNumber: contactInfo,
        },
        success: function (responseData) {
          console.log("responseData", responseData);
          const applicationData = responseData.msg;

          // Helper function
          const setField = (selector, value) => {
            $(selector).text(value || "-");
          };

          // Remove any old color classes and apply new ones
          const statusText = applicationData.status_label;
          const $statusEl = $("#visaStatus");

          $statusEl
            .removeClass() // remove all classes first
            .addClass("px-4 py-1 rounded-full text-sm font-medium") // base classes
            .text(statusText); // set text

          switch (statusText.toLowerCase()) {
            case "approved":
              $statusEl.addClass("bg-green-100 text-green-700");
              break;
            case "payment pending":
              $statusEl.addClass("bg-yellow-100 text-yellow-700");
              break;
            case "payment failed":
              $statusEl.addClass("bg-red-100 text-red-700");
              break;
            case "draft":
              $statusEl.addClass("bg-gray-100 text-gray-700");
              break;
            case "pending":
              $statusEl.addClass("bg-blue-100 text-blue-700");
              break;
            case "rejected":
              $statusEl.addClass("bg-red-100 text-red-700");
              break;
            default:
              $statusEl.addClass("bg-gray-100 text-gray-700");
          }

          setField(
            "#visaApplicationId",
            applicationData.visa_application_order_id
          );
          setField("#visaAppliedDate", applicationData.visa_application_time);
          setField("#visaType", applicationData.visa_package_visa_type);
          setField("#visaCountry", applicationData.visa_country_title);
          setField("#lastUpdateText", applicationData.visa_application_update);

          // Show the entire status block
          $("#applicationStatus").removeClass("hidden");
          $("html, body").animate(
            {
              scrollTop: $("#applicationStatus").offset().top - 112, // 6rem offset
            },
            600 // smooth animation in ms
          );
          // Enable button again
          $btn
            .prop("disabled", false)
            .removeClass("opacity-70 cursor-not-allowed")
            .html("Track Application");
        },
        error: function (responseError) {
          $btn
            .prop("disabled", false)
            .removeClass("opacity-70 cursor-not-allowed")
            .html("Track Application");
          popUpMessage("center", responseError.responseJSON.msg, "error");
        },
      });
    } else {
      $btn
        .prop("disabled", false)
        .removeClass("opacity-70 cursor-not-allowed")
        .html("Track Application");
    }
  });
});
