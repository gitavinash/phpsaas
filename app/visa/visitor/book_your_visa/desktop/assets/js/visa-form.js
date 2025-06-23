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
