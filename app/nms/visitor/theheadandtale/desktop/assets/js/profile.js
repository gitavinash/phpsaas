const profile = document.querySelector(".profile--sideBar");
const profileBtn = document.querySelector(".user--name");
const cancelProfile = document.querySelector(".profile_cancel");

profileBtn.addEventListener("click", () => {
  profile.classList.toggle("active");
});
cancelProfile.addEventListener("click", () => {
  profile.classList.toggle("active");
});
