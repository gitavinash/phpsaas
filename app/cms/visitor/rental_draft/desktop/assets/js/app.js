$(document).ready(function () {
  var notaryServiceCharge = 0;
  var serviceCharges = 0;
  var deliveryCharges = 0;
  var totalOrdervalue = 0;
  var notaryStampValue = 0;
  var updatedTotalValue = 0;
  function getQueryParam(name) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
  }

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
              `/generate-agreement/?request_id=` +
              btoa(response.enquiryId) +
              "&state=" +
              state.toLowerCase();
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
    state = $("#state").val();

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
    formData.push({ name: "type", value: "rent_agreement" });
    formData.push({ name: "action", value: "publish" });

    if (nextStep) {
      $.ajax({
        type: "POST",
        url: `/api/rent_agreement/publish/`,
        dataType: "json",
        data: formData,
        success: function (response) {
          console.log(`success`, response);
          if (response.status == "success") {
            window.location.href =
              `/review-agreement/?request_id=` +
              response.enquiryId +
              "&state=" +
              state.toLowerCase();
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

  if ($(".datepicker").length > 0) {
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
  }

  if ($("#totalAmount").length > 0) {
    var totalValue = $("#totalAmount").text();
  }

  function totalStampValue() {
    var stampValue = $("#stampValue").text();
    serviceCharges = $("#serviceCharges").text();
    var notaryService = $("#notaryService").text();
    var deliveryService = $("#deliveryServices").text();

    stampValue = stampValue.replace("₹ ", "");
    serviceCharges = serviceCharges.replace("₹ ", "");
    notaryService = notaryService.replace("₹ ", "");
    deliveryService = deliveryService.replace("₹ ", "");

    updatedTotalValue =
      parseInt(stampValue) +
      parseInt(serviceCharges) +
      parseInt(notaryService) +
      parseInt(deliveryService);

    totalOrdervalue = updatedTotalValue;

    $("#totalAmount").html("₹ " + updatedTotalValue);
  }

  $('input[type="radio"][name="stampValue"]').change(function () {
    notaryStampValue = $(
      'input[type="radio"][name="stampValue"]:checked'
    ).val();
    var stateValue = getQueryParam("state");

    if (notaryStampValue === "other") {
      $("#otherValueWrapper").show();
    } else {
      $("#otherValueWrapper").hide();
    }

    if (stateValue.toLowerCase() == "rajasthan") {
      if (notaryStampValue == 100) {
        notaryServiceCharge = 299;
      } else {
        notaryServiceCharge = 399;
      }
    } else {
      notaryServiceCharge = 249;
    }
    if (notaryStampValue != "other") {
      $("#stampValue").html("₹ " + notaryStampValue);
    }
    $("#serviceCharges").html("₹ " + notaryServiceCharge);
    totalStampValue();
  });

  // Optional: Update the stamp value when 'Other' input changes
  if ($("#otherValue").length) {
    $("#otherValue").on("input", function () {
      var stateValue = getQueryParam("state");
      var otherNotaryStampValue = $(this).val();
      if (otherNotaryStampValue != "other") {
        if (stateValue.toLowerCase() == "rajasthan") {
          if (otherNotaryStampValue < 999) {
            notaryServiceCharge = 299;
          } else if (otherNotaryStampValue < 50001) {
            notaryServiceCharge = 399;
          } else {
            notaryServiceCharge = 499;
          }
        } else {
          if (otherNotaryStampValue < 999) {
            notaryServiceCharge = 249;
          } else if (otherNotaryStampValue < 50001) {
            notaryServiceCharge = (otherNotaryStampValue * 25) / 100;
          } else {
            notaryServiceCharge = (otherNotaryStampValue * 20) / 100;
          }
        }
        notaryStampValue = otherNotaryStampValue;
        $("#stampValue").html("₹ " + otherNotaryStampValue);
        if (notaryServiceCharge > 299) {
          $("#serviceCharges").html("₹ " + notaryServiceCharge);
        }
        if (notaryStampValue < 1000) {
          $("#notaryService").html("₹ " + 149);
        } else {
          $("#notaryService").html("₹ " + 299);
        }
        totalStampValue();
      }
    });
  }

  $('input[type="radio"][name="notaryService"]').change(function () {
    var notaryStamp = $(
      'input[type="radio"][name="notaryService"]:checked'
    ).val();
    if (notaryStamp == "true") {
      if (notaryStampValue < 1000) {
        $("#notaryService").html("₹ " + 149);
      } else {
        $("#notaryService").html("₹ " + 299);
      }
    } else {
      $("#notaryService").html("₹ " + 0);
    }
    totalStampValue();
  });

  $('input[type="radio"][name="deliveryServices"]').change(function () {
    var deliveryServices = $(
      'input[type="radio"][name="deliveryServices"]:checked'
    ).val();
    if (deliveryServices == "true") {
      deliveryCharges = 250;
      $("#deliveryServices").html("₹ " + 250);
    } else {
      $("#deliveryServices").html("₹ " + 0);
    }
    totalStampValue();
  });

  $("#processToPaymentCustom").click(function (e) {
    e.preventDefault();
    $("#processToPaymentCustom").hide();

    var totalAmount = $("#totalAmount").text();
    var totalAmount = parseFloat(totalAmount.replace("₹", "").trim());

    var couponCode = $("#couponCode").val();

    var formData = $(".shipping-box").serializeArray();
    // formData.push({ name: "coupon", value: couponCode });
    formData.push({ name: "type", value: "order" });
    formData.push({ name: "action", value: "generate" });
    formData.push({ name: "order", value: $("#orderId").val() });
    formData.push({ name: "paymentMode", value: $("#paymentMode").val() });
    formData.push({ name: "amount", value: totalAmount });
    formData.push({ name: "currency", value: "INR" });

    if (totalAmount != 0) {
      $.ajax({
        type: "POST",
        dataType: "json",
        data: formData,
        success: function (data) {
          if (data.status == "success") {
            window.location.href = data.url;
          } else {
            console.log(`Success : `, data);
            $("#processToPaymentCustom").show();
          }
        },
        error: function (data) {
          console.log(`error : `, data);
          $("#processToPaymentCustom").show();
        },
      });
    } else {
      alert("Stamp amount is wrong");
      $("#processToPaymentCustom").show();
    }
  });
});
