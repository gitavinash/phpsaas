$("#page-loader").show();
var totalNumberOfPassenger = 0;
// Reset personsData and form storage
localStorage.setItem("personsData", JSON.stringify([]));

// Prefilled variables
let firstName = "",
  middleName = "",
  lastName = "",
  dateOfBirth = "",
  gender = "",
  passportNumber = "",
  passportExpiry = "",
  email = "",
  mobile = localStorage.getItem("mobile") || "",
  address = "",
  arrivalDate = "",
  departureDate = "",
  purposeOfVisit = "",
  visaEntry = "",
  accommodation = "",
  passportCopy = "",
  profilePhoto = "",
  supportingDocuments = [],
  supportingDocumentsUploaded = false;

// Decode user mobile from cookie
const decodedURIComponent = decodeURIComponent(getCookie("user_mobile"));
const authUserMobile = atob(decodedURIComponent);

function renderAllPersonCards() {
  const personsData = JSON.parse(localStorage.getItem("personsData")) || [];
  $("#additionalPersonDetails").html(""); // Clear before appending

  personsData.forEach((person, index) => {
    const initials =
      (person.first_name?.[0] || "") + (person.last_name?.[0] || "");

    const card = `
      <div class="w-full bg-white rounded-xl shadow-sm p-4 flex items-center justify-between space-x-4 border mb-3">
        <div class="flex-shrink-0">
          <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-lg font-semibold uppercase">
            ${initials}
          </div>
        </div>
        <div class="flex-1">
          <h3 class="text-lg font-bold text-gray-800">
            ${person.first_name} ${person.last_name}
          </h3>
          <div class="text-sm text-gray-600 mt-1">
            <span class="mr-3">DOB: ${person.date_of_birth}</span>
            <span class="mr-3">Gender: ${
              person.gender.charAt(0).toUpperCase() + person.gender.slice(1)
            }</span>
            <span class="mr-3">Passport: ${person.passport_number}</span>
          </div>
        </div>
        <div class="text-gray-500 hover:text-blue-600 cursor-pointer" onclick="editPerson(${index})">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
          </svg>
        </div>
      </div>
    `;

    $("#additionalPersonDetails").append(card);
  });
}

function editPerson(index) {
  const personsData = JSON.parse(localStorage.getItem("personsData")) || [];
  const person = personsData[index];
  if (!person) return;

  // Populate form
  $("#firstName").val(person.first_name);
  $("#middleName").val(person.middle_name);
  $("#lastName").val(person.last_name);
  $("#dob").val(person.date_of_birth);
  $("#passportNumber").val(person.passport_number);
  $("#passportExpiry").val(person.passport_expiry);
  $("#email").val(person.email);
  $("#mobile").val(person.mobile);
  $("#address").val(person.address);
  $('input[name="gender"][value="' + person.gender + '"]').prop(
    "checked",
    true
  );

  // Set edit index in hidden field
  $("#editIndex").val(index);

  // Show Update button, hide Add
  $("#updateUser").removeClass("hidden");
  $("#addNewPerson").addClass("hidden");

  document
    .getElementById("visaApplicationProcess")
    .scrollIntoView({ behavior: "smooth" });
}

// Fetch profile data
$.ajax({
  url: "/api/user/search/?type=mobile&mobile=" + authUserMobile,
  type: "GET",
  dataType: "json",
  success: function (data) {
    const userInfo = data.userData;
    const userMetaInfo = data.userData.profileMeta;

    firstName = userInfo.firstName;
    middleName = userMetaInfo?.middle_name || "";
    lastName = userInfo.lastName;
    email = userInfo.email;
    dateOfBirth = userMetaInfo?.date_of_birth || "";
    passportNumber = userMetaInfo?.passport_number || "";
    passportExpiry = userMetaInfo?.passport_expiry || "";
    address = userMetaInfo?.address || "";
    gender = userMetaInfo?.gender || "";

    if (data.isFound == 1) {
      // 🔽 Populate form fields
      $("#firstName").val(firstName);
      $("#middleName").val(middleName);
      $("#lastName").val(lastName);
      if (dateOfBirth != "") $("#dob").val(formatDateToYMD(dateOfBirth));
      $("#passportNumber").val(passportNumber);
      if (passportExpiry != "")
        $("#passportExpiry").val(formatDateToYMD(passportExpiry));
      $("#email").val(email);
      $("#mobile").val(mobile);
      $("#address").val(address);
      $('input[name="gender"][value="' + gender + '"]').prop("checked", true);

      const newPerson = {
        first_name: firstName,
        middle_name: middleName,
        last_name: lastName,
        email: email,
        mobile: mobile,
        address: address,
        date_of_birth: dateOfBirth,
        gender: gender,
        passport_number: passportNumber,
        passport_expiry: passportExpiry,
      };

      localStorage.setItem("personsData", JSON.stringify([newPerson]));
    } else {
      console.log("User information not found");
    }

    $("#page-loader").hide();
  },
  error: function (xhr) {
    $("#page-loader").hide();
    console.error("Error fetching data:", xhr.responseText);
  },
});

$("#addNewPerson").click(function (e) {
  e.preventDefault();

  const firstName = $("#firstName").val().trim();
  const middleName = $("#middleName").val().trim();
  const lastName = $("#lastName").val().trim();
  const dob = $("#dob").val().trim();
  const gender = $("input[name='gender']:checked").val();
  const passportNumber = $("#passportNumber").val().trim();
  const passportExpiry = $("#passportExpiry").val().trim();
  const email = $("#email").val().trim();
  const mobile = $("#mobile").val().trim();
  const address = $("#address").val().trim();

  if (
    !firstName ||
    !lastName ||
    !dob ||
    !gender ||
    !passportNumber ||
    !passportExpiry ||
    !email ||
    !mobile ||
    !address
  ) {
    popUpMessage("center", "Please fill all required fields.", "warning");
    return;
  }

  const newPerson = {
    first_name: firstName,
    middle_name: middleName,
    last_name: lastName,
    date_of_birth: dob,
    gender: gender,
    passport_number: passportNumber,
    passport_expiry: passportExpiry,
    email: email,
    mobile: mobile,
    address: address,
  };

  if (totalNumberOfPassenger == 0) {
    localStorage.setItem("personsData", JSON.stringify([newPerson]));
    totalNumberOfPassenger++;
  } else {
    personsData = JSON.parse(localStorage.getItem("personsData")) || [];
    personsData.push(newPerson);
    localStorage.setItem("personsData", JSON.stringify(personsData));
  }

  $("#visaApplicationProcess")[0].reset();
  $("#email").val(email);
  $("#mobile").val(mobile);
  $("#address").val(address);

  renderAllPersonCards();
});

$("#updateUser").click(function (e) {
  e.preventDefault();

  const firstName = $("#firstName").val().trim();
  const middleName = $("#middleName").val().trim();
  const lastName = $("#lastName").val().trim();
  const dob = $("#dob").val().trim();
  const gender = $("input[name='gender']:checked").val();
  const passportNumber = $("#passportNumber").val().trim();
  const passportExpiry = $("#passportExpiry").val().trim();
  const email = $("#email").val().trim();
  const mobile = $("#mobile").val().trim();
  const address = $("#address").val().trim();

  if (
    !firstName ||
    !lastName ||
    !dob ||
    !gender ||
    !passportNumber ||
    !passportExpiry ||
    !email ||
    !mobile ||
    !address
  ) {
    popUpMessage("center", "Please fill all required fields.", "warning");
    return;
  }

  const newPerson = {
    first_name: firstName,
    middle_name: middleName,
    last_name: lastName,
    date_of_birth: dob,
    gender: gender,
    passport_number: passportNumber,
    passport_expiry: passportExpiry,
    email: email,
    mobile: mobile,
    address: address,
  };

  const index = parseInt($("#editIndex").val());
  if (isNaN(index)) return;

  let personsData = JSON.parse(localStorage.getItem("personsData")) || [];
  personsData[index] = newPerson;
  localStorage.setItem("personsData", JSON.stringify(personsData));

  // Reset form and state
  $("#visaApplicationProcess")[0].reset();
  $("#editIndex").val("");
  $("#updateUser").addClass("hidden");
  $("#addNewPerson").removeClass("hidden");

  $("#email").val(email);
  $("#mobile").val(mobile);
  $("#address").val(address);

  renderAllPersonCards();
});

$("#step1Btn").on("click", function (e) {
  e.preventDefault();

  if (totalNumberOfPassenger == 0) {
    const fields = {
      "First Name": $("#firstName").val().trim(),
      "Middle Name": $("#middleName").val().trim(),
      "Last Name": $("#lastName").val().trim(),
      "Date of Birth": $("#dob").val().trim(),
      Gender: $("input[name='gender']:checked").val(),
      "Passport Number": $("#passportNumber").val().trim(),
      "Passport Expiry Date": $("#passportExpiry").val().trim(),
      Email: $("#email").val().trim(),
      "Mobile Number": $("#mobile").val().trim(),
      Address: $("#address").val().trim(),
    };

    // Check for missing required fields (excluding optional ones like Middle Name)
    const requiredFields = Object.entries(fields).filter(
      ([label]) => label !== "Middle Name"
    );
    const missingField = requiredFields.find(([_, value]) => !value);

    if (missingField) {
      popUpMessage("center", `${missingField[0]} is required.`, "warning");
      return;
    }

    const updatedPerson = {
      first_name: fields["First Name"],
      middle_name: fields["Middle Name"],
      last_name: fields["Last Name"],
      date_of_birth: fields["Date of Birth"],
      gender: fields["Gender"],
      passport_number: fields["Passport Number"],
      passport_expiry: fields["Passport Expiry Date"],
      email: fields["Email"],
      mobile: fields["Mobile Number"],
      address: fields["Address"],
    };

    localStorage.setItem("personsData", JSON.stringify([updatedPerson]));
  }

  var payloadData = localStorage.getItem("personsData");

  // All validations passed
  const newApplicationRequest = localStorage.getItem("newApplicationRequest");

  if (newApplicationRequest == "true") {
    saveVisaApplicationDraft(payloadData, 1);
  }

  goToStep(2);
  $("#additionalPersonDetails").hide();
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
    const isValid = field.validator ? field.validator(value) : value.length > 0;

    if (!isValid) {
      popUpMessage("center", `${field.name} is required or invalid`, "warning");
      const inputEl = document.getElementById(field.id);
      if (inputEl) inputEl.focus();
      return;
    }

    formData[field.id || field.name.toLowerCase().replace(/\s+/g, "_")] = value;
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

  $("#reviewTravelDates").text(`${formattedArrival} to ${formattedDeparture}`);

  // Set review values (assuming firstName, lastName, email, etc. are in scope)
  $("#reviewFullName").text(`${firstName} ${lastName}`);
  $("#reviewEmail").text(email);
  $("#reviewPassport").text(passportNumber);
  $("#reviewDateOfBirth").text(dateOfBirth);
  $("#reviewTravelDates").text(`${formattedArrival} to ${formattedDeparture}`);
  $("#reviewTourism").text(toCapitalize(purposeOfVisit));
  $("#reviewEntry").text(toCapitalize(visaEntry) + " entry");
  $("#reviewSupportingDocuments").text(
    supportingDocumentsUploaded ? "Uploaded" : "Not Uploaded"
  );

  // Optionally, use the file URLs or supportingDocumentsUploaded flag for preview or other logic
  saveVisaApplicationDraft(files, 3);
  goToStep(4);
});

function saveVisaApplicationDraft(formData, stepNumber) {
  const packageId = urlParams.get("package_id");
  formData.packageId = packageId;
  const fd = new FormData();
  fd.append("packageId", packageId);
  if (stepNumber !== 1) {
    fd.append("applicationId", localStorage.getItem("visaApplicationId") || "");
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
      console.log("âœ… Draft saved successfully:");
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

  if (stepNumber == 1) {
    $("#additionalPersonDetails").show();
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

for (let i = 1; i <= 5; i++) {
  $(`#goToStep${i}Btn`).on("click", function (e) {
    e.preventDefault();
    goToStep(i);
  });
}

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
