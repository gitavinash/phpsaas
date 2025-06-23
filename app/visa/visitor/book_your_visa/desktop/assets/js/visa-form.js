$("#page-loader").show();

// Prefilled variables

var firstName = "";
var middleName = "";
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
    middleName = userMetaInfo?.middle_name || "";
    console.log(`Middle name:`, middleName);
    lastName = userInfo.lastName;
    email = userInfo.email;
    dateOfBirth = userMetaInfo?.date_of_birth || "";
    passportNumber = userMetaInfo?.passport_number || "";
    passportExpiry = userMetaInfo?.passport_expiry || "";
    address = userMetaInfo?.address || "";
    if (data.isFound == 1) {
      // 🔽 Populate form fields
      $("#firstName").val(firstName);
      $("#middleName").val(middleName);
      $("#lastName").val(lastName);
      if (dateOfBirth != "") {
        $("#dob").val(formatDateToYMD(dateOfBirth));
      }
      $("#passportNumber").val(passportNumber);
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
