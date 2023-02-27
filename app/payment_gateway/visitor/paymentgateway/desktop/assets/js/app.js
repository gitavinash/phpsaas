$(document).ready(function () {
  $("#searchUser").click(function (e) {
    e.preventDefault;
    var mobileNo = $("#mobile").val();

    if (mobileNo.length < 10) {
    } else {
      // Call Domain search API

      $.ajax({
        type: "GET",
        url: "/api/user/search?type=mobile&value=" + mobileNo,
        dataType: "json",
        success: function (data) {
          console.log(data);
          $("#searchByMobile").hide();
        },
        error: function (data) {
          console.log(`Error`);
          console.log(data);
        },
      });
    }

    return false;
  });
});
