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
      try {
        formData.append("packageId", packageId); // Attach package ID if available
        // Attach application ID for steps after 1
        if (stepNumber !== 1) {
          const appId = localStorage.getItem("visaApplicationId");
          formData.append("applicationId", appId);
        }

        $.ajax({
          url: "/api/application/draft/",
          method: "POST",
          data: JSON.stringify(formData),
          contentType: false, // Let browser set the correct multipart/form-data headers
          processData: false, // Don't process data as a query string
          success: function (res) {
            console.log("✅ Draft saved successfully:", res);

            // Save applicationId to localStorage only on Step 1
            if (stepNumber === 1 && res.applicationId) {
              localStorage.setItem("visaApplicationId", res.applicationId);
            }
          },
          error: function (xhr, status, err) {
            console.error("❌ Failed to save draft:", err);
            console.debug("Server Response:", xhr.responseText);
          },
        });
      } catch (e) {
        console.error("❌ Unexpected error in saveVisaApplicationDraft:", e);
      }
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
      const formData = {};

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
        formData[field.id || field.name.toLowerCase().replace(/\s+/g, "_")] =
          value;
      }

      // All validations passed
      saveVisaApplicationDraft(formData, 1);
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
        { name: "Accommodation", id: "accommodation" },
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
      alert("SavesubmitVisaApplicationFormBtnt button clicked");
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

  // Form steps navigation
  const steps = ["step1", "step2", "step3", "step4"];

  function showStep(stepId) {
    $(".form-step").addClass("hidden");
    $("#" + stepId).removeClass("hidden");

    const stepIndex = steps.indexOf(stepId);
    const progress = (stepIndex / (steps.length - 1)) * 100;
    $("#progressBar").css("width", progress + "%");
    $("#currentStep").text(stepIndex + 1);
  }

  $("#nextToStep2").on("click", () => showStep("step2"));
  $("#backToStep1").on("click", () => showStep("step1"));
  $("#nextToStep3").on("click", () => showStep("step3"));
  $("#backToStep2").on("click", () => showStep("step2"));
  $("#nextToStep4").on("click", () => showStep("step4"));
  $("#backToStep3").on("click", () => showStep("step3"));

  // File input labels
  $("#businessLicense").on("change", function () {
    const fileName = this.files[0]
      ? this.files[0].name
      : "Upload license (PDF, JPG, PNG)";
    $("#businessLicenseLabel").text(fileName);
  });

  $("#companyLogo").on("change", function () {
    const fileName = this.files[0]
      ? this.files[0].name
      : "Upload logo (JPG, PNG)";
    $("#companyLogoLabel").text(fileName);
  });

  // Password toggle
  $("#togglePassword").on("click", function () {
    const passwordInput = $("#password");
    const type =
      passwordInput.attr("type") === "password" ? "text" : "password";
    passwordInput.attr("type", type);
    $(this).html(
      type === "password"
        ? '<i class="ri-eye-line"></i>'
        : '<i class="ri-eye-off-line"></i>'
    );
  });

  $("#toggleConfirmPassword").on("click", function () {
    const passwordInput = $("#confirmPassword");
    const type =
      passwordInput.attr("type") === "password" ? "text" : "password";
    passwordInput.attr("type", type);
    $(this).html(
      type === "password"
        ? '<i class="ri-eye-line"></i>'
        : '<i class="ri-eye-off-line"></i>'
    );
  });

  // Password strength meter
  $("#password").on("input", function () {
    const password = $(this).val();
    let strength = 0;

    if (password.length >= 8) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;
    if (/[^A-Za-z0-9]/.test(password)) strength += 25;

    $("#passwordStrengthBar").css("width", strength + "%");

    if (strength <= 25) {
      $("#passwordStrengthBar").css("background-color", "#ef4444");
      $("#passwordStrengthText").text("Weak password");
    } else if (strength <= 50) {
      $("#passwordStrengthBar").css("background-color", "#f97316");
      $("#passwordStrengthText").text("Fair password");
    } else if (strength <= 75) {
      $("#passwordStrengthBar").css("background-color", "#eab308");
      $("#passwordStrengthText").text("Good password");
    } else {
      $("#passwordStrengthBar").css("background-color", "#22c55e");
      $("#passwordStrengthText").text("Strong password");
    }
  });

  // Password match check
  $("#confirmPassword").on("input", function () {
    if ($(this).val() === $("#password").val()) {
      $("#passwordMatchText")
        .text("Passwords match")
        .attr("class", "text-xs text-green-600 mt-1");
    } else {
      $("#passwordMatchText")
        .text("Passwords do not match")
        .attr("class", "text-xs text-red-600 mt-1");
    }
  });

  // Clear form
  $("#clearForm").on("click", function () {
    if (confirm("Are you sure you want to clear all form data?")) {
      $("#agentRegistrationForm")[0].reset();
      $("#businessLicenseLabel").text("Upload license (PDF, JPG, PNG)");
      $("#companyLogoLabel").text("Upload logo (JPG, PNG)");
      $("#passwordStrengthBar").css("width", "0%");
      $("#passwordStrengthText").text("Password strength");
      $("#passwordMatchText").text("");
      showStep("step1");
    }
  });

  // Form submission
  $("#agentRegistrationForm").on("submit", function (e) {
    e.preventDefault();

    const submitBtn = $("#submitForm");
    submitBtn
      .prop("disabled", true)
      .html('<i class="ri-loader-4-line animate-spin mr-2"></i> Processing...');

    setTimeout(() => {
      const refNumber = `TA-${Math.floor(Math.random() * 10000)
        .toString()
        .padStart(4, "0")}-${new Date().getFullYear()}`;
      $(".bg-white.rounded-xl.shadow-lg").html(`
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-check-line text-green-600 ri-3x"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Registration Successful!</h2>
                    <p class="text-gray-600 mb-8">Thank you for registering as a travel agent. Your application has been submitted and is under review. You will receive a confirmation email shortly.</p>
                    <p class="text-gray-600 mb-8">Application Reference: <span class="font-medium">${refNumber}</span></p>
                    <a href="/" class="bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-3 cursor-pointer inline-block whitespace-nowrap">Return to Homepage</a>
                </div>
            `);
    }, 2000);
  });

  // FAQ toggles
  $(".faq-toggle").on("click", function () {
    const content = $(this).next();
    content.toggleClass("hidden");

    const icon = $(this).find("i");
    icon.attr(
      "class",
      content.hasClass("hidden")
        ? "ri-arrow-down-s-line w-5 h-5 flex items-center justify-center"
        : "ri-arrow-up-s-line w-5 h-5 flex items-center justify-center"
    );
  });
});
