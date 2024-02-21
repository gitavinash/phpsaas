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

// Total Order

if ($("#totalOrder").length) {
  increaseCountingNumberAnimation("totalOrderValue", 500 - 100, 500, 1, 86);
  createLineChart(
    (objectID = "totalOrderChart"),
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

// Total Amount of sale

if ($("#totalOrderAmount").length) {
  increaseCountingNumberAnimation(
    "totalOrderAmountValue",
    500 - 100,
    500,
    1,
    86
  );
  createLineChart(
    (objectID = "totalOrderAmountChart"),
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
    (currentDataColor = "#E74C3C"),
    (previousDataLabel = "1st May 2021 - 8th May 2021"),
    (previousData = ["20", "24", "13", "13", "50", "40", "45"]),
    (previousDataColor = "#F9D1D1")
  );
}

// Recent orders

if ($("#recentOrderAmount").length) {
  increaseCountingNumberAnimation(
    "recentOrderAmountValue",
    500 - 100,
    500,
    1,
    86
  );
  createLineChart(
    (objectID = "recentOrderAmountChart"),
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
    (currentDataColor = "#F1C40F"),
    (previousDataLabel = "1st May 2021 - 8th May 2021"),
    (previousData = ["20", "24", "13", "13", "50", "40", "45"]),
    (previousDataColor = "#F9EFD1")
  );
}

// Order List

if ($("#showTableList").length) {
  $("#showTableList").DataTable({
    scrollY: 400,
    language: {
      search: "Filter Table",
      sLengthMenu: "Display _MENU_ records",
      searchPlaceholder: "Search records",
    },
  });
}

if ($(".showTableListWithoutFilter").length) {
  $(".showTableListWithoutFilter").DataTable({});
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

  $(".dateRangePicker").daterangepicker(
    {
      startDate: start,
      endDate: end,
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
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
  );

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

if (isStatus) {
  const responseMsg = urlParams.get("responseMsg");
  popUpMessage("center", responseMsg, isStatus);
}
