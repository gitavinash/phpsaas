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

  var myChart = new Chart(document.getElementById(objectDiv).getContext("2d"), {
    type: "pie",
    data: {
      labels: labels,
      datasets: [
        {
          data: counts,
          backgroundColor: [
            "#2ca02c",
            "#FFD700",
            "#87CEEB",
            "#FFA07A",
            "#98FB98",
            "#FFB6C1",
            "#B0E0E6",
            "#FFDEAD",
            "#DDA0DD",
            "#F0E68C",
            // Add more colors if needed
          ],
          borderWidth: 0,
        },
      ],
    },
    options: {
      options: {
        plugins: {
          legend: {
            position: "bottom",
            labels: {
              fontSize: 16, // Adjust label font size
              formatter: function (value, context) {
                return (
                  context.chart.data.labels[context.dataIndex] +
                  ": " +
                  Math.round(context.parsed * 100) +
                  "%"
                );
              },
            },
          },
        },
        radius: "50%", // Adjust the pie chart area size
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
  console.log(`Data : `, data);
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
  console.log(`Object ID`, objectID);
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
      console.log(response);
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
      console.log(response);
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
      console.log(response);
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
      console.log(response);
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
      console.log(response);
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
      console.log(response);
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
    columnDefs: [
      { type: "date", targets: 4 }, // Assuming the date column is the first column (index 0)
    ],
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

    $(".step-open-box").hide();
    showCurrentBox = currentElementID + "_box";
    console.log(currentElementID + " -> " + showCurrentBox);
    $("." + showCurrentBox).show();
  });
}

// Material CSS like input

$(".form-control").change(function () {
  currentClickID = $(this).attr("id");
  currentValue = $(this).val();

  console.log("Current value : " + currentValue);

  if (currentValue != "") {
    $("#" + currentClickID + "Label").addClass("heading");
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
  e.preventDefault();
});
$("#noteDetailsBtn").click(function (e) {
  $("#noteDetailsBtn").addClass("btn-green");
  $("#leadDetailsBtn").removeClass("btn-green");
  $("#paymentDetailsBtn").removeClass("btn-green");
  e.preventDefault();
});
$("#paymentDetailsBtn").click(function (e) {
  $("#paymentDetailsBtn").addClass("btn-green");
  $("#leadDetailsBtn").removeClass("btn-green");
  $("#noteDetailsBtn").removeClass("btn-green");
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
  e.preventDefault();
});

$("#paymentBtn").click(function (e) {
  document.querySelector(".paymentLinkBox").style.display = "flex";
  $(".paymentLinkBox").show();
  e.preventDefault();
});

$("#followUpBtn").click(function (e) {
  document.querySelector(".followUpBox").style.display = "flex";
  $(".followUpBox").show();
  e.preventDefault();
});

$("#itineraryBtn").click(function (e) {
  document.querySelector(".itineraryBox").style.display = "flex";
  $(".itineraryBox").show();
  e.preventDefault();
});

$("#leadDetailsBtn").click(function (e) {
  document.querySelector("#leadDetailsBox").style.display = "block";
  document.querySelector("#noteDetailsBox").style.display = "none";
  document.querySelector("#paymentDetailsBox").style.display = "none";
  document.querySelector("#leadEditBox").style.display = "none";
  document.querySelector("#leadEditBtn").style.display = "block";
  e.preventDefault();
});

$("#noteDetailsBtn").click(function (e) {
  document.querySelector("#noteDetailsBox").style.display = "block";
  document.querySelector("#leadDetailsBox").style.display = "none";
  document.querySelector("#paymentDetailsBox").style.display = "none";
  document.querySelector("#leadEditBox").style.display = "none";
  document.querySelector("#leadEditBtn").style.display = "none";
  e.preventDefault();
});

$("#paymentDetailsBtn").click(function (e) {
  document.querySelector("#paymentDetailsBox").style.display = "block";
  document.querySelector("#leadDetailsBox").style.display = "none";
  document.querySelector("#noteDetailsBox").style.display = "none";
  document.querySelector("#leadEditBox").style.display = "none";
  document.querySelector("#leadEditBtn").style.display = "none";
  e.preventDefault();
});

$("#leadEditBtn").click(function (e) {
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
          console.log(`Response : `, response);
          popUpMessage("center", response.msg, response.status);
        },
        error: function (response) {
          console.log(response);
        },
      });
    } else {
      popUpMessage("center", "Message cannot be blank", "warning");
    }
  } else {
    popUpMessage("center", "Title cannot be blank", "warning");
  }
  setTimeout(function () {
    document.querySelector(".notesBox").style.display = "none";
  }, 1500);
  e.preventDefault();
});

// Update leads data

$("#updateEnquiry").click(function (e) {
  e.preventDefault();
});
