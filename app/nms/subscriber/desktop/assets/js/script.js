var profileSideBar = document.querySelector(".profile--sideBar");
var faqBox = document.querySelector(".faq__box");
var loginBox = document.querySelector(".login");

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
