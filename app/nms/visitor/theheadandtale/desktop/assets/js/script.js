var profileSideBar = document.querySelector(".profile--sideBar");
var faqBox = document.querySelector(".faq__box");
var loginBox = document.querySelector(".login");

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

if (loginBox) {
  document
    .getElementById("loginBtn")
    .addEventListener("click", function (event) {
      event.preventDefault();

      document.getElementById("loginBtn").style.display = "none";

      var email = document.getElementById("email").value;
      var password = document.getElementById("pass").value;

      if (email === "") {
        popUpMessage("center", "Please enter your email", "warning");
        document.getElementById("loginBtn").style.display = "block";
      } else if (password === "") {
        popUpMessage("center", "Please enter your password", "warning");
        document.getElementById("loginBtn").style.display = "block";
      } else {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/", true);
        xhr.setRequestHeader(
          "Content-Type",
          "application/x-www-form-urlencoded"
        );
        xhr.onreadystatechange = function () {
          if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
              var data = JSON.parse(xhr.responseText);
              if (data.status === "success") {
                window.location.href = "//" + document.domain + data.url;
              } else {
                alert(data.msg);
                document.getElementById("loginBtn").style.display = "block";
              }
            } else {
              console.log("Error:", xhr.status);
              document.getElementById("loginBtn").style.display = "block";
            }
          }
        };
        xhr.send(
          new URLSearchParams(new FormData(document.getElementById("login")))
        );
      }
    });
}
// // disable right click
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
          setInterval(() => {
            window.href = "/login/";
          }, 1500);
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
          setInterval(() => {
            window.href = "/thankyou_newsletter/";
          }, 200);
        } else {
          popUpMessage("center", data.msg, data.status);
          $("#submitNewsletter").show();
        }
      },
      error: function (data) {
        console.error(`error: `, data);
        $("#submitNewsletter").show();
      },
    });
  } else {
    popUpMessage("center", "Kindly enter valid email address", "warning");
    $("#submitNewsletter").show();
  }
});
