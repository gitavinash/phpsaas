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
        if (dateOfBirth != "") {
          $("#dob").val(formatDateToYMD(dateOfBirth));
        }
        $("#passportNumber").val(passportNumber);
        console.log(`passportExpiry:`, passportExpiry);
        if (passportExpiry != "") {
          $("#passportExpiry").val(formatDateToYMD(passportExpiry));
        }
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

    payloadData.middle_name = $("#middleName").val();

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
        popUpMessage("center", successResponse.msg, successResponse.status);
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
