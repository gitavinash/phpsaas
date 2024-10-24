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

  $("#addUpayBtn").click(function (e) {
    e.preventDefault();

    var data = new FormData(document.getElementById("addUpayForm"));
    data.append("leadId", leadId);
    $.ajax({
      url: "/api/astro_client/leads/upay/",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      dataType: "JSON",
      encrypt: "multipart/form-data",
      success: function (response) {
        popUpMessage("center", response.msg, response.status);
        setTimeout(function () {
          location.reload();
        }, 1500);
      },
      error: function (error, response) {
        popUpMessage(
          "center",
          error.responseJSON.msg,
          error.responseJSON.status
        );
        $("#addAmenities").show();
      },
    });
  });
});
