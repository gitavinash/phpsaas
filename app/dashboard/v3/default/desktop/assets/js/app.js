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

/** SweetAlert Pop Up Box & Global Normalizer */
(function() {
  // SweetAlert2 exports itself as window.Sweetalert2, window.swal, and window.Swal
  // but Object.assign misses prototype / non-enumerable methods like .fire().
  // Ensure window.Swal always has a working .fire() method.

  var sw2 = window.Sweetalert2 || null;

  if (!sw2 && typeof window.swal === 'function' && typeof window.swal.fire === 'function') {
    // SweetAlert2 loaded directly as window.swal with .fire already
    sw2 = window.swal;
  }

  if (sw2 && typeof sw2.fire === 'function') {
    // Canonical SweetAlert2 — expose consistently as both Swal and swal
    window.Swal = sw2;
    window.swal = sw2;
    return;
  }

  // Legacy SweetAlert v1 (window.swal is a plain function, no .fire)
  if (typeof window.swal === 'function') {
    var originalSwal = window.swal;
    var wrappedSwal = function(firstArg, secondArg, thirdArg) {
      if (typeof firstArg === 'object' && firstArg !== null) {
        var opts = Object.assign({}, firstArg);
        if (opts.icon && !opts.type) { opts.type = opts.icon; }
        if (opts.type && !opts.icon) { opts.icon = opts.type; }
        if (opts.button && !opts.confirmButtonText) { opts.confirmButtonText = opts.button; }
        return originalSwal.call(this, opts);
      } else if (typeof firstArg === 'string' && thirdArg) {
        return originalSwal.call(this, { title: firstArg, text: secondArg || '', type: thirdArg, icon: thirdArg });
      }
      return originalSwal.apply(this, arguments);
    };
    Object.assign(wrappedSwal, originalSwal);

    // Polyfill .fire() so all code using Swal.fire({...}).then(...) works
    wrappedSwal.fire = function(opts) {
      return wrappedSwal.call(window, opts);
    };

    window.swal = wrappedSwal;
    window.Swal = wrappedSwal;
  }
})();


function popUpMessage(msgPostion, msgTxt, msgType) {
  if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
    Swal.fire({
      position: msgPostion || 'center',
      type: msgType || 'info',
      icon: msgType || 'info',
      title: msgTxt,
      showConfirmButton: false,
      timer: 1800,
    });
  } else if (typeof window.swal === 'function') {
    window.swal({
      title: msgTxt,
      type: msgType || 'info',
      icon: msgType || 'info',
      timer: 1800,
      showConfirmButton: false
    });
  } else {
    alert(msgTxt);
  }
}

function showWarning(message, selector) {
  popUpMessage("center", message, "warning");
  if (selector && $(selector).length) {
    $(selector).focus();
  }
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

// Modular Select2 and AutoComplete Initializer
function initSelect2Components() {
  if (typeof $.fn.select2 === "undefined") return;

  if ($(".indianCityAutoComplete").length) {
    initializeSelect2HereMaps(".indianCityAutoComplete");
  }

  if ($(".internationalCityAutoComplete").length) {
    initializeSelect2HereMapsForInternational(".internationalCityAutoComplete");
  }

  if ($(".addressAutoComplete").length) {
    initializeSelect2HereMapsWithCompleteAddress(".addressAutoComplete");
  }

  if ($(".selectJS").length) {
    $(".selectJS").each(function () {
      if (!$(this).hasClass("select2-hidden-accessible")) {
        $(this).select2();
      }
    });
  }
}
window.initSelect2Components = initSelect2Components;

// Modular Gallery Uploader Initializer
function initGalleryUploaders() {
  if (typeof $.fn.imageUploader === "undefined") return;

  if ($(".addGallery").length) {
    $(".addGallery").each(function () {
      if (!$(this).find(".image-uploader").length) {
        $(this).imageUploader({
          imagesInputName: "gallery",
          maxFiles: 50,
        });
      }
    });
  }

  if ($(".updateGallery").length && typeof oldGalleryImages !== "undefined") {
    $(".updateGallery").each(function () {
      if (!$(this).find(".image-uploader").length) {
        $(this).imageUploader({
          preloaded: oldGalleryImages,
          imagesInputName: "gallery",
          preloadedInputName: "oldGallery",
          maxSize: 2 * 1024 * 1024,
          maxFiles: 50,
        });
      }
    });
  }
}
window.initGalleryUploaders = initGalleryUploaders;

$(document).ready(function (e) {
  initSelect2Components();
  initGalleryUploaders();

  dateRangeParams = dateRangeParams === "" ? "" : dateRangeParams;
  if (dateRangeParams == null) {
    dateRangeValue = "";
  } else {
    dateRangeValue = dateRangeParams;
  }
  if (typeof tempId !== "undefined") {
    dateRangeParams += "&userId=" + tempId;
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
    let parentContainer = document.getElementById(objectID);

    // Fallback ID mapping for widget container variants
    if (!parentContainer) {
      if (objectID === "topEnquirySource") parentContainer = document.getElementById("sourceData");
      if (objectID === "enquiryPerformance") parentContainer = document.getElementById("enquiryPerformace");
    }

    if (!parentContainer) return;

    // Check if valid data exists and total sum > 0
    let hasData = false;
    if (chartData && chartData.labels && chartData.data && Array.isArray(chartData.data) && chartData.data.length > 0) {
      const sum = chartData.data.reduce((a, b) => a + (Number(b) || 0), 0);
      if (sum > 0) {
        hasData = true;
      }
    }

    if (!hasData) {
      parentContainer.innerHTML = `
        <div class="no-data-state">
          <div class="no-data-icon"><i class="fi-rr-folder-open"></i></div>
          <p class="no-data-title">No Data Available</p>
          <p class="no-data-desc">No records found for the selected reporting period</p>
        </div>
      `;
      return;
    }

    const actualID = parentContainer.id;

    // Restore 2-column layout structure if container previously showed empty state
    if (parentContainer.querySelector(".no-data-state")) {
      parentContainer.innerHTML = `
        <div class="chart-legend-side" id="${actualID}Legend"></div>
        <div class="pieChart">
          <canvas id="${actualID}Chart"></canvas>
        </div>
      `;
    }

    let rawLabels = chartData.labels || [];
    let rawCounts = (chartData.data || []).map((v) => Number(v) || 0);

    // Group items: Keep top 4 items, club remaining into "Others"
    let items = rawLabels.map((lbl, idx) => ({
      label: lbl,
      count: rawCounts[idx] || 0,
    }));

    items.sort((a, b) => b.count - a.count);

    let labels = [];
    let counts = [];

    const MAX_TOP_ITEMS = 4;
    if (items.length > MAX_TOP_ITEMS + 1) {
      let topItems = items.slice(0, MAX_TOP_ITEMS);
      let otherItems = items.slice(MAX_TOP_ITEMS);

      let otherSum = otherItems.reduce((sum, item) => sum + item.count, 0);

      topItems.forEach((item) => {
        labels.push(item.label);
        counts.push(item.count);
      });

      if (otherSum > 0) {
        labels.push("Others");
        counts.push(otherSum);
      }
    } else {
      items.forEach((item) => {
        labels.push(item.label);
        counts.push(item.count);
      });
    }

    const canvasId = `${actualID}Chart`;
    const legendId = `${actualID}Legend`;
    const canvasEl = document.getElementById(canvasId);
    const legendEl = document.getElementById(legendId);
    if (!canvasEl) return;

    let colors = {};
    const predefinedColors = [
      "#6366f1",
      "#10b981",
      "#0ea5e9",
      "#f59e0b",
      "#8b5cf6",
      "#ec4899",
      "#64748b",
      "#14b8a6",
    ];
    let nextColorIndex = 0;

    labels.forEach((label) => {
      const key = label.toLowerCase().trim();
      switch (key) {
        case "google adwords":
        case "google":
          colors[label] = "#0ea5e9";
          break;
        case "facebook":
          colors[label] = "#4f46e5";
          break;
        case "direct":
          colors[label] = "#64748b";
          break;
        case "fresh":
          colors[label] = "#3b82f6";
          break;
        case "pending":
          colors[label] = "#38bdf8";
          break;
        case "follow up":
        case "followup":
          colors[label] = "#8b5cf6";
          break;
        case "cancel":
        case "cancelled":
          colors[label] = "#ef4444";
          break;
        case "confirm":
        case "confirmed":
          colors[label] = "#10b981";
          break;
        case "duplicate":
          colors[label] = "#ec4899";
          break;
        default:
          if (nextColorIndex < predefinedColors.length) {
            colors[label] = predefinedColors[nextColorIndex];
            nextColorIndex++;
          } else {
            colors[label] = "#10b981";
          }
      }
    });

    var datasets = [
      {
        data: counts,
        backgroundColor: labels.map((label) => colors[label]),
        borderWidth: 2,
        borderColor: "#ffffff",
        hoverOffset: 4,
      },
    ];

    // Render custom HTML metric list on the left side
    if (legendEl) {
      const totalSum = counts.reduce((a, b) => a + b, 0);
      let legendHtml = "";
      labels.forEach((lbl, idx) => {
        const val = counts[idx] || 0;
        const pct = totalSum > 0 ? Math.round((val / totalSum) * 100) : 0;
        const color = colors[lbl] || "#6366f1";
        legendHtml += `
          <div class="legend-item-row">
            <div class="legend-item-left">
              <span class="legend-color-dot" style="background-color: ${color};"></span>
              <span class="legend-item-name">${lbl}</span>
            </div>
            <span class="legend-item-value">${val} <small style="color:var(--slate-400);font-weight:500;">(${pct}%)</small></span>
          </div>
        `;
      });
      legendEl.innerHTML = legendHtml;
    }

    if (typeof Chart !== "undefined") {
      const existingChart = typeof Chart.getChart === "function" ? Chart.getChart(canvasEl) : canvasEl._chartInstance;
      if (existingChart) {
        existingChart.destroy();
      }
    }

    var myChart = new Chart(
      canvasEl.getContext("2d"),
      {
        type: "doughnut",
        data: {
          labels: labels,
          datasets: datasets,
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          aspectRatio: 1,
          cutout: "68%",
          plugins: {
            legend: {
              display: false,
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
    canvasEl._chartInstance = myChart;
  }

  function createLineChart(
    objectID,
    chartLabel = "",
    currentDataLabel,
    currentData,
    currentDataColor = "#4f46e5",
    previousDataLabel,
    previousData,
    previousDataColor = "#e2e8f0",
    showBackGroundAreaColor = true,
    lineCurveTension = 0.4,
    border = 2,
    animationDuration = 1000,
    showTopLabel = false,
    showXLable = false,
    showYLable = false,
  ) {
    const canvasEl = document.getElementById(objectID);
    if (!canvasEl) return;
    const ctx = canvasEl.getContext("2d");

    let fillBg = currentDataColor;
    if (showBackGroundAreaColor && ctx) {
      const gradient = ctx.createLinearGradient(0, 0, 0, 160);
      gradient.addColorStop(0, "rgba(79, 70, 229, 0.22)");
      gradient.addColorStop(1, "rgba(79, 70, 229, 0.0)");
      fillBg = gradient;
    }

    if (typeof Chart !== "undefined") {
      const existingChart = typeof Chart.getChart === "function" ? Chart.getChart(canvasEl) : canvasEl._chartInstance;
      if (existingChart) {
        existingChart.destroy();
      }
    }

    var myChart = new Chart(ctx, {
      type: "line",
      data: {
        labels: chartLabel,
        datasets: [
          {
            label: currentDataLabel,
            data: currentData,
            tension: lineCurveTension,
            fill: showBackGroundAreaColor,
            backgroundColor: fillBg,
            borderColor: currentDataColor,
            borderWidth: border,
            pointRadius: 2,
            pointHoverRadius: 5,
            pointBackgroundColor: currentDataColor,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: showTopLabel,
          },
        },
        scales: {
          x: { display: showXLable, grid: { display: false } },
          y: { display: showYLable, grid: { color: "#f1f5f9" } },
        },
      },
    });
    canvasEl._chartInstance = myChart;
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
    const valueEl = document.getElementById(`${objectID}Value`);
    if (!valueEl) return;
    const rawVal = data ? (data.totalLeads ?? data.total ?? data.count ?? 0) : 0;
    const total = Number(rawVal) || 0;
    if (total === 0) {
      valueEl.textContent = "0";
      return;
    }
    const startVal = Math.max(0, total - Math.min(total, 10));
    increaseCountingNumberAnimation(`${objectID}Value`, startVal, total, 1, 86);
  }

  // High-Performance V3 Modular Dashboard API Engine
  let lastLoadedDashboardRange = null;

  // 1. Fresh Enquiries KPI
  function loadKpiFresh(daterange) {
    const $el = $("#totalLeadsValue");
    if (!$el.length) return;
    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/kpis/fresh/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (res && res.status === 200 && res.count !== undefined) {
          $el.text(res.count);
        }
      },
    });
  }

  // 2. Confirmed Enquiries KPI
  function loadKpiConfirmed(daterange) {
    const $el = $("#totalConfirmLeadsValue, #totalConfirmedLeadsValue");
    if (!$el.length) return;
    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/kpis/confirmed/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (res && res.status === 200 && res.count !== undefined) {
          $el.text(res.count);
        }
      },
    });
  }

  // 3. Pending Enquiries KPI
  function loadKpiPending(daterange) {
    const $el = $("#totalPendingLeadsValue");
    if (!$el.length) return;
    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/kpis/pending/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (res && res.status === 200 && res.count !== undefined) {
          $el.text(res.count);
        }
      },
    });
  }

  // 4. Follow Up Enquiries KPI
  function loadKpiFollowup(daterange) {
    const $el = $("#totalFollowupLeadsValue");
    if (!$el.length) return;
    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/kpis/followup/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (res && res.status === 200 && res.count !== undefined) {
          $el.text(res.count);
        }
      },
    });
  }

  // 5. Duplicate Enquiries KPI
  function loadKpiDuplicate(daterange) {
    const $el = $("#totalDuplicateLeadsValue");
    if (!$el.length) return;
    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/kpis/duplicate/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (res && res.status === 200 && res.count !== undefined) {
          $el.text(res.count);
        }
      },
    });
  }

  // 6. Cancelled Enquiries KPI
  function loadKpiCancel(daterange) {
    const $el = $("#totalCancelLeadsValue");
    if (!$el.length) return;
    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/kpis/cancel/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (res && res.status === 200 && res.count !== undefined) {
          $el.text(res.count);
        }
      },
    });
  }

  // 7. Pending Requests KPI
  function loadKpiPendingRequests(daterange) {
    const $el = $("#totalPendingRequestsValue, #totalPendingRequestValue");
    if (!$el.length) return;
    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/kpis/pending-requests/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (res && res.status === 200 && res.count !== undefined) {
          $el.text(res.count);
        }
      },
    });
  }

  // 8. Conversion Rate KPI
  function loadKpiConversionRate(daterange) {
    const $el = $("#kpiConversionRateValue");
    if (!$el.length) return;
    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/kpis/conversion-rate/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (res && res.status === 200 && res.conversionRate !== undefined) {
          $el.text(res.conversionRate);
        }
      },
    });
  }

  // 9. Modular Revenue Summary
  function loadDashboardRevenue(daterange) {
    if (!$("#kpiRevenueSummaryValue").length) return;
    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/revenue/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (!res || res.status !== 200) return;
        if ($("#kpiRevenueSummaryValue").length) $("#kpiRevenueSummaryValue").text(res.totalRevenue || "₹0");
      },
    });
  }

  // 10. Modular Enquiry Sources
  function loadDashboardSources(daterange) {
    if (!document.getElementById("topEnquirySource")) return;
    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/sources/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (!res || res.status !== 200) return;
        createPieChart("topEnquirySource", { labels: res.labels, data: res.data });
      },
    });
  }

  // 11. Modular Enquiry Performance
  function loadDashboardPerformance(daterange) {
    if (!document.getElementById("enquiryPerformance")) return;
    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/performance/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (!res || res.status !== 200) return;
        createPieChart("enquiryPerformance", { labels: res.labels, data: res.data });
      },
    });
  }

  // 12. Modular Timeline Flow Chart
  function loadDashboardFlow(daterange) {
    const flowContainer = document.getElementById("enquiryFlowContainer");
    if (!flowContainer && !document.getElementById("enquiryFlowCanvas")) return;

    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/flow/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (!res || res.status !== 200) return;
        let flowCanvas = document.getElementById("enquiryFlowCanvas");
        if (!flowCanvas && flowContainer) {
          flowContainer.innerHTML = '<canvas id="enquiryFlowCanvas"></canvas>';
          flowCanvas = document.getElementById("enquiryFlowCanvas");
        }
        if (!flowCanvas) return;

        const flowCtx = flowCanvas.getContext("2d");
        if (window.enquiryFlowChartInstance) {
          window.enquiryFlowChartInstance.destroy();
          window.enquiryFlowChartInstance = null;
        }
        if (typeof Chart !== "undefined") {
          const existingFlow = typeof Chart.getChart === "function" ? Chart.getChart(flowCanvas) : flowCanvas._chartInstance;
          if (existingFlow) existingFlow.destroy();
        }

        const flowLabels = res.labels || [];
        const flowData = res.data || [];

        if (flowLabels.length > 0) {
          window.enquiryFlowChartInstance = new Chart(flowCtx, {
            type: "line",
            data: {
              labels: flowLabels,
              datasets: [
                {
                  label: "Enquiries",
                  data: flowData,
                  borderColor: "#6366f1",
                  backgroundColor: "rgba(99, 102, 241, 0.08)",
                  borderWidth: 2.5,
                  fill: true,
                  tension: 0.35,
                  pointRadius: 4,
                  pointBackgroundColor: "#6366f1",
                  pointHoverRadius: 6,
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { display: false },
                tooltip: {
                  mode: "index",
                  intersect: false,
                  padding: 10,
                },
              },
              scales: {
                x: {
                  grid: { display: false },
                  ticks: { font: { size: 11 }, color: "#64748b" },
                },
                y: {
                  beginAtZero: true,
                  grid: { color: "#f1f5f9" },
                  ticks: { font: { size: 11 }, color: "#64748b" },
                },
              },
            },
          });
          flowCanvas._chartInstance = window.enquiryFlowChartInstance;
        } else if (flowContainer) {
          flowContainer.innerHTML = `
            <div class="no-data-state">
              <div class="no-data-icon"><i class="fi-rr-folder-open"></i></div>
              <p class="no-data-title">No Timeline Data Available</p>
              <p class="no-data-desc">No records found for the selected reporting period</p>
            </div>
          `;
        }
      },
    });
  }

  // 13. Modular Upcoming Follow-ups Table
  function loadDashboardFollowups() {
    const $followupsBody = $("#recentFollowupsTableBody");
    if (!$followupsBody.length) return;

    $.ajax({
      type: "GET",
      url: "/api/v3/dashboard/followups/",
      dataType: "json",
      success: function (res) {
        if (!res || res.status !== 200) return;
        const followupsList = Array.isArray(res.followups) ? res.followups : [];
        $followupsBody.empty();

        if (followupsList.length === 0) {
          $followupsBody.html(`
            <tr>
              <td colspan="6" style="text-align: center; padding: 24px; color: #64748b;">
                <i class="fi fi-rr-check-circle" style="color: #10b981; font-size: 20px; display: block; margin-bottom: 4px;"></i>
                No upcoming follow-ups scheduled.
              </td>
            </tr>
          `);
        } else {
          followupsList.forEach((item) => {
            const rowHtml = `
              <tr>
                <td><strong>${item.name}</strong></td>
                <td>${item.mobile !== "N/A" ? `<a href="tel:${item.mobile}"><i class="fi fi-rr-phone-call"></i> ${item.mobile}</a>` : '<span style="color:#94a3b8;">N/A</span>'}</td>
                <td>${item.subject}</td>
                <td><span class="v3-source-badge" style="background:#f1f5f9; color:#475569; padding:3px 8px; border-radius:6px; font-size:12px;">${item.source}</span></td>
                <td><span style="color:#0284c7; font-weight:600;"><i class="fi fi-rr-calendar"></i> ${item.time}</span></td>
                <td style="text-align: right;">
                  <a href="leads/view/?id=${item.encoded_id}" class="btn dataTableBtn" style="padding: 4px 10px; font-size: 12px;">
                    View Details
                  </a>
                </td>
              </tr>
            `;
            $followupsBody.append(rowHtml);
          });
        }
      },
    });
  }

  // 14. Modular Kanban Board Data & Interactive CTAs
  function renderKanbanCard(item, st) {
    const rawName = item.name && item.name.trim() !== "" ? item.name.trim() : (item.customer_name || item.lead_name || "Customer");
    const customerName = rawName.toLowerCase().replace(/\b\w/g, (c) => c.toUpperCase());
    const sourceTag = item.source ? `<span class="kanban-source-pill">${item.source}</span>` : "";
    const cleanPhone = (item.mobile && item.mobile !== "N/A") ? item.mobile.replace(/[^0-9+]/g, "") : "";
    const waPhone = cleanPhone.startsWith("+") ? cleanPhone.substring(1) : (cleanPhone.length === 10 ? "91" + cleanPhone : cleanPhone);
    const isMobile = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent) || window.innerWidth <= 768;

    // 1. Call Button (Mobile: direct call, Desktop: reveals/copies number)
    let callBtnHtml = "";
    if (cleanPhone) {
      if (isMobile) {
        callBtnHtml = `
          <a href="tel:${cleanPhone}" class="kanban-cta-btn cta-call" title="Call ${customerName}">
            <i class="fi fi-rr-phone-call"></i> Call
          </a>
        `;
      } else {
        callBtnHtml = `
          <button type="button" class="kanban-cta-btn cta-call btn-kanban-call-desktop" data-phone="${cleanPhone}" data-original-text="Call" title="Click to copy: ${item.mobile}">
            <i class="fi fi-rr-phone-call"></i> <span class="call-btn-label">Call</span>
          </button>
        `;
      }
    }

    // 2. WhatsApp CTA
    let whatsappBtnHtml = "";
    if (cleanPhone) {
      whatsappBtnHtml = `
        <a href="https://api.whatsapp.com/send?phone=${waPhone}&text=Hello%20${encodeURIComponent(customerName)},%20regarding%20your%20travel%20enquiry" target="_blank" class="kanban-cta-btn cta-whatsapp" title="Chat on WhatsApp">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: -1px; flex-shrink: 0;"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg> WhatsApp
        </a>
      `;
    }

    // 3. Quick Stage Advance Button (Explicit Clear Action)
    let advanceBtnHtml = "";
    if (st === "0") {
      advanceBtnHtml = `<button type="button" class="kanban-cta-btn cta-advance btn-kanban-advance" data-lead-id="${item.encoded_id}" data-target-status="2" data-stage-name="Pending" title="Move status to Pending"><i class="fi fi-rr-arrow-right"></i> Move to Pending</button>`;
    } else if (st === "2") {
      advanceBtnHtml = `<button type="button" class="kanban-cta-btn cta-advance btn-kanban-advance" data-lead-id="${item.encoded_id}" data-target-status="3" data-stage-name="Follow Up" title="Move status to Follow Up"><i class="fi fi-rr-arrow-right"></i> Move to Follow Up</button>`;
    } else if (st === "3") {
      advanceBtnHtml = `<button type="button" class="kanban-cta-btn cta-advance cta-confirm btn-kanban-advance" data-lead-id="${item.encoded_id}" data-target-status="1" data-stage-name="Confirmed" title="Confirm Booking for this lead"><i class="fi fi-rr-check-circle"></i> Confirm Booking</button>`;
    } else if (st === "1") {
      advanceBtnHtml = `<span class="kanban-won-badge"><i class="fi fi-rr-check-circle"></i> Confirmed Booking</span>`;
    }

    // 4. Assign Agent, Lost, and Duplicate Buttons (ONLY ON FRESH STAGE st === "0")
    let assignBtnHtml = "";
    let lostBtnHtml = "";
    let duplicateBtnHtml = "";

    if (st === "0") {
      assignBtnHtml = `<button type="button" class="kanban-cta-btn cta-assign btn-kanban-assign-trigger" data-lead-id="${item.encoded_id}" data-lead-name="${customerName}" title="Assign lead to team member"><i class="fi fi-rr-user"></i> Assign</button>`;
      lostBtnHtml = `<button type="button" class="kanban-cta-btn cta-lost btn-kanban-confirm-status" data-lead-id="${item.encoded_id}" data-status="5" data-action-name="Lost / Not Interested" title="Mark as Lost (Not Interested)"><i class="fi fi-rr-cross"></i> Lost</button>`;
      duplicateBtnHtml = `<button type="button" class="kanban-cta-btn cta-duplicate btn-kanban-confirm-status" data-lead-id="${item.encoded_id}" data-status="4" data-action-name="Duplicate" title="Mark as Duplicate"><i class="fi fi-rr-copy"></i> Duplicate</button>`;
    }

    // Primary Row CTAs
    const primaryCtas = [callBtnHtml, whatsappBtnHtml, assignBtnHtml].filter(Boolean).join("");
    // Secondary Stage Actions
    const secondaryCtas = [advanceBtnHtml, lostBtnHtml, duplicateBtnHtml].filter(Boolean).join("");

    const jsonStr = encodeURIComponent(JSON.stringify(item));

    return `
      <div class="kanban-card" draggable="true" data-lead-id="${item.encoded_id}" data-status="${st}" data-lead-json="${jsonStr}">
        <div class="kanban-card-head">
          <span class="kanban-customer-name">${customerName}</span>
          <div class="kanban-card-drag-indicator">
            ${sourceTag}
            <i class="fi fi-rr-apps-sort drag-grip-icon" title="Drag card"></i>
          </div>
        </div>
        <div class="kanban-card-subject">
          <i class="fi fi-rr-map-marker"></i> ${item.subject}
        </div>
        ${primaryCtas ? `<div class="kanban-quick-cta-row">${primaryCtas}</div>` : ""}
        ${secondaryCtas ? `<div class="kanban-quick-cta-row">${secondaryCtas}</div>` : ""}
        <div class="kanban-card-actions-bar">
          <span class="kanban-card-time"><i class="fi fi-rr-clock"></i> ${item.time}</span>
          <a href="leads/view/?id=${item.encoded_id}" class="btn-kanban-action">
            <i class="fi fi-rr-eye"></i> Details
          </a>
        </div>
      </div>
    `;
  }

  // SPA In-Place Kanban DOM Helpers
  function updateKanbanCounts() {
    const statuses = ["0", "2", "3", "1"];
    statuses.forEach((st) => {
      const count = $(`#kanban-container-${st}`).find(".kanban-card").length;
      $(`#kanban-count-${st}`).text(count);
    });
  }

  function checkKanbanEmptyStates() {
    const statuses = ["0", "2", "3", "1"];
    const labelMap = { "0": "fresh", "2": "pending", "3": "follow-up", "1": "confirmed" };
    statuses.forEach((st) => {
      const container = $(`#kanban-container-${st}`);
      if (container.find(".kanban-card").length === 0) {
        if (!container.find(".kanban-empty-drop-zone").length) {
          container.html(`<div class="kanban-empty-drop-zone">Drop ${labelMap[st] || "leads"} here</div>`);
        }
      } else {
        container.find(".kanban-empty-drop-zone").remove();
      }
    });
  }

  function moveKanbanCardDom(leadId, targetStatus) {
    const $card = $(`.kanban-card[data-lead-id="${leadId}"]`);
    if (!$card.length) return;

    const st = String(targetStatus);
    const targetContainer = $(`#kanban-container-${st}`);
    targetContainer.find(".kanban-empty-drop-zone").remove();

    let leadData = null;
    const rawJson = $card.attr("data-lead-json");
    if (rawJson) {
      try {
        leadData = JSON.parse(decodeURIComponent(rawJson));
      } catch (e) {
        try {
          leadData = JSON.parse(rawJson);
        } catch (err) {}
      }
    }

    if (!leadData || !leadData.encoded_id) {
      leadData = {
        encoded_id: leadId,
        name: $card.find(".kanban-customer-name").text().trim(),
        subject: $card.find(".kanban-card-subject").text().trim(),
        time: $card.find(".kanban-card-time").text().trim(),
        mobile: $card.find(".btn-kanban-call-desktop").attr("data-phone") || "",
        source: $card.find(".kanban-source-pill").text().trim(),
        status: st,
      };
    } else {
      leadData.status = st;
    }

    const newCardEl = $(renderKanbanCard(leadData, st));
    newCardEl.css("display", "none");
    targetContainer.prepend(newCardEl);
    newCardEl.fadeIn(250);

    $card.fadeOut(200, function () {
      $(this).remove();
      updateKanbanCounts();
      checkKanbanEmptyStates();
    });
  }

  function removeKanbanCardDom(leadId) {
    const $card = $(`.kanban-card[data-lead-id="${leadId}"]`);
    if (!$card.length) return;

    $card.fadeOut(250, function () {
      $(this).remove();
      updateKanbanCounts();
      checkKanbanEmptyStates();
    });
  }

  function renderKanbanBoardGrouped(boardData) {
    if (!boardData) return;
    const statuses = ["0", "2", "3", "1"];
    statuses.forEach((st) => {
      const container = $(`#kanban-container-${st}`);
      const countPill = $(`#kanban-count-${st}`);
      const items = Array.isArray(boardData[st]) ? boardData[st] : [];

      countPill.text(items.length);
      const columnCol = container.closest(".kanban-column");
      columnCol.find(".kanban-load-more-btn").remove();
      container.empty();

      if (items.length === 0) {
        const labelMap = { "0": "fresh", "2": "pending", "3": "follow-up", "1": "confirmed" };
        container.html(`<div class="kanban-empty-drop-zone">Drop ${labelMap[st] || "leads"} here</div>`);
      } else {
        items.forEach((item) => {
          container.append(renderKanbanCard(item, st));
        });

        if (items.length >= 10) {
          columnCol.append(`<button class="kanban-load-more-btn" data-status="${st}">Load More</button>`);
        }
      }
    });
  }

  function loadDashboardKanban(daterange) {
    if (!$(".kanban-widget-wrapper").length) return;

    $.ajax({
      type: "GET",
      url: "/api/v3/leads/kanban/?daterange=" + encodeURIComponent(daterange),
      dataType: "json",
      success: function (res) {
        if (!res) return;
        let boardData = res.data || res.grouped;
        if (Array.isArray(res.data) && !res.grouped) {
          boardData = { "0": [], "2": [], "3": [], "1": [] };
          res.data.forEach((lead) => {
            const st = String(lead.status);
            if (boardData[st]) {
              boardData[st].push(lead);
            }
          });
        }
        renderKanbanBoardGrouped(boardData);
      },
    });
  }

  // Dashboard Aggregator Orchestrator
  function loadDashboardV3Data(force) {
    const daterange = typeof dateRangeParams !== "undefined" ? dateRangeParams : "";
    if (!force && lastLoadedDashboardRange === daterange) {
      return;
    }
    lastLoadedDashboardRange = daterange;

    // Load each KPI independently
    loadKpiFresh(daterange);
    loadKpiConfirmed(daterange);
    loadKpiPending(daterange);
    loadKpiFollowup(daterange);
    loadKpiDuplicate(daterange);
    loadKpiCancel(daterange);
    loadKpiPendingRequests(daterange);
    loadKpiConversionRate(daterange);
    loadDashboardRevenue(daterange);

    // Load Charts, Follow-ups, and Kanban Board
    loadDashboardSources(daterange);
    loadDashboardPerformance(daterange);
    loadDashboardFlow(daterange);
    loadDashboardFollowups();
    loadDashboardKanban(daterange);
  }
  window.loadDashboardV3Data = loadDashboardV3Data;

  // Full-Screen Kanban Board Toggle
  $(document).on("click", "#toggleKanbanFullscreenBtn", function (e) {
    e.preventDefault();
    const wrapper = $("#kanbanWidgetWrapper");
    const isFS = wrapper.hasClass("kanban-fullscreen-mode");

    if (isFS) {
      wrapper.removeClass("kanban-fullscreen-mode");
      $(this).html('<i class="fi-rr-expand"></i> <span class="btn-fs-label">Full Screen</span>');
    } else {
      wrapper.addClass("kanban-fullscreen-mode");
      $(this).html('<i class="fi-rr-compress"></i> <span class="btn-fs-label">Exit Full Screen</span>');
    }
  });

  $(document).on("keydown", function (e) {
    if (e.key === "Escape" && $("#kanbanWidgetWrapper").hasClass("kanban-fullscreen-mode")) {
      $("#kanbanWidgetWrapper").removeClass("kanban-fullscreen-mode");
      $("#toggleKanbanFullscreenBtn").html('<i class="fi-rr-expand"></i> <span class="btn-fs-label">Full Screen</span>');
    }
  });

  // Handle Load More click for specific Kanban column status
  $(document).on("click", ".kanban-load-more-btn", function (e) {
    e.preventDefault();
    const btn = $(this);
    const status = btn.attr("data-status");
    const container = $(`#kanban-container-${status}`);
    const currentOffset = container.find(".kanban-card").length;

    btn.text("Loading...");

    $.ajax({
      type: "GET",
      url: `/api/v3/leads/kanban/?status=${status}&offset=${currentOffset}&limit=10&daterange=` + encodeURIComponent(typeof dateRangeParams !== "undefined" ? dateRangeParams : ""),
      dataType: "json",
      success: function (res) {
        if (res && res.data && Array.isArray(res.data) && res.data.length > 0) {
          res.data.forEach((item) => {
            container.append(renderKanbanCard(item, status));
          });
          btn.text("Load More");
        } else {
          btn.text("No more leads").prop("disabled", true).css("opacity", 0.6);
        }
      },
      error: function () {
        btn.text("Load More");
      },
    });
  });

  // CTA Event 1: Desktop Phone Click to Reveal and Copy Number
  $(document).on("click", ".btn-kanban-call-desktop", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const btn = $(this);
    const phone = btn.attr("data-phone");
    if (!phone) return;

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(phone);
    }
    btn.find(".call-btn-label").text(phone);
    btn.addClass("copied");

    setTimeout(() => {
      btn.find(".call-btn-label").text(btn.attr("data-original-text") || "Call");
      btn.removeClass("copied");
    }, 2500);
  });

  // CTA Event 2: Quick Status Change & Stage Advance with Clear Feedback
  $(document).on("click", ".btn-kanban-quick-status, .btn-kanban-advance", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const btn = $(this);
    const leadId = btn.attr("data-lead-id");
    const targetStatus = btn.attr("data-target-status") || btn.attr("data-status");
    const stageName = btn.attr("data-stage-name") || "next stage";
    if (!leadId || targetStatus === undefined) return;

    const originalHtml = btn.html();
    btn.html(`<i class="fi fi-rr-spinner fi-rr-spin"></i> Moving...`).prop("disabled", true).css("opacity", 0.7);

    $.ajax({
      type: "POST",
      url: "/api/v3/leads/update-status/",
      data: { leadId: leadId, status: targetStatus },
      dataType: "json",
      success: function (res) {
        if (res && res.status === 200) {
          moveKanbanCardDom(leadId, targetStatus);
        } else {
          btn.html(originalHtml).prop("disabled", false).css("opacity", 1);
        }
      },
      error: function () {
        btn.html(originalHtml).prop("disabled", false).css("opacity", 1);
      },
    });
  });

  // CTA Event 3: Confirm Status Change for Lost & Duplicate using SweetAlert
  $(document).on("click", ".btn-kanban-confirm-status", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const btn = $(this);
    const leadId = btn.attr("data-lead-id");
    const targetStatus = btn.attr("data-status");
    const actionName = btn.attr("data-action-name") || "this status";

    const doUpdate = () => {
      const originalHtml = btn.html();
      btn.html(`<i class="fi fi-rr-spinner fi-rr-spin"></i> Updating...`).prop("disabled", true).css("opacity", 0.6);

      $.ajax({
        type: "POST",
        url: "/api/v3/leads/update-status/",
        data: { leadId: leadId, status: targetStatus },
        dataType: "json",
        success: function (res) {
          if (res && res.status === 200) {
            removeKanbanCardDom(leadId);
            if (typeof Swal !== "undefined") {
              Swal.fire({
                type: "success",
                icon: "success",
                title: `Marked as ${actionName}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }
          } else {
            btn.html(originalHtml).prop("disabled", false).css("opacity", 1);
            if (typeof Swal !== "undefined") {
              Swal.fire({
                type: "error",
                icon: "error",
                title: "Failed to update",
                text: (res && res.msg) ? res.msg : "Could not update lead status.",
              });
            }
          }
        },
        error: function () {
          btn.html(originalHtml).prop("disabled", false).css("opacity", 1);
          if (typeof Swal !== "undefined") {
            Swal.fire({
              type: "error",
              icon: "error",
              title: "Error",
              text: "An error occurred while updating status.",
            });
          }
        },
      });
    };

    if (typeof Swal !== "undefined") {
      Swal.fire({
        title: `Mark as ${actionName}?`,
        text: "This lead will be removed from the active pipeline.",
        type: "warning",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: targetStatus === "5" ? "#dc2626" : "#64748b",
        cancelButtonColor: "#94a3b8",
        confirmButtonText: `Yes, mark as ${actionName}`,
        cancelButtonText: "Cancel",
      }).then((result) => {
        if (result && (result.value || result.isConfirmed === true || result === true)) {
          doUpdate();
        }
      });
    } else {
      doUpdate();
    }
  });

  // Helper functions for user avatars in Select2
  function getInitials(name) {
    if (!name) return "U";
    const parts = name.trim().split(" ");
    if (parts.length >= 2) {
      return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return name.slice(0, 2).toUpperCase();
  }

  function getAvatarColor(str) {
    const colors = ["#0284c7", "#0d9488", "#7c3aed", "#d97706", "#e11d48", "#2563eb", "#059669", "#4f46e5"];
    let hash = 0;
    for (let i = 0; i < (str || "").length; i++) {
      hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colors[Math.abs(hash) % colors.length];
  }

  // CTA Event 4: Quick Assign Agent Modal (Rich Select2 UI with Leads List parity)
  $(document).on("click", ".btn-kanban-assign-trigger", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const btn = $(this);
    const leadId = btn.attr("data-lead-id");
    const leadName = btn.attr("data-lead-name") || "Lead";

    // Clean up any existing overlay
    if ($("#kanbanAssignSelect").data("select2")) {
      try { $("#kanbanAssignSelect").select2("destroy"); } catch (err) {}
    }
    $("#kanbanAssignOverlay").remove();

    // Fetch active team members
    $.ajax({
      type: "GET",
      url: "/api/v3/leads/agents/",
      dataType: "json",
      success: function (res) {
        const agents = (res && Array.isArray(res.agents)) ? res.agents : [];
        let optionsHtml = '<option value="0">-- Select Team Member --</option>';
        
        agents.forEach((ag) => {
          const initials = getInitials(ag.name);
          const color = getAvatarColor(ag.name || ag.email);
          const role = ag.role || "Team Member";
          optionsHtml += `<option value="${ag.id}" data-name="${ag.name}" data-email="${ag.email || ''}" data-role="${role}" data-initials="${initials}" data-color="${color}">${ag.name} (${ag.email || role})</option>`;
        });

        const modalHtml = `
          <div class="kanban-assign-overlay" id="kanbanAssignOverlay">
            <div class="kanban-assign-card">
              <div class="kanban-assign-header">
                <div class="assign-icon-bubble">
                  <i class="fi fi-rr-user-add"></i>
                </div>
                <div>
                  <h4 class="kanban-assign-heading">Assign Lead</h4>
                  <p class="kanban-assign-subheading">Assign <span class="assign-lead-tag">${leadName}</span> to a team member</p>
                </div>
                <button type="button" class="btn-close-assign" id="closeKanbanAssignBtn" title="Close dialog">&times;</button>
              </div>
              <div class="assign-form-group">
                <label>Select Team Member</label>
                <select id="kanbanAssignSelect" class="selectJS form-control" style="width: 100%;">
                  ${optionsHtml}
                </select>
              </div>
              <div class="kanban-assign-footer">
                <button type="button" class="btn-assign-cancel" id="cancelKanbanAssignBtn">Cancel</button>
                <button type="button" class="btn-assign-submit" id="submitKanbanAssignBtn" data-lead-id="${leadId}">
                  <i class="fi fi-rr-check"></i> Assign Lead
                </button>
              </div>
            </div>
          </div>
        `;
        $("body").append(modalHtml);

        // Initialize Select2 with custom rich template
        if (typeof $.fn.select2 !== "undefined") {
          $("#kanbanAssignSelect").select2({
            dropdownParent: $("#kanbanAssignOverlay"),
            placeholder: "Search team member by name or email...",
            width: "100%",
            templateResult: function (data) {
              if (!data.id || data.id === "0") {
                return data.text;
              }
              const elem = $(data.element);
              const name = elem.attr("data-name") || data.text;
              const email = elem.attr("data-email") || "";
              const role = elem.attr("data-role") || "Team Member";
              const initials = elem.attr("data-initials") || "U";
              const color = elem.attr("data-color") || "#0284c7";

              return $(`
                <div class="select2-user-item">
                  <div class="select2-user-avatar" style="background-color: ${color};">${initials}</div>
                  <div class="select2-user-meta">
                    <span class="select2-user-name">${name}</span>
                    <div class="select2-user-sub">
                      <span class="select2-user-role-badge">${role}</span>
                      ${email ? `<span>• ${email}</span>` : ""}
                    </div>
                  </div>
                </div>
              `);
            },
            templateSelection: function (data) {
              if (!data.id || data.id === "0") {
                return data.text;
              }
              const elem = $(data.element);
              const name = elem.attr("data-name") || data.text;
              const email = elem.attr("data-email") || "";
              return `${name} ${email ? "(" + email + ")" : ""}`;
            },
            escapeMarkup: function (m) {
              return m;
            },
          });
        }
      },
    });
  });

  $(document).on("click", "#closeKanbanAssignBtn, #cancelKanbanAssignBtn", function () {
    if ($("#kanbanAssignSelect").data("select2")) {
      try { $("#kanbanAssignSelect").select2("destroy"); } catch (err) {}
    }
    $("#kanbanAssignOverlay").remove();
  });

  $(document).on("click", "#submitKanbanAssignBtn", function () {
    const submitBtn = $(this);
    const leadId = submitBtn.attr("data-lead-id");
    const agentId = $("#kanbanAssignSelect").val();
    const selectedOption = $("#kanbanAssignSelect option:selected");
    const agentName = selectedOption.attr("data-name") || selectedOption.text();

    if (!agentId || agentId === "0") {
      if (typeof Swal !== "undefined") {
        Swal.fire({
          type: "warning",
          icon: "warning",
          title: "Select Team Member",
          text: "Please select a user to assign this lead to.",
        });
      }
      return;
    }

    submitBtn.html('<i class="fi fi-rr-spinner fi-rr-spin"></i> Assigning...').prop("disabled", true);

    $.ajax({
      type: "POST",
      url: "/api/v3/leads/assign/",
      data: { leadId: leadId, agentId: agentId },
      dataType: "json",
      success: function (res) {
        if ($("#kanbanAssignSelect").data("select2")) {
          try { $("#kanbanAssignSelect").select2("destroy"); } catch (err) {}
        }
        $("#kanbanAssignOverlay").remove();

        if (res && res.status === 200) {
          moveKanbanCardDom(leadId, "2");
          if (typeof Swal !== "undefined") {
            Swal.fire({
              type: "success",
              icon: "success",
              title: "Lead Assigned",
              text: `Lead successfully assigned to ${agentName}.`,
              showConfirmButton: false,
              timer: 1500,
            });
          }
        } else {
          if (typeof Swal !== "undefined") {
            Swal.fire({
              type: "error",
              icon: "error",
              title: "Assignment Failed",
              text: (res && res.message) ? res.message : ((res && res.msg) ? res.msg : "Failed to assign lead to user."),
            });
          }
        }
      },
      error: function () {
        submitBtn.html('<i class="fi fi-rr-check"></i> Assign Lead').prop("disabled", false);
        if (typeof Swal !== "undefined") {
          Swal.fire({
            type: "error",
            icon: "error",
            title: "Error",
            text: "An error occurred during assignment.",
          });
        }
      },
    });
  });

  // Legacy Enquiry performance & pending request fallback (only if not v3 dashboard)
  if (!$("#totalLeadsValue").length && $("#enquiryPerformace").length) {
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

  if (!$("#totalLeadsValue").length && ($("#totalPendingRequests").length || $("#totalPendingRequest").length)) {
    const targetId = $("#totalPendingRequests").length ? "totalPendingRequests" : "totalPendingRequest";
    $.ajax({
      type: "GET",
      url: "/api/report/pending-request/?daterange=" + dateRangeParams,
      dataType: "json",
      success: function (response) {
        dashboardWidgetWithoutChart(targetId, response);
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

  // Modular DataTables Initialization Engine (SPA Compatible)
  function initDataTables() {
    if (typeof $.fn.DataTable === "undefined") return;

    if ($.fn.dataTable && $.fn.dataTable.ext) {
      $.fn.dataTable.ext.errMode = "none";
    }

    if ($(".showTableList").length) {
      $(".showTableList").each(function () {
        if ($(this).hasClass("package-v3-datatable") || $(this).closest(".package-table-card").length > 0) {
          return;
        }
        if (!$.fn.DataTable.isDataTable(this)) {
          const isPkgV3 = $(this).hasClass("package-v3-datatable") || $(this).closest(".package-table-card").length > 0;
          $(this).DataTable({
            pageLength: 50,
            fixedHeader: false,
            responsive: false,
            searching: true,
            lengthChange: false,
            autoWidth: false,
            ordering: false,
            scrollX: isPkgV3 ? false : true,
            scrollCollapse: isPkgV3 ? false : true,
            columnDefs: [
              {
                targets: "_all",
                createdCell: function (td, cellData, rowData, row, col) {
                  $(td).css({
                    "white-space": "normal",
                    "word-wrap": "break-word",
                  });
                },
              },
            ],
            createdRow: function (row, data, dataIndex) {
              var rowDataStatus = (data[data.length - 2] || "").toLowerCase().replace(/[^a-z0-9]/g, "_");
              if (rowDataStatus) {
                $(row).addClass("dt-status-row-" + rowDataStatus);
              }
            },
          });
        }
      });
    }

    if ($(".showTableListWithoutFilter").length) {
      $(".showTableListWithoutFilter").each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
          $(this).DataTable({
            columnDefs: [{ type: "date", targets: 4 }],
            order: [[4, "desc"]],
          });
        }
      });
    }
  }
  window.initDataTables = initDataTables;

  // Package Management Real-Time Filter Engine (SPA Compatible)
  function initPackageTableFilters() {
    if (typeof $.fn.DataTable === "undefined") return;

    if (Array.isArray($.fn.dataTable.ext.search)) {
      $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function (fn) {
        return fn._isPkgFilter !== true;
      });

      var pkgCustomFilter = function (settings, data, dataIndex) {
        if (!settings.nTable || !$(settings.nTable).hasClass("package-v3-datatable")) {
          return true;
        }

        var searchVal = $.trim($("#pkgTableSearch").val()).toLowerCase();
        var statusVal = $.trim($("#filterStatus").val()).toLowerCase();
        var themeVal = $.trim($("#filterTheme").val()).toLowerCase();
        var durVal = $.trim($("#filterDuration").val()).toLowerCase();
        var hotelVal = $.trim($("#filterHotel").val()).toLowerCase();
        var destVal = $.trim($("#filterDestination").val()).toLowerCase();

        var rowNode = settings.aoData[dataIndex] ? settings.aoData[dataIndex].nTr : null;
        var rowStatus = rowNode ? (rowNode.getAttribute("data-status") || "") : (data[2] || "").toLowerCase();
        var rowTheme = rowNode ? (rowNode.getAttribute("data-theme") || "") : (data[0] || "").toLowerCase();
        var rowDuration = rowNode ? parseInt(rowNode.getAttribute("data-duration") || "0", 10) : 0;
        var rowHotel = rowNode ? (rowNode.getAttribute("data-hotel") || "") : (data[1] || "").toLowerCase();
        var rowCity = rowNode ? (rowNode.getAttribute("data-city") || "") : (data[1] || "").toLowerCase();

        if (!rowDuration && data[0]) {
          var dMatch = data[0].match(/(\d+)\s*n/i);
          if (dMatch) rowDuration = parseInt(dMatch[1], 10);
        }

        var rowFullText = (data[0] + " " + data[1] + " " + data[2] + " " + rowStatus + " " + rowTheme + " " + rowHotel + " " + rowCity).toLowerCase();

        // 1. Text Search
        if (searchVal && rowFullText.indexOf(searchVal) === -1) {
          return false;
        }

        // 2. Status Filter
        if (statusVal && rowStatus !== statusVal && rowStatus.indexOf(statusVal) === -1) {
          return false;
        }

        // 3. Theme Filter
        if (themeVal && rowTheme !== themeVal && rowTheme.indexOf(themeVal) === -1) {
          return false;
        }

        // 4. Duration Filter
        if (durVal === "short" && (rowDuration < 1 || rowDuration > 3)) {
          return false;
        }
        if (durVal === "medium" && (rowDuration < 4 || rowDuration > 6)) {
          return false;
        }
        if (durVal === "long" && rowDuration < 7) {
          return false;
        }

        // 5. Hotel Star Category Filter
        if (hotelVal && rowHotel.indexOf(hotelVal) === -1) {
          return false;
        }

        // 6. Destination Filter
        if (destVal && rowCity.indexOf(destVal) === -1) {
          return false;
        }

        return true;
      };
      pkgCustomFilter._isPkgFilter = true;
      $.fn.dataTable.ext.search.push(pkgCustomFilter);
    }
  }
  window.initPackageTableFilters = initPackageTableFilters;

  // Delegated Package Table Filter Events
  $(document).off("input.pkgFilter keyup.pkgFilter", "#pkgTableSearch").on("input.pkgFilter keyup.pkgFilter", "#pkgTableSearch", function () {
    $("#clearSearchBtn").toggle(Boolean($.trim($(this).val())));
    $(".package-v3-datatable").each(function () {
      if ($.fn.DataTable.isDataTable(this)) {
        $(this).DataTable().draw();
      }
    });
  });

  $(document).off("click.pkgFilter", "#clearSearchBtn").on("click.pkgFilter", "#clearSearchBtn", function (e) {
    e.preventDefault();
    $("#pkgTableSearch").val("").trigger("input").focus();
  });

  $(document).off("change.pkgFilter", "#filterStatus, #filterTheme, #filterDuration, #filterHotel, #filterDestination").on("change.pkgFilter", "#filterStatus, #filterTheme, #filterDuration, #filterHotel, #filterDestination", function () {
    $(".package-v3-datatable").each(function () {
      if ($.fn.DataTable.isDataTable(this)) {
        $(this).DataTable().draw();
      }
    });
  });

  $(document).off("click.pkgFilter", "#resetPkgFilters").on("click.pkgFilter", "#resetPkgFilters", function (e) {
    e.preventDefault();
    $("#pkgTableSearch").val("");
    $("#filterStatus").val("");
    $("#filterTheme").val("");
    $("#filterDuration").val("");
    $("#filterHotel").val("");
    $("#filterDestination").val("");
    $("#clearSearchBtn").hide();
    $(".package-v3-datatable").each(function () {
      if ($.fn.DataTable.isDataTable(this)) {
        $(this).DataTable().draw();
      }
    });
  });

  // Global Lightweight Floating Tooltip Engine (Strictly Buttons & Hyperlinks)
  function initGlobalTooltips() {
    let $tooltip = $("#v3GlobalTooltip");
    if (!$tooltip.length) {
      $tooltip = $('<div id="v3GlobalTooltip" class="v3-global-tooltip" role="tooltip"></div>');
      $("body").append($tooltip);
    }

    const selector = "a[data-tooltip], button[data-tooltip], a[title], button[title], a.btn-action-icon, button.btn-action-icon, a.btn-action-pill, button.btn-action-pill, a.btn-filter-reset, button.btn-filter-reset, .btn-reset-filters-v3, .btn-lead-view-cta, .btn-lead-icon-action, .lead-wa-btn-vibrant, .lead-contact-link, .lead-email-link, .clear-search-btn, .clear-date-btn, .btn-empty-reset-filters, .lead-page-btn";

    $(document).off("mouseenter.v3Tooltip", selector).on("mouseenter.v3Tooltip", selector, function () {
      const $el = $(this);

      // Ensure tooltips only show on actual buttons and hyperlinks
      if (!$el.is("a, button, [role='button'], .btn-action-icon, .btn-action-pill, .btn-filter-reset, .btn-reset-filters-v3, .btn-lead-view-cta, .btn-lead-icon-action, .lead-wa-btn-vibrant, .clear-search-btn, .clear-date-btn, .btn-empty-reset-filters, .lead-page-btn")) {
        return;
      }

      let text = $el.attr("data-tooltip");

      if (!text && $el.attr("title")) {
        text = $el.attr("title");
        $el.data("v3-orig-title", text);
        $el.removeAttr("title"); // Suppress native browser tooltip
      }

      if (!text) {
        text = $el.data("v3-orig-title");
      }

      if (!text || !text.trim()) return;

      $tooltip.text(text.trim()).removeClass("placement-bottom visible");

      const rect = this.getBoundingClientRect();
      const tooltipHeight = $tooltip.outerHeight() || 28;
      const tooltipWidth = $tooltip.outerWidth() || 80;

      let top = rect.top - 7;
      let left = rect.left + rect.width / 2;
      let isBottom = false;

      if (top - tooltipHeight < 10) {
        top = rect.bottom + 7;
        isBottom = true;
      }

      if (left - tooltipWidth / 2 < 10) {
        left = 10 + tooltipWidth / 2;
      } else if (left + tooltipWidth / 2 > window.innerWidth - 10) {
        left = window.innerWidth - 10 - tooltipWidth / 2;
      }

      $tooltip.css({
        top: top + "px",
        left: left + "px",
      });

      if (isBottom) {
        $tooltip.addClass("placement-bottom");
      }

      requestAnimationFrame(() => {
        $tooltip.addClass("visible");
      });
    });

    $(document).off("mouseleave.v3Tooltip click.v3Tooltip", selector).on("mouseleave.v3Tooltip click.v3Tooltip", selector, function () {
      $tooltip.removeClass("visible");
      const orig = $(this).data("v3-orig-title");
      if (orig && !$(this).attr("title")) {
        $(this).attr("title", orig);
      }
    });

    // Auto-dismiss tooltip on scroll, modal/select2 open, or user click outside
    $(window).off("scroll.v3Tooltip").on("scroll.v3Tooltip", function () {
      $tooltip.removeClass("visible");
    });
    $(document).off("select2:opening select2:closing mousedown.v3Tooltip spa:loaded.v3Tooltip").on("select2:opening select2:closing mousedown.v3Tooltip spa:loaded.v3Tooltip", function () {
      $tooltip.removeClass("visible");
    });
  }
  window.initGlobalTooltips = initGlobalTooltips;

  // Modular DateRangePicker Initialization Engine (SPA Compatible)
  function initDateRangePicker() {
    if (!$(".dateRangePicker").length || typeof moment === "undefined" || typeof $.fn.daterangepicker === "undefined") return;

    let start = moment().startOf("month");
    let end = moment().endOf("month");

    if (dateRangeParams) {
      const parts = dateRangeParams.split(" - ");
      if (parts.length === 2) {
        const pStart = moment(parts[0], ["DD-MMM-YYYY", "YYYY-MM-DD", "D MMM YYYY"]);
        const pEnd = moment(parts[1], ["DD-MMM-YYYY", "YYYY-MM-DD", "D MMM YYYY"]);
        if (pStart.isValid() && pEnd.isValid()) {
          start = pStart;
          end = pEnd;
        }
      }
    }

    function getFriendlyPeriodLabel(s, e) {
      if (!s || !e) return "This Month";
      const today = moment().startOf("day");
      const sDay = s.clone().startOf("day");
      const eDay = e.clone().startOf("day");

      if (sDay.isSame(today) && eDay.isSame(today)) return "Today";
      const yesterday = today.clone().subtract(1, "days");
      if (sDay.isSame(yesterday) && eDay.isSame(yesterday)) return "Yesterday";
      const last7 = today.clone().subtract(6, "days");
      if (sDay.isSame(last7) && eDay.isSame(today)) return "Last 7 Days";
      const last30 = today.clone().subtract(29, "days");
      if (sDay.isSame(last30) && eDay.isSame(today)) return "Last 30 Days";
      const startThisMonth = today.clone().startOf("month");
      const endThisMonth = today.clone().endOf("month");
      if (sDay.isSame(startThisMonth) && (eDay.isSame(endThisMonth) || eDay.isSame(today))) return "This Month";
      const startLastMonth = today.clone().subtract(1, "month").startOf("month");
      const endLastMonth = today.clone().subtract(1, "month").endOf("month");
      if (sDay.isSame(startLastMonth) && eDay.isSame(endLastMonth)) return "Last Month";

      return "Custom Range";
    }

    function cb(s, e) {
      if (!s || !e) return;
      const formattedText = s.format("D MMM YYYY") + " - " + e.format("D MMM YYYY");
      $(".dateRangePicker span").html(formattedText);

      const friendlyLabel = getFriendlyPeriodLabel(s, e);
      $(".selected-range-text, #selectedRangeText").text(friendlyLabel);

      const diffDays = Math.abs(e.diff(s, "days")) + 1;
      let durationText = `${diffDays} Days`;
      if (diffDays === 1) {
        durationText = "1 Day";
      } else if (diffDays >= 28 && diffDays <= 31) {
        durationText = "1 Month";
      } else if (diffDays > 31) {
        const months = Math.round(diffDays / 30);
        durationText = months === 1 ? "1 Month" : `${months} Months`;
      }
      $("#dateDurationTag").text(durationText);
      dateRangeParams = s.format("DD-MMM-YYYY") + " - " + e.format("DD-MMM-YYYY");
      dateRangeValue = dateRangeParams;

      if ($("#totalLeadsValue").length || $(".kanban-widget-wrapper").length || $("#enquiryFlowCanvas").length) {
        loadDashboardV3Data();
      }
    }

    $(".dateRangePicker").each(function () {
      const $el = $(this);
      if ($el.data("daterangepicker")) {
        $el.data("daterangepicker").remove();
      }

      $el.daterangepicker(
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
      );

      cb(start, end);
    });
  }
  window.initDateRangePicker = initDateRangePicker;

  // Real-Time Lead Kanban Board Drag & Drop System
  function loadKanbanBoardData() {
    if (!$(".kanban-widget-wrapper").length) return;

    $.ajax({
      type: "GET",
      url: "/api/v3/leads/kanban/?daterange=" + encodeURIComponent(dateRangeParams),
      dataType: "json",
      success: function (res) {
        if (res && res.data) {
          const statuses = ["0", "2", "3", "1"];
          statuses.forEach((st) => {
            const container = $(`#kanban-container-${st}`);
            const countPill = $(`#kanban-count-${st}`);
            const items = res.data[st] || [];

            countPill.text(items.length);
            container.empty();

            if (items.length === 0) {
              const labelMap = {
                "0": "fresh",
                "2": "pending",
                "3": "follow-up",
                "1": "confirmed",
              };
              container.html(`<div class="kanban-empty-drop-zone">Drop ${labelMap[st] || "leads"} here</div>`);
            } else {
              items.forEach((item) => {
                const sourceTag = item.source ? `<span class="kanban-source-pill">${item.source}</span>` : "";
                const cardHtml = `
                  <div class="kanban-card" draggable="true" data-lead-id="${item.encoded_id}" data-status="${st}">
                    <div class="kanban-card-head">
                      <span class="kanban-customer-name">${item.name}</span>
                      ${sourceTag}
                    </div>
                    <div class="kanban-card-subject">${item.subject}</div>
                    <div class="kanban-card-foot">
                      <span><i class="fi-rr-clock"></i> ${item.time}</span>
                      <a href="leads/view/?id=${item.encoded_id}"><i class="fi-rr-eye"></i> View</a>
                    </div>
                  </div>
                `;
                container.append(cardHtml);
              });
            }
          });
        }
      },
      error: function (err) {
        console.log("Kanban API load error:", err);
      },
    });
  }

  function initKanbanBoard() {
    let draggedCard = null;

    $(document).off("dragstart.kanban").on("dragstart.kanban", ".kanban-card", function (e) {
      draggedCard = this;
      $(this).addClass("is-dragging");
      if (e.originalEvent && e.originalEvent.dataTransfer) {
        e.originalEvent.dataTransfer.setData("text/plain", $(this).attr("data-lead-id"));
        e.originalEvent.dataTransfer.effectAllowed = "move";
      }
    });

    $(document).on("dragend", ".kanban-card", function () {
      $(this).removeClass("is-dragging");
      draggedCard = null;
      $(".kanban-column").removeClass("drag-over");
    });

    $(document).on("dragover", ".kanban-column, .kanban-cards-container", function (e) {
      e.preventDefault();
      if (e.originalEvent && e.originalEvent.dataTransfer) {
        e.originalEvent.dataTransfer.dropEffect = "move";
      }
      $(this).closest(".kanban-column").addClass("drag-over");
    });

    $(document).on("dragleave", ".kanban-column", function () {
      $(this).removeClass("drag-over");
    });

    $(document).on("drop", ".kanban-column, .kanban-cards-container", function (e) {
      e.preventDefault();
      $(".kanban-column").removeClass("drag-over");

      const columnEl = $(this).closest(".kanban-column");
      const targetContainer = columnEl.find(".kanban-cards-container");
      const newStatus = columnEl.attr("data-status");

      if (draggedCard && newStatus !== undefined) {
        const oldStatus = $(draggedCard).attr("data-status");
        if (oldStatus === newStatus) return;

        const leadId = $(draggedCard).attr("data-lead-id");

        // Send AJAX request to update lead status in database via API v3
        $.ajax({
          type: "POST",
          url: "/api/v3/leads/update-status/",
          data: {
            leadId: leadId,
            status: newStatus,
          },
          success: function (res) {
            moveKanbanCardDom(leadId, newStatus);
          },
          error: function (err) {
            console.log("Lead status API update notice:", err);
          },
        });
      }
    });

    function updateKanbanCounts() {
      $(".kanban-column").each(function () {
        const status = $(this).attr("data-status");
        const count = $(this).find(".kanban-card").length;
        $(`#kanban-count-${status}`).text(count);
      });
    }
  }

  // Kanban Real-time Instant & Database Deep Search
  let kanbanSearchTimer = null;
  let kanbanSearchXhr = null;

  $(document).on("input", "#kanbanSearchInput, .kanban-search-input", function () {
    const rawVal = $(this).val();
    const query = rawVal.trim();
    const clearBtn = $("#kanbanSearchClearBtn, .kanban-search-clear-btn");

    if (query.length > 0) {
      clearBtn.show();
    } else {
      clearBtn.hide();
    }

    // Step 1: Instant Client-Side DOM filter on currently rendered cards
    const statuses = ["0", "2", "3", "1"];
    let totalClientMatches = 0;
    statuses.forEach((st) => {
      const container = $(`#kanban-container-${st}`);
      const cards = container.find(".kanban-card");
      let visibleCount = 0;

      cards.each(function () {
        const card = $(this);
        let leadText = card.text();
        const rawJson = card.attr("data-lead-json");
        if (rawJson) {
          try {
            const parsed = JSON.parse(decodeURIComponent(rawJson));
            leadText += " " + Object.values(parsed).join(" ");
          } catch (e) {
            try {
              const parsed = JSON.parse(rawJson);
              leadText += " " + Object.values(parsed).join(" ");
            } catch (err) {}
          }
        }
        leadText = leadText.toLowerCase();

        if (!query || leadText.includes(query.toLowerCase())) {
          card.removeClass("is-hidden-by-search");
          visibleCount++;
          totalClientMatches++;
        } else {
          card.addClass("is-hidden-by-search");
        }
      });

      const countPill = $(`#kanban-count-${st}`);
      countPill.text(query ? visibleCount : cards.length);

      container.find(".kanban-no-search-results").remove();
      if (query && cards.length > 0 && visibleCount === 0) {
        container.append('<div class="kanban-no-search-results">Searching database...</div>');
      }
    });

    // Step 2: Debounced Server-side Database Search
    if (kanbanSearchTimer) {
      clearTimeout(kanbanSearchTimer);
    }
    if (kanbanSearchXhr) {
      kanbanSearchXhr.abort();
      kanbanSearchXhr = null;
    }

    if (!query) {
      // Search cleared -> restore original date range board
      const daterange = typeof dateRangeParams !== "undefined" ? dateRangeParams : "";
      loadDashboardKanban(daterange);
      return;
    }

    kanbanSearchTimer = setTimeout(() => {
      kanbanSearchXhr = $.ajax({
        type: "GET",
        url: "/api/v3/leads/kanban/?search=" + encodeURIComponent(query),
        dataType: "json",
        success: function (res) {
          // Verify user hasn't changed query while request was in-flight
          const currentVal = $("#kanbanSearchInput, .kanban-search-input").val().trim();
          if (currentVal !== query) return;

          if (res && res.data) {
            renderKanbanBoardGrouped(res.data);

            let totalFound = 0;
            statuses.forEach((st) => {
              totalFound += (res.data[st] ? res.data[st].length : 0);
            });

            if (totalFound === 0) {
              statuses.forEach((st) => {
                const container = $(`#kanban-container-${st}`);
                container.html(`<div class="kanban-no-search-results">No matching leads in database</div>`);
              });
            }
          }
        },
        error: function (xhr, status) {
          if (status === "abort") return;
        },
      });
    }, 300);
  });

  $(document).on("click", "#kanbanSearchClearBtn, .kanban-search-clear-btn", function (e) {
    e.preventDefault();
    $("#kanbanSearchInput, .kanban-search-input").val("").trigger("input");
  });

  initKanbanBoard();

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

  // Master SPA Lifecycle Initializer
  function initAppComponents() {
    // 1. Sync URL Query Parameters
    const urlParams = new URLSearchParams(window.location.search);
    dateRangeParams = urlParams.get("daterange") || "";
    dateRangeValue = dateRangeParams;
    leadId = urlParams.get("id");
    if (typeof tempId !== "undefined" && tempId) {
      if (!dateRangeParams.includes("userId=")) {
        dateRangeParams += (dateRangeParams ? "&" : "") + "userId=" + tempId;
      }
    }

    // 2. Auto-initialize DateRangePicker
    if (typeof initDateRangePicker === "function") {
      initDateRangePicker();
    }

    // 3. Auto-initialize Dashboard V3 Data & Charts if on dashboard
    if (
      $("#totalLeadsValue").length ||
      $("#enquiryFlowCanvas").length ||
      $(".kanban-widget-wrapper").length ||
      $(".kpi-grid").length
    ) {
      loadDashboardV3Data();
    }

    // 4. Auto-initialize DataTables
    if (typeof initDataTables === "function") {
      initDataTables();
    }

    // 5. Auto-initialize Select2 & AutoComplete
    if (typeof initSelect2Components === "function") {
      initSelect2Components();
    }

    // 6. Auto-initialize Gallery Uploaders
    if (typeof initGalleryUploaders === "function") {
      initGalleryUploaders();
    }

    // 7. Auto-initialize Package Table Filter Engine
    if (typeof initPackageTableFilters === "function") {
      initPackageTableFilters();
    }

    // 8. Auto-initialize Global High-Contrast Tooltip Engine
    if (typeof initGlobalTooltips === "function") {
      initGlobalTooltips();
    }
  }
  window.initAppComponents = initAppComponents;

  // Prevent full-page submission on dashboard datepicker form
  $(document).on("submit", ".widgetForm", function (e) {
    if ($("#totalLeadsValue").length || $(".kanban-widget-wrapper").length || $("#enquiryFlowCanvas").length) {
      e.preventDefault();
      loadDashboardV3Data();
      return false;
    }
  });

  // V3 Dropdown Toggle & Outside Click Dismissal
  $(document).on("click", ".btn-cta-more", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const $wrapper = $(this).closest(".v3-dropdown-wrapper");
    $(".v3-dropdown-wrapper").not($wrapper).removeClass("active");
    $wrapper.toggleClass("active");
  });

  $(document).on("click", function (e) {
    if (!$(e.target).closest(".v3-dropdown-wrapper").length) {
      $(".v3-dropdown-wrapper").removeClass("active");
    }
  });

  // Modular V3 Script Registry & Component Auto-Initializer (Instant SPA Support)
  const v3LoadedScripts = {};

  function ensureScriptLoaded(url, callback) {
    if (v3LoadedScripts[url] === "loaded") {
      if (typeof callback === "function") callback();
      return;
    }
    if (Array.isArray(v3LoadedScripts[url])) {
      v3LoadedScripts[url].push(callback);
      return;
    }
    v3LoadedScripts[url] = [callback];
    $.ajax({
      url: url,
      dataType: "script",
      cache: true,
      success: function () {
        const callbacks = v3LoadedScripts[url];
        v3LoadedScripts[url] = "loaded";
        if (Array.isArray(callbacks)) {
          callbacks.forEach(function (cb) {
            if (typeof cb === "function") cb();
          });
        }
      },
      error: function () {
        v3LoadedScripts[url] = null;
      },
    });
  }

  window.initV3PageModules = function () {
    const ADMIN_JS = "/app/dashboard/v3/default/desktop/assets/js/";

    // 1. Package Gallery Module
    const $gallery = $("#packageGalleryApp, .package-gallery-wrapper");
    if ($gallery.length > 0) {
      if (window.V3Gallery && typeof window.V3Gallery.autoInit === "function") {
        window.V3Gallery.autoInit();
      } else {
        ensureScriptLoaded(ADMIN_JS + "gallery.js", function () {
          if (window.V3Gallery && typeof window.V3Gallery.autoInit === "function") {
            window.V3Gallery.autoInit();
          }
        });
      }
    }

    // 2. Tour Packages & DataTable Module
    const $packages = $("#packageListApp, .package-list-wrapper");
    if ($packages.length > 0) {
      if (window.V3Packages && typeof window.V3Packages.autoInit === "function") {
        window.V3Packages.autoInit();
      } else {
        ensureScriptLoaded(ADMIN_JS + "table.js", function () {
          ensureScriptLoaded(ADMIN_JS + "packages.js", function () {
            if (window.V3Packages && typeof window.V3Packages.autoInit === "function") {
              window.V3Packages.autoInit();
            }
          });
        });
      }
    }

    // 3. Package Edit Module
    const $pkgEdit = $("#packageEditApp, .package-edit-wrapper");
    if ($pkgEdit.length > 0) {
      if (window.V3PackageEdit && typeof window.V3PackageEdit.autoInit === "function") {
        window.V3PackageEdit.autoInit();
      } else if (window.V3PackageEdit && typeof window.V3PackageEdit.init === "function") {
        window.V3PackageEdit.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "package-edit.js", function () {
          if (window.V3PackageEdit && typeof window.V3PackageEdit.autoInit === "function") {
            window.V3PackageEdit.autoInit();
          } else if (window.V3PackageEdit && typeof window.V3PackageEdit.init === "function") {
            window.V3PackageEdit.init();
          }
        });
      }
    }

    // 4. Package Price List Module
    const $pkgPrices = $("#packagePriceListApp, .package-price-list-wrapper");
    if ($pkgPrices.length > 0) {
      if (window.V3PackagePriceList && typeof window.V3PackagePriceList.autoInit === "function") {
        window.V3PackagePriceList.autoInit();
      } else {
        ensureScriptLoaded(ADMIN_JS + "package-price-list.js?v=" + Date.now(), function () {
          if (window.V3PackagePriceList && typeof window.V3PackagePriceList.autoInit === "function") {
            window.V3PackagePriceList.autoInit();
          }
        });
      }
    }

    // 4b. Package Hotel List Module
    const $pkgHotels = $("#packageHotelListApp, .package-hotel-list-wrapper");
    if ($pkgHotels.length > 0) {
      if (window.V3PackageHotelList && typeof window.V3PackageHotelList.init === "function") {
        window.V3PackageHotelList.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "package-hotel-list.js?v=" + Date.now(), function () {
          if (window.V3PackageHotelList && typeof window.V3PackageHotelList.init === "function") {
            window.V3PackageHotelList.init();
          }
        });
      }
    }

    // 4c. Package Transport & Transporter Module
    const $pkgTransport = $("#packageTransportApp, .package-transport-wrapper");
    if ($pkgTransport.length > 0) {
      if (window.V3PackageTransport && typeof window.V3PackageTransport.init === "function") {
        window.V3PackageTransport.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "package-transport.js?v=" + Date.now(), function () {
          if (window.V3PackageTransport && typeof window.V3PackageTransport.init === "function") {
            window.V3PackageTransport.init();
          }
        });
      }
    }

    // 4d. Transporters Directory Module
    const $transporters = $("#transportersListApp, .transporter-list-wrapper");
    if ($transporters.length > 0) {
      if (window.V3Transporters && typeof window.V3Transporters.init === "function") {
        window.V3Transporters.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "transporters.js?v=" + Date.now(), function () {
          if (window.V3Transporters && typeof window.V3Transporters.init === "function") {
            window.V3Transporters.init();
          }
        });
      }
    }

    // 4e. Transporter Add Module
    const $transporterAdd = $("#addTransporterApp, .transporter-form-wrapper");
    if ($transporterAdd.length > 0) {
      if (window.V3TransporterAdd && typeof window.V3TransporterAdd.init === "function") {
        window.V3TransporterAdd.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "transporter-add.js?v=" + Date.now(), function () {
          if (window.V3TransporterAdd && typeof window.V3TransporterAdd.init === "function") {
            window.V3TransporterAdd.init();
          }
        });
      }
    }

    // 4f. Transporter Fleet & Vehicles Master Module
    const $vehiclesMaster = $("#vehiclesMasterApp, .vehicle-master-wrapper");
    if ($vehiclesMaster.length > 0) {
      if (window.V3TransporterVehicles && typeof window.V3TransporterVehicles.init === "function") {
        window.V3TransporterVehicles.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "transporter-vehicles.js?v=" + Date.now(), function () {
          if (window.V3TransporterVehicles && typeof window.V3TransporterVehicles.init === "function") {
            window.V3TransporterVehicles.init();
          }
        });
      }
    }

    // 4g. Team & Users Directory Module
    const $usersList = $("#usersListApp, .user-list-wrapper");
    if ($usersList.length > 0) {
      if (window.V3Users && typeof window.V3Users.init === "function") {
        window.V3Users.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "users.js?v=" + Date.now(), function () {
          if (window.V3Users && typeof window.V3Users.init === "function") {
            window.V3Users.init();
          }
        });
      }
    }

    // 4h. Team & User Forms Module (Add / Edit / KYC)
    const $userForms = $("#addUserApp, #editUserApp, #userKycApp, .user-form-wrapper");
    if ($userForms.length > 0) {
      if (window.V3UserForm && typeof window.V3UserForm.init === "function") {
        window.V3UserForm.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "user-form.js?v=" + Date.now(), function () {
          if (window.V3UserForm && typeof window.V3UserForm.init === "function") {
            window.V3UserForm.init();
          }
        });
      }
    }

    // 5. Leads Pipeline Module
    const $leads = $("#leadsListApp, .leads-page-wrapper");
    if ($leads.length > 0) {
      if (window.V3Leads && typeof window.V3Leads.init === "function") {
        window.V3Leads.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "leads.js?v=" + Date.now(), function () {
          if (window.V3Leads && typeof window.V3Leads.init === "function") {
            window.V3Leads.init();
          }
        });
      }
    }

    // 5b. Employee Performance Report Module
    const $empReport = $("#employeesReportApp, .employees-report-wrapper");
    if ($empReport.length > 0) {
      if (window.V3ReportEmployees && typeof window.V3ReportEmployees.init === "function") {
        window.V3ReportEmployees.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "report-employees.js?v=" + Date.now(), function () {
          if (window.V3ReportEmployees && typeof window.V3ReportEmployees.init === "function") {
            window.V3ReportEmployees.init();
          }
        });
      }
    }

    // 5c. Settings & Profile Module
    const $settings = $("#settingConfigApp, #settingProfileApp, .setting-page-wrapper");
    if ($settings.length > 0) {
      if (window.V3Settings && typeof window.V3Settings.init === "function") {
        window.V3Settings.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "settings.js?v=" + Date.now(), function () {
          if (window.V3Settings && typeof window.V3Settings.init === "function") {
            window.V3Settings.init();
          }
        });
      }
    }

    // 5. Lead Details View Module
    const $leadView = $("#leadViewApp, .lead-view-wrapper");
    if ($leadView.length > 0) {
      if (window.V3LeadView && typeof window.V3LeadView.init === "function") {
        window.V3LeadView.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "lead-view.js?v=" + Date.now(), function () {
          if (window.V3LeadView && typeof window.V3LeadView.init === "function") {
            window.V3LeadView.init();
          }
        });
      }
    }

    // 6. Integration Hub & Plugin Modules
    const $integration = $(
      "#integrationApp, .integration-page-wrapper, .integration-plugin-wrapper, [id^='plugin'], #fbLeadsApp, #fbPageLeadsApp"
    );
    if ($integration.length > 0) {
      if (window.V3Integration && typeof window.V3Integration.init === "function") {
        window.V3Integration.init();
      } else {
        ensureScriptLoaded(ADMIN_JS + "integration.js?v=" + Date.now(), function () {
          if (window.V3Integration && typeof window.V3Integration.init === "function") {
            window.V3Integration.init();
          }
        });
      }
    }
  };

  // Re-run component initialization on every SPA page load
  $(document).on("spa:loaded", function () {
    lastLoadedDashboardRange = null;
    if (typeof window.initAppComponents === "function") {
      window.initAppComponents();
    }
    if (typeof window.initV3PageModules === "function") {
      window.initV3PageModules();
    }
  });

  // Run on initial page load
  initAppComponents();
  window.initV3PageModules();

  loadExternalJS("/app/dashboard/v2/default/desktop/assets/js/policy.min.js");
});

/* ==========================================================================
   DASHBOARD V3 SPA ROUTER & SIDEBAR INTERACTION ENGINE
   ========================================================================== */
(function() {
  document.addEventListener('DOMContentLoaded', function () {
    const progressBar = document.getElementById('spa-loader-bar');
    const containerSelector = 'main .container';
    const sidebarSelector = '#sidebar';
    let isNavigating = false;

    function startProgress() {
      if (!progressBar) return;
      progressBar.classList.add('loading');
      progressBar.style.width = '25%';
      setTimeout(() => {
        if (progressBar && progressBar.classList.contains('loading')) {
          progressBar.style.width = '70%';
        }
      }, 150);
    }

    function endProgress() {
      if (!progressBar) return;
      progressBar.style.width = '100%';
      setTimeout(() => {
        if (progressBar) {
          progressBar.classList.remove('loading');
          progressBar.style.width = '0%';
        }
      }, 300);
    }

    function updateActiveSidebar(targetUrl) {
      try {
        const path = new URL(targetUrl, window.location.origin).pathname;
        const sidebar = document.querySelector(sidebarSelector);
        if (!sidebar) return;

        sidebar.querySelectorAll('li.active').forEach(el => el.classList.remove('active'));

        const links = sidebar.querySelectorAll('a[href]');
        let matchedLi = null;

        links.forEach(link => {
          const hrefAttr = link.getAttribute('href');
          if (!hrefAttr) return;
          const linkPath = new URL(hrefAttr, window.location.origin).pathname;
          if (linkPath === path || (path.startsWith(linkPath) && linkPath !== '/dashboard/')) {
            const li = link.closest('li');
            if (li) matchedLi = li;
          }
        });

        if (matchedLi) {
          matchedLi.classList.add('active');
        } else if (path === '/dashboard/' || path === '/dashboard') {
          const homeLink = sidebar.querySelector('a[href="/dashboard/"]');
          if (homeLink) {
            const homeLi = homeLink.closest('li');
            if (homeLi) homeLi.classList.add('active');
          }
        }
      } catch (e) {
        console.warn('Sidebar state update error:', e);
      }
    }

    async function loadPage(url, pushState = true) {
      if (isNavigating) return;
      isNavigating = true;

      const container = document.querySelector(containerSelector);
      if (container) container.classList.add('spa-navigating');
      startProgress();

      try {
        const response = await fetch(url, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-SPA-Request': '1'
          }
        });

        if (!response.ok) {
          window.location.href = url;
          return;
        }

        const htmlText = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlText, 'text/html');

        const newContainer = doc.querySelector(containerSelector);
        if (!newContainer) {
          window.location.href = url;
          return;
        }

        if (doc.title) {
          document.title = doc.title;
        }

        // Clean up any stray breadcrumbs outside container to prevent duplication
        document.querySelectorAll('main > .i-m-here-pagination').forEach(el => el.remove());

        container.innerHTML = newContainer.innerHTML;

        if (pushState) {
          window.history.pushState({ spa: true, url: url }, doc.title, url);
        }

        updateActiveSidebar(url);

        const scripts = container.querySelectorAll('script');
        scripts.forEach(oldScript => {
          const newScript = document.createElement('script');
          Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
          newScript.appendChild(document.createTextNode(oldScript.innerHTML));
          oldScript.parentNode.replaceChild(newScript, oldScript);
        });

        window.scrollTo({ top: 0, behavior: 'instant' });

        if (window.jQuery) {
          window.jQuery(document).trigger('spa:loaded');
        } else {
          if (typeof window.initAppComponents === 'function') {
            window.initAppComponents();
          }
          if (typeof window.initV3PageModules === 'function') {
            window.initV3PageModules();
          }
        }
      } catch (err) {
        console.error('SPA Router error:', err);
        window.location.href = url;
      } finally {
        if (container) container.classList.remove('spa-navigating');
        endProgress();
        isNavigating = false;
      }
    }

    // SPA link interceptor
    document.addEventListener('click', function (e) {
      const anchor = e.target.closest('a');
      if (!anchor) return;

      const href = anchor.getAttribute('href');
      if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
        return;
      }

      if (anchor.target === '_blank' || 
          anchor.hasAttribute('download') || 
          anchor.hasAttribute('data-no-spa') ||
          href.includes('/logout/')) {
        return;
      }

      try {
        const targetUrl = new URL(anchor.href, window.location.origin);
        if (targetUrl.origin !== window.location.origin) return;

        if (targetUrl.pathname.startsWith('/dashboard/')) {
          e.preventDefault();
          if (targetUrl.href === window.location.href) return;
          loadPage(targetUrl.href, true);
        }
      } catch (err) {}
    });

    window.addEventListener('popstate', function () {
      loadPage(window.location.href, false);
    });

    // Dynamic Right-side Flyout Submenus Positioning & Auto-close Handler
    let currentActiveSideMenu = null;

    function closeAllSideMenus() {
      document.querySelectorAll('nav.side-nav .side-menu').forEach(function (menu) {
        menu.style.display = 'none';
      });
      currentActiveSideMenu = null;
    }

    window.addEventListener('scroll', closeAllSideMenus, { passive: true });
    document.addEventListener('scroll', closeAllSideMenus, { passive: true, capture: true });

    document.addEventListener('mouseover', function (e) {
      const hoveredSideMenu = e.target.closest('.side-menu');
      if (hoveredSideMenu) {
        // Pointer is inside the open flyout submenu, keep it visible
        return;
      }

      const hoveredLi = e.target.closest('nav.side-nav ul li');
      if (hoveredLi) {
        const sideMenu = hoveredLi.querySelector('.side-menu');
        if (sideMenu) {
          if (currentActiveSideMenu && currentActiveSideMenu !== sideMenu) {
            currentActiveSideMenu.style.display = 'none';
          }
          const rect = hoveredLi.getBoundingClientRect();
          sideMenu.style.position = 'fixed';
          sideMenu.style.top = rect.top + 'px';
          sideMenu.style.left = (rect.right - 2) + 'px';
          sideMenu.style.display = 'block';
          sideMenu.style.zIndex = '99999';
          currentActiveSideMenu = sideMenu;
        } else {
          // Hovering an item without a sub-menu (e.g. Dashboard, Domestic packages)
          closeAllSideMenus();
        }
      } else {
        // Pointer left the sidebar entirely
        closeAllSideMenus();
      }
    });

    document.addEventListener('mouseleave', function () {
      closeAllSideMenus();
    });

    // Profile Dropdown Click & Toggle Handler
    document.addEventListener('click', function (e) {
      const profileItem = e.target.closest('.right-side ul li');
      if (profileItem && profileItem.querySelector('.profile-menu')) {
        const trigger = e.target.closest('a');
        if (trigger && !e.target.closest('.profile-menu')) {
          e.preventDefault();
          profileItem.classList.toggle('menu-open');
        }
      } else {
        document.querySelectorAll('.right-side ul li.menu-open').forEach(function (el) {
          el.classList.remove('menu-open');
        });
      }
    });
  });
})();

