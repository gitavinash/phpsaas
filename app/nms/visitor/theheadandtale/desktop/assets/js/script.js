var profileSideBar = document.querySelector(".profile--sideBar");
var faqBox = document.querySelector(".faq__box");

/** SweetAlert Pop Up Box */

function popUpMessage(msgPostion, msgTxt, msgType = "warning") {
  Swal.fire({
    position: msgPostion,
    icon: msgType,
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

if (profileSideBar) {
  const profile = document.querySelector(".profile--sideBar");
  const profileBtn = document.querySelector(".user--name");
  const cancelProfile = document.querySelector(".profile_cancel");

  profileBtn.addEventListener("click", () => {
    profile.classList.toggle("active");
  });
  cancelProfile.addEventListener("click", () => {
    profile.classList.toggle("active");
  });
}
if (faqBox) {
  const footerFaq = document.querySelectorAll(".faq__box");
  footerFaq.forEach((faq) => {
    faq.addEventListener("click", () => {
      faq.classList.toggle("active");
    });
  });
}

window.addEventListener(
  "contextmenu",
  function (e) {
    e.preventDefault();
  },
  false
);

// disable cntrl+shift+I
document.onkeydown = function (e) {
  if (
    e.ctrlKey &&
    (e.keyCode === 67 ||
      e.keyCode === 85 ||
      e.keyCode === 83 ||
      e.keyCode === 73)
  ) {
    return false;
  }
};

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
          setInterval(() => {
            window.href = "/login/";
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

function validateEmail(email) {
  // Regular expression for basic email validation
  var regex = /^\S+@\S+\.\S+$/;
  return regex.test(email);
}

$("#submitNewsletter").on("click", function (e) {
  e.preventDefault();
  const email = $("#newsletterEmail").val();
  $("#submitNewsletter").hide();
  if (validateEmail(email)) {
    $.ajax({
      url: "/api/mailchimp/addEmail/?list=6366f51d70&email=" + email,
      type: "GET",
      dataType: "json",
      success: function (data) {
        if (data.status == "success") {
          $("#submitNewsletter").show();
          window.location = "/thankyou_newsletter/";
        } else {
          popUpMessage("center", data.msg, data.status);
          $("#submitNewsletter").show();
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
        $("#submitNewsletter").show();
      },
    });
  } else {
    popUpMessage("center", "Kindly enter valid email address", "warning");
    $("#submitNewsletter").show();
  }
});

$("body").on("contextmenu", function (e) {
  return false;
});

$("body").bind("cut copy", function (e) {
  e.preventDefault();
});

//
// For Print
//

// Override the print method
window.print = function () {
  alert("Print is not allowed");
};

// Listen for print media queries and show the message
const noPrintDiv = document.querySelector(".no-print");
const mediaQueryList = window.matchMedia("print");

mediaQueryList.addEventListener("change", (e) => {
  if (e.matches) {
    // Print dialog is opened
    noPrintDiv.style.display = "block";
  } else {
    // Print dialog is closed
    noPrintDiv.style.display = "none";
  }
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
        reg_type: "subscriber",
      },
      success: function (data) {
        $("#emailLoginContinueBtn").show();
        if (data.status == "success") {
          // Switch sections
          $("#enterEmail").hide();
          $("#verifyEmailOTP").show();
          $("#otpEmailValue").focus().select();
          // Make OTP focusable + validated now
          $("#otpEmailValue")
            .prop("disabled", false)
            .prop("required", true)
            .val("")
            .focus();

          startResendCountdown("resendEmailOTPBtn");
          localStorage.setItem("email", email);
          localStorage.setItem("otpRef", data.otpRef);
        } else {
          popUpMessage("center", data.msg, data.status);
        }
      },
      error: function (data) {
        console.log(data);
        popUpMessage("center", data.responseJSON.msg, data.responseJSON.status);
        $("#emailLoginContinueBtn").show();
      },
    });
  } else {
    popUpMessage("center", "Kindly enter a valid email address", "warning");
    $("#emailLoginContinueBtn").show();
  }
});

$("#verifyEmailOTPBtn").click(function (e) {
  e.preventDefault();
  $("#verifyEmailOTPBtn").hide();

  var otpValue = $("#otpEmailValue").val().trim();
  var regex = /^\d{6}$/;

  if (regex.test(otpValue)) {
    $.ajax({
      url: "/api/auth/verify-otp/",
      type: "GET",
      dataType: "json",
      data: {
        otp_ref_id: localStorage.getItem("otpRef"),
        otp_code: otpValue,
        reg_type: "subscriber",
        otp_mode: "email",
      },
      success: function (data) {
        $("#verifyEmailOTPBtn").show();
        if (data.status == "success") {
          if (data.existingUser) {
            console.log(`Login by existing user: ${data.existingUser}`);

            popUpMessage("center", data.msg, data.status);
            // Hide OTP section and make input non-validatable again
            $("#verifyEmailOTP").hide();
            $("#otpEmailValue").prop("required", false).prop("disabled", true);

            popUpMessage(
              "center",
              "You are successfully logged in",
              data.status
            );

            const redirect = getURLParameter("redirect_url");
            if (redirect) {
              window.location.href = redirect;
            } else {
              window.location.href = "/manage-subscription/";
            }
          } else {
            console.log("New user, proceed to registration");
            $("#verifyEmailOTP").hide();
            $("#userRegistration").show();
          }
        } else {
          popUpMessage("center", data.msg, data.status);
        }
      },
      error: function (data) {
        console.log(data);
        popUpMessage("center", data.responseJSON.msg, data.responseJSON.status);
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
  if ($(this).hasClass("disabled")) return;

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
      popUpMessage("center", data.msg, data.status);
    },
    error: function (data) {
      console.log(data);
      popUpMessage("center", data.responseJSON.msg, data.responseJSON.status);
    },
  });
});

$("#userRegistrationBtn").click(function (e) {
  e.preventDefault();

  const $btn = $("#userRegistrationBtn");
  const userName = $("#userName").val().trim();
  // const userId = $.cookie("user_data"); // ✅ Corrected from $.cokie to $.cookie

  // Disable the button to prevent multiple clicks
  $btn.prop("disabled", true).hide();

  // Validate user name
  if (!userName) {
    popUpMessage("center", "Please enter your name.", "warning");
    $btn.prop("disabled", false).show();
    return;
  }

  // Perform the AJAX request to update the name
  $.ajax({
    url: "/api/auth/user-profile-update/",
    type: "POST", // ✅ Use POST for updates (better than GET)
    dataType: "json",
    data: {
      // user_id: userId,
      name: userName,
    },
    success: function (data) {
      if (data.status == "success") {
        popUpMessage(
          "center",
          data.msg || "Profile updated successfully!",
          "success"
        );

        setTimeout(() => {
          window.location.href = "/manage-subscription/";
        }, 2000);
      } else {
        popUpMessage("center", data.msg || "Failed to update name.", "error");
      }
    },
    error: function (xhr) {
      console.error("Error:", xhr);
      const errMsg =
        xhr.responseJSON?.msg ||
        "An unexpected error occurred. Please try again.";
      popUpMessage("center", errMsg, "error");
    },
    complete: function () {
      // Re-enable the button
      $btn.prop("disabled", false).show();
    },
  });
});
