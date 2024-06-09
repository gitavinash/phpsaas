const urlQuery = new URLSearchParams(window.location.search);
let dateRangeParams = urlQuery.get("daterange");
let leadId = urlQuery.get("id");
dateRangeParams = dateRangeParams === "" ? "" : dateRangeParams;
if (dateRangeParams == null) {
  dateRangeValue = "";
} else {
  dateRangeValue = dateRangeParams;
}
if (typeof tempId !== "undefined") {
  // Update dateRangeParams to include userId=tempId if tempId is not empty
  dateRangeParams += "&userId=" + tempId;
}

function showPageLoader() {
  $(".pageLoader").fadeIn();
}

function hidePageLoader() {
  $(".pageLoader").fadeOut();
}

$(window).on("load", function () {
  hidePageLoader();
});

// function showNotification() {
//   if (!("Notification" in window)) {
//     alert("This browser does not support desktop notification");
//   } else if (Notification.permission === "granted") {
//     const notification = new Notification("Hi there!");
//   } else if (Notification.permission !== "denied") {
//     Notification.requestPermission().then((permission) => {
//       if (permission === "granted") {
//         const notification = new Notification("Hi there!");
//         // …
//       }
//     });
//   }
// }

// $(document).click(function () {
//   showPageLoader();
//   setTimeout(function () {
//     hidePageLoader();
//   }, 500);
// });

function isNumeric(input) {
  return /^\d+$/.test(input);
}

/** SweetAlert Pop Up Box */

function popUpMessage(msgPostion, msgTxt, msgType) {
  Swal.fire({
    position: msgPostion,
    type: msgType,
    title: msgTxt,
    showConfirmButton: false,
    timer: 1500,
  });
}

function createPieChart(objectID, chartData) {
  // Extract labels and counts
  const labels = chartData.labels;
  const counts = chartData.data;

  const objectDiv = `${objectID}Chart`;

  var colors = {};

  // Predefined colors for the next 10 unmatched labels
  const predefinedColors = [
    "#FFD1DC",
    "#AEC6CF",
    "#BDFCC9",
    "#E6E6FA",
    "#FFDAB9",
    "#FFFFE0",
    "#D3D3D3",
    "#F08080",
    "#E0B0FF",
    "#98FB98",
  ];
  let nextColorIndex = 0;

  labels.forEach((label, index) => {
    switch (label.toLowerCase()) {
      case "google adwords":
        colors[label] = "#BFE6FF";
        break;
      case "facebook":
        colors[label] = "#6D84B4";
        break;
      case "direct":
        colors[label] = "#CCCCCC";
        break;
      case "fresh":
        colors[label] = "#AED6F1";
        break;
      case "pending":
        colors[label] = "#F2F3F4";
        break;
      case "follow up":
        colors[label] = "#FFD699";
        break;
      case "cancel":
        colors[label] = "#FFCCCC";
        break;
      case "confirm":
        colors[label] = "#C7EAE4";
        break;
      case "duplicate":
        colors[label] = "#E6E6FA";
        break;

      default:
        if (nextColorIndex < predefinedColors.length) {
          colors[label] = predefinedColors[nextColorIndex];
          nextColorIndex++;
        } else {
          colors[label] = "#4CAF50";
        }
    }
  });

  var datasets = [
    {
      data: counts,
      backgroundColor: labels.map((label) => colors[label]), // Map label names to colors
      borderWidth: 0,
    },
  ];

  var myChart = new Chart(document.getElementById(objectDiv).getContext("2d"), {
    type: "pie",
    data: {
      labels: labels,
      datasets: datasets,
    },
    options: {
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            font: {
              size: 14, // Set font size to 14
              weight: 500, // Set font weight to 500
            },
          },
        },
      },
      radius: "90%", // Adjust the pie chart area size
      // Add the 3D effect
      layout: {
        padding: {
          left: 0,
          right: 0,
          top: 0,
          bottom: 0,
        },
      },
      plugins: {
        legend: {
          position: "top",
          labels: {
            font: {
              size: 14, // Set font size to 14
              weight: 500, // Set font weight to 500
            },
          },
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              let label = context.label || "";
              if (label) {
                label += ": ";
              }
              label += context.formattedValue;
              return label;
            },
          },
        },
      },
    },
  });
}

function createLineChart(
  objectID,
  chartLabel = "",
  currentDataLabel,
  currentData,
  currentDataColor,
  previousDataLabel,
  previousData,
  previousDataColor,
  showBackGroundAreaColor = true,
  lineCurveTension = 0.4,
  border = 1,
  animationDuration = 1000,
  showTopLabel = false,
  showXLable = false,
  showYLable = false
) {
  var myChart = new Chart(document.getElementById(objectID).getContext("2d"), {
    type: "line",
    data: {
      labels: chartLabel,
      datasets: [
        {
          label: currentDataLabel,
          data: currentData,
          lineTension: lineCurveTension,
          fill: showBackGroundAreaColor,
          backgroundColor: [currentDataColor],
          borderColor: [currentDataColor],
          borderWidth: border,
        },
        {
          label: previousDataLabel,
          data: previousData,
          lineTension: lineCurveTension,
          fill: showBackGroundAreaColor,
          backgroundColor: [previousDataColor],
          borderColor: [previousDataColor],
          borderWidth: border,
        },
      ],
    },
    options: {
      animations: {
        tension: {
          duration: animationDuration,
        },
      },
      plugins: {
        legend: {
          display: showTopLabel,
        },
      },
      scales: {
        x: {
          display: showXLable,
        },
        y: {
          display: showYLable,
        },
      },
      // manage dots in lines
      elements: {
        point: {
          radius: 0,
        },
      },
      responsive: true,
      maintainAspectRatio: false,
    },
  });
}

// Counting number animation

function increaseCountingNumberAnimation(
  objectID,
  start,
  end,
  duration,
  percentage
) {
  if (start === end) return;
  var range = end - start;
  var current = start;
  var increment = end > start ? 1 : +1;
  var stepTime = Math.abs(Math.floor(duration / range));
  var obj = document.getElementById(objectID);
  var timer = setInterval(function () {
    current += increment;
    obj.innerHTML = current;
    // '+ <span class="danger"><i class="fi-rr-arrow-small-down"></i>' +
    // percentage + "%";
    if (current == end) {
      clearInterval(timer);
    }
  }, stepTime);
}

function decreaseCountingNumberAnimation(
  objectID,
  start,
  end,
  duration,
  percentage
) {
  if (start === end) return;
  var range = end - start;
  var current = start;
  var increment = end > start ? 1 : -1;
  var stepTime = Math.abs(Math.floor(duration / range));
  var obj = document.getElementById(objectID);
  var timer = setInterval(function () {
    current += increment;
    obj.innerHTML =
      "₹ " +
      current +
      '<span class="danger"><i class="fi-rr-arrow-small-down"></i>' +
      percentage +
      "%";
    if (current == end) {
      clearInterval(timer);
    }
  }, stepTime);
}

function comparisonWidgetChart(objectID, data) {
  increaseCountingNumberAnimation(`totalLeadsValue`, 500 - 100, 500, 1, 86);
  createLineChart(
    (objectID = `${objectID}Chart`),
    (chartLabel = [
      "Monday",
      "Tueday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
      "Sunday",
    ]),
    (currentDataLabel = "1st May 2021 - 8th May 2021"),
    (currentData = ["20", "24", "13", "50", "5", "40", "24"]),
    (currentDataColor = "#239B56"),
    (previousDataLabel = "1st May 2021 - 8th May 2021"),
    (previousData = ["20", "24", "13", "13", "50", "40", "45"]),
    (previousDataColor = "#D1F9D1")
  );
}

function dashboardWidgetWithChart(objectID, data) {
  increaseCountingNumberAnimation(
    `${objectID}Value`,
    data.totalLeads - 10,
    data.totalLeads,
    1,
    86
  );
  createLineChart(
    (objectID = `${objectID}Chart`),
    (chartLabel = [
      "Monday",
      "Tueday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
      "Sunday",
    ]),
    (currentDataLabel = "1st May 2021 - 8th May 2021"),
    (currentData = ["20", "24", "13", "50", "5", "40", "24"]),
    (currentDataColor = "#239B56"),
    (previousDataLabel = "1st May 2021 - 8th May 2021"),
    (previousData = ["20", "24", "13", "13", "50", "40", "45"]),
    (previousDataColor = "#D1F9D1")
  );
}

function dashboardWidgetWithoutChart(objectID, data) {
  increaseCountingNumberAnimation(
    `${objectID}Value`,
    data.totalLeads - 10,
    data.totalLeads,
    1,
    86
  );
}

// Total leads

if ($("#totalLeads").length) {
  $.ajax({
    type: "GET",
    url: "/api/report/total-leads/?daterange=" + dateRangeParams,
    dataType: "json",
    success: function (response) {
      dashboardWidgetWithoutChart("totalLeads", response);
    },
    error: function (response) {
      console.log(`Error : `, response);
    },
  });
}

// Confirm leads

if ($("#totalConfirmLeads").length) {
  $.ajax({
    type: "GET",
    url: "/api/report/confirm-leads/?daterange=" + dateRangeParams,
    dataType: "json",
    success: function (response) {
      dashboardWidgetWithoutChart("totalConfirmLeads", response);
    },
    error: function (response) {
      console.log(`Error : `, response);
    },
  });
}

// Pending leads

if ($("#totalPendingLeads").length) {
  $.ajax({
    type: "GET",
    url: "/api/report/pending-leads/?daterange=" + dateRangeParams,
    dataType: "json",
    success: function (response) {
      dashboardWidgetWithoutChart("totalPendingLeads", response);
    },
    error: function (response) {
      console.log(`Error : `, response);
    },
  });
}

// Followups leads

if ($("#totalFollowupLeads").length) {
  $.ajax({
    type: "GET",
    url: "/api/report/followup-leads/?daterange=" + dateRangeParams,
    dataType: "json",
    success: function (response) {
      dashboardWidgetWithoutChart("totalFollowupLeads", response);
    },
    error: function (response) {
      console.log(`Error : `, response);
    },
  });
}

// Duplicate leads

if ($("#totalDuplicateLeads").length) {
  $.ajax({
    type: "GET",
    url: "/api/report/duplicate-leads/?daterange=" + dateRangeParams,
    dataType: "json",
    success: function (response) {
      dashboardWidgetWithoutChart("totalDuplicateLeads", response);
    },
    error: function (response) {
      console.log(`Error : `, response);
    },
  });
}

// Cancel leads

if ($("#totalCancelLeads").length) {
  $.ajax({
    type: "GET",
    url: "/api/report/cancel-leads/?daterange=" + dateRangeParams,
    dataType: "json",
    success: function (response) {
      dashboardWidgetWithoutChart("totalCancelLeads", response);
    },
    error: function (response) {
      console.log(`Error : `, response);
    },
  });
}

// Source lead

if ($("#sourceData").length) {
  $.ajax({
    type: "GET",
    url: "/api/report/enquiry-source/?daterange=" + dateRangeParams,
    dataType: "json",
    success: function (response) {
      createPieChart("sourceData", response);
    },
    error: function (response) {
      console.log(`Error : `, response);
    },
  });
}

if ($("#enquiryPerformace").length) {
  $.ajax({
    type: "GET",
    url: "/api/report/enquiry-performance/?daterange=" + dateRangeParams,
    dataType: "json",
    success: function (response) {
      createPieChart("enquiryPerformace", response);
    },
    error: function (response) {
      console.log(`Error : `, response);
    },
  });
}

// Total Order
if ($("#totalOrder").length) {
  increaseCountingNumberAnimation("totalOrderValue", 500 - 100, 500, 1, 86);
  // createLineChart(
  //   (objectID = "totalOrderChart"),
  //   (chartLabel = [
  //     "Monday",
  //     "Tueday",
  //     "Wednesday",
  //     "Thursday",
  //     "Friday",
  //     "Saturday",
  //     "Sunday",
  //   ]),
  //   (currentDataLabel = "1st May 2021 - 8th May 2021"),
  //   (currentData = ["20", "24", "13", "50", "5", "40", "24"]),
  //   (currentDataColor = "#239B56"),
  //   (previousDataLabel = "1st May 2021 - 8th May 2021"),
  //   (previousData = ["20", "24", "13", "13", "50", "40", "45"]),
  //   (previousDataColor = "#D1F9D1")
  // );
}

// Total Amount of sale

if ($("#totalOrderAmount").length) {
  increaseCountingNumberAnimation("totalOrderAmountValue", 0, 0, 1, 86);
  // createLineChart(
  //   (objectID = "totalOrderAmountChart"),
  //   (chartLabel = [
  //     "Monday",
  //     "Tueday",
  //     "Wednesday",
  //     "Thursday",
  //     "Friday",
  //     "Saturday",
  //     "Sunday",
  //   ]),
  //   (currentDataLabel = "1st May 2021 - 8th May 2021"),
  //   (currentData = ["20", "24", "13", "50", "5", "40", "24"]),
  //   (currentDataColor = "#E74C3C"),
  //   (previousDataLabel = "1st May 2021 - 8th May 2021"),
  //   (previousData = ["20", "24", "13", "13", "50", "40", "45"]),
  //   (previousDataColor = "#F9D1D1")
  // );
}

// Order List

if ($(".showTableList").length) {
  $(".showTableList").DataTable({
    pageLength: 50,
    columnDefs: [
      { type: "date", targets: 4 }, // Assuming the date column is the fifth column (index 4)
      { width: "20%", targets: 1 },
      {
        targets: "_all", // The second column (index 1)
        createdCell: function (td, cellData, rowData, row, col) {
          $(td).css({
            "max-width": "150px", // Set the maximum width (adjust as needed)
            "white-space": "normal", // Allow text wrapping
            "word-wrap": "break-word", // Ensure words break if necessary
          }); // Set the maximum width (adjust as needed)
        },
      },
    ],
    fixedColumns: true,
    order: [[4, "desc"]],
  });
}

if ($(".showTableListWithoutFilter").length) {
  $(".showTableListWithoutFilter").DataTable({
    columnDefs: [
      { type: "date", targets: 4 }, // Assuming the date column is the first column (index 0)
    ],
    order: [[4, "desc"]],
  });
}

// Date Range picker

if ($(".dateRangePicker").length) {
  // Last 30 Days
  // var start = moment().subtract(29, 'days');
  // var end = moment();

  // Date range start from current month
  var start = moment().startOf("month");
  var end = moment().endOf("month");

  function cb(start, end) {
    $(".dateRangePicker span").html(
      start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
    );
  }

  $(".dateRangePicker")
    .daterangepicker(
      {
        startDate: start,
        endDate: end,
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        locale: {
          format: "DD-MMM-YYYY",
        },
      },
      cb
    )
    .val(dateRangeValue);

  cb(start, end);
}

// Select js 2

if ($(".selectJS").length) {
  $(".selectJS").select2({
    placeholder: "Select drop down placeholder",
  });
}

// Main Box tabs

if ($(".steps-tab").length) {
  $(".step-box").click(function (e) {
    e.preventDefault();
    currentElementID = $(this).attr("id");
    $(".step-box").removeClass("active");
    showCurrentBox = currentElementID + "_box";
    showCurrentLable = currentElementID;
    $(".step-open-box").hide();
    $("#" + showCurrentLable).addClass("active");
    $("." + showCurrentBox).show();
    hidePageLoader();
  });
}

// Material CSS like input

$(".form-control").change(function () {
  currentClickID = $(this).attr("id");
  currentValue = $(this).val();
  if (currentValue != "") {
    $("#" + currentClickID + "Label").addClass("labelHeading");
  }
});

const urlParams = new URLSearchParams(window.location.search);
const isStatus = urlParams.get("status");
const responseMsg = urlParams.get("responseMsg");

if (isStatus && responseMsg) {
  popUpMessage("center", decodeURI(responseMsg), isStatus);
}

// Lead details page

$("#leadDetailsBtn").click(function (e) {
  $("#leadDetailsBtn").addClass("btn-green");
  $("#noteDetailsBtn").removeClass("btn-green");
  $("#paymentDetailsBtn").removeClass("btn-green");
  hidePageLoader();
  e.preventDefault();
});
$("#noteDetailsBtn").click(function (e) {
  $("#noteDetailsBtn").addClass("btn-green");
  $("#leadDetailsBtn").removeClass("btn-green");
  $("#paymentDetailsBtn").removeClass("btn-green");
  hidePageLoader();
  e.preventDefault();
});
$("#paymentDetailsBtn").click(function (e) {
  $("#paymentDetailsBtn").addClass("btn-green");
  $("#leadDetailsBtn").removeClass("btn-green");
  $("#noteDetailsBtn").removeClass("btn-green");
  hidePageLoader();
  e.preventDefault();
});

function closePopup(btn) {
  var popupContainer = btn.closest(".popUp");
  popupContainer.style.display = "none";
  document.querySelector(".popUp").style.display = "none";
}

$("#noteBtn").click(function (e) {
  document.querySelector(".notesBox").style.display = "flex";
  $(".notesBox").show();
  hidePageLoader();
  e.preventDefault();
});

$("#paymentBtn").click(function (e) {
  document.querySelector(".paymentLinkBox").style.display = "flex";
  $(".paymentLinkBox").show();
  hidePageLoader();
  e.preventDefault();
});

$("#updatePaymentBtn").click(function (e) {
  document.querySelector(".updatePaymentBox").style.display = "flex";
  $(".updatePaymentBox").show();
  hidePageLoader();
  e.preventDefault();
});

$("#followUpBtn").click(function (e) {
  document.querySelector(".followUpBox").style.display = "flex";
  $(".followUpBox").show();
  hidePageLoader();
  e.preventDefault();
});

$("#itineraryBtn").click(function (e) {
  document.querySelector(".itineraryBox").style.display = "flex";
  $(".itineraryBox").show();
  hidePageLoader();
  e.preventDefault();
});

$("#voucherBtn").click(function (e) {
  document.querySelector(".voucherBox").style.display = "flex";
  $(".voucherBox").show();
  hidePageLoader();
  e.preventDefault();
});

$("#leadDetailsBtn").click(function (e) {
  document.querySelector("#leadDetailsBox").style.display = "block";
  document.querySelector("#noteDetailsBox").style.display = "none";
  document.querySelector("#paymentDetailsBox").style.display = "none";
  document.querySelector("#leadEditBox").style.display = "none";
  document.querySelector("#leadEditBtn").style.display = "block";
  hidePageLoader();
  e.preventDefault();
});

$("#noteDetailsBtn").click(function (e) {
  document.querySelector("#noteDetailsBox").style.display = "block";
  document.querySelector("#leadDetailsBox").style.display = "none";
  document.querySelector("#paymentDetailsBox").style.display = "none";
  document.querySelector("#leadEditBox").style.display = "none";
  document.querySelector("#leadEditBtn").style.display = "none";
  hidePageLoader();
  e.preventDefault();
});

$("#paymentDetailsBtn").click(function (e) {
  document.querySelector("#paymentDetailsBox").style.display = "block";
  document.querySelector("#leadDetailsBox").style.display = "none";
  document.querySelector("#noteDetailsBox").style.display = "none";
  document.querySelector("#leadEditBox").style.display = "none";
  document.querySelector("#leadEditBtn").style.display = "none";
  hidePageLoader();
  e.preventDefault();
});

$("#leadEditBtn").click(function (e) {
  hidePageLoader();
  document.querySelector("#leadEditBox").style.display = "block";
  document.querySelector("#paymentDetailsBox").style.display = "none";
  document.querySelector("#leadDetailsBox").style.display = "none";
  document.querySelector("#noteDetailsBox").style.display = "none";
  document.querySelector("#leadEditBtn").style.display = "none";
  e.preventDefault();
});

// Update notes

$("#updateNotes").click(function (e) {
  var title = $("#notesTitle").val();
  var msg = $("#noteMsg").val();

  if (title.trim() !== "") {
    if (title.trim() !== "other" || msg.trim() !== "") {
      $.ajax({
        type: "POST",
        url: "/api/leads/notes/",
        dataType: "json",
        data: {
          leadId,
          title,
          msg,
          finalNotes: true,
        },
        success: function (response) {
          popUpMessage("center", response.msg, response.status);
          setTimeout(function () {
            document.querySelector(".notesBox").style.display = "none";
          }, 1500);
        },
        error: function (response) {
          console.log(`Error : `, response);
        },
      });
    } else {
      popUpMessage("center", "Message cannot be blank", "warning");
    }
  } else {
    popUpMessage("center", "Title cannot be blank", "warning");
  }
  hidePageLoader();
  e.preventDefault();
});

// Follow up reminder

$("#addFollowUp").click(function (e) {
  var title = $("#reason").val();
  var followupTime = $("#followUpTime").val();
  var msg = $("#comment").val();

  if (title.trim() !== "") {
    if (followupTime.trim() !== "") {
      if (title.trim() !== "other" || msg.trim() !== "") {
        $.ajax({
          type: "POST",
          url: "/api/leads/notes/",
          dataType: "json",
          data: {
            leadId,
            title,
            msg,
            followupTime,
            finalNotes: false,
          },
          success: function (response) {
            popUpMessage("center", response.msg, response.status);
            setTimeout(function () {
              document.querySelector(".followUpBox").style.display = "none";
            }, 1500);
          },
          error: function (response) {
            console.log(`Error : `, response);
          },
        });
      } else {
        popUpMessage("center", "Comment cannot be blank", "warning");
      }
    } else {
      popUpMessage("center", "Please select follow up time", "warning");
    }
  } else {
    popUpMessage("center", "Reason cannot be blank", "warning");
  }
  hidePageLoader();
  e.preventDefault();
});

// Add new lead

$("#addEnquiry").click(function (e) {
  var formData = $("#addNewForm").serialize();
  $.ajax({
    type: "POST",
    url: "/api/leads/create/",
    dataType: "json",
    data: formData,
    success: function (response) {
      popUpMessage("center", response.msg, response.status);
      if (response.status == "success") {
        setTimeout(function () {
          const leadIdBase64 = btoa(response.leadId);
          window.location.href = `/dashboard/leads/view/?id=${leadIdBase64}`;
        }, 1500);
      }
    },
    error: function (response) {
      console.log(`Error : `, response);
    },
  });
  hidePageLoader();
  e.preventDefault();
});

// Update leads data

$("#updateEnquiry").click(function (e) {
  var formData = $("#updateLeadForm").serialize();
  $.ajax({
    type: "POST",
    url: "/api/leads/update/",
    dataType: "json",
    data: formData,
    success: function (response) {
      popUpMessage("center", response.msg, response.status);
      if (response.status == "success") {
        setTimeout(function () {
          const leadIdBase64 = btoa(response.leadId);
          window.location.href = `/dashboard/leads/view/?id=${leadIdBase64}`;
        }, 1500);
      }
    },
    error: function (response) {
      console.log(`Error : `, response);
    },
  });
  hidePageLoader();
  e.preventDefault();
});

$(".duplicateLead").click(function (e) {
  showPageLoader();
  var id = $(this).attr("id");
  $.ajax({
    type: "GET",
    url: "/api/leads/duplicate/?id=" + id,
    dataType: "json",
    success: function (response) {
      popUpMessage("center", response.msg, response.status);
      setTimeout(function () {
        location.reload();
      }, 1500);
      hidePageLoader();
    },
    error: function (response) {
      console.log(`Error : `, response);
      hidePageLoader();
    },
  });
  hidePageLoader();
  e.preventDefault();
});

if ($("#fixedDiv").length) {
  window.onscroll = function () {
    fixDiv();
  };

  var fixedDiv = document.getElementById("fixedDiv");
  var prevScrollpos = window.pageYOffset;

  function fixDiv() {
    var currentScrollPos = window.pageYOffset;
    if (prevScrollpos > currentScrollPos) {
      fixedDiv.classList.remove("fixed");
    } else {
      if (currentScrollPos > 100) {
        fixedDiv.classList.add("fixed");
      } else {
        fixedDiv.classList.remove("fixed");
      }
    }
    prevScrollpos = currentScrollPos;
  }
}

$(".paymentLink").click(function (e) {
  var id = $(this).attr("id");

  if (id == "customPaymentLink") {
    $("#minPaymentLinkBox").show();
    $("#paymentValue").focus();
  } else {
    $("#minPaymentLinkBox").hide();
  }
  hidePageLoader();
});

$("#sendPaymentLink").click(function (e) {
  var selectedValue = "";
  selectedValue = $('input[name="paymentLink"]:checked').val();
  if (selectedValue == 0) {
    selectedValue = $("#paymentValue").val();
  }

  if (selectedValue != "") {
    if (isNumeric(selectedValue)) {
      popUpMessage("center", "Payment link sent to customer", "warning");
    } else {
      popUpMessage("center", "Please enter valid amount", "warning");
    }
  } else {
    popUpMessage("center", "Please enter amount", "warning");
  }
  hidePageLoader();
  e.preventDefault();
});
// });

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
                $.ajax({
                  type: "POST",
                  url: "/api/property/publish/",
                  dataType: "json",
                  data: {
                    name,
                    description,
                    address,
                    city,
                    pinCode,
                    email,
                    mobile,
                    category,
                    googleMapUrl,
                  },
                  success: function (response) {
                    popUpMessage("center", response.msg, response.status);
                    setTimeout(function () {
                      window.location.href =
                        "/dashboard/vendors/property/update/?id" +
                        btoa(response.propertyId);
                    }, 1500);
                    $("#addProperty").show();
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
                  "Please select property type",
                  "warning"
                );
              }
            } else {
              popUpMessage(
                "center",
                "Enter property contact number",
                "warning"
              );
            }
          } else {
            popUpMessage("center", "Enter property email address", "warning");
          }
        } else {
          popUpMessage("center", "Enter property city pin code", "warning");
        }
      } else {
        popUpMessage("center", "Enter property city name", "warning");
      }
    } else {
      popUpMessage("center", "Enter property address", "warning");
    }
  } else {
    popUpMessage("center", "Enter property name", "warning");
  }
  $("#addProperty").hide();
});

$("#updateProperty").click(function (e) {
  e.preventDefault();
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
