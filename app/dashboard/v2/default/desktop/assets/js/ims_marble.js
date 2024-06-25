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

// Add new supplier

$("#addSupplier").click(function (e) {
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

  submitRequest = false;
  msg = "Required input is missing";

  if (name != "") {
    if (address != "") {
      if (state != "") {
        if (city != "") {
          if (pinCode != "") {
            if (email != "") {
              if (mobile != "") {
                submitRequest = true;
              } else {
                msg = "Kindly enter supplier mobile";
              }
            } else {
              msg = "Kindly enter supplier email";
            }
          } else {
            msg = "Kindly enter supplier address pin code";
          }
        } else {
          msg = "Kindly enter supplier address city";
        }
      } else {
        msg = "Kindly select state of supplier address";
      }
    } else {
      msg = "Kindly enter supplier address";
    }
  } else {
    msg = "Kindly enter supplier name";
  }

  if (!submitRequest) {
    popUpMessage("center", msg, "warning");
  } else {
    var formData = $("#addNewSupplierForm").serialize();

    $.ajax({
      type: "POST",
      url: "/api/supplier/create/",
      dataType: "JSON",
      data: formData,
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            const supplierId = btoa(successResponse.supplierId);
            window.location.href = `/dashboard/supplier/update/?id=${supplierId}`;
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

// Update supplier

$("#updateSupplier").click(function (e) {
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

  submitRequest = false;
  msg = "Required input is missing";

  if (name != "") {
    if (address != "") {
      if (state != "") {
        if (city != "") {
          if (pinCode != "") {
            if (email != "") {
              if (mobile != "") {
                submitRequest = true;
              } else {
                msg = "Kindly enter supplier mobile";
              }
            } else {
              msg = "Kindly enter supplier email";
            }
          } else {
            msg = "Kindly enter supplier address pin code";
          }
        } else {
          msg = "Kindly enter supplier address city";
        }
      } else {
        msg = "Kindly select state of supplier address";
      }
    } else {
      msg = "Kindly enter supplier address";
    }
  } else {
    msg = "Kindly enter supplier name";
  }

  if (!submitRequest) {
    popUpMessage("center", msg, "warning");
  } else {
    var formData = $("#updateSupplierForm").serialize();
    $.ajax({
      type: "POST",
      url: "/api/supplier/update/",
      dataType: "JSON",
      data: formData,
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
        if (successResponse.status == "success") {
          setTimeout(function () {
            window.location.href = `/dashboard/supplier/list/`;
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
