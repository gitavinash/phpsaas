function disableButtonWithCTA(selector, ctaText = "Processing...") {
  $(selector).prop("disabled", true).text(ctaText).addClass("disabled-button");
}
function enableButtonWithCTA(selector, ctaText = "Submit") {
  $(selector)
    .prop("disabled", true)
    .html(ctaText)
    .removeClass("disabled-button");
}

function closePopup(btn) {
  var popupContainer = btn.closest(".popUp");
  popupContainer.style.display = "none";
  document.querySelector(".popUp").style.display = "none";
}

function capitalizeWords(str) {
  return str.replace(/\b\w/g, (char) => char.toUpperCase());
}

function loadExternalJS(filePath) {
  let script = document.createElement("script"); // Create a <script> element
  script.src = filePath; // Set the src attribute to the JS file path
  script.type = "text/javascript"; // Set the type (optional, defaults to "text/javascript")
  document.body.appendChild(script); // Append the script to the body (or head)
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

function showWarning(message, selector) {
  popUpMessage("center", message, "warning");
  $(selector).focus();
}

function toggleDisplay(showBox, showEditBtn) {
  $(".openBtnBox").hide(); // Hide all
  document.querySelector(showBox).style.display = "block";
  if (showEditBtn) {
    $("#leadEditBtn").show();
  } else {
    $("#leadEditBtn").hide();
  }
  hidePageLoader();
}

function handleButtonClick(activeBtn) {
  $(".detailsBtn").removeClass("btn-green"); // Assuming the buttons have a common class 'btn'
  $(activeBtn).addClass("btn-green");
  hidePageLoader();
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

const urlQuery = new URLSearchParams(window.location.search);
let dateRangeParams = urlQuery.get("daterange");
let leadId = urlQuery.get("id");

function initializeSelect2HereMaps(element) {
  $(element).select2({
    placeholder: "Select location",
    minimumInputLength: 3,
    ajax: {
      url: "/api/maps/here/autocomplete/", // Replace with your API URL
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          searchQuery: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: $.map(data.locationList, function (item) {
            return {
              id: item.location,
              text: item.location,
            };
          }),
        };
      },
      cache: true,
    },
  });
}

function initializeSelect2HereMapsForInternational(element) {
  $(element).select2({
    placeholder: "Select location",
    minimumInputLength: 3,
    ajax: {
      url: "/api/maps/here/autocomplete-international/", // Replace with your API URL
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          searchQuery: params.term,
          country: $("#dropDownCountry").length
            ? { country: $("#dropDownCountry").val() }
            : {},
        };
      },
      processResults: function (data) {
        return {
          results: $.map(data.locationList, function (item) {
            return {
              id: item.location,
              text: item.location,
            };
          }),
        };
      },
      cache: true,
    },
  });
}

function initializeSelect2HereMapsWithCompleteAddress(element) {
  $(element)
    .select2({
      placeholder: "Select location",
      minimumInputLength: 2,
      ajax: {
        url: "/api/maps/here/autocomplete-full-address/?address=full",
        dataType: "json",
        delay: 250,
        data: function (params) {
          return {
            searchQuery: params.term,
          };
        },
        processResults: function (data) {
          return {
            results: $.map(data.locationList, function (item) {
              return {
                id: item.address,
                address: item.address,
                text: item.city,
                city: item.city,
                state: item.state,
                country: item.country,
                pinCode: item.pincode,
              };
            }),
          };
        },
        cache: true,
      },
    })
    .on("select2:select", function (e) {
      var selectedData = e.params.data;

      console.log(`selectedData`, selectedData);

      // Update the #pinCode input field with the pinCode from the selected item
      if (selectedData && selectedData.pinCode) {
        $("#pinCode").val(selectedData.pinCode);
        $("#stateName").val(selectedData.state);
        $("#countryName").val(selectedData.country);
      } else {
        $("#pinCode").val(""); // Clear if no pinCode available
      }
    });
}

function initializeSelect2(element) {
  console.log(`inside initializeSelect2`);
  $(element).select2({
    placeholder: "Select destination",
    minimumInputLength: 2,
    ajax: {
      url: "/api/maps/here/autocomplete-international/",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          searchQuery: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: $.map(data.locationList, function (item) {
            return {
              id: item.location,
              text: item.location,
            };
          }),
        };
      },
      cache: true,
    },
  });
}

function uploadCSV(inputId, uploadUrl, btnId) {
  var fileInput = $("#" + inputId)[0];
  var selectedFiles = fileInput.files;
  var $btn = $("#" + btnId);
  $("#loader").show();
  $(".uploadBox").hide();
  $($btn).hide();

  if (selectedFiles.length === 0) {
    popUpMessage("center", "Please upload a CSV file.", "error");
    $("#loader").hide();
    $(".uploadBox").show();
    $($btn).show();
    return;
  }

  // Validate extension
  var fileName = selectedFiles[0].name.toLowerCase();
  if (!fileName.endsWith(".csv")) {
    popUpMessage("center", "Only .csv files are allowed.", "error");
    $("#" + inputId).val(""); // reset input
    $("#loader").hide();
    $(".uploadBox").show();
    $($btn).show();
    return;
  }

  // Prepare FormData
  var formData = new FormData();
  formData.append("file", selectedFiles[0]);

  var originalText = $btn.text();
  $btn.text("Uploading...").prop("disabled", true);

  // AJAX upload
  $.ajax({
    url: "/api/" + uploadUrl + "/import/",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    timeout: 600000,
    success: function (response) {
      $("#loader").hide();
      $("#csvResult").show();
      popUpMessage("center", "CSV uploaded successfully!", "success");
      const uploadMetrix = response.summary.properties;

      $("#importedCount").text(uploadMetrix.created);
      $("#duplicateCount").text(uploadMetrix.updated);
      $("#failedCount").text(uploadMetrix.failed);

      $btn.text(originalText).prop("disabled", false);
    },
    error: function () {
      $("#loader").hide();
      $(".uploadBox").show();
      $($btn).show();
      popUpMessage("center", "Upload failed. Please try again.", "error");
      $btn.text(originalText).prop("disabled", false);
    },
  });
}

$(document).ready(function (e) {
  if ($(".indianCityAutoComplete").length) {
    // Initialize select2 for the existing element
    initializeSelect2HereMaps(".indianCityAutoComplete");
  }

  if ($(".internationalCityAutoComplete").length) {
    // Initialize select2 for the existing element
    initializeSelect2HereMapsForInternational(".internationalCityAutoComplete");
  }

  if ($(".addressAutoComplete").length) {
    // Initialize select2 for the existing element
    initializeSelect2HereMapsWithCompleteAddress(".addressAutoComplete");
  }

  dateRangeParams = dateRangeParams === "" ? "" : dateRangeParams;
  if (dateRangeParams == null) {
    dateRangeValue = "";
  } else {
    dateRangeValue = dateRangeParams;
  }
  if (typeof tempId !== "undefined") {
    dateRangeParams += "&userId=" + tempId;
  }

  /** gallery */

  var addGallery = document.getElementsByClassName("addGallery");
  var updateGallery = document.getElementsByClassName("updateGallery");

  if (addGallery) {
    $(".addGallery").imageUploader({
      imagesInputName: "gallery",
      maxFiles: 50,
    });
  }

  if (typeof oldGalleryImages !== "undefined" && updateGallery.length > 0) {
    $(".updateGallery").imageUploader({
      preloaded: oldGalleryImages,
      imagesInputName: "gallery",
      preloadedInputName: "oldGallery",
      maxSize: 2 * 1024 * 1024,
      maxFiles: 50,
    });
  }

  $(document).on("click", ".deleteButton", function (event) {
    event.preventDefault();
    $(".deleteButton").hide();
    const redirectUrl = $(this).data("redirect");
    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    }).then((result) => {
      if (result.value) {
        var requestType = $(this).attr("data-type");
        var requestId = $(this).attr("data-id");
        $.ajax({
          type: "GET",
          url: `/api/${requestType}/delete/?id=` + btoa(requestId),
          dataType: "json",
          success: function (response) {
            popUpMessage("center", response.msg, response.status);
            if (response.status == "success") {
              setTimeout(function () {
                if (redirectUrl) {
                  window.location.href = redirectUrl;
                } else {
                  location.reload();
                }
              }, 1500);
              $(".deleteButton").show();
            } else {
              $(".deleteButton").show();
            }
          },
          error: function (response) {
            console.log(`Error : `, response);
            $(".deleteButton").show();
            popUpMessage(
              "center",
              response.responseJSON.msg,
              response.responseJSON.status,
            );
          },
        });
      } else {
        $(".deleteButton").show();
      }
    });
  });

  function isNumeric(input) {
    return /^\d+$/.test(input);
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

    var myChart = new Chart(
      document.getElementById(objectDiv).getContext("2d"),
      {
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
      },
    );
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
    showYLable = false,
  ) {
    var myChart = new Chart(
      document.getElementById(objectID).getContext("2d"),
      {
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
      },
    );
  }

  // Counting number animation

  function increaseCountingNumberAnimation(
    objectID,
    start,
    end,
    duration,
    percentage,
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
    percentage,
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
      (previousDataColor = "#D1F9D1"),
    );
  }

  function dashboardWidgetWithChart(
    widgetId,
    apiData,
    primaryChartBackgroundColor = "#239B56",
    secondaryChartBackgroundColor = "#D1F9D1",
  ) {
    console.log("dashboardWidgetWithChart called for:", widgetId, apiData);

    const valueElementId = widgetId + "Value"; // e.g. "totalSalesAmountValue"
    const chartElementId = widgetId + "Chart"; // e.g. "totalSalesAmountChart"

    // ---- 1) Total value animation ----
    // Support multiple possible keys from different APIs
    const rawTotal =
      apiData.total ?? apiData.totalSalesAmount ?? apiData.totalLeads ?? 0;

    const total = Number(rawTotal) || 0; // handle NaN safely

    let startValue;

    if (total <= 100) {
      // For small totals, animate from 0 → total
      startValue = 0;
    } else {
      // For bigger totals, start a bit below total
      let diff = Math.round(total * 0.01); // 1% of total
      diff = Math.min(diff, 100); // max 100
      startValue = Math.max(total - diff, 0);
    }

    const valueEl = document.getElementById(valueElementId);
    if (!valueEl) {
      console.error("Value element not found:", valueElementId);
      // nothing more to do
      return;
    }

    // If total is 0 (or start == end), don't animate, just set the value once
    if (total === 0 || startValue === total) {
      valueEl.textContent = "0";
      // also remove any loading state if you have one
      // valueEl.classList.remove("is-loading");
      return;
    }

    // Run the animation only when we actually have something to count up
    increaseCountingNumberAnimation(valueElementId, startValue, total, 1, 86);

    // ---- 2) Prepare chart data from apiData.data ----
    const dataObj = apiData.data || {};
    const labels = Object.keys(dataObj);
    const currentData = Object.values(dataObj);

    const currentDataLabel =
      apiData.rangeLabel ||
      (typeof dateRangeParams !== "undefined"
        ? dateRangeParams
        : "Current Period");

    const previousDataLabel = "";
    const previousData = new Array(labels.length).fill(0);

    // ---- 3) Draw chart ----
    const canvas = document.getElementById(chartElementId);
    if (!canvas) {
      console.error("Canvas not found for ID:", chartElementId);
      return;
    }

    createLineChart(
      chartElementId,
      labels,
      currentDataLabel,
      currentData,
      primaryChartBackgroundColor,
      previousDataLabel,
      previousData,
      secondaryChartBackgroundColor,
    );
  }

  function dashboardWidgetWithoutChart(objectID, data) {
    increaseCountingNumberAnimation(
      `${objectID}Value`,
      data.totalLeads - 10,
      data.totalLeads,
      1,
      86,
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

  // Enquiry performance

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

  // Pending request

  if ($("#totalPendingRequest").length) {
    $.ajax({
      type: "GET",
      url: "/api/report/pending-request/?daterange=" + dateRangeParams,
      dataType: "json",
      success: function (response) {
        dashboardWidgetWithoutChart("totalPendingRequest", response);
      },
      error: function (response) {
        console.log(`Error : `, response);
      },
    });
  }

  // Total Order
  if ($("#totalOrder").length) {
    increaseCountingNumberAnimation("totalOrderValue", 500 - 100, 500, 1, 86);
  }

  // Total Amount of sale

  if ($("#totalOrderAmount").length) {
    increaseCountingNumberAnimation("totalOrderAmountValue", 0, 0, 1, 86);
  }

  // Total Sales Chart

  if ($("#totalSalesAmount").length) {
    $.ajax({
      type: "GET",
      url:
        "/api/report/sales/?daterange=" + encodeURIComponent(dateRangeParams),
      dataType: "json",
      success: function (response) {
        dashboardWidgetWithChart(
          "totalSalesAmount",
          response,
          "#00657B",
          "#38BDF8",
        );
      },
      error: function (response) {
        console.log("Error:", response);
      },
    });
  }

  // Total Check in Charts

  if ($("#totalVisits").length) {
    $.ajax({
      type: "GET",
      url:
        "/api/report/check-in/?daterange=" +
        encodeURIComponent(dateRangeParams),
      dataType: "json",
      success: function (response) {
        dashboardWidgetWithChart("totalVisits", response, "#4CAF50", "#81C784");
      },
      error: function (response) {
        console.log("Error:", response);
      },
    });
  }

  // Total Expenses Charts

  if ($("#totalExpenses").length) {
    $.ajax({
      type: "GET",
      url:
        "/api/report/expenses/?daterange=" +
        encodeURIComponent(dateRangeParams),
      dataType: "json",
      success: function (response) {
        dashboardWidgetWithChart(
          "totalExpenses",
          response,
          "#FF5722",
          "#FF9800",
        );
      },
      error: function (response) {
        console.log("Error:", response);
      },
    });
  }

  // Custom sorting for extracting the numeric part of the string
  $.fn.dataTable.ext.type.order["numeric-string-asc"] = function (a, b) {
    var numA = parseInt(a.match(/^\d+/)); // Extract the leading numeric part
    var numB = parseInt(b.match(/^\d+/)); // Extract the leading numeric part
    return numA - numB; // Compare the numeric parts
  };

  $.fn.dataTable.ext.type.order["numeric-string-desc"] = function (a, b) {
    var numA = parseInt(a.match(/^\d+/)); // Extract the leading numeric part
    var numB = parseInt(b.match(/^\d+/)); // Extract the leading numeric part
    return numB - numA; // Compare the numeric parts in reverse order
  };

  // Order List
  $(".showTableList").DataTable({
    pageLength: 50,
    fixedHeader: true,
    responsive: false, // Disable responsiveness when using fixedColumns and scrollX
    searching: true,
    lengthChange: false,
    autoWidth: false,
    ordering: false,
    scrollX: true, // Enable horizontal scrolling
    scrollCollapse: true, // Collapse the table to fit content
    columnDefs: [
      { type: "date", targets: 4 }, // Assuming the date column is the fifth column (index 4)
      { width: "20%", targets: 1 }, // Set width for the second column
      { width: "150px", targets: -1 },
      {
        targets: "_all", // Apply to all columns
        createdCell: function (td, cellData, rowData, row, col) {
          $(td).css({
            "max-width": "150px", // Set the maximum width
            "white-space": "normal", // Allow text wrapping
            "word-wrap": "break-word", // Ensure words break if necessary
          });
        },
      },
    ],
    createdRow: function (row, data, dataIndex) {
      var rowDataStatus = data[data.length - 2].toLowerCase();
      var rowDataStatus = rowDataStatus.replace(" ", "_");

      // Apply inline background color for each cell in the row
      var color;

      switch (rowDataStatus) {
        case "draft":
          color = "#f9e29f"; // Darkened pastel yellow for draft
          break;
        case "initiate":
          color = "#f5f5f5"; // Soft greyish white for initiate
          break;
        case "active":
          color = "#b4e2b4"; // Darkened pastel green for active
          break;
        case "approved":
          color = "#b4e2b4"; // Darkened pastel green for active
          break;
        case "closed":
          color = "#b4e2b4"; // Darkened pastel green for active
          break;
        case "sold_out":
          color = "#b87085"; // Darkened pastel pinkish-red for sold out
          break;
        case "success":
          color = "#A9D5D1"; // Darkened pastel teal for success
          break;
        case "duplicate":
          color = "#C8C8F0"; // Darkened lavender for duplicate
          break;
        case "follow_up":
          color = "#E6AF60"; // Darkened pastel orange for follow-up
          break;
        case "payment initiate":
          color = "#B3E6F0"; // Darkened pastel blue for payment initiate
          break;
        case "payment refund":
          color = "#F6B0DB"; // Darkened pastel pink-purple for refund
          break;
        case "failed":
          color = "#F8B3B3"; // Darkened pastel coral for failed
          break;
        case "soft delete":
          color = "#F5B0B0"; // Darkened pastel red for soft delete
          break;
        case "delete":
          color = "#F8A6A6"; // Darkened pastel pink-red for delete
          break;
        default:
          color = "#D1D2D4"; // Soft grey for default (pending)
          break;
      }

      // Apply the background color to the row and all its cells
      $(row).css("background-color", color); // Apply background to row
      $(row)
        .find("td")
        .css("background-color", color + " !important"); // Apply to individual cells in case of overrides
    },
    fixedColumns: {
      leftColumns: 1, // Freeze the first column
      rightColumns: 1, // Freeze the last column
    },
    order: [[4, "desc"]], // Order by the fifth column (date)
  });

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
        start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"),
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
        cb,
      )
      .val(dateRangeValue);

    cb(start, end);
  }

  // Date picker without year
  $("body").on("focus", ".datePickerWithoutYear", function () {
    $(this)
      .daterangepicker({
        startDate: start,
        endDate: end,
        singleDatePicker: true,
        minViewMode: "months",
        locale: {
          format: "DD MMMM",
        },
      })
      .val(dateRangeValue);
  });
  // $(".datePickerWithoutYear")
  //   .daterangepicker({
  //     startDate: start,
  //     endDate: end,
  //     singleDatePicker: true,
  //     minViewMode: "months",
  //     locale: {
  //       format: "DD MMMM",
  //     },
  //   })
  //   .val(dateRangeValue);

  // Select js 2

  if ($(".selectJS").length) {
    $(".selectJS").select2({
      placeholder: "Select options",
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
      // console.log(`showCurrentBox: `, showCurrentBox);
      // console.log(`showCurrentLable: `, showCurrentLable);
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

  $(".detailsBtn").click(function (e) {
    e.preventDefault();
    handleButtonClick(this);
  });

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

  $("#convertToSale").click(function (e) {
    e.preventDefault();
    $("#convertToSale").hide();
    let leadId = $(this).data("id");
    if (!leadId) {
      popUpMessage("center", "Lead ID is missing", "warning");
      $("#convertToSale").show();
      return;
    } else {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Convert to sales",
      }).then((result) => {
        if (result.value) {
          $.ajax({
            type: "GET",
            url: "/api/leads/confirm/?id=" + leadId,
            dataType: "json",
            success: function (response) {
              popUpMessage("center", response.msg, response.status);
              $("#convertToSale").show();
              setTimeout(function () {
                window.location.reload();
              }, 1500);
            },
            error: function (response) {
              console.log(`Error : `, response);
              $("#convertToSale").show();
            },
          });
        } else {
          $("#convertToSale").show();
        }
      });
    }
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
    const quotationFor = $("#quotationFor").text().trim();
    if (quotationFor !== "") {
      const $destination = $("#itineraryDestination");

      // Check if the value exists already
      if ($destination.find(`option[value="${quotationFor}"]`).length === 0) {
        // Add new option if not found
        $destination.append(
          $("<option></option>").val(quotationFor).text(quotationFor),
        );
      }

      // Now set it
      $destination.val(quotationFor);

      // Auto-click the fetch button
      $("#fetchItinerary").trigger("click");
    }
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
    toggleDisplay("#leadDetailsBox", true);
    e.preventDefault();
  });

  $("#noteDetailsBtn").click(function (e) {
    toggleDisplay("#noteDetailsBox", false);
    e.preventDefault();
  });

  $("#paymentDetailsBtn").click(function (e) {
    toggleDisplay("#paymentDetailsBox", false);
    e.preventDefault();
  });

  $("#leadEditBtn").click(function (e) {
    toggleDisplay("#leadEditBox", false);
    e.preventDefault();
  });

  $("#additionalPassengerBtn").click(function (e) {
    toggleDisplay("#additionalPassengerBox", false);
    e.preventDefault();
  });

  $("#documentBtn").click(function (e) {
    toggleDisplay("#documentBox", false);
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

  // Update payment records

  $("#upatePaymentRecord").click(function (e) {
    e.preventDefault();

    var data = new FormData(document.getElementById("updatePaymentForm"));
    data.append("leadId", leadId);

    $.ajax({
      url: "/api/leads/payment_record/",
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
          error.responseJSON.status,
        );
        $("#addAmenities").show();
      },
    });
  });

  // Update leads data

  $("#updateEnquiry").click(function (e) {
    $("#updateEnquiry").hide();
    var formData = $("#updateLeadForm").serialize();
    $.ajax({
      type: "POST",
      url: "/api/leads/update/",
      dataType: "json",
      data: formData,
      success: function (response) {
        $("#updateEnquiry").show();
        popUpMessage("center", response.msg, response.status);
        if (response.status == "success") {
          setTimeout(function () {
            const leadIdBase64 = btoa(response.leadId);
            window.location.href = `/dashboard/leads/view/?id=${leadIdBase64}`;
          }, 1500);
        }
      },
      error: function (response) {
        $("#updateEnquiry").show();
        console.log(`Error : `, response);
      },
    });
    hidePageLoader();
    e.preventDefault();
  });

  $(".duplicateLead").click(function (e) {
    event.preventDefault();
    var disableRedirect = false;
    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, proceed",
    }).then((result) => {
      if (result.value) {
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
      }
    });
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
    hidePageLoader();
    e.preventDefault();
    disableButtonWithCTA("#sendPaymentLink", "Sending payment link...");
    var selectedValue = "";
    selectedValue = $('input[name="paymentLink"]:checked').val();
    if (selectedValue == 0) {
      selectedValue = $("#paymentValue").val();
    }

    if (selectedValue == "") {
      selectedValue = $("#quotationPrice").val();
    }
    const amount = parseFloat(selectedValue);
    if (!isNaN(amount) && isFinite(amount)) {
      const quotationId = $("#quotationId").val();
      $.ajax({
        type: "GET",
        url:
          "/api/payment/request/?amount=" +
          amount +
          "&quotationId=" +
          btoa(quotationId),
        dataType: "json",
        success: function (response) {
          popUpMessage(
            "center",
            "Payment link sent to customer",
            response.status,
          );
          $(".paymentLinkBox").hide();
          enableButtonWithCTA(
            "#sendPaymentLink",
            "<i class='fi-rr-paper-plane'></i> Send payment link",
          );
        },
        error: function (errorResponse) {
          enableButtonWithCTA(
            "#sendPaymentLink",
            "<i class='fi-rr-paper-plane'></i> Send payment link",
          );
          popUpMessage(
            "center",
            errorResponse.responseJSON.msg,
            response.status,
          );
          console.log(`Error : `);
        },
      });
    } else {
      console.log(`selectedValue: `, selectedValue);

      popUpMessage("center", "Please enter valid amount", "warning");
      enableButtonWithCTA(
        "#sendPaymentLink",
        "<i class='fi-rr-paper-plane'></i> Send payment link",
      );
    }
  });

  var stateDropDown = document.getElementById("stateDropDown");

  if ($("#stateDropDown")) {
    $("#state").on("change", function (e) {
      e.preventDefault();
      var stateId = $("#state").val();
      $.ajax({
        type: "GET",
        url: "/api/city/?state_id=" + stateId,
        dataType: "json",
        success: function (response) {
          $("#cityBox").show();
          $("#city").empty();
          $("#city").append(new Option("Select city", "select"));
          response.cityData.forEach(function (city) {
            $("#city").append(new Option(city.name, city.id));
          });
        },
        error: function (response) {
          console.log(`Error : `, response);
        },
      });
    });
  }

  // Add new company

  $("#addCompany").click(function (e) {
    e.preventDefault();

    const name = $("#name").val().trim();
    const address = $("#address").val().trim();
    const state = $("#state").val();
    const city = $("#city").val();
    const pinCode = $("#pinCode").val().trim();
    const email = $("#email").val().trim();
    const mobile = $("#mobile").val().trim();
    const companyType = $("#companyType").val().trim();
    const fileInput = document.getElementById("fileInput");

    // Validation
    if (!name) return showWarning("Kindly enter company name", "#name");
    if (!address)
      return showWarning("Kindly enter company address", "#address");
    if (state === "select" || !state)
      return showWarning("Kindly select company state", "#state");
    if (city === "select" || !city)
      return showWarning("Kindly select company city", "#city");
    if (!pinCode) return showWarning("Kindly enter pin code", "#pinCode");
    if (!email) return showWarning("Kindly enter company email", "#email");
    if (!mobile) return showWarning("Kindly enter company mobile", "#mobile");
    if (companyType === "select" || !companyType)
      return showWarning("Kindly select company Type", "#companyType");

    // Build FormData
    const formData = new FormData($("#addNewCompanyForm")[0]);
    formData.append("featureImage", fileInput.files[0]);

    // Submit AJAX
    $.ajax({
      type: "POST",
      url: "/api/company/create/",
      dataType: "json",
      processData: false,
      contentType: false,
      data: formData,
      success: function (res) {
        popUpMessage("center", res.msg, res.status);
        if (res.status === "success") {
          setTimeout(() => {
            const companyId = btoa(res.companyId);
            window.location.href = `/dashboard/company/update/?id=${companyId}`;
          }, 1500);
        }
      },
      error: function (err) {
        console.error("❌ Error:", err);
        const res = err.responseJSON || {
          msg: "Unknown error",
          status: "error",
        };
        popUpMessage("center", res.msg, res.status);
      },
    });
  });

  $("#updateCompany").click(function (e) {
    e.preventDefault();

    const $btn = $(this);
    const name = $("#name").val().trim();
    const address = $("#address").val().trim();
    const state = $("#state").val() === "select" ? "" : $("#state").val();
    const city = $("#city").val() === "select" ? "" : $("#city").val();
    const pinCode = $("#pinCode").val().trim();
    const email = $("#email").val().trim();
    const mobile = $("#mobile").val().trim();
    const companyType = $("#companyType").val().trim();
    const fileInput = document.getElementById("fileInput");
    const oldGallery = $("#oldGallery").text().trim();

    if (!name) return showWarning("Kindly enter company name", "#name");
    if (!address)
      return showWarning("Kindly enter company address", "#address");
    if (!state)
      return showWarning("Kindly select state of company address", "#state");
    if (!city) return showWarning("Kindly enter company address city", "#city");
    if (!pinCode)
      return showWarning("Kindly enter company address pin code", "#pinCode");
    if (!email) return showWarning("Kindly enter company email", "#email");
    if (!mobile) return showWarning("Kindly enter company mobile", "#mobile");
    if (!companyType)
      return showWarning("Kindly enter company type", "#companyType");

    if (!fileInput) {
      popUpMessage(
        "center",
        "File input not found. Please reload the page.",
        "error",
      );
      return;
    }

    const selectedFile = fileInput.files[0];

    if (!oldGallery && !selectedFile) {
      popUpMessage("center", "Please upload a company feature image.", "error");
      return;
    }

    const formData = new FormData($("#updateCompanyForm")[0]);
    if (selectedFile) formData.append("featureImage", selectedFile);
    formData.append("oldFeatureImage", oldGallery);

    $btn.prop("disabled", true).text("Updating...");

    $.ajax({
      type: "POST",
      url: "/api/company/update/",
      dataType: "json",
      processData: false,
      contentType: false,
      data: formData,
      success: function (res) {
        popUpMessage("center", res.msg, res.status);
        if (res.status === "success") {
          setTimeout(() => {
            window.location.href = `/dashboard/company/list/`;
          }, 1500);
        }
      },
      error: function (xhr) {
        console.error("❌ Error:", xhr);
        const errorMsg =
          xhr.responseJSON && xhr.responseJSON.msg
            ? xhr.responseJSON.msg
            : "Something went wrong. Please try again.";
        popUpMessage("center", errorMsg, "error");
      },
      complete: function () {
        $btn.prop("disabled", false).text("Update Company");
      },
    });
  });

  $("#sendKYCRequest").click(function (e) {
    e.preventDefault();
    var requestId = $(this).attr("data-id");
    $.ajax({
      type: "GET",
      url: `/api/auth/request-kyc/?id=` + requestId,
      dataType: "json",
      success: function (successResponse) {
        popUpMessage("center", successResponse.msg, successResponse.status);
      },
      error: function (response) {
        console.log(`Error : `, response);
        popUpMessage(
          "center",
          response.responseJSON.msg,
          response.responseJSON.status,
        );
      },
    });
  });

  // For category

  if ($("#categoryFormV2").length) {
    $("#title").on("input", function () {
      let title = $(this).val().trim();

      // Convert title to URL-friendly permalink
      let permalink = title
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-") // Remove special characters
        .replace(/\s+/g, "-") // Replace spaces with hyphens
        .replace(/-+/g, "-"); // Collapse multiple hyphens

      $("#permalink").val(permalink);
    });

    $("#submitCategoryV2").on("click", function (e) {
      e.preventDefault();

      console.log(`dfsdf`);

      const $btn = $(this);
      $btn.prop("disabled", true).text("Submitting...");

      const title = $("#title").val().trim();
      const permalink = $("#permalink").val().trim();
      var description = $("#editor-0").val() || "";
      const parentCategory = $("#parentCategory").val().trim();
      const action = $("#action").val().trim();

      if (!title) {
        popUpMessage("center", "Please enter a title.", "warning");
        $btn.prop("disabled", false).text("Submit");
        return;
      }

      if (!permalink) {
        popUpMessage("center", "Please enter a permalink.", "warning");
        $btn.prop("disabled", false).text("Submit");
        return;
      }

      const formData = {
        name: title,
        url: permalink,
        category: parentCategory,
        description,
        type: "category",
        action,
      };

      const categoryId = $("#categoryID").val();
      if (categoryId) {
        formData.categoryID = categoryId; // Add to formData
      }
      const oldURL = $("#oldUrl").val();
      if (oldURL) {
        formData.oldURL = oldURL; // Add to formData
      }
      const postType = $("#postType").val();
      if (postType) {
        formData.postType = postType; // Add to formData
      }

      console.log(`formData`, formData);

      $.ajax({
        type: "POST",
        dataType: "json",
        data: formData,
        success: function (data) {
          if (data.status == 200) {
            if (data.postType == "post") {
              data.postType = "blog";
            }
            popUpMessage("center", data.msg, "success");
            window.setTimeout(function () {
              window.location.href =
                "/dashboard/" + data.postType + "/categories/";
            }, 1500);
          } else {
            popUpMessage("center", data.msg, "warning");
            $("#submitCategoryV2").show();
          }
        },
        error: function (data) {
          $("#submitCategoryV2").show();
          popUpMessage("center", data.responseJSON.msg, "error");
        },
      });
    });
  }

  if ($("#blogFormV2").length) {
    // Auto-generate permalink from title input
    $("#title").on("input", function () {
      let title = $(this).val().trim();

      let permalink = title
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-")
        .replace(/^-|-$/g, "");

      $("#permalink").val(permalink);
    });

    // Trigger file input on drop zone click
    $("#dropZone").on("click", function () {
      $("#fileInput").click();
    });

    // Submit blog form
    $("#addBlogV2").click(function (e) {
      e.preventDefault();

      $("#addBlogV2").hide(); // Disable submit button

      // Get description safely from textarea
      var description = $("#editor-0").val() || "";
      $("#editor-0").val(description.trim());

      const fileInput = document.getElementById("fileInput");
      const formData = new FormData(document.getElementById("blogFormV2"));
      formData.append("action", "publish");

      // Append image file if selected
      if (fileInput.files.length > 0) {
        formData.append("featureImage", fileInput.files[0]); // single file, not array
      }

      $.ajax({
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "JSON",
        success: function (response) {
          if (response.status == 200) {
            popUpMessage("center", response.msg, "success");
            setTimeout(function () {
              window.location.href = "/dashboard/blog/list/";
            }, 1600);
          } else {
            popUpMessage("center", response.msg, "warning");
          }
          $("#addBlogV2").show(); // Re-enable button
        },
        error: function (error) {
          popUpMessage("center", error.responseJSON.msg, "error");
          $("#addBlogV2").show();
        },
      });
    });

    // Submit blog form
    $("#updateBlogV2").click(function (e) {
      e.preventDefault();

      $("#updateBlogV2").hide(); // Disable submit button

      // Get description safely from textarea
      var description = $("#editor-0").val() || "";
      $("#editor-0").val(description.trim());

      const fileInput = document.getElementById("fileInput");
      const formData = new FormData(document.getElementById("blogFormV2"));
      formData.append("action", "publish");

      // Append image file if selected
      if (fileInput.files.length > 0) {
        formData.append("featureImage", fileInput.files[0]); // single file, not array
      }

      $.ajax({
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "JSON",
        success: function (response) {
          if (response.status == 200) {
            popUpMessage("center", response.msg, "success");
            setTimeout(function () {
              window.location.href = "/dashboard/blog/list/";
            }, 1600);
          } else {
            popUpMessage("center", response.msg, "warning");
          }
          $("#updateBlogV2").show(); // Re-enable button
        },
        error: function (error) {
          popUpMessage("center", error.responseJSON.msg, "error");
          $("#updateBlogV2").show();
        },
      });
    });
  }

  if ($("#importData").length) {
    $("#startImportProperty").on("click", function (e) {
      e.preventDefault();
      uploadCSV("fileInput", "property", "startImportProperty");
    });
  }

  if ($("#showFilterPopUp").length) {
    $("#showFilterPopUp").on("click", function (e) {
      e.preventDefault();
      $(".filterBox").show();
    });

    $("#applyFilterOnLeads").on("click", function (e) {
      e.preventDefault();

      function showLoader() {
        var $loader = $(".pageLoader");
        if ($loader.length) {
          $loader.removeClass("hidden").css("display", "flex");
        } else {
          // Fallback overlay (no external assets)
          var spinnerCSS = "@keyframes _spin{to{transform:rotate(360deg)}}";
          if (!$("style[data-loader]").length) {
            $("head").append("<style data-loader>" + spinnerCSS + "</style>");
          }
          var $fallback = $(
            '<div class="pageLoader" style="position:fixed;inset:0;background:#fff;display:flex;align-items:center;justify-content:center;z-index:99999">' +
              '<div style="width:44px;height:44px;border:4px solid #e5e7eb;border-top-color:#167a92;border-radius:50%;animation:_spin 1s linear infinite"></div>' +
              "</div>",
          );
          $("body").append($fallback);
        }
      }

      // Parse current query into an object (no URL API needed)
      function getQueryObject() {
        var q = window.location.search.replace(/^\?/, "");
        var obj = {};
        if (!q) return obj;
        q.split("&").forEach(function (pair) {
          if (!pair) return;
          var kv = pair.split("=");
          var k = decodeURIComponent(kv[0] || "");
          var v = decodeURIComponent(kv[1] || "");
          if (k) obj[k] = v;
        });
        return obj;
      }

      var params = getQueryObject();

      // Read fields
      var fields = {
        leadStatus: $("#filterStatus").val(),
        leadName: $("#filterName").val(),
        leadEmail: $("#filterEmail").val(),
        leadMobile: $("#filterMobile").val(),
        daterange: $("#filterDuration").val(),
      };

      // Merge: set if value, delete if empty
      $.each(fields, function (key, val) {
        var v = $.trim(val || "");
        if (v) {
          params[key] = v;
        } else {
          delete params[key];
        }
      });

      // Build query string & navigate (reload)
      var qs = $.param(params); // jQuery encodes properly
      var newUrl =
        window.location.pathname + (qs ? "?" + qs : "") + window.location.hash;
      showLoader();
      window.location.assign(newUrl);
    });
  }

  loadExternalJS("/app/dashboard/v2/default/desktop/assets/js/policy.min.js");
});
