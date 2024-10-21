$("#addPolicy").click(function (e) {
  e.preventDefault();
  $("#addPolicy").hide();
  console.log(`addPolicy is clicked`);

  var title = $("#title").val() || "";
  var description = $("#editor-0").val() || "";
  console.log(`description`, description);

  if (title.trim() !== "") {
    if (description.trim() !== "") {
      $.ajax({
        type: "POST",
        url: "/api/policy/publish/",
        dataType: "json",
        data: {
          title,
          description,
        },
        success: function (response) {
          popUpMessage("center", response.msg, response.status);
          if (response.status == "success") {
            setTimeout(function () {
              window.location.href = "/dashboard/policy/list/";
            }, 1500);
          } else {
            $("#addPolicy").show();
          }
        },
        error: function (error) {
          $("#addPolicy").show();
          console.log(`Error : `, error);
          // popUpMessage("center", error.me, "error");
        },
      });
    } else {
      popUpMessage("center", "Kindly write policy description", "warning");
      $("#addPolicy").show();
    }
  } else {
    popUpMessage("center", "Kindly write policy title", "warning");
    $("#addPolicy").show();
  }
});

$("#updatePolicy").click(function (e) {
  e.preventDefault();
  $("#updatePolicy").hide();
  console.log(`updatePolicy is clicked`);

  var title = $("#title").val() || "";
  var description = $("#editor-0").val() || "";
  var id = $("#id").val() || "";
  console.log(`description`, description);

  if (id.trim() !== "") {
    if (title.trim() !== "") {
      if (description.trim() !== "") {
        $.ajax({
          type: "POST",
          url: "/api/policy/update/",
          dataType: "json",
          data: {
            id,
            title,
            description,
          },
          success: function (response) {
            popUpMessage("center", response.msg, response.status);
            if (response.status == "success") {
              setTimeout(function () {
                window.location.reload();
              }, 1500);
            } else {
              $("#updatePolicy").show();
            }
          },
          error: function (error) {
            $("#updatePolicy").show();
            console.log(`Error : `, error);
            // popUpMessage("center", error.me, "error");
          },
        });
      } else {
        popUpMessage("center", "Kindly write policy description", "warning");
        $("#updatePolicy").show();
      }
    } else {
      popUpMessage("center", "Kindly write policy title", "warning");
      $("#updatePolicy").show();
    }
  } else {
    popUpMessage("center", "Policy id is missing.", "warning");
    $("#updatePolicy").show();
  }
});
