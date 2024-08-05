// #########################################################
// Room types
// #########################################################

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

// #########################################################
// Property
// #########################################################

$("#addProperty").click(function (e) {
  e.preventDefault();
  $("#addProperty").hide();
  const name = $("#name").val();
  const description = $("#description").val();
  const address = $("#address").val();
  const city = $("#city").val();
  const pinCode = $("#pinCode").val();
  const email = $("#email").val();
  const mobile = $("#mobile").val();
  const category = $("#type").val();
  const googleMapUrl = $("#mapUrl").val();

  if (name != "") {
    if (address != "") {
      if (city != "") {
        if (pinCode != "") {
          if (email != "") {
            if (mobile != "") {
              if (category != "") {
                var formData = new FormData(
                  document.getElementById("addPropertyForm")
                );
                $.ajax({
                  type: "POST",
                  url: "/api/property/publish/",
                  dataType: "json",
                  data: formData,
                  processData: false,
                  contentType: false,
                  encrypt: "multipart/form-data",
                  success: function (response) {
                    popUpMessage("center", response.msg, response.status);
                    setTimeout(function () {
                      window.location.href =
                        "/dashboard/vendors/property/update/?updateType=rooms&id=" +
                        btoa(response.propertyId);
                    }, 1500);
                    $("#addProperty").show();
                  },
                  error: function (response) {
                    console.log(`Error`, response);
                    popUpMessage(
                      "center",
                      response.responseJSON.msg,
                      response.responseJSON.status
                    );
                    $("#addProperty").show();
                  },
                });
              } else {
                popUpMessage(
                  "center",
                  "Please select property type",
                  "warning"
                );
                $("#addProperty").show();
              }
            } else {
              popUpMessage(
                "center",
                "Enter property contact number",
                "warning"
              );
              $("#addProperty").show();
            }
          } else {
            popUpMessage("center", "Enter property email address", "warning");
            $("#addProperty").show();
          }
        } else {
          popUpMessage("center", "Enter property city pin code", "warning");
          $("#addProperty").show();
        }
      } else {
        popUpMessage("center", "Enter property city name", "warning");
        $("#addProperty").show();
      }
    } else {
      popUpMessage("center", "Enter property address", "warning");
      $("#addProperty").show();
    }
  } else {
    popUpMessage("center", "Enter property name", "warning");
    $("#addProperty").show();
  }
});

$("#updateProperty").click(function (e) {
  e.preventDefault();
  $("#updateProperty").hide();
  const name = $("#name").val();
  const description = $("#description").val();
  const address = $("#address").val();
  const city = $("#city").val();
  const pinCode = $("#pinCode").val();
  const email = $("#email").val();
  const mobile = $("#mobile").val();
  const category = $("#type").val();
  const googleMapUrl = $("#mapUrl").val();

  if (name != "") {
    if (address != "") {
      if (city != "") {
        if (pinCode != "") {
          if (email != "") {
            if (mobile != "") {
              if (category != "") {
                var formData = new FormData(
                  document.getElementById("updatePropertyForm")
                );
                $.ajax({
                  type: "POST",
                  url: "/api/property/update/",
                  dataType: "json",
                  data: formData,
                  processData: false,
                  contentType: false,
                  encrypt: "multipart/form-data",
                  success: function (response) {
                    popUpMessage("center", response.msg, response.status);
                    setTimeout(function () {
                      window.location.href =
                        "/dashboard/vendors/property/update/?id=" +
                        btoa(response.propertyId);
                    }, 1500);
                    $("#updateProperty").show();
                  },
                  error: function (response) {
                    console.log(`Error`, response);
                    popUpMessage(
                      "center",
                      response.responseJSON.msg,
                      response.responseJSON.status
                    );
                    $("#updateProperty").show();
                  },
                });
              } else {
                popUpMessage(
                  "center",
                  "Please select property type",
                  "warning"
                );
                $("#updateProperty").show();
              }
            } else {
              popUpMessage(
                "center",
                "Enter property contact number",
                "warning"
              );
              $("#updateProperty").show();
            }
          } else {
            popUpMessage("center", "Enter property email address", "warning");
            $("#updateProperty").show();
          }
        } else {
          popUpMessage("center", "Enter property city pin code", "warning");
          $("#updateProperty").show();
        }
      } else {
        popUpMessage("center", "Enter property city name", "warning");
        $("#updateProperty").show();
      }
    } else {
      popUpMessage("center", "Enter property address", "warning");
      $("#updateProperty").show();
    }
  } else {
    popUpMessage("center", "Enter property name", "warning");
    $("#updateProperty").show();
  }
});

// #########################################################
// Vehicle
// #########################################################

$(".addNewVehicle").click(function (e) {
  e.preventDefault();
  $(".popUp").hide();
  document.querySelector(".vehicleBox").style.display = "flex";
  $(".vehicleBox").show();
});

$(".editVehicle").click(function (e) {
  e.preventDefault();
  $(".popUp").hide();
  updateVehicleId = $(this).attr("id");
  vehicleName = $(this).attr("data_name");
  vehicleDescription = $(this).attr("data_desc");
  vehicleImage = $(this).attr("data_image");
  vehicleType = $(this).attr("data_type");
  $("#updateVehicle").show();
  $("#editName").val(vehicleName);
  $("#editDescription").val(vehicleDescription);
  $("#vehicleIcon").attr("src", vehicleImage);
  if (vehicleType != "") {
    $("#editType").val(vehicleType).change();
  } else {
    $("#editType").val("bus").change();
  }
  document.querySelector(".editVehicleBox").style.display = "flex";
  $(".editVehicleBox").show();
});

$("#addVehicle").on("click", function (e) {
  e.preventDefault();
  $("#addVehicle").hide();
  var title = $("#name").val();
  if (title != "") {
    var data = new FormData(document.getElementById("addVehicleForm"));

    $.ajax({
      url: "/api/vehicle/publish/",
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
        $("#addVehicle").show();
      },
    });
  } else {
    popUpMessage("center", "Kindly enter vehicle name", "warning");
    $("#addVehicle").show();
  }
});

$("#updateVehicle").on("click", function (e) {
  e.preventDefault();

  $("#updateVehicle").hide();
  var title = $("#editName").val();
  if (title != "") {
    var data = new FormData(document.getElementById("updateVehicleForm"));
    data.append("id", updateVehicleId);

    $.ajax({
      url: "/api/vehicle/update/",
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
        $("#updateVehicle").show();
      },
    });
  } else {
    popUpMessage("center", "Kindly enter vehicle name", "warning");
    $("#updateVehicle").show();
  }
});

$("#addTransporter").click(function (e) {
  e.preventDefault();
  $("#addTransporter").hide();
  const name = $("#name").val();
  const description = $("#description").val();
  const address = $("#address").val();
  const city = $("#city").val();
  const pinCode = $("#pinCode").val();
  const email = $("#email").val();
  const mobile = $("#mobile").val();
  const serviceArea = $("#serviceArea").val();

  if (name != "") {
    if (address != "") {
      if (city != "") {
        if (pinCode != "") {
          if (email != "") {
            if (mobile != "") {
              if (serviceArea != "") {
                $.ajax({
                  type: "POST",
                  url: "/api/transporter/publish/",
                  dataType: "json",
                  data: {
                    name,
                    description,
                    address,
                    city,
                    pinCode,
                    email,
                    mobile,
                    serviceArea,
                  },
                  success: function (response) {
                    popUpMessage("center", response.msg, response.status);
                    setTimeout(function () {
                      window.location.href =
                        "/dashboard/vendors/transporter/update/?id" +
                        btoa(response.transporterId);
                    }, 1500);
                    $("#addTransporter").show();
                  },
                  error: function (response) {
                    popUpMessage(
                      "center",
                      response.responseJSON.msg,
                      response.responseJSON.status
                    );
                  },
                });
              } else {
                popUpMessage(
                  "center",
                  "Please enter transporter's service area",
                  "warning"
                );
              }
            } else {
              popUpMessage(
                "center",
                "Enter transporter contact number",
                "warning"
              );
            }
          } else {
            popUpMessage(
              "center",
              "Enter transporter email address",
              "warning"
            );
          }
        } else {
          popUpMessage("center", "Enter transporter city pin code", "warning");
        }
      } else {
        popUpMessage("center", "Enter transporter city name", "warning");
      }
    } else {
      popUpMessage("center", "Enter transporter address", "warning");
    }
  } else {
    popUpMessage("center", "Enter transporter name", "warning");
  }
  $("#addTransporter").show();
});

$("#openRoomPopUP").click(function (e) {
  e.preventDefault();
  $(".addNewRoomBox").show();
});

$("#addNewRoom").click(function (e) {
  e.preventDefault();
  $("#addNewRoom").hide();
  var fields = [
    { id: "#roomType", name: "room type" },
    { id: "#priceMRP", name: "EP MRP price" },
    { id: "#priceB2C", name: "EP B2C price" },
    { id: "#priceB2B", name: "EP B2B price" },
    { id: "#priceCP", name: "CP MRP price" },
    { id: "#priceCPB2C", name: "CP B2C price" },
    { id: "#priceCPB2B", name: "CP B2B price" },
    { id: "#priceMAP", name: "MAP MRP price" },
    { id: "#priceMAPB2C", name: "MAP B2C price" },
    { id: "#priceMAPB2B", name: "MAP B2B price" },
    { id: "#priceAP", name: "AP MRP price" },
    { id: "#priceAPB2C", name: "AP B2C price" },
    { id: "#priceAPB2B", name: "AP B2B price" },
  ];

  var submitForm = true;
  var msg = "Kindly enter all required fields";

  for (var i = 0; i < fields.length; i++) {
    if ($(fields[i].id).val() === "") {
      msg = "Kindly enter " + fields[i].name;
      submitForm = false;
      break;
    }
  }

  if (submitForm) {
    var formData = new FormData(document.getElementById("addNewRoomForm"));
    $.ajax({
      type: "POST",
      url: "/api/room/publish/",
      dataType: "json",
      data: formData,
      processData: false,
      contentType: false,
      encrypt: "multipart/form-data",
      success: function (response) {
        popUpMessage("center", response.msg, response.status);
        setTimeout(function () {
          window.location.href =
            "/dashboard/vendors/property/update/?updateType=rooms&id=" +
            response.propertyId;
        }, 1500);
        $("#addNewRoom").show();
      },
      error: function (response) {
        console.log(`Error`, response);
        $("#addNewRoom").show();
        popUpMessage(
          "center",
          response.responseJSON.msg,
          response.responseJSON.status
        );
      },
    });
  } else {
    popUpMessage("center", msg, "warning");
    $("#addNewRoom").show();
  }
});
