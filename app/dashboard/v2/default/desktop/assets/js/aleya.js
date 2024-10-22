$(document).ready(function () {
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
          $("#updateLocations").show();
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
          $("#updateLocations").show();
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
    console.log(`addProperty button clicked`);
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
    console.log(`addNewVehicle clicked`);
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
    vehicleSeating = $(this).attr("data_seating");
    vehicleImage = $(this).attr("data_image");
    vehicleType = $(this).attr("data_type");
    console.log(`vehicleSeating:`, vehicleSeating);

    $("#updateVehicle").show();
    $("#editName").val(vehicleName);
    $("#editDescription").val(vehicleDescription);
    $("#vehicleIcon").attr("src", vehicleImage);
    $("#vehicleSeating").val(vehicleSeating);
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
            popUpMessage(
              "center",
              "Enter transporter city pin code",
              "warning"
            );
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

  $(document).ready(function (e) {
    // Amenities

    $(".addNewAmenities").click(function (e) {
      e.preventDefault();
      $(".popUp").hide();
      document.querySelector(".amenitiesBox").style.display = "flex";
      $(".amenitiesBox").show();
      $("#type").val("basic_facilities").change();
    });

    $(".editAmenities").click(function (e) {
      e.preventDefault();
      $(".popUp").hide();
      updateAmenitiesId = $(this).attr("id");
      amenitiesName = $(this).attr("data_name");
      amenitiesDescription = $(this).attr("data_desc");
      amenitiesIcon = $(this).attr("data_image");
      amenitiesType = $(this).attr("data_type");
      $("#updateAmenities").show();
      $("#editName").val(amenitiesName);
      $("#editDescription").val(amenitiesDescription);
      if (amenitiesType != "") {
        $("#editType").val(amenitiesType).change();
      } else {
        $("#editType").val("basic_facilities").change();
      }
      $("#amenitiesIcon").attr("src", amenitiesIcon);
      document.querySelector(".editAmenitiesBox").style.display = "flex";
      $(".editAmenitiesBox").show();
    });

    // Amenities
    $("#addAmenities").on("click", function (e) {
      e.preventDefault();
      $("#addAmenities").hide();
      var title = $("#name").val();
      if (title != "") {
        var data = new FormData(document.getElementById("addAmenitiesForm"));

        $.ajax({
          url: "/api/amenities/publish/",
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
      } else {
        popUpMessage("center", "Kindly enter amenities name", "warning");
        $("#addAmenities").show();
      }
    });

    $("#updateAmenities").on("click", function (e) {
      e.preventDefault();

      $("#updateAmenities").hide();
      var title = $("#editName").val();
      if (title != "") {
        var data = new FormData(document.getElementById("updateAmenitiesForm"));
        data.append("id", updateAmenitiesId);

        $.ajax({
          url: "/api/amenities/update/",
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
            $("#updateAmenities").show();
          },
        });
      } else {
        popUpMessage("center", "Kindly enter amenities name", "warning");
        $("#updateAmenities").show();
      }
    });

    // Package locations

    $(".addNewLocations").click(function (e) {
      e.preventDefault();
      $(".popUp").hide();
      document.querySelector(".locationsBox").style.display = "flex";
      $(".locationsBox").show();
      $("#type").val("basic_facilities").change();
    });

    $(".editLocations").click(function (e) {
      e.preventDefault();
      $(".popUp").hide();
      updateLocationsId = $(this).attr("id");
      locationsName = $(this).attr("data_name");
      locationsDescription = $(this).attr("data_desc");
      locationsIcon = $(this).attr("data_image");
      locationsType = $(this).attr("data_type");
      $("#updateLocations").show();
      $("#editName").val(locationsName);
      $("#editDescription").val(locationsDescription);
      if (locationsType != "") {
        $("#editType").val(locationsType).change();
      } else {
        $("#editType").val("basic_facilities").change();
      }
      $("#locationsIcon").attr("src", locationsIcon);
      document.querySelector(".editLocationsBox").style.display = "flex";
      $(".editLocationsBox").show();
    });

    // Locations
    $("#addLocations").on("click", function (e) {
      e.preventDefault();
      $("#addLocations").hide();
      var title = $("#name").val();
      if (title != "") {
        var data = new FormData(document.getElementById("addLocationsForm"));

        $.ajax({
          url: "/api/locations/publish/",
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
            $("#addLocations").show();
          },
        });
      } else {
        popUpMessage("center", "Kindly enter locations name", "warning");
        $("#addLocations").show();
      }
    });

    $("#updateLocations").on("click", function (e) {
      e.preventDefault();

      $("#updateLocations").hide();
      var title = $("#editName").val();
      if (title != "") {
        var data = new FormData(document.getElementById("updateLocationsForm"));
        data.append("id", updateLocationsId);

        $.ajax({
          url: "/api/locations/update/",
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
            $("#updateLocations").show();
          },
        });
      } else {
        popUpMessage("center", "Kindly enter locations name", "warning");
        $("#updateLocations").show();
      }
    });

    // Clone add  new destination location

    $("#locationDurationContainer").on("click", ".addLocation", function () {
      // e.preventDefault();
      const $clone = $(".locationDurationGroup:first").clone();

      const newIndex =
        $("#locationDurationContainer .locationDurationGroup").length + 1;
      $clone
        .find(".indianCityAutoComplete")
        .attr("id", "destination-" + newIndex)
        .val("");

      $clone.find(".select2-container").remove();

      $clone.find("input").val("");

      $("#locationDurationContainer").append($clone);

      if ($(".locationDurationGroup").length > 1) {
        $(".removeLocation").show();
      }

      initializeSelect2($clone.find(".indianCityAutoComplete"));

      $clone.find(".indianCityAutoComplete").show();
    });

    $("#locationDurationContainer").on("click", ".removeLocation", function () {
      $(this).closest(".locationDurationGroup").remove();

      if ($(".locationDurationGroup").length === 1) {
        $(".removeLocation").hide();
      }
    });

    if ($(".locationDurationGroup").length === 1) {
      $(".removeLocation").hide();
    }

    // Vehicle selection in package

    $('select[name="outstationVehicle"]').on("change", function () {
      var selectedValue = $(this).val();
      if (selectedValue.toLowerCase() !== "volvo") {
        $('select[name="localSighseeignVehicle"]')
          .val(selectedValue)
          .trigger("change");
      }
    });

    // Generate domestic tour package

    $("#addDomesticPackageBtn").click(function (e) {
      e.preventDefault();
      console.log(`addDomesticPackageBtn is clicked:`, addDomesticPackageBtn);
      $("#addDomesticPackageBtn").hide();

      // Collect and trim all the form values, ensuring they are not undefined
      var packageName = $("#name").val() ? $("#name").val().trim() : "";
      var startingCity = $("#startingCity").val()
        ? $("#startingCity").val().trim()
        : "";
      var endCity = $("#endCity").val() ? $("#endCity").val().trim() : "";
      var travelStyle = $("#travelStyle").val()
        ? $("#travelStyle").val().trim()
        : "";
      var outstationVehicle = $("#outstationVehicle").val()
        ? $("#outstationVehicle").val().trim()
        : "";
      var localSighseeignVehicle = $("#localSighseeignVehicle").val()
        ? $("#localSighseeignVehicle").val().trim()
        : "";

      // Check if any of the required fields are empty
      if (
        !packageName ||
        !startingCity ||
        !travelStyle ||
        !outstationVehicle ||
        !localSighseeignVehicle
      ) {
        $("#addDomesticPackageBtn").show();
        popUpMessage(
          "center",
          "All fields are required to generate itinerary",
          "warning"
        );
        return;
      }

      // Collect location and duration data
      const locationsData = [];
      let allLocationsFilled = true;

      $(".locationDurationGroup").each(function () {
        const destination = $(this).find(".indianCityAutoComplete").val()
          ? $(this).find(".indianCityAutoComplete").val().trim()
          : "";
        const duration = $(this).find("input[name='duration[]']").val()
          ? $(this).find("input[name='duration[]']").val().trim()
          : "";

        // Check if both destination and duration are filled
        if (!destination || !duration) {
          allLocationsFilled = false;
        } else {
          locationsData.push({
            destination: destination,
            duration: duration,
          });
        }
      });

      // Check if all locations are filled
      if (!allLocationsFilled) {
        $("#addDomesticPackageBtn").show();
        popUpMessage(
          "center",
          "Please fill out all destination and duration fields",
          "warning"
        );
        return;
      }

      // Encode URL parameters
      packageName = encodeURIComponent(packageName);
      startingCity = encodeURIComponent(startingCity);
      endCity = encodeURIComponent(endCity);
      travelStyle = encodeURIComponent(travelStyle);
      outstationVehicle = encodeURIComponent(outstationVehicle);
      localSighseeignVehicle = encodeURIComponent(localSighseeignVehicle);

      var destinationCity = [];
      var destinationDuration = [];

      // Prepare parameters for each location
      let locationParams = locationsData.map((data, index) => {
        destinationCity.push(data.destination);
        destinationDuration.push(data.duration);
      });

      destinationCity = destinationCity.join("_");
      destinationDuration = destinationDuration.join("_");

      const redirectUrl = `/dashboard/packages/domestic/preview/?packageName=${packageName}&destinationCity=${destinationCity}&tripDuration=${destinationDuration}&startingCity=${startingCity}&endCity=${endCity}&outstationVehicle=${outstationVehicle}&localSighseeignVehicle=${localSighseeignVehicle}&travelStyle=${travelStyle}`;

      window.location.href = redirectUrl;
    });

    $("#createTourPackageBtn").click(function (e) {
      e.preventDefault();
      $("#createTourPackageBtn").hide();
      var packageType = $("#packageType").val();
      var formData = $("#createTourPackageForm").serialize();

      $.ajax({
        type: "POST",
        url: "/api/packages/publish/", // Replace with your server-side endpoint URL
        data: formData,
        dataType: "json",
        success: function (response) {
          if (response.status) {
            popUpMessage("center", response.msg, response.status);
            setTimeout(function () {
              window.location.href =
                "/dashboard/packages/" +
                packageType +
                "/price/?id=" +
                response.id +
                "request=new";
            }, 1500);
          } else {
            $("#createTourPackageBtn").show();
          }
        },
        error: function (errorMsg) {
          $("#createTourPackageBtn").show();
          console.log(`errorMsg`, errorMsg);
          popUpMessage("center", errorMsg.msg, errorMsg.status);
          console.log(`Error:`, error);
        },
      });
    });

    $("#updateTourPackageBtn").click(function (e) {
      e.preventDefault();
      $("#updateTourPackageBtn").hide();
      var packageType = $("#packageType").val();
      var formData = $("#createTourPackageForm").serialize();

      $.ajax({
        type: "POST",
        url: "/api/packages/update/", // Replace with your server-side endpoint URL
        data: formData,
        dataType: "json",
        success: function (response) {
          if (response.status) {
            popUpMessage("center", response.msg, response.status);
            setTimeout(function () {
              window.location.reload();
            }, 1500);
          } else {
            $("#updateTourPackageBtn").show();
          }
        },
        error: function (xhr, status, error) {
          popUpMessage("center", response.msg, response.status);
          console.log(`Error:`, error);
          $("#updateTourPackageBtn").show();
        },
      });
    });

    loadExternalJS(
      "/app/dashboard/v2/default/desktop/assets/js/aleya/package-price.min.js"
    );
    loadExternalJS(
      "/app/dashboard/v2/default/desktop/assets/js/aleya/lead-view.min.js"
    );
    loadExternalJS(
      "/app/dashboard/v2/default/desktop/assets/js/aleya/property.min.js"
    );
  });
});
