$(document).ready(function () {
  $("#addMembership").on("click", function (e) {
    e.preventDefault();
    $("#addMembership").hide();
    // Get values
    var name = $("#name").val().trim();
    var warehouseId = $("#warehouseId").val().trim();
    var mobile = $("#mobile").val().trim();
    var packageType = $("#package").val();
    var price = $("#price").val().trim();
    var dueAmount = $("#dueAmount").val().trim();
    var visit = $("#visit").val().trim();
    var requirements = $("#requirements").val().trim();

    // ===== VALIDATION =====
    if (name === "") {
      popUpMessage("center", "Please enter customer name", "warning");
      $("#addMembership").show();
      return;
    }

    if (mobile === "" || !/^[0-9]{10}$/.test(mobile)) {
      popUpMessage(
        "center",
        "Please enter a valid 10-digit mobile number",
        "warning"
      );
      $("#addMembership").show();
      return;
    }

    if (packageType === "") {
      popUpMessage("center", "Please select a room type", "warning");
      $("#addMembership").show();
      return;
    }

    if (price === "" || parseFloat(price) <= 0) {
      popUpMessage(
        "center",
        "Please enter a valid price greater than 0",
        "warning"
      );
      $("#addMembership").show();
      return;
    }

    if (dueAmount === "" || parseFloat(dueAmount) < 0) {
      popUpMessage("center", "Due amount cannot be negative", "warning");
      $("#addMembership").show();
      return;
    }

    if (parseFloat(dueAmount) > parseFloat(price)) {
      popUpMessage(
        "center",
        "Due amount cannot exceed the total price",
        "warning"
      );
      $("#addMembership").show();
      return;
    }

    if (visit === "" || parseInt(visit) < 1) {
      popUpMessage("center", "Total visit must be at least 1", "warning");
      $("#addMembership").show();
      return;
    }

    $.ajax({
      url: "/api/membership/add/",
      type: "POST",
      data: {
        warehouseId: warehouseId,
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
          window.location.href = "/dashboard/membership/list/";
        } else {
          popUpMessage(
            "center",
            response.msg || "Something went wrong",
            "error"
          );
          $("#addMembership").show();
        }
      },
      error: function (xhr) {
        $("#addMembership").show();
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

  $(".addNewRoomType").click(function (e) {
    e.preventDefault();
    $(".popUp").hide();
    document.querySelector(".roomTypeBox").style.display = "flex";
    $(".roomTypeBox").show();
  });

  $(".editRoomType").click(function (e) {
    e.preventDefault();
    $(".popUp").hide();
    updateRoomTypeId = $(this).attr("id");
    roomTypeName = $(this).attr("data_name");
    roomTypeDescription = $(this).attr("data_desc");
    $("#updateRoomType").show();
    $("#editName").val(roomTypeName);
    $("#editDescription").val(roomTypeDescription);
    document.querySelector(".editRoomTypeBox").style.display = "flex";
    $(".editRoomTypeBox").show();
  });

  $("#addRoomType").on("click", function (e) {
    e.preventDefault();
    var name = $("#name").val();
    if (name != "") {
      var description = $("#description").val();
      $.ajax({
        url: "/api/room_type/publish/",
        type: "POST",
        data: {
          name,
          description,
        },
        dataType: "JSON",
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
          $("#updateAmenities").show();
        },
      });
    } else {
      popUpMessage("center", "Kindly enter name of room type", "warning");
    }
  });

  $("#updateRoomType").on("click", function (e) {
    e.preventDefault();
    var name = $("#editName").val();
    if (name != "") {
      var description = $("#editDescription").val();
      $.ajax({
        url: "/api/room_type/update/",
        type: "POST",
        data: {
          id: updateRoomTypeId,
          name,
          description,
        },
        dataType: "JSON",
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
          $("#updateAmenities").show();
        },
      });
    } else {
      popUpMessage("center", "Kindly enter name of room type", "warning");
    }
  });

  // Add new warehouse

  $("#addWarehouse").click(function (e) {
    e.preventDefault();
    var name = $("#name").val();
    var address = $("#address").val();
    var state = $("#state").val();
    var city = $("#city").val();
    var pinCode = $("#pinCode").val();
    var email = $("#email").val();
    var mobile = $("#mobile").val();
    var companyId = $("#companyId").val();

    if (state == "select") {
      state = "";
    }

    if (city == "select") {
      city = "";
    }

    if (companyId == "select") {
      companyId = "";
    }

    submitRequest = false;
    msg = "Required input is missing";

    if (name != "") {
      if (address != "") {
        if (state != "") {
          if (city != "") {
            if (pinCode != "") {
              if (email != "") {
                if (mobile != "") {
                  if (companyId != "") {
                    submitRequest = true;
                  } else {
                    msg = "Kindly select company";
                  }
                } else {
                  msg = "Kindly enter branch mobile";
                }
              } else {
                msg = "Kindly enter branch email";
              }
            } else {
              msg = "Kindly enter branch address pin code";
            }
          } else {
            msg = "Kindly enter branch address city";
          }
        } else {
          msg = "Kindly select state of branch address";
        }
      } else {
        msg = "Kindly enter branch address";
      }
    } else {
      msg = "Kindly enter branch name";
    }

    if (!submitRequest) {
      popUpMessage("center", msg, "warning");
    } else {
      var formData = $("#addNewWarehouseForm").serialize();

      $.ajax({
        type: "POST",
        url: "/api/warehouse/create/",
        dataType: "JSON",
        data: formData,
        success: function (successResponse) {
          popUpMessage("center", successResponse.msg, successResponse.status);
          if (successResponse.status == "success") {
            setTimeout(function () {
              const warehouseId = btoa(successResponse.warehouseId);
              window.location.href = `/dashboard/warehouse/update/?id=${warehouseId}`;
            }, 1500);
          }
        },
        error: function (errorResponse) {
          console.log(`Error:`, errorResponse);
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
        },
      });
    }
  });

  // Update warehouse

  $("#updateWarehouse").click(function (e) {
    e.preventDefault();
    var name = $("#name").val();
    var address = $("#address").val();
    var state = $("#state").val();
    var city = $("#city").val();
    var pinCode = $("#pinCode").val();
    var email = $("#email").val();
    var mobile = $("#mobile").val();

    if (state == "select") {
      state = "";
    }

    if (city == "select") {
      city = "";
    }

    if (companyId == "select") {
      companyId = "";
    }

    submitRequest = false;
    msg = "Required input is missing";

    if (name != "") {
      if (address != "") {
        if (state != "") {
          if (city != "") {
            if (pinCode != "") {
              if (email != "") {
                if (mobile != "") {
                  if (companyId != "") {
                    submitRequest = true;
                  } else {
                    msg = "Kindly select company";
                  }
                } else {
                  msg = "Kindly enter branch mobile";
                }
              } else {
                msg = "Kindly enter branch email";
              }
            } else {
              msg = "Kindly enter branch address pin code";
            }
          } else {
            msg = "Kindly enter branch address city";
          }
        } else {
          msg = "Kindly select state of branch address";
        }
      } else {
        msg = "Kindly enter branch address";
      }
    } else {
      msg = "Kindly enter branch name";
    }

    if (!submitRequest) {
      popUpMessage("center", msg, "warning");
    } else {
      var formData = $("#updateWarehouseForm").serialize();
      $.ajax({
        type: "POST",
        url: "/api/warehouse/update/",
        dataType: "JSON",
        data: formData,
        success: function (successResponse) {
          popUpMessage("center", successResponse.msg, successResponse.status);
          if (successResponse.status == "success") {
            setTimeout(function () {
              window.location.href = `/dashboard/warehouse/list/`;
            }, 1500);
          }
        },
        error: function (errorResponse) {
          console.log(`Error:`, errorResponse);
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            errorResponse.responseJSON.status
          );
        },
      });
    }
  });

  // User

  var companyDropDown = document.getElementById("companyDropDown");

  if (companyDropDown) {
    if ($("#companyDropDown")) {
      $("#company").on("change", function (e) {
        e.preventDefault();
        var companyId = $("#company").val();
        $.ajax({
          type: "GET",
          url: "/api/warehouse/list/?company_id=" + companyId,
          dataType: "json",
          success: function (response) {
            $("#warehouseBox").show();
            $("#warehouse").empty();
            $("#warehouse").append(new Option("Select warehouse", ""));
            response.warehouseData.forEach(function (warehouse) {
              $("#warehouse").append(new Option(warehouse.name, warehouse.id));
            });
          },
          error: function (response) {
            console.log(`Error : `, response);
          },
        });
      });
    }
  }
});
