$(document).ready(function () {
  /** Default Pop Up Box */

  function popUpMessage(msgPostion, msgTxt, msgType) {
    Swal.fire({
      position: msgPostion,
      type: msgType,
      title: msgTxt,
      showConfirmButton: false,
      timer: 1500,
    });
  }

  function startResendCountdown() {
    let countdown = 30;
    $("#resendOTPBtn")
      .text(`Resend OTP in ${countdown}s`)
      .addClass("disabled") // Optional: add a class for styling
      .css("pointer-events", "none"); // Disable clicking

    let timer = setInterval(function () {
      countdown--;
      $("#resendOTPBtn").text(`Resend OTP in ${countdown}s`);

      if (countdown <= 0) {
        clearInterval(timer);
        $("#resendOTPBtn")
          .text("Resend OTP")
          .removeClass("disabled")
          .css("pointer-events", "auto"); // Enable clicking again
      }
    }, 1000);
  }

  function getURLParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    let value = urlParams.get(name);

    if (value) {
      try {
        value = decodeURIComponent(decodeURIComponent(value));
      } catch (e) {
        console.warn("Failed to decode", name, e);
      }

      // Optional: restrict to same-origin relative URLs
      if (value.startsWith("/")) {
        return value;
      }
    }

    return null;
  }

  /** Password Validation */

  /** Check Password fiels typing status */

  // $('#password').on('keyup', function(e) {

  // 	$('.btn').prop('disabled', true);
  // 	var password = $('#password').val();

  // 	var passwordLenght=password.length;
  // 	var smallLetterCharacters=new RegExp("/([a-z])/g");
  // 	var capitalLetterCharacters="/[A-Z]/i";
  // 	var numericCharacter="/[0-9]/";
  // 	var specialCharacter="/[!@#$%^&*_]/g";
  // 	console.log(passwordLenght + ' small Latter ' + smallLetterCharacters.test(password));
  // 	if(passwordLenght<8) {

  // 		$('.btn').val('Please enter atleast 8 characters');

  // 	} else if (password.search(smallLetterCharacters)>0) {

  // 		$('.btn').val('Please enter atleast one small characters');

  // 	} else if (password.search(capitalLetterCharacters)<0) {

  // 		$('.btn').val('Please enter atleast one CAPITAL characters');

  // 	} else if (numericCharacter.search(password)<0) {

  // 		$('.btn').val('Please enter atleast 1 number');

  // 	} else if(specialCharacter.search(password)<0) {

  // 		$('.btn').val('Please enter atleast one special character');

  // 	} else {

  // 		$('.btn').val('Strong Password');
  // 		$('.btn').prop('disabled', false);

  // 	}

  // });

  /* login form */

  $("#loginBtn").on("click", function (event) {
    event.preventDefault();

    $("#loginBtn").hide();

    var email = $("#email").val();
    var password = $("#pass").val();

    if (email == "") {
      popUpMessage("center", "Please enter your email", "warning");
      $("#loginBtn").show();
    } else if (password == "") {
      popUpMessage("center", "Please enter your password", "warning");
      $("#loginBtn").show();
    } else {
      $.ajax({
        type: "POST",
        dataType: "json",
        data: $("#login").serialize(),
        success: function (data) {
          if (data.status == "success") {
            window.location.href = "//" + document.domain + data.url;
          } else {
            popUpMessage("center", data.msg, data.status);
            $("#loginBtn").show();
          }
        },
        error: function (data) {
          console.log(data);
          $("#loginBtn").show();
        },
      });
    }
  });

  /* register form */

  $("#registerBtn").on("click", function (event) {
    event.preventDefault();
    $("#registerBtn").hide();

    var first_name = $("#first_name").val();
    var last_name = $("#last_name").val();
    var mobile = $("#mobile").val();
    var email = $("#email").val();
    var password = $("#password").val();
    var confirmPassword = $("#confirmPassword").val();

    var isPasswordSame = password.localeCompare(confirmPassword);

    if (first_name == "") {
      popUpMessage("center", "Please enter your first name", "warning");
      $("#registerBtn").show();
    } else if (last_name == "") {
      popUpMessage("center", "Please enter your last name", "warning");
      $("#registerBtn").show();
    } else if (mobile == "") {
      popUpMessage("center", "Please enter your mobile", "warning");
      $("#registerBtn").show();
    } else if (email == "") {
      popUpMessage("center", "Please enter your email", "warning");
      $("#registerBtn").show();
    } else if (password == "") {
      popUpMessage("center", "Please enter your password", "warning");
      $("#registerBtn").show();
    } else if (confirmPassword == "") {
      popUpMessage("center", "Please confirm your password", "warning");
      $("#registerBtn").show();
    } else if (isPasswordSame === 0) {
      $.ajax({
        type: "POST",
        dataType: "json",
        data: $("#register").serialize(),
        success: function (data) {
          if (data.status == "success") {
            window.location.href = "//" + document.domain + data.url;
          } else {
            popUpMessage("center", data.msg, data.status);
            $("#registerBtn").show();
          }
        },
        error: function (data) {
          console.log(data);
          $("#registerBtn").show();
        },
      });
    } else {
      popUpMessage("center", "Your password not matched", "warning");
      $("#registerBtn").show();
    }
  });

  /* Forgot Password form */

  $("#forgotPassword").on("click", function (event) {
    event.preventDefault();
    $("#forgotPassword").hide();
    $.ajax({
      type: "POST",
      dataType: "json",
      data: $("#forgot").serialize(),
      success: function (data) {
        if (data.status == "success") {
          $(".beforeClick").hide();
          $(".afterClick").show();
          $("#statusMsg").text(data.msg);
          popUpMessage("center", data.msg, data.status);
          $("#forgotPassword").show();
        } else {
          popUpMessage("center", data.msg, data.status);
          $("#forgotPassword").show();
        }
      },
      error: function (data) {
        console.log(data);
        $("#forgotPassword").show();
      },
    });
  });

  /* Update Forgot Password form */

  $("#updatePassword").on("click", function (event) {
    event.preventDefault();
    $("#updatePassword").hide();

    var password = $("#password").val();
    var confirmPassword = $("#confirmPassword").val();

    var isPasswordSame = password.localeCompare(confirmPassword);

    if (password == "") {
      popUpMessage("center", "Please enter your password", "warning");
      $("#updatePassword").show();
    } else if (confirmPassword == "") {
      popUpMessage("center", "Please confirm your password", "warning");
      $("#updatePassword").show();
    } else if (isPasswordSame === 0) {
      $.ajax({
        type: "POST",
        dataType: "json",
        data: $("#update").serialize(),
        success: function (data) {
          if (data.status == "success") {
            $(".beforeClick").hide();
            $(".afterClick").show();
            $("#statusMsg").text(data.msg);
            popUpMessage("center", data.msg, data.status);
          } else {
            popUpMessage("center", data.msg, data.status);
          }
        },
        error: function (data) {
          console.log(data);
          $("#updatePassword").show();
        },
      });
    } else {
      popUpMessage("center", "Your password not matched", "warning");
      $("#updatePassword").show();
    }
  });

  /** Resend verification form */

  $("#resendVerificationMail").click(function (event) {
    event.preventDefault();
    $("#resendVerificationMail").hide();
    $.ajax({
      type: "POST",
      dataType: "json",
      data: $("#verifyEmail").serialize(),
      success: function (data) {
        if (data.status == "success") {
          $(".beforeClick").hide();
          $(".afterClick").show();
          $("#statusMsg").text(data.msg);
          popUpMessage("center", data.msg, data.status);
        } else {
          popUpMessage("center", data.msg, data.status);
          $("#resendVerificationMail").show();
        }
      },
      error: function (data) {
        console.log(data);
        $("#resendVerificationMail").show();
      },
    });
  });

  /** Website setup registration form */

  $("#websiteSetupBtn").click(function (e) {
    e.preventDefault();
    $("#websiteSetupBtn").hide();
    /** Form Validation */

    var domain = $("#domain").val();
    var app = $("#app").val();

    if (domain == "") {
      popUpMessage(
        "center",
        "Please enter domain name of your website",
        "warning"
      );
      $("#websiteSetupBtn").show();
    } else if (app == "") {
      popUpMessage(
        "center",
        "Please select any Application for your website",
        "warning"
      );
      $("#websiteSetupBtn").show();
    } else {
      /** Calling ajax function */

      $.ajax({
        type: "POST",
        dataType: "json",
        data: $("#websiteSetup").serialize(),
        success: function (data) {
          if (data.status == "success") {
            popUpMessage("center", data.msg, data.status);
            window.setTimeout(function () {
              window.location.href = "//" + document.domain + data.url;
            }, 1700);
          } else {
            popUpMessage("center", data.msg, data.status);
          }
          $("#websiteSetupBtn").show();
        },
        error: function (data) {
          console.log(data);
          $("#websiteSetupBtn").show();
        },
      });
    }
  });

  /** Website Configuration */

  $("#pos1").click(function () {
    $("#pos1").addClass("active");
    $("#pos1Box").css("display", "block");

    $("#pos2").removeClass("active");
    $("#pos2Box").hide();

    $("#pos3").removeClass("active");
    $("#pos3Box").hide();

    $("#pos4").removeClass("active");
    $("#pos4Box").hide();
  });

  $("#pos2").click(function () {
    $("#pos2").addClass("active");
    $("#pos2Box").css("display", "block");

    $("#pos1").removeClass("active");
    $("#pos1Box").hide();

    $("#pos3").removeClass("active");
    $("#pos3Box").hide();

    $("#pos4").removeClass("active");
    $("#pos4Box").hide();
  });

  $("#pos3").click(function () {
    $("#pos3").addClass("active");
    $("#pos3Box").css("display", "block");

    $("#pos1").removeClass("active");
    $("#pos1Box").hide();

    $("#pos2").removeClass("active");
    $("#pos2Box").hide();

    $("#pos4").removeClass("active");
    $("#pos4Box").hide();
  });

  $("#pos4").click(function () {
    $("#pos4").addClass("active");
    $("#pos4Box").css("display", "block");

    $("#pos1").removeClass("active");
    $("#pos1Box").hide();

    $("#pos2").removeClass("active");
    $("#pos2Box").hide();

    $("#pos3").removeClass("active");
    $("#pos3Box").hide();
  });

  $("#adsTXT").click(function () {
    $.ajax({
      type: "POST",
      data: {
        type: "api",
        action: "setting",
        key: "ads",
        data: $("#ads_txt").val(),
      },
      success: function (data) {
        simplePopUp("success", "success", data.msg);
      },
      error: function (data) {
        simplePopUp("error", "Error", data.msg);
      },
    });
  });

  $("#robotsTXT").click(function () {
    $.ajax({
      type: "POST",
      data: {
        type: "api",
        action: "setting",
        key: "robots",
        data: $("#robots_txt").val(),
      },
      success: function (data) {
        simplePopUp("success", "success", data.msg);
      },
      error: function (data) {
        simplePopUp("error", "Error", data.msg);
      },
    });
  });

  $("#mainfestJSON").click(function () {
    $.ajax({
      type: "POST",
      data: {
        type: "api",
        action: "setting",
        key: "mainfest",
        data: $("#mainfest_json").val(),
      },
      success: function (data) {
        simplePopUp("success", "success", data.msg);
      },
      error: function (data) {
        simplePopUp("error", "Error", data.msg);
      },
    });
  });

  // Mobile authentication

  $("#mobileLoginContinueBtn").click(function (e) {
    e.preventDefault();
    $("#mobileLoginContinueBtn").hide();
    var mobile = $("#mobile").val();
    var regex = /^[6-9]\d{9}$/; // Indian phone number format
    if (regex.test(mobile)) {
      $.ajax({
        url: "/api/auth/auth-by-mobile/",
        type: "GET",
        dataType: "json",
        data: {
          mobile: mobile,
          reg_type: "customer",
        },
        success: function (data) {
          if (data.status == "success") {
            popUpMessage("center", data.msg, data.status);
            $("#mobileLoginContinueBtn").show();
            $("#enterMobile").hide();
            $("#verifyOTP").show();
            startResendCountdown();
            localStorage.setItem("mobile", mobile);
            localStorage.setItem("otpRef", data.otpRef);
          } else {
            popUpMessage("center", data.msg, data.status);
            $("#mobileLoginContinueBtn").show();
          }
        },
        error: function (data) {
          console.log(data);
          popUpMessage(
            "center",
            data.responseJSON.msg,
            data.responseJSON.status
          );
          $("#mobileLoginContinueBtn").show();
        },
      });
    } else {
      popUpMessage("center", "Kindly enter a valid mobile number", "warning");
      $("#mobileLoginContinueBtn").show();
    }
  });

  $("#verifyOTPBtn").click(function (e) {
    e.preventDefault();
    $("#verifyOTPBtn").hide();
    var otpValue = $("#otpValue").val();
    var regex = /^\d{6}$/; // Indian phone number format
    if (regex.test(otpValue)) {
      $.ajax({
        url: "/api/auth/verify-otp/",
        type: "GET",
        dataType: "json",
        data: {
          otp_ref_id: localStorage.getItem("otpRef"),
          otp_code: otpValue,
          otp_mode: "sms",
        },
        success: function (data) {
          $("#verifyOTPBtn").show();
          if (data.status == "success") {
            popUpMessage("center", data.msg, data.status);
            $("#verifyOTP").hide();
            popUpMessage(
              "center",
              "You are successfully logged in",
              data.status
            );
            console.log(`data`, data);

            localStorage.setItem("existingUser", data.existingUser);

            const redirect = getURLParameter("redirect_url");

            if (redirect) {
              window.location.href = redirect;
            } else {
              window.location.href = "/dashboard/";
            }
          } else {
            popUpMessage("center", data.msg, data.status);
          }
        },
        error: function (data) {
          console.log(data);
          popUpMessage(
            "center",
            data.responseJSON.msg,
            data.responseJSON.status
          );
          $("#verifyOTPBtn").show();
        },
      });
    } else {
      popUpMessage(
        "center",
        "Invalid OTP. Please enter a 6-digit code.",
        "warning"
      );
      $("#verifyOTPBtn").show();
    }
  });

  $("#resendOTPBtn").click(function (e) {
    e.preventDefault();
    startResendCountdown();
    $.ajax({
      url: "/api/auth/resend-otp/",
      type: "GET",
      dataType: "json",
      data: {
        otp_ref_id: localStorage.getItem("otpRef"),
        otp_mode: "sms",
      },
      success: function (data) {
        if (data.status == "success") {
          popUpMessage("center", data.msg, data.status);
          startResendCountdown();
        } else {
          popUpMessage("center", data.msg, data.status);
          startResendCountdown();
        }
      },
      error: function (data) {
        console.log(data);
        popUpMessage("center", data.responseJSON.msg, data.responseJSON.status);
        startResendCountdown();
      },
    });
  });
});
