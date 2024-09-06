$(document).ready(function (e) {
  $("#upayDetailsBtn").click(function (e) {
    toggleDisplay("#upayDetailsBox", false);
    e.preventDefault();
  });

  $("#upayBtn").click(function (e) {
    document.querySelector(".upayBox").style.display = "flex";
    $(".upayBox").show();
    hidePageLoader();
    e.preventDefault();
  });
});
