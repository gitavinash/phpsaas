localStorage.setItem(`newApplicationRequest`, true);
localStorage.removeItem(`visaApplicationId`);

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
  // Prefilled variables

  var firstName = "";
  var lastName = "";
  var dateOfBirth = "";
  var gender = "";
  var passportNumber = "";
  var passportExpiry = "";
  var email = "";
  var mobile = localStorage.getItem("mobile");
  var address = "";
  var arrivalDate = "";
  var departureDate = "";
  var purposeOfVisit = "";
  var visaEntry = "";
  var accommodation = "";
  let passportCopy = "";
  let profilePhoto = "";
  let supportingDocuments = [];
  let supportingDocumentsUploaded = false;

  // Visa Application Process page

  if ($("#visaApplicationProcess").length) {
    // Show loader

    console.log(`visaApplicationProcess start`);

    $("#page-loader").show();

    // Fetch user profile data from API and append into existing form

    var decodedURIComponent = decodeURIComponent(getCookie("user_mobile"));
    const authUserMobile = atob(decodedURIComponent);

    $.ajax({
      url: "/api/user/search/?type=mobile&mobile=" + authUserMobile,
      type: "GET",
      dataType: "json",
      success: function (data) {
        const userInfo = data.userData;
        const userMetaInfo = data.userData.profileMeta;
        firstName = userInfo.firstName;
        lastName = userInfo.lastName;
        email = userInfo.email;
        dateOfBirth = userMetaInfo?.date_of_birth || "";
        passportNumber = userMetaInfo?.passport_number || "";
        passportExpiry = userMetaInfo?.passport_expiry || "";
        address = userMetaInfo?.address || "";
        if (data.isFound == 1) {
          // 🔽 Populate form fields
          $("#firstName").val(firstName);
          $("#lastName").val(lastName);
          $("#dob").val(formatDateToYMD(dateOfBirth));
          $("#passportExpiry").val(formatDateToYMD(passportExpiry));
          $("#passportNumber").val(passportNumber);
          $("#passportExpiry").val(formatDateToYMD(passportExpiry));
          $("#email").val(email);
          $("#mobile").val(mobile);
          $("#address").val(address);

          // for gender
          $('input[name="gender"][value="' + (gender || "") + '"]').prop(
            "checked",
            true
          );
          $("#page-loader").hide();
        } else {
          console.log(`User information not found`);
        }
      },
      error: function (xhr) {
        $("#page-loader").hide();
        console.error("Error fetching data:", xhr.responseText);
      },
    });

    function saveVisaApplicationDraft(formData, stepNumber) {
      const packageId = urlParams.get("package_id");
      formData.packageId = packageId;
      const fd = new FormData();
      fd.append("packageId", packageId);
      if (stepNumber !== 1) {
        fd.append(
          "applicationId",
          localStorage.getItem("visaApplicationId") || ""
        );
      }

      for (let key in formData) {
        const value = formData[key];
        if (Array.isArray(value)) {
          value.forEach((file, index) => {
            fd.append(`${key}[]`, file);
          });
        } else if (value instanceof File) {
          fd.append(key, value);
        } else {
          fd.append(key, value); // e.g., packageId, applicationId
        }
      }

      $.ajax({
        url: "/api/application/draft/",
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        success: function (res) {
          console.log("✅ Draft saved successfully:");
          if (res.applicationId) {
            localStorage.setItem("visaApplicationId", res.applicationId);
            localStorage.setItem("newApplicationRequest", false);
          }
          if (stepNumber === 1 && res.applicationId) {
            // Save applicationId to localStorage only on Step 1
            localStorage.setItem("visaApplicationId", res.applicationId);
          }
        },
        error: function (xhr, status, err) {
          console.error("Failed to save draft:", err);
          console.debug("Server Response:", xhr.responseText);
        },
      });
    }

    function goToStep(stepNumber) {
      // Animate step visibility
      for (let i = 1; i <= 4; i++) {
        if (i === stepNumber) {
          $(`#step${i}`).fadeIn(400);
        } else {
          $(`#step${i}`).fadeOut(200);
        }
      }

      // Animate scroll to top
      $("html, body").animate({ scrollTop: 0 }, 400);

      // Update step indicators
      for (let i = 1; i <= 4; i++) {
        const $indicator = $(`#step${i}-indicator`);
        const $circle = $indicator.find("div").first(); // circle
        const $line = $indicator.find("div").last(); // horizontal line

        if (i <= stepNumber) {
          // Active step or previous
          $circle
            .removeClass("bg-gray-200 text-gray-600")
            .addClass("bg-primary text-white");

          $line.removeClass("bg-gray-200").addClass("bg-primary");
        } else {
          // Future steps
          $circle
            .removeClass("bg-primary text-white")
            .addClass("bg-gray-200 text-gray-600");

          $line.removeClass("bg-primary").addClass("bg-gray-200");
        }
      }
    }

    // back step button
    for (let i = 1; i <= 5; i++) {
      $(`#goToStep${i}Btn`).on("click", function (e) {
        e.preventDefault();
        goToStep(i);
      });
    }

    $("#step1Btn").on("click", function (e) {
      e.preventDefault();

      const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      const isValidMobile = (mobile) => /^\d{10}$/.test(mobile);
      const isValidDate = (date) => /^\d{4}-\d{2}-\d{2}$/.test(date);

      const getValue = (id) => document.getElementById(id)?.value.trim() || "";

      const fields = [
        { name: "First Name", id: "firstName" },
        { name: "Last Name", id: "lastName" },
        { name: "Date of Birth", id: "dob", validator: isValidDate },
        { name: "Passport Number", id: "passportNumber" },
        {
          name: "Passport Expiry",
          id: "passportExpiry",
          validator: isValidDate,
        },
        { name: "Email", id: "email", validator: isValidEmail },
        {
          name: "Mobile",
          value: localStorage.getItem("mobile")?.trim() || "",
          validator: isValidMobile,
        },
        { name: "Address", id: "address" },
      ];

      dateOfBirth = getValue("dob");
      passportNumber = getValue("passportNumber");
      const payloadData = {};

      for (const field of fields) {
        const value = field.hasOwnProperty("value")
          ? field.value
          : getValue(field.id);
        const isValid = field.validator ? field.validator(value) : !!value;

        if (!isValid) {
          popUpMessage(
            "center",
            `${field.name} is required or invalid`,
            "warning"
          );
          if (field.id) document.getElementById(field.id)?.focus();
          return;
        }
        payloadData[field.id || field.name.toLowerCase().replace(/\s+/g, "_")] =
          value;
      }

      // All validations passed
      saveVisaApplicationDraft(payloadData, 1);
      goToStep(2);
    });

    $("#step2Btn").on("click", function (e) {
      e.preventDefault();

      // Utility to get trimmed value with jQuery (since you use jQuery)
      const getValue = (id) =>
        $("#" + id)
          .val()
          ?.trim() || "";

      // Declare these with let or const
      arrivalDate = getValue("arrivalDate");
      departureDate = getValue("departureDate");
      purposeOfVisit = getValue("purposeOfTravel");
      visaEntry = getValue("visaEntry");
      accommodation = getValue("accommodation");

      // Fields to validate, you can add custom validators if needed
      const fields = [
        { name: "Arrival date", id: "arrivalDate" },
        { name: "Departure date", id: "departureDate" },
        { name: "Purpose of travel", id: "purposeOfTravel" },
        { name: "VISA entry", id: "visaEntry" },
      ];
      const formData = {};
      for (const field of fields) {
        const value = getValue(field.id);

        // Simple presence check, can be replaced with more complex validators if needed
        const isValid = field.validator
          ? field.validator(value)
          : value.length > 0;

        if (!isValid) {
          popUpMessage(
            "center",
            `${field.name} is required or invalid`,
            "warning"
          );
          const inputEl = document.getElementById(field.id);
          if (inputEl) inputEl.focus();
          return;
        }

        formData[field.id || field.name.toLowerCase().replace(/\s+/g, "_")] =
          value;
      }

      formData.accommodation = accommodation;

      // All validations passed
      saveVisaApplicationDraft(formData, 2);
      goToStep(3);
    });

    $("#step3Btn").on("click", function (e) {
      e.preventDefault();

      // Define required fields info
      const fields = [
        {
          name: "Passport",
          id: "passportCopy",
          type: "single",
          required: true,
        },
        {
          name: "Profile photo",
          id: "profilePhoto",
          type: "single",
          required: true,
        },
        {
          name: "Supporting documents",
          id: "supportingDocuments",
          type: "multiple",
          required: false,
        },
      ];

      const files = {};
      let supportingDocumentsUploaded = false;

      for (const field of fields) {
        const input = document.getElementById(field.id);
        const fileList = input?.files || [];

        // Validation for required fields
        if (field.required && fileList.length === 0) {
          popUpMessage("center", `Please upload ${field.name}`, "warning");
          input?.focus();
          return;
        }

        // Store files
        if (fileList.length > 0) {
          if (field.type === "single") {
            files[field.id] = fileList[0]; // single File
          } else if (field.type === "multiple") {
            files[field.id] = Array.from(fileList); // array of Files
            supportingDocumentsUploaded = true;
          }
        }
      }

      const formattedArrival = formatDateToDDMMYYYY(arrivalDate);
      const formattedDeparture = formatDateToDDMMYYYY(departureDate);

      $("#reviewTravelDates").text(
        `${formattedArrival} to ${formattedDeparture}`
      );

      // Set review values (assuming firstName, lastName, email, etc. are in scope)
      $("#reviewFullName").text(`${firstName} ${lastName}`);
      $("#reviewEmail").text(email);
      $("#reviewPassport").text(passportNumber);
      $("#reviewDateOfBirth").text(dateOfBirth);
      $("#reviewTravelDates").text(
        `${formattedArrival} to ${formattedDeparture}`
      );
      $("#reviewTourism").text(toCapitalize(purposeOfVisit));
      $("#reviewEntry").text(toCapitalize(visaEntry) + " entry");
      $("#reviewSupportingDocuments").text(
        supportingDocumentsUploaded ? "Uploaded" : "Not Uploaded"
      );

      // Optionally, use the file URLs or supportingDocumentsUploaded flag for preview or other logic
      saveVisaApplicationDraft(files, 3);
      goToStep(4);
    });

    // Save draft button

    // $(".saveAsDraft").on("click", function (e) {
    //   e.preventDefault();
    //   alert("Save as draft button clicked");
    // });

    // Submit visa application form

    $("#submitVisaApplicationFormBtn").on("click", function (e) {
      e.preventDefault();

      $("#submitVisaApplicationFormBtn").prop("disabled", true);

      // Submit draft to cash on delivery

      $.ajax({
        url: "/api/application/publish/",
        method: "POST",
        data: {
          applicationId: localStorage.getItem(`visaApplicationId`),
        },
        success: function (successResponse) {
          console.log(`Respinse`, successResponse);

          console.log("Application submit successfully");
          localStorage.removeItem(`visaApplicationId`);
          popUpMessage("center", res.msg, res.status);
          setTimeout(function () {
            window.location.href =
              "/thank-you/?application_id=" +
              btoa(localStorage.getItem(`visaApplicationId`));
          }, 1500);
          $("#submitVisaApplicationFormBtn").prop("disabled", false);
        },
        error: function (errorResponse) {
          console.error("Failed to submit VISA application:", errorResponse);
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
          $("#submitVisaApplicationFormBtn").prop("disabled", false);
        },
      });
    });
  }

  // File upload

  function handleUploadText(inputSelector) {
    $(inputSelector).on("change", function () {
      const files = this.files;
      const $uploadBox = $(this).closest(".relative");
      const $textDiv = $uploadBox.find("div.text-sm");

      if (files.length > 0) {
        const names = Array.from(files)
          .map((file) => file.name)
          .join(", ");
        $textDiv
          .text("Uploaded: " + names)
          .removeClass("text-gray-600")
          .addClass("text-green-600 font-medium");
      } else {
        const defaultText =
          inputSelector === "#supportingDocuments"
            ? "Drag and drop or click to upload multiple files"
            : "Drag and drop or click to upload";

        $textDiv
          .text(defaultText)
          .removeClass("text-green-600 font-medium")
          .addClass("text-gray-600");
      }
    });
  }

  // Initialize for each input
  handleUploadText("#passportCopy");
  handleUploadText("#profilePhoto");
  handleUploadText("#supportingDocuments");

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
    $("#startApplicationDropDown").change(function () {
      let selectedCountry = $(this).val();
      if (selectedCountry) {
        selectedCountry = selectedCountry.toLowerCase().replace(/\s+/g, "_");
        window.location.href = `/start-application/?country=${selectedCountry}`;
      }
    });
  }

  const urlParams = new URLSearchParams(window.location.search);
  const country = urlParams.get("country");
  const purpose = urlParams.get("purpose");
  const type = urlParams.get("type"); // Optional

  // Hide all initially
  $("#selectCountry, #purposeOfVisa, #visaPackages").hide();

  // Case: No country selected
  if (!country) {
    console.log("Country not selected");
    $("#selectCountry").fadeIn(400);
    return;
  }

  // Case: Country selected but no purpose
  if (country && !purpose) {
    console.log("Show visa purpose");

    $("#selectCountry").fadeOut(200);
    const $visaContainer = $("#visaPurposeListBox");

    if ($("#visaPurposeListBox").length) {
      $visaContainer.html('<p class="text-gray-500">Loading visa types...</p>');

      $.ajax({
        url: `/api/country/visas/?country=${country}`,
        method: "GET",
        success: function (res) {
          $visaContainer.empty();

          if (Array.isArray(res.countryList)) {
            res.countryList.forEach((type) => {
              const formattedType =
                type.charAt(0).toUpperCase() + type.slice(1);
              const purposeUrl = formattedType.toLowerCase().replace(" ", "_");

              const visaCard = `
                <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl mb-4">
                  <a href='/start-application/?country=${country}&purpose=${purposeUrl}'>
                    <div class="flex items-center mb-3">
                      <div class="w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-gray-600/5">
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
                </div>`;
              $visaContainer.append(visaCard);
            });
          } else {
            $visaContainer.html(
              '<p class="text-gray-500">No visa information found.</p>'
            );
          }

          $("#purposeOfVisa").hide().fadeIn(400);
        },
        error: function (error) {
          console.error("Visa type error:", error);
          popUpMessage(
            "center",
            error?.responseJSON?.msg || "Something went wrong!",
            "error"
          );
        },
      });
    }
    return;
  }

  // Case: Country and purpose selected
  if (country && purpose) {
    console.log("Show visa package");

    $("#purposeOfVisa").fadeOut(200);
    const $packageContainer = $("#visaPackagesListBox");

    if ($("#visaPackagesListBox").length) {
      $packageContainer.html(
        '<p class="text-gray-500">Loading visa packages...</p>'
      );

      $.ajax({
        url: `/api/visa_packages/list/?country=${country}&purpose=${purpose}`,
        method: "GET",
        success: function (res) {
          $packageContainer.empty();

          if (Array.isArray(res.packageList) && res.packageList.length > 0) {
            res.packageList.forEach((pkg) => {
              const formattedTitle =
                pkg.visa_package_title.charAt(0).toUpperCase() +
                pkg.visa_package_title.slice(1);
              const encodedId = btoa(pkg.visa_package_id);
              const mrp = parseFloat(pkg.visa_package_mrp);
              const price = parseFloat(pkg.visa_package_price);
              const discountAmount = mrp - price;
              const discountPercent = Math.round((discountAmount / mrp) * 100);
              const packageUrl = pkg.visa_package_title
                .toLowerCase()
                .replace(/\s+/g, "_");

              const packageCard = `
                <div class="purpose-card bg-white border border-gray-200 rounded-xl p-6 shadow-lg hover:shadow-xl mb-4">
                  <a href='/package/?package_id=${encodedId}&package_name=${encodeURIComponent(
                packageUrl
              )}'>
                    <div class="flex items-center mb-3">
                      <div class="w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100/10 rounded-xl flex items-center justify-center mr-3 floating-icon shadow-lg shadow-gray-600/5">
                        <i class="ri-passport-line text-gray-600 ri-xl"></i>
                      </div>
                      <h3 class="text-2xl font-semibold text-gray-900">${formattedTitle}</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Stay Duration: ${
                      pkg.visa_package_stay_duration
                    } days, Validity: ${
                pkg.visa_package_validity_duration
              } days.</p>
                    <div class="text-gray-600 mb-2">Price: <del>₹${mrp}</del></div>
                    <div class="text-green-600 font-bold text-lg">Offer Price: ₹${price}</div>
                    <div class="text-red-600 text-sm mt-1">You save ₹${discountAmount} (${discountPercent}% off)</div>
                  </a>
                </div>`;
              $packageContainer.append(packageCard);
            });
          } else {
            $packageContainer.html(
              '<p class="text-gray-500">No visa packages found.</p>'
            );
          }

          $("#visaPackages").hide().fadeIn(400);
        },
        error: function (error) {
          console.error("Visa package error:", error);
          popUpMessage(
            "center",
            error?.responseJSON?.msg || "Something went wrong!",
            "error"
          );
        },
      });
    }
    return;
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
});
