$(document).ready(function () {
  $("#addMembership").on("click", function (e) {
    e.preventDefault();

    // Get values
    var name = $("#name").val().trim();
    var mobile = $("#mobile").val().trim();
    var packageType = $("#package").val();
    var price = $("#price").val().trim();
    var dueAmount = $("#dueAmount").val().trim();
    var visit = $("#visit").val().trim();
    var requirements = $("#requirements").val().trim();

    // ===== VALIDATION =====
    if (name === "") {
      popUpMessage("center", "Please enter customer name", "warning");
      return;
    }

    if (mobile === "" || !/^[0-9]{10}$/.test(mobile)) {
      popUpMessage(
        "center",
        "Please enter a valid 10-digit mobile number",
        "warning"
      );
      return;
    }

    if (packageType === "") {
      popUpMessage("center", "Please select a room type", "warning");
      return;
    }

    if (price === "" || parseFloat(price) <= 0) {
      popUpMessage(
        "center",
        "Please enter a valid price greater than 0",
        "warning"
      );
      return;
    }

    if (dueAmount === "" || parseFloat(dueAmount) < 0) {
      popUpMessage("center", "Due amount cannot be negative", "warning");
      return;
    }

    if (parseFloat(dueAmount) > parseFloat(price)) {
      popUpMessage(
        "center",
        "Due amount cannot exceed the total price",
        "warning"
      );
      return;
    }

    if (visit === "" || parseInt(visit) < 1) {
      popUpMessage("center", "Total visit must be at least 1", "warning");
      return;
    }

    $.ajax({
      url: "/api/membership/add/",
      type: "POST",
      data: {
        name: name,
        mobile: mobile,
        package: packageType,
        price: price,
        dueAmount: dueAmount,
        visit: visit,
        requirements: requirements,
      },
      dataType: "JSON",
      success: function (response) {
        if (response.status === "success") {
          // Reset form after success
          $("#addNewMembershipForm")[0].reset();
          popUpMessage(
            "center",
            response.msg || "Something went wrong",
            "success"
          );
        } else {
          popUpMessage(
            "center",
            response.msg || "Something went wrong",
            "error"
          );
        }
      },
      error: function (xhr) {
        var errorMsg = "An error occurred while saving membership.";
        if (xhr.responseJSON && xhr.responseJSON.msg) {
          errorMsg = xhr.responseJSON.msg;
        }
        popUpMessage("center", errorMsg, "error");
      },
    });
  });

  $("#checkInMembership").on("click", function (e) {
    e.preventDefault();

    // Get values
    var visits = $("#visits").val().trim();
    var mobile = $("#mobile").val().trim();

    // ===== VALIDATION =====
    if (visits === "") {
      popUpMessage("center", "Please enter customer's visits", "warning");
      return;
    }

    if (mobile === "" || !/^[0-9]{10}$/.test(mobile)) {
      popUpMessage(
        "center",
        "Please enter a valid 10-digit mobile number",
        "warning"
      );
      return;
    }

    $.ajax({
      url: "/api/membership/check-in/",
      type: "POST",
      data: {
        visits: visits,
        mobile: mobile,
      },
      dataType: "JSON",
      success: function (response) {
        if (response.status === "success") {
          popUpMessage("center", response.msg, "success");
        }
      },
      error: function (xhr) {
        var errorMsg = "An error occurred while saving membership.";
        if (xhr.responseJSON && xhr.responseJSON.msg) {
          errorMsg = xhr.responseJSON.msg;
        }
        popUpMessage("center", errorMsg, "error");
      },
    });
  });
});
