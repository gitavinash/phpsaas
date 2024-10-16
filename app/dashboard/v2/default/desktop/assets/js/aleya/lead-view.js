// Show itinerary

$("#fetchItinerary").click(function (e) {
  e.preventDefault();
  $("#fetchItinerary").hide();
  $("#itineraryListBox").hide();
  $("#editorBox").hide();

  var destination = $("#itineraryDestination").val();
  var theme = $("#itineraryStyle").val();

  if (destination != null) {
    $.ajax({
      type: "GET",
      url: `/api/itinerary/list/`,
      dataType: "json",
      data: {
        destination,
        theme,
      },
      success: function (response) {
        $("#fetchItinerary").show();
        localStorage.removeItem("itineraryList");
        localStorage.setItem(
          "itineraryList",
          JSON.stringify(response.itineraryList)
        );
        $("#itineraryList").empty();
        var itineraryResponse = response.itineraryList;
        var itineraryList = `<option value=''>Select</option>`;
        itineraryResponse.forEach((list) => {
          itineraryList += `<option value='${list.tour_package_id}'>${list.tour_package_title}</option>`;
        });
        $("#itineraryList").append(itineraryList);
        $("#itineraryListBox").show();
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
    popUpMessage("center", "Select itinerary destination", "warning");
  }
});

// Show itinerary

// $("#showItinerary").click(function (e) {
//   e.preventDefault();
//   $("#itineraryListBox").hide();

//   var itineraryList = $("#itineraryList").val();
//   var itineraryData = JSON.parse(localStorage.getItem("itineraryList"));
//   var matchedItinerary = itineraryData.find(
//     (item) => item.tour_package_id == itineraryList
//   );

//   if (matchedItinerary) {
//     var dayWiseItinerary = JSON.parse(matchedItinerary.tour_package_itinerary);

//     var dayWiseItineraryContent = "";
//     dayWiseItinerary.forEach((list) => {
//       dayWiseItineraryContent += `
//         <h3>${list.day}</h3>
//         <p>${list.overview}</p>
//       `;
//     });

//     var itineraryHtmlBody = `
//       <h1>${matchedItinerary.tour_package_title}</h1>
//       <table>
//         <tr>
//           <td>Destination</td>
//           <td></td>
//         </tr>
//         <tr>
//           <td>Theme</td>
//           <td>${matchedItinerary.tour_package_theme}</td>
//         </tr>
//         <tr>
//           <td>Stay option</td>
//           <td>${matchedItinerary.tour_package_hotel}</td>
//         </tr>
//         <tr>
//           <td>Pickup point</td>
//           <td>${matchedItinerary.tour_package_start_from}</td>
//         </tr>
//         <tr>
//           <td>Drop point</td>
//           <td>${matchedItinerary.tour_package_end_to}</td>
//         </tr>
//         <tr>
//           <td>Duration</td>
//           <td>${matchedItinerary.tour_package_duration} Nights / ${
//       matchedItinerary.tour_package_duration + 1
//     } Days</td>
//         </tr>
//         <tr>
//           <td>Prepared by</td>
//           <td></td>
//         </tr>
//       </table>
//       <h2>Overview</h2>
//       <p>${matchedItinerary.tour_package_overview}</p>
//       <h2>Day wise itinerary</h2>
//       <div>${dayWiseItineraryContent}</div>
//       <h2>Inclusions</h2>
//       <p>${matchedItinerary.tour_package_inclusions}</p>
//       <h2>Exclusions</h2>
//       <p>${matchedItinerary.tour_package_exclusions}</p>
//     `;

//     var quill = new Quill(".itineraryEditor", {
//       theme: "snow", // You can choose other themes like 'bubble'
//     });

//     var delta = quill.clipboard.convert(itineraryHtmlBody);
//     quill.setContents(delta);

//     $("#editorBox").show();
//   } else {
//     popUpMessage(
//       "center",
//       "Something went wrong while fetching local Itineary details",
//       "error"
//     );
//   }
// });

$("#showItinerary").click(function (e) {
  e.preventDefault();
  $("#itineraryListBox").hide();

  var itineraryList = $("#itineraryList").val();
  var itineraryData = JSON.parse(localStorage.getItem("itineraryList"));
  var matchedItinerary = itineraryData.find(
    (item) => item.tour_package_id == itineraryList
  );

  if (matchedItinerary) {
    var dayWiseItinerary = JSON.parse(matchedItinerary.tour_package_itinerary);

    var dayWiseItineraryContent = "";
    dayWiseItinerary.forEach((list) => {
      dayWiseItineraryContent += `
        <h3>${list.day}</h3>
        <p>${list.overview}</p>
      `;
    });

    var itineraryHtmlBody = `
      <h1>${matchedItinerary.tour_package_title}</h1>
      <table>
        <tr>
          <td>Destination</td>
          <td></td>
        </tr>
        <tr>
          <td>Theme</td>
          <td>${matchedItinerary.tour_package_theme}</td>
        </tr>
        <tr>
          <td>Stay option</td>
          <td>${matchedItinerary.tour_package_hotel}</td>
        </tr>
        <tr>
          <td>Pickup point</td>
          <td>${matchedItinerary.tour_package_start_from}</td>
        </tr>
        <tr>
          <td>Drop point</td>
          <td>${matchedItinerary.tour_package_end_to}</td>
        </tr>
        <tr>
          <td>Duration</td>
          <td>${matchedItinerary.tour_package_duration} Nights / ${
      matchedItinerary.tour_package_duration + 1
    } Days</td>
        </tr>
        <tr>
          <td>Prepared by</td>
          <td></td>
        </tr>
      </table>
      <h2>Overview</h2>
      <p>${matchedItinerary.tour_package_overview}</p>
      <h2>Day wise itinerary</h2>
      <div>${dayWiseItineraryContent}</div>
      <h2>Inclusions</h2>
      <p>${matchedItinerary.tour_package_inclusions}</p>
      <h2>Exclusions</h2>
      <p>${matchedItinerary.tour_package_exclusions}</p>
    `;

    // Ensure the editor is properly initialized
    if (!window.quill) {
      window.quill = new Quill(".itineraryEditor", {
        theme: "snow", // You can choose other themes like 'bubble'
      });
    }

    // Convert HTML to Quill Delta and set the content
    var delta = window.quill.clipboard.convert(itineraryHtmlBody);
    window.quill.setContents(delta);

    $("#editorBox").show();
  } else {
    popUpMessage(
      "center",
      "Something went wrong while fetching local Itinerary details",
      "error"
    );
  }
});

// Send itinerary

$("#sendItinerary").click(function (e) {
  e.preventDefault();
});
