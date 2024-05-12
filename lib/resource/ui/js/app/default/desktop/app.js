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
          error: function (data) {
            console.log(data);
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
              window.location.href = data.url;
            }, 1700);
          } else {
            popUpMessage("center", data.msg, data.status);
          }
        },
        error: function (data) {
          console.log(data);
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
});
