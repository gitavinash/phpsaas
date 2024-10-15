// Show itinerary

$("#fetchItinerary").click(function (e) {
  e.preventDefault();
  var destination = $("#itineraryDestination").val();
  var theme = $("#itineraryStyle").val();

  console.log(`destination:`, destination);
  console.log(`theme:`, theme);

  if (destination != null) {
    if (theme != null) {
      $.ajax({
        type: "GET",
        url: `/api/packages/itinerary/`,
        dataType: "json",
        data: {
          destination,
          theme,
        },
        success: function (response) {
          if (response.status == "success") {
            window.location.href =
              `/generate-agreement/?request_id=` +
              btoa(response.enquiryId) +
              "&state=" +
              state.toLowerCase();
          } else {
            popUpMessage("center", response.msg, response.status);
          }
          $("#fetchItinerary").show();
        },
        error: function (response) {
          console.log(`Error : `, response);
          popUpMessage(
            "center",
            response.responseJSON.msg,
            response.responseJSON.status
          );
          $("#fetchItinerary").show();
        },
      });
    } else {
      popUpMessage("center", "Select itinerary theme", "warning");
    }
  } else {
    popUpMessage("center", "Select itinerary destination", "warning");
  }
});

// Send itinerary

$("#sendItinerary").click(function (e) {
  e.preventDefault();
});
