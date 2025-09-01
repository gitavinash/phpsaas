// Show itinerary

$("#fetchItinerary").click(function (e) {
  e.preventDefault();
  $("#loader").show();
  $("#fetchItinerary").hide();
  $("#itineraryListBox").hide();
  $("#editorBox").hide();
  $("#sendItinerary").hide();
  $("#downloadItinerary").hide();
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
        $("#loader").hide();
        localStorage.removeItem("itineraryList");
        localStorage.setItem(
          "itineraryList",
          JSON.stringify(response.itineraryList)
        );
        $("#itineraryList").empty();
        var itineraryResponse = response.itineraryList;
        var itineraryList = `<option value=''>Select</option>`;
        itineraryResponse.forEach((list) => {
          itineraryList += `<option value='${list.tour_package_id}_${list.tour_package_price_mrp_adult}'>${list.tour_package_title} ( at ₹ ${list.tour_package_price_selling_adult} per person )</option>`;
        });
        $("#itineraryList").append(itineraryList);
        $("#itineraryListBox").show();
      },
      error: function (response) {
        popUpMessage(
          "center",
          response.responseJSON.msg,
          response.responseJSON.status
        );
        $("#fetchItinerary").show();
        $("#loader").hide();
      },
    });
  } else {
    popUpMessage("center", "Select itinerary destination", "warning");
    $("#fetchItinerary").show();
  }
});

$("#showItinerary").click(function (e) {
  e.preventDefault();
  $("#itineraryListBox").hide();

  var itineraryList = $("#itineraryList").val();
  var itineraryDetails = itineraryList.split("_");

  $("#itinenaryId").val(itineraryDetails[0]);
  $("#itinenaryMrp").val(itineraryDetails[1]);

  var itinenaryPrice = itineraryDetails[0];

  var itineraryData = JSON.parse(localStorage.getItem("itineraryList"));
  var matchedItinerary = itineraryData.find(
    (item) => item.tour_package_id == itinenaryPrice
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

    var tripDurationDays = parseInt(matchedItinerary.tour_package_duration) + 1;

    var tripDuration =
      matchedItinerary.tour_package_duration +
      " Nights / " +
      tripDurationDays +
      " Days";

    var itineraryHtmlBody =
      `
      <h1>${matchedItinerary.tour_package_title}</h1>
      <table>
        <tr>
          <td>Destination</td>
          <td>${matchedItinerary.tour_package_city}</td>
        </tr>
        <tr>
          <td>Theme</td>
          <td>` +
      capitalizeWords(matchedItinerary.tour_package_theme) +
      `</td>
        </tr>
        <tr>
          <td>Stay option</td>
          <td>` +
      matchedItinerary.tour_package_hotel.replace(/_/g, " ") +
      `</td>
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
          <td>${tripDuration}</td>
        </tr>
        <tr>
          <td>Price</td>
          <td>₹ ${matchedItinerary.tour_package_price_selling_adult} per person</td>
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

    $("#itineraryEditorBox").html(itineraryHtmlBody);

    tinymce.init({
      selector: "#itineraryEditorBox",
      license_key: "gpl",
      menubar: true,
    });
    $("#editorBox").show();

    // Show button

    $("#sendItinerary").show();
    $("#downloadItinerary").show();
  } else {
    popUpMessage(
      "center",
      "Something went wrong while fetching local Itinerary details",
      "error"
    );
  }
});

function parseItineraryHtml(html) {
  const container = document.createElement("div");
  container.innerHTML = html
    .replace(/\+/g, " ")
    .replace(/&nbsp;/g, " ")
    .trim();

  const json = {};

  const h1 = container.querySelector("h1");
  json.title = h1 ? h1.textContent.trim() : "";

  const rows = container.querySelectorAll("table tr");
  rows.forEach(function (row) {
    const cells = row.querySelectorAll("td");
    if (cells.length === 2) {
      const key = cells[0].textContent
        .trim()
        .toLowerCase()
        .replace(/ /g, "_")
        .replace(/[^a-z_]/g, "");
      const value = cells[1].textContent.trim();
      json[key] = value;
    }
  });

  var h2s = container.querySelectorAll("h2");
  var overview = "",
    inclusions = "",
    exclusions = "",
    itinerary = [];

  for (var i = 0; i < h2s.length; i++) {
    var text = h2s[i].textContent.toLowerCase();
    if (text.includes("overview") && h2s[i].nextElementSibling) {
      overview = h2s[i].nextElementSibling.textContent.trim();
    } else if (text.includes("inclusions") && h2s[i].nextElementSibling) {
      inclusions = h2s[i].nextElementSibling.textContent.trim();
    } else if (text.includes("exclusions") && h2s[i].nextElementSibling) {
      exclusions = h2s[i].nextElementSibling.textContent.trim();
    } else if (text.includes("day wise itinerary")) {
      const div = h2s[i].nextElementSibling;
      if (div) {
        const daySections = div.querySelectorAll("h3");
        for (var j = 0; j < daySections.length; j++) {
          var overviewText = "";
          var next = daySections[j].nextElementSibling;
          if (next && next.tagName.toLowerCase() === "p") {
            overviewText = next.textContent.trim();
          }
          itinerary.push({
            day: daySections[j].textContent.trim(),
            overview: overviewText,
          });
        }
      }
    }
  }

  json.overview = overview;
  json.itinerary = itinerary;
  json.inclusions = inclusions;
  json.exclusions = exclusions;

  return json;
}

function sendItineraryToCustomer(isDownload = false) {
  $("#sendItinerary").hide();
  $("#downloadItinerary").hide();
  const itinenaryId = $("#itinenaryId").val();
  const itinenaryType = $("#itinenaryType").val();
  const itinenaryMrp = $("#itinenaryMrp").val();
  const itinenaryRefId = $("#itinenaryRefId").val();
  const itinenaryRefType = $("#itinenaryRefType").val();
  const editorContent = tinymce.get("itineraryEditorBox").getContent();
  const intinenaryObject = parseItineraryHtml(editorContent);
  const payload = {
    id: itinenaryId,
    type: itinenaryType,
    mrp: itinenaryMrp,
    refId: itinenaryRefId,
    refType: itinenaryRefType,
    content: intinenaryObject,
  };

  $.ajax({
    url: "/api/itinerary/publish/",
    method: "POST",
    contentType: "application/json",
    processData: false,
    data: JSON.stringify(payload),
    success: function (response) {
      // Show appropriate success message
      const message = isDownload
        ? "Itinerary download started successfully!"
        : "Itinerary sent successfully!";
      // Trigger download in a new tab after short delay
      if (isDownload && response.itineraryId) {
        const encodedId = btoa(response.itineraryId);
        const downloadUrl = `/download-itinerary/${encodedId}/`;
        const newWindow = window.open("about:blank", "_blank");

        if (newWindow) {
          // Show a loading message
          newWindow.document.write(
            "<p style='font-family: sans-serif;'>Preparing your itinerary PDF...</p>"
          );
          newWindow.document.close(); // Required for some browsers

          setTimeout(() => {
            newWindow.location.href = downloadUrl;
          }, 300); // Optional delay for smoother feel
        } else {
          alert("Popup blocked! Please allow popups for this site.");
        }
      } else {
        // If not downloading, just log the itinerary ID
        console.log("Itinerary ID:", response);
        popUpMessage("center", message, "success");
      }

      // Ensure buttons are visible
      $("#sendItinerary, #downloadItinerary").show();
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
      popUpMessage("center", "Failed to send itinerary!", "error");
      $("#sendItinerary").show();
      $("#downloadItinerary").show();
    },
  });
}

// Send itinerary

$("#sendItinerary").click(function (e) {
  e.preventDefault();
  const itinenaryId = sendItineraryToCustomer(false);
});

// Download PDF itinerary

$("#downloadItinerary").click(function (e) {
  e.preventDefault();
  const itinenaryId = sendItineraryToCustomer(true);
});
