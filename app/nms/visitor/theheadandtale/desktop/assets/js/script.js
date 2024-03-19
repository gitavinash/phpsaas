var profileSideBar = document.querySelector(".profile--sideBar");
var faqBox = document.querySelector(".faq__box");

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
