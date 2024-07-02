$(document).ready(function () {
  // Landlord

  var landlordName = "";
  var landlordFatherName = "";
  var landlordEmail = "";
  var landlordPhone = "";
  var landlordAddress = "";

  // Tenate

  var tenateName = "";
  var tenateFatherName = "";
  var tenateEmail = "";
  var tenatePhone = "";
  var tenateAddress = "";

  // Property

  var propertyFloor;
  var propertyBHK;
  var propertyAddress;

  // Agreement

  var agreementStartDate = "";
  var rentAmount = "";
  var paymentDate = "";
  var securityAmount = "";
  var noticePeriod = "";

  var mobileRegex = /^[6-9]\d{9}$/;
  function validateMobileNumber(mobileNumber) {
    var mobileNumber = removeLeadingZero(mobileNumber);
    return mobileRegex.test(mobileNumber);
  }
  function removeLeadingZero(inputString) {
    if (inputString.startsWith("0")) {
      return inputString.substring(1);
    }
    return inputString;
  }
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

  $("#getStarted").click(function (e) {
    e.preventDefault();
    $("#getStarted").hide();
    var name = $("#name").val();
    var email = $("#email").val();
    var mobile = $("#mobile").val();
    var state = $("#state").val();

    if (name != "") {
      if (email != "") {
        if (mobile != "") {
          var mobile = removeLeadingZero(mobile);
          if (validateMobileNumber(mobile)) {
            if (state != "") {
              var requestIsValid = true;
            } else {
              msg = "Kindly enter your state name";
            }
          } else {
            msg = "Kindly enter valid mobile number";
          }
        } else {
          msg = "Please enter your mobile number";
        }
      } else {
        msg = "Please enter your email address";
      }
    } else {
      msg = "Please enter your full name";
    }

    if (requestIsValid) {
      var formData = $("#getStartedForm").serializeArray();
      formData.push({ name: "type", value: "rent_agreement" });
      formData.push({ name: "action", value: "generate" });
      $.ajax({
        type: "POST",
        url: `/api/rent_agreement/generate/`,
        dataType: "json",
        data: formData,
        success: function (response) {
          if (response.status == "success") {
            window.location.href =
              `/generate-agreement/?request_id=` + btoa(response.enquiryId);
          } else {
            popUpMessage("center", response.msg, response.status);
          }
          $("#getStarted").show();
        },
        error: function (response) {
          console.log(`Error : `, response);
          popUpMessage(
            "center",
            response.responseJSON.msg,
            response.responseJSON.status
          );
          $("#getStarted").show();
        },
      });
    } else {
      popUpMessage("center", msg, "warning");
      $("#getStarted").show();
    }
  });

  function updateFormValues(btnName) {
    $("#infoBox").hide();
    $("#landlordBox").hide();
    $("#tenateBox").hide();
    $("#houseBox").hide();
    $("#contractBox").hide();
    var imgValue = $(`#${btnName}Img`).attr("src");
    // if (btnName == "landlord") {
    //   var defaultImgValue = $(`#infoImg`).attr("src");
    //   var defaultImgValue = defaultImgValue.replace(".png", "-active.png");
    //   $(`#infoImg`).attr("src", defaultImgValue);
    // }
    if (imgValue != undefined) {
      var updateImgValue = imgValue.replace(".png", "-active.png");
      $(`#${btnName}Img`).attr("src", updateImgValue);
    }
    $(`#${btnName}Box`).show();
  }

  $("#tenate").click(function (e) {
    e.preventDefault();

    // Landlord

    nextStep = false;
    landlordName = $("#landlordName").val();
    landlordFatherName = $("#landlordFatherName").val();
    landlordEmail = $("#landlordEmail").val();
    landlordPhone = $("#landlordPhone").val();
    landlordAddress = $("#landlordAddress").val();

    if (landlordName != "") {
      if (landlordFatherName != "") {
        if (landlordAddress != "") {
          if (landlordEmail != "") {
            if (landlordPhone != "") {
              if (validateMobileNumber(landlordPhone)) {
                nextStep = true;
              } else {
                msg = "Kindly enter valid mobile number";
              }
            } else {
              msg = "Kindly enter landlord mobile number";
            }
          } else {
            msg = "Kindly enter landlord email";
          }
        } else {
          msg = "Kindly enter landlord address";
        }
      } else {
        msg = "Kindly enter landlord father name";
      }
    } else {
      msg = "Kindly enter landlord name";
    }

    if (nextStep == true) {
      $("#propertyAddress").val(landlordAddress);
      updateFormValues("tenate");
    } else {
      popUpMessage("center", msg, "warning");
    }
  });

  $("#house").click(function (e) {
    e.preventDefault();

    // Tenate

    var nextStep = false;
    tenateName = $("#tenateName").val();
    tenateFatherName = $("#tenateFatherName").val();
    tenateEmail = $("#tenateEmail").val();
    tenatePhone = $("#tenatePhone").val();
    tenateAddress = $("#tenateAddress").val();

    if (tenateName != "") {
      if (tenateFatherName != "") {
        if (tenateAddress != "") {
          if (tenateEmail != "") {
            if (tenatePhone != "") {
              if (validateMobileNumber(tenatePhone)) {
                nextStep = true;
              } else {
                msg = "Kindly enter valid mobile number";
              }
            } else {
              msg = "Kindly enter tenate mobile number";
            }
          } else {
            msg = "Kindly enter tenate email";
          }
        } else {
          msg = "Kindly enter tenate address";
        }
      } else {
        msg = "Kindly enter tenate father name";
      }
    } else {
      msg = "Kindly enter tenate name";
    }

    if (nextStep == true) {
      updateFormValues("house");
    } else {
      popUpMessage("center", msg, "warning");
    }
  });

  // Handle checkbox change
  $("#propertyAddressCheck").change(function () {
    if ($(this).is(":checked")) {
      $("#propertyAddress").val(landlordAddress).prop("readonly", true);
    } else {
      $("#propertyAddress").prop("readonly", false);
    }
  });

  $("#contract").click(function (e) {
    e.preventDefault();
    var nextStep = false;
    $("#propertyAddress").prop("readonly", true);
    propertyFloor = $("#propertyFloor").val();
    propertyBHK = $("#propertyBHK").val();

    if (propertyFloor != "#") {
      if (propertyBHK != "#") {
        if (propertyAddress != "") {
          nextStep = true;
        } else {
          msg = "Property address cannot be empty";
        }
      } else {
        msg = "Kindly select Property size in BHK";
      }
    } else {
      msg = "Kindly select Property Floor";
    }

    if (nextStep) {
      updateFormValues("contract");
    } else {
      popUpMessage("center", msg, "warning");
    }
  });

  $("#submitAgreementForm").click(function (e) {
    e.preventDefault();

    var nextStep = false;

    // Agreement

    agreementStartDate = $("#agreementStartDate").val();
    rentAmount = $("#rentAmount").val();
    paymentDate = $("#paymentDate").val();
    securityAmount = $("#securityAmount").val();
    noticePeriod = $("#noticePeriod").val();

    if (agreementStartDate != "") {
      if (rentAmount != "") {
        if (paymentDate != "") {
          if (paymentDate < 31) {
            if (securityAmount != "") {
              if (noticePeriod != "#") {
                nextStep = true;
              } else {
                msg = "Kindly select notice period";
              }
            } else {
              msg = "Kindly select security amount";
            }
          } else {
            msg = "Kindly enter a valid date";
          }
        } else {
          msg = "Kindly enter payment date";
        }
      } else {
        msg = "Kindly enter rent amount";
      }
    } else {
      msg = "Kindly select agreement start date";
    }

    var formData = $("#agreementForm").serializeArray();

    if (nextStep) {
      $.ajax({
        type: "POST",
        url: `/api/rent_agreement/publish/`,
        dataType: "json",
        data: formData,
        success: function (response) {
          if (response.status == "success") {
            window.location.href =
              `/review-agreement/?request_id=` + btoa(response.enquiryId);
          } else {
            popUpMessage("center", response.msg, response.status);
          }
          $("#getStarted").show();
        },
        error: function (response) {
          console.log(`Error : `, response);
          popUpMessage(
            "center",
            response.responseJSON.msg,
            response.responseJSON.status
          );
          $("#getStarted").show();
        },
      });
    } else {
      popUpMessage("center", msg, "warning");
    }
  });

  $(".datepicker").datepicker({
    format: "mm/dd/yyyy",
    todayHighlight: true,
    autoclose: true,
    beforeShowDay: function (date) {
      // Calculate the date 7 days ago
      var last7Days = new Date();
      last7Days.setDate(last7Days.getDate() - 7);

      // Disable dates earlier than 7 days ago
      return date.valueOf() >= last7Days.valueOf()
        ? { enabled: true }
        : { enabled: false };
    },
  });
});
