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
    var name = $("#name").val();
    var email = $("#email").val();
    var mobile = $("#mobile").val();

    if (name != "") {
      if (email != "") {
        if (mobile != "") {
          var mobile = removeLeadingZero(mobile);
          if (validateMobileNumber(mobile)) {
            var requestIsValid = true;
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
      var formData = $("#getStartedForm").serialize();
      $.ajax({
        type: "GET",
        url: `/api/rent_agreement/generate/`,
        dataType: "json",
        data: formData,
        success: function (response) {
          popUpMessage("center", response.msg, response.status);
          if (response.status == "success") {
            setTimeout(function () {
              window.location.href =
                `/generate-rent-agreement/?request_id=` + btoa(enquiryId);
            }, 1500);
          }
        },
        error: function (response) {
          console.log(`Error : `, response);
          popUpMessage(
            "center",
            response.responseJSON.msg,
            response.responseJSON.status
          );
        },
      });
    } else {
      popUpMessage("center", msg, "warning");
    }
  });
});
