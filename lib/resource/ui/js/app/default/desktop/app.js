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

  function startResendCountdown(btnId) {
    let countdown = 30;
    $(`#${btnId}`)
      .text(`Resend OTP in ${countdown}s`)
      .addClass("disabled") // Optional: add a class for styling
      .css("pointer-events", "none"); // Disable clicking

    let timer = setInterval(function () {
      countdown--;
      $(`#${btnId}`).text(`Resend OTP in ${countdown}s`);

      if (countdown <= 0) {
        clearInterval(timer);
        $(`#${btnId}`)
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
        error: function (errorResponse) {
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
          setTimeout(function () {
            window.location.reload();
          }, 1500);
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
      var mobile = $("#mobile").val().trim();
      mobile = mobile.replace(/^(\+91[\-\s]?|0)/, "");
      var regex = /^[6-9]\d{9}$/; // Indian phone number format
      if (regex.test(mobile)) {
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
          error: function (errorResponse) {
            popUpMessage(
              "center",
              errorResponse.responseJSON.msg,
              errorResponse.responseJSON.status
            );
            setTimeout(function () {
              window.location.reload();
            }, 1500);
            $("#registerBtn").show();
          },
        });
      } else {
        popUpMessage("center", "Kindly enter a valid mobile number", "warning");
        $("#registerBtn").show();
      }
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
          $("#forgotPassword").show();
          popUpMessage("center", data.msg, data.status);
        }
      },
      error: function (errorResponse) {
        $("#forgotPassword").show();
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
        setTimeout(function () {
          window.location.reload();
        }, 1500);
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
      const formPayload = $("#updatePasswordForm").serialize();
      $.ajax({
        type: "POST",
        dataType: "json",
        data: formPayload,
        success: function (data) {
          if (data.status == "success") {
            $(".beforeClick").hide();
            $(".afterClick").show();
            $("#statusMsg").text(data.msg);
            popUpMessage("center", data.msg, data.status);
            setTimeout(function () {
              window.location.href = "/login/";
            }, 1500);
          } else {
            popUpMessage("center", data.msg, data.status);
          }
        },
        error: function (errorResponse) {
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
          setTimeout(function () {
            window.location.reload();
          }, 1500);
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
      error: function (errorResponse) {
        popUpMessage(
          "center",
          errorResponse.responseJSON.msg,
          errorResponse.responseJSON.status
        );
        setTimeout(function () {
          window.location.reload();
        }, 1500);
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
              window.location.href = data.url;
            }, 1700);
          } else {
            popUpMessage("center", data.msg, data.status);
          }
        },
        error: function (errorResponse) {
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
          setTimeout(function () {
            window.location.reload();
          }, 1500);
          $("#websiteSetupBtn").show();
        },
      });
    }
  });

  /** Website Configuration */

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

  // Add new app

  $("#installNewApp").click(function () {
    e.preventDefault();
    console.log("i m here");
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
            $("#mobileLoginContinueBtn").show();
            $("#enterMobile").hide();
            $("#verifyOTP").show();
            startResendCountdown("resendOTPBtn");
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
    startResendCountdown("resendOTPBtn");
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
          startResendCountdown("resendOTPBtn");
        } else {
          popUpMessage("center", data.msg, data.status);
          startResendCountdown("resendOTPBtn");
        }
      },
      error: function (data) {
        console.log(data);
        popUpMessage("center", data.responseJSON.msg, data.responseJSON.status);
        startResendCountdown("resendOTPBtn");
      },
    });
  });

  // Email authentication

  $("#emailLoginContinueBtn").click(function (e) {
    e.preventDefault();
    $("#emailLoginContinueBtn").hide();
    var email = $("#email").val().trim();
    email = email.replace(/^(\+91[\-\s]?|0)/, "");
    var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (regex.test(email)) {
      $.ajax({
        url: "/api/auth/auth-by-email/",
        type: "GET",
        dataType: "json",
        data: {
          email: email,
          reg_type: "customer",
        },
        success: function (data) {
          if (data.status == "success") {
            $("#emailLoginContinueBtn").show();
            $("#enterEmail").hide();
            $("#verifyEmailOTP").show();
            startResendCountdown("resendEmailOTPBtn");
            localStorage.setItem("email", email);
            localStorage.setItem("otpRef", data.otpRef);
          } else {
            popUpMessage("center", data.msg, data.status);
            $("#emailLoginContinueBtn").show();
          }
        },
        error: function (data) {
          console.log(data);
          popUpMessage(
            "center",
            data.responseJSON.msg,
            data.responseJSON.status
          );
          $("#emailLoginContinueBtn").show();
        },
      });
    } else {
      popUpMessage("center", "Kindly enter a valid email number", "warning");
      $("#emailLoginContinueBtn").show();
    }
  });

  $("#verifyEmailOTPBtn").click(function (e) {
    e.preventDefault();
    $("#verifyEmailOTPBtn").hide("verifyEmailOTP");
    var otpValue = $("#otpEmailValue").val();
    var regex = /^\d{6}$/; // Indian phone number format
    if (regex.test(otpValue)) {
      $.ajax({
        url: "/api/auth/verify-otp/",
        type: "GET",
        dataType: "json",
        data: {
          otp_ref_id: localStorage.getItem("otpRef"),
          otp_code: otpValue,
          otp_mode: "email",
        },
        success: function (data) {
          $("#verifyEmailOTPBtn").show();
          if (data.status == "success") {
            popUpMessage("center", data.msg, data.status);
            $("#verifyEmailOTP").hide();
            popUpMessage(
              "center",
              "You are successfully logged in",
              data.status
            );
            localStorage.setItem("existingUser", data.existingUser);
            const redirect = getURLParameter("redirect_url");

            if (redirect) {
              window.location.href = redirect;
            } else {
              window.location.href = "/dashboard/";
            }
          } else {
            $("#verifyEmailOTPBtn").show();
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
          $("#verifyEmailOTPBtn").show();
        },
      });
    } else {
      $("#verifyEmailOTPBtn").show();
      popUpMessage(
        "center",
        "Invalid OTP. Please enter a 6-digit code.",
        "warning"
      );
    }
  });

  $("#resendEmailOTPBtn").click(function (e) {
    e.preventDefault();
    startResendCountdown("resendEmailOTPBtn");
    $.ajax({
      url: "/api/auth/resend-otp/",
      type: "GET",
      dataType: "json",
      data: {
        otp_ref_id: localStorage.getItem("otpRef"),
        otp_mode: "email",
      },
      success: function (data) {
        if (data.status == "success") {
          popUpMessage("center", data.msg, data.status);
          startResendCountdown("resendEmailOTPBtn");
        } else {
          popUpMessage("center", data.msg, data.status);
          startResendCountdown("resendEmailOTPBtn");
        }
      },
      error: function (data) {
        console.log(data);
        popUpMessage("center", data.responseJSON.msg, data.responseJSON.status);
        startResendCountdown("resendEmailOTPBtn");
      },
    });
  });
});
