$(document).ready(function () {
  var userId = 0;
  var mobileNo = 0;
  var fullName = "";
  var email = "";
  var message = "";
  var amount = 0;

  $("#searchUser").click(function (e) {
    $("#mobileNumberError").hide();
    mobileNo = $("#mobile").val();
    if (mobileNo.length < 10) {
      $("#mobileNumberError").show();
    } else {
      // Call Domain search API

      $.ajax({
        type: "GET",
        url: "/api/user/search?type=mobile&value=" + mobileNo,
        dataType: "json",
        success: function (response) {
          showPaymentForm(response);
        },
        error: function (response) {
          console.log(response);
        },
      });
    }

    return false;
  });

  $("#generatePayment").click(function (e) {
    $("#nameError").hide();
    $("#mobileNumberError").hide();
    $("#emailError").hide();
    $("#amountError").hide();
    if (mobileNo.length < 10) {
      $("#mobileNumberError").show();
    } else {
      // Call Domain search API

      message = $("#message").val();
      amount = $("#amount").val();

      if (amount == 0) {
        $("#amountError").show();
      } else {
        $.ajax({
          type: "GET",
          url: `/api/payment/request/?userId=${userId}&name=${fullName}&email=${email}&mobile=${mobileNo}&amount=${amount}&message=${message}`,
          dataType: "json",
          success: function (response) {
            showPaymentForm(response);
          },
          error: function (response) {
            console.log(response);
          },
        });
      }
    }
    return false;
  });

  function showPaymentForm(apiResponse) {
    var result = apiResponse.data;
    userId = result.userId;
    if (apiResponse.isFound == 1) {
      fullName = result.firstName + " " + result.lastName;
      email = result.email;
    }
    mobileNo = result.mobile;
    $("#searchByMobile").hide();
    $("#profileBox").show();
    $("#name").val(fullName);
    $("#email").val(email);
    $("#resMobile").val(mobileNo);
  }
});
