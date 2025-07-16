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
  $("#addNewPerson").hide();
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
  $("html, body").animate({ scrollTop: 0 }, 400);
  popUpMessage("center", "Person details added successfully", "success");
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
  $("html, body").animate({ scrollTop: 0 }, 400);
  popUpMessage("center", "Person details updated successfully", "success");
  $("#addNewPerson").show();
});

$("#step1Btn").on("click", function (e) {
  e.preventDefault();

  // Get current persons data from localStorage or empty array
  let personsData = JSON.parse(localStorage.getItem("personsData")) || [];

  // Check if the form is touched: any field (including radio for gender) has value
  const formFields = {
    firstName: $("#firstName").val().trim(),
    middleName: $("#middleName").val().trim(),
    lastName: $("#lastName").val().trim(),
    dob: $("#dob").val().trim(),
    gender: $("input[name='gender']:checked").val() || "",
    passportNumber: $("#passportNumber").val().trim(),
    passportExpiry: $("#passportExpiry").val().trim(),
    email: $("#email").val().trim(),
    mobile: $("#mobile").val().trim(),
    address: $("#address").val().trim(),
  };

  const isFormTouched = Object.values(formFields).some((value) => value !== "");

  if (isFormTouched) {
    // Form touched, validate required fields (excluding middleName, email, mobile, address)
    const requiredFields = {
      "First Name": formFields.firstName,
      "Last Name": formFields.lastName,
      "Date of Birth": formFields.dob,
      Gender: formFields.gender,
      "Passport Number": formFields.passportNumber,
      "Passport Expiry Date": formFields.passportExpiry,
    };

    // Find first missing required field
    const missingField = Object.entries(requiredFields).find(
      ([_, val]) => !val
    );

    if (missingField) {
      popUpMessage("center", `${missingField[0]} is required.`, "warning");
      return; // Stop and alert
    }

    // All required fields valid, add/update this person in personsData
    const newPerson = {
      first_name: formFields.firstName,
      middle_name: formFields.middleName,
      last_name: formFields.lastName,
      date_of_birth: formFields.dob,
      gender: formFields.gender,
      passport_number: formFields.passportNumber,
      passport_expiry: formFields.passportExpiry,
      email: formFields.email,
      mobile: formFields.mobile,
      address: formFields.address,
    };

    // If you want to add new person to existing array (allow multiple persons)
    personsData.push(newPerson);

    // Save updated array back to localStorage
    localStorage.setItem("personsData", JSON.stringify(personsData));
  } else {
    // Form not touched at all, just continue without saving anything new
    // But ensure personsData exists in localStorage (do nothing here)
  }

  // Proceed to next step and hide form section
  goToStep(2);
  $("#additionalPersonDetails").hide();

  // Save draft if needed
  const newApplicationRequest = localStorage.getItem("newApplicationRequest");
  if (newApplicationRequest === "true") {
    const payLoadData = JSON.stringify(formFields);
    saveVisaApplicationDraft(payLoadData, 1);
  }
});
``;
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
  renderDocumentUploadTabs();
});

$("#step3Btn").on("click", function (e) {
  e.preventDefault();

  const personsData = JSON.parse(localStorage.getItem("personsData")) || [];
  const firstPerson = personsData[0];

  if (!firstPerson) {
    popUpMessage("center", "Person 1 is missing", "warning");
    return;
  }

  // Validate first person file inputs only
  const requiredFields = [
    {
      name: "Passport",
      id: `passportCopy_0`,
      required: true,
    },
    {
      name: "Profile photo",
      id: `profilePhoto_0`,
      required: true,
    },
  ];

  for (const field of requiredFields) {
    const input = document.getElementById(field.id);
    if (field.required && (!input || input.files.length === 0)) {
      popUpMessage("center", `Please upload ${field.name}`, "warning");
      input?.focus();
      return;
    }
  }

  // Update review preview
  const formattedArrival = formatDateToDDMMYYYY(arrivalDate);
  const formattedDeparture = formatDateToDDMMYYYY(departureDate);
  $("#reviewFullName").text(
    `${firstPerson.first_name} ${firstPerson.last_name}`
  );
  $("#reviewEmail").text(firstPerson.email);
  $("#reviewPassport").text(firstPerson.passport_number);
  $("#reviewDateOfBirth").text(firstPerson.date_of_birth);
  $("#reviewTravelDates").text(`${formattedArrival} to ${formattedDeparture}`);
  $("#reviewTourism").text(toCapitalize(purposeOfVisit));
  $("#reviewEntry").text(toCapitalize(visaEntry) + " entry");

  const docs = document.getElementById("supportingDocuments_0")?.files || [];
  $("#reviewSupportingDocuments").text(
    docs.length > 0 ? "Uploaded" : "Not Uploaded"
  );

  // ✅ Call actual upload function
  saveVisaApplicationDraft(null, 3);

  // Show price

  const totalPassengers = JSON.parse(
    localStorage.getItem("personsData")
  ).length;

  const serviceFeePerPerson =
    parseFloat(localStorage.getItem("serviceFees")) || 0;
  const governmentFeePerPerson =
    parseFloat(localStorage.getItem("governmentFees")) || 0;

  const serviceCharge = totalPassengers * serviceFeePerPerson;
  const governmentCharge = totalPassengers * governmentFeePerPerson;
  const totalPrice = serviceCharge + governmentCharge;

  $("#totalPerson").text(totalPassengers);

  // Government Fee Display
  let govtText = "₹ " + governmentCharge;
  if (totalPassengers > 1) {
    govtText += ` (₹ ${governmentFeePerPerson} per person)`;
  }
  $("#governmentVisaCharges").text(govtText);

  // Service Fee Display
  let serviceText = "₹ " + serviceCharge;
  if (totalPassengers > 1) {
    serviceText += ` (₹ ${serviceFeePerPerson} per person)`;
  }
  $("#serviceFee").text(serviceText);

  // Total
  $("#totalAmount").text("₹ " + totalPrice);

  goToStep(4);
});

function renderDocumentUploadTabs() {
  const personsData = JSON.parse(localStorage.getItem("personsData")) || [];

  let tabButtonsHTML = "";
  let tabContentsHTML = "";

  personsData.forEach((person, index) => {
    const tabId = `personTab${index}`;
    const activeClass =
      index === 0 ? "bg-primary text-white" : "bg-white text-gray-800";
    const contentStyle = index === 0 ? "" : "display: none;";

    // Tab button with person name
    tabButtonsHTML += `
      <button type='button'
        class="tab-btn px-4 py-2 rounded-md border ${activeClass}" 
        data-tab="${tabId}">
        ${person.first_name} ${person.last_name}
      </button>
    `;

    // Tab content with file inputs and file name text containers
    tabContentsHTML += `
      <div id="${tabId}" class="tab-content" style="${contentStyle}">
        <!-- Passport Upload -->
        <div class="border border-gray-300 rounded-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="font-medium">Passport</h3>
              <p class="text-sm text-gray-500">Upload clear scan of passport bio page</p>
            </div>
            <div class="text-sm text-gray-500">Max size: 5MB</div>
          </div>
          <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <input type="file" id="passportCopy_${index}" name="passportCopy_${index}" 
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*,.pdf">
            <i class="ri-upload-cloud-line text-gray-400 text-3xl mb-2"></i>
            <div id="passportCopyText_${index}" class="text-sm text-gray-600">Drag and drop or click to upload</div>
          </div>
        </div>

        <!-- Photo Upload -->
        <div class="border border-gray-300 rounded-lg p-6 mt-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="font-medium">Passport Size Photo</h3>
              <p class="text-sm text-gray-500">Recent photo with white background</p>
            </div>
            <div class="text-sm text-gray-500">Max size: 2MB</div>
          </div>
          <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <input type="file" id="profilePhoto_${index}" name="profilePhoto_${index}" 
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
            <i class="ri-upload-cloud-line text-gray-400 text-3xl mb-2"></i>
            <div id="profilePhotoText_${index}" class="text-sm text-gray-600">Drag and drop or click to upload</div>
          </div>
        </div>

        <!-- Supporting Documents Upload -->
        <div class="border border-gray-300 rounded-lg p-6 mt-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="font-medium">Supporting Documents</h3>
              <p class="text-sm text-gray-500">Bank statements, flight tickets, hotel bookings, etc.</p>
            </div>
            <div class="text-sm text-gray-500">Max size: 10MB</div>
          </div>
          <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <input type="file" id="supportingDocuments_${index}" 
              name="supportingDocuments_${index}" multiple
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*,.pdf">
            <i class="ri-upload-cloud-line text-gray-400 text-3xl mb-2"></i>
            <div id="supportingDocumentsText_${index}" class="text-sm text-gray-600">Drag and drop or click to upload multiple files</div>
          </div>
        </div>
      </div>
    `;
  });

  $("#personTabs").html(tabButtonsHTML);
  $("#tabContentArea").html(tabContentsHTML);

  // Tab switching logic
  $(".tab-btn").click(function () {
    $(".tab-btn")
      .removeClass("bg-primary text-white")
      .addClass("bg-white text-gray-800");
    $(this)
      .removeClass("bg-white text-gray-800")
      .addClass("bg-primary text-white");

    const tabId = $(this).data("tab");
    $(".tab-content").hide();
    $("#" + tabId).show();
  });

  // File name preview logic
  personsData.forEach((_, index) => {
    const fileInputs = [
      { inputId: `passportCopy_${index}`, textId: `passportCopyText_${index}` },
      { inputId: `profilePhoto_${index}`, textId: `profilePhotoText_${index}` },
      {
        inputId: `supportingDocuments_${index}`,
        textId: `supportingDocumentsText_${index}`,
      },
    ];

    fileInputs.forEach(({ inputId, textId }) => {
      $(`#${inputId}`).on("change", function () {
        const files = this.files;
        const $text = $(`#${textId}`);

        if (files && files.length > 0) {
          const names = Array.from(files)
            .map((file) => file.name)
            .join(", ");
          $text
            .text("Uploaded: " + names)
            .removeClass("text-gray-600")
            .addClass("text-green-600 font-medium");
        } else {
          // Reset if no file
          const defaultText = inputId.includes("supporting")
            ? "Drag and drop or click to upload multiple files"
            : "Drag and drop or click to upload";
          $text
            .text(defaultText)
            .removeClass("text-green-600 font-medium")
            .addClass("text-gray-600");
        }
      });
    });
  });
}

function saveVisaApplicationDraft(data, stepNumber) {
  console.log(`step number:`, stepNumber);

  const packageId = urlParams.get("package_id");
  const applicationId = localStorage.getItem("visaApplicationId") || "";
  const fd = new FormData();

  fd.append("packageId", packageId);

  if (stepNumber === 1) {
    const rawData = localStorage.getItem("personsData");

    if (rawData) {
      let persons = JSON.parse(rawData);

      const seen = new Set();
      const filtered = [];

      for (const person of persons) {
        const uniqueKey = person.passport_number; // Or use person.email or combined fields
        if (!seen.has(uniqueKey)) {
          seen.add(uniqueKey);
          filtered.push(person); // Keep only the first occurrence
        }
        // All subsequent duplicates will be ignored
      }

      localStorage.setItem("personsData", JSON.stringify(filtered));
      console.log("✅ Duplicates removed, first occurrence kept.");
    } else {
      console.warn("⚠️ No personsData found in localStorage.");
    }

    fd.append(
      "personsData",
      JSON.stringify(localStorage.getItem("personsData"))
    ); // data should be a JSON string
  }

  if (stepNumber === 2) {
    // Step 2: save travel meta
    fd.append("arrivalDate", arrivalDate);
    fd.append("departureDate", departureDate);
    fd.append("purposeOfTravel", purposeOfVisit);
    fd.append("visaEntry", visaEntry);
    fd.append("accommodation", accommodation);
    fd.append("applicationId", applicationId);
  }

  if (stepNumber === 3) {
    fd.append("applicationId", applicationId);

    const personsData = JSON.parse(localStorage.getItem("personsData") || "[]");

    personsData.forEach((_, index) => {
      const passportInput = document.getElementById(`passportCopy_${index}`);
      const photoInput = document.getElementById(`profilePhoto_${index}`);
      const supportingInput = document.getElementById(
        `supportingDocuments_${index}`
      );

      if (passportInput?.files?.[0]) {
        fd.append(`passportCopy_${index}`, passportInput.files[0]);
      }

      if (photoInput?.files?.[0]) {
        fd.append(`profilePhoto_${index}`, photoInput.files[0]);
      }

      if (supportingInput?.files?.length > 0) {
        Array.from(supportingInput.files).forEach((file, i) => {
          fd.append(`supportingDocuments_${index}[${i}]`, file);
        });
      }
    });
  }

  $.ajax({
    url: "/api/application/draft/",
    method: "POST",
    data: fd,
    contentType: false,
    processData: false,
    success: function (res) {
      console.log("✅ Draft saved successfully");
      if (res.applicationId) {
        localStorage.setItem("visaApplicationId", res.applicationId);
        localStorage.setItem("newApplicationRequest", false);
      }
    },
    error: function (xhr) {
      console.error("❌ Failed to save draft:", xhr);
      popUpMessage(
        "center",
        xhr.responseJSON?.msg || "Draft save failed",
        "error"
      );
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
  const $btn = $("#submitVisaApplicationFormBtn");
  $btn.prop("disabled", true);

  const applicationId = localStorage.getItem("visaApplicationId");
  const personsData = JSON.parse(localStorage.getItem("personsData")) || [];

  if (!applicationId || personsData.length === 0) {
    popUpMessage("center", "Missing data to submit application", "error");
    $btn.prop("disabled", false);
    return;
  }

  const finalData = new FormData();
  finalData.append("applicationId", applicationId);

  let missingRequired = false;

  personsData.forEach((person, index) => {
    const passportInput = document.getElementById(`passportCopy_${index}`);
    const photoInput = document.getElementById(`profilePhoto_${index}`);
    const supportingInput = document.getElementById(
      `supportingDocuments_${index}`
    );

    // First person document is mandatory
    if (index === 0) {
      if (!passportInput?.files?.[0]) {
        popUpMessage(
          "center",
          "First person's passport copy is required",
          "warning"
        );
        passportInput?.focus();
        missingRequired = true;
        return;
      }
      if (!photoInput?.files?.[0]) {
        popUpMessage("center", "First person's photo is required", "warning");
        photoInput?.focus();
        missingRequired = true;
        return;
      }
    }

    // Append passport file
    if (passportInput?.files?.[0]) {
      finalData.append(`passportCopy[${index}]`, passportInput.files[0]);
    }

    // Append photo
    if (photoInput?.files?.[0]) {
      finalData.append(`profilePhoto[${index}]`, photoInput.files[0]);
    }

    // Append supporting documents
    if (supportingInput?.files?.length > 0) {
      Array.from(supportingInput.files).forEach((file, i) => {
        finalData.append(`supportingDocuments[${index}][${i}]`, file);
      });
    }
  });

  if (missingRequired) {
    $btn.prop("disabled", false);
    return;
  }

  $.ajax({
    url: "/api/application/publish/",
    method: "POST",
    data: finalData,
    contentType: false,
    processData: false,
    success: function (res) {
      console.log("✅ Application submitted successfully");

      localStorage.removeItem("visaApplicationId");
      localStorage.removeItem("personsData");

      popUpMessage("center", res.msg, res.status);

      setTimeout(function () {
        window.location.href =
          "/thank-you/?application_id=" +
          btoa(res.applicationId || applicationId);
      }, 1500);

      $btn.prop("disabled", false);
    },
    error: function (xhr) {
      console.error("❌ Submission failed:", xhr);
      popUpMessage(
        "center",
        xhr.responseJSON?.msg || "Submission failed. Please try again.",
        "error"
      );
      $btn.prop("disabled", false);
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
