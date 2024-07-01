$(document).ready(function () {
  var mobileRegex = /^[6-9]\d{9}$/;
  function validateMobileNumber(mobileNumber) {
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

  function updateFormValues(btnName, nextBox) {
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

  $("#landlord").click(function (e) {
    e.preventDefault();
    updateFormValues("landlord", "tenate");
  });

  $("#tenate").click(function (e) {
    e.preventDefault();
    updateFormValues("tenate", "house");
  });

  $("#house").click(function (e) {
    e.preventDefault();
    updateFormValues("house", "contract");
  });

  $("#contract").click(function (e) {
    e.preventDefault();
    updateFormValues("contract", "");
  });
  $(".datepicker").datepicker({
    format: "mm/dd/yyyy",
    todayHighlight: true,
    autoclose: true,
  });
});
