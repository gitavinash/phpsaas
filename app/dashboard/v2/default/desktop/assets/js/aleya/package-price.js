var selectedItinaryId = 0;
function addRemoveButton(box, removeClass) {
  if (!$(box).first().find(removeClass).length) {
    $(box)
      .first()
      .append(
        `<button type="button" class="btn-red ${removeClass.slice(
          1
        )}">Remove</button>`
      );
  }
}

function validateInputs(box) {
  let isValid = true;
  $(box)
    .last()
    .find("input")
    .each(function () {
      if ($(this).prop("required") && $(this).val() === "") {
        isValid = false;
        $(this).addClass("highlight-required");
      } else {
        $(this).removeClass("highlight-required");
      }
    });
  return isValid;
}

function cloneBox(boxClass, removeClass, containerClass) {
  let isValid = validateInputs(boxClass);

  if (!isValid) {
    popUpMessage(
      "center",
      "Please fill all required fields before cloning.",
      "warning"
    );
    return;
  }

  let $boxClone = $(boxClass).last().clone();

  $boxClone.find("input").each(function () {
    $(this).val("");
  });

  $boxClone.addClass("cloneSlabForm");

  if (!$boxClone.find(removeClass).length) {
    $boxClone.append(
      `<button type="button" class="${removeClass.slice(1)}">Remove</button>`
    );
  }

  // Append the cloned box to its respective container
  $(containerClass).append($boxClone);

  // Toggle the visibility of remove buttons
  toggleRemoveButton(boxClass, removeClass);
}

// Function to handle remove button clicks
function removeBox(removeClass, boxClass) {
  $("body").on("click", removeClass, function () {
    if ($(boxClass).length > 1) {
      $(this).closest(boxClass).remove();
    }
    toggleRemoveButton(boxClass, removeClass);
  });
}

// Function to toggle the visibility of remove buttons
function toggleRemoveButton(boxClass, removeClass) {
  if ($(boxClass).length > 1) {
    $(removeClass).show();
  } else {
    $(removeClass).hide();
  }
}

$(document).ready(function () {
  // Single sharing price
  addRemoveButton(".priceBox", ".removePriceBox");
  $(".cloneSingleSharingPriceBox").click(function (e) {
    e.preventDefault();
    cloneBox(".priceBox", ".removePriceBox", ".priceContainer");
  });
  removeBox(".removePriceBox", ".priceBox");
  toggleRemoveButton(".priceBox", ".removePriceBox");

  // Double Sharing
  addRemoveButton(".doublePriceBox", ".removeDoublePriceBox");
  $(".cloneDoubleSharingPriceBox").click(function (e) {
    e.preventDefault();
    cloneBox(
      ".doublePriceBox",
      ".removeDoublePriceBox",
      ".doublePriceContainer"
    );
  });
  removeBox(".removeDoublePriceBox", ".doublePriceBox");
  toggleRemoveButton(".doublePriceBox", ".removeDoublePriceBox");

  // Triple Sharing
  addRemoveButton(".triplePriceBox", ".removeTriplePriceBox");
  $(".cloneTripleSharingPriceBox").click(function (e) {
    e.preventDefault();
    cloneBox(
      ".triplePriceBox",
      ".removeTriplePriceBox",
      ".triplePriceContainer"
    );
  });
  removeBox(".removeTriplePriceBox", ".triplePriceBox");
  toggleRemoveButton(".triplePriceBox", ".removeTriplePriceBox");

  // Quad Sharing
  addRemoveButton(".quadPriceBox", ".removeQuadPriceBox");
  $(".cloneQuadSharingPriceBox").click(function (e) {
    e.preventDefault();
    cloneBox(".quadPriceBox", ".removeQuadPriceBox", ".quadPriceContainer");
  });
  removeBox(".removeQuadPriceBox", ".quadPriceBox");
  toggleRemoveButton(".quadPriceBox", ".removeQuadPriceBox");
});

// Price Box

$("#openPackagePriceBox").click(function (e) {
  e.preventDefault();
  $(".priceBox").show();
});

// Submit form

$(".saveItineraryPriceBtn").click(function (e) {
  e.preventDefault();
  $(".saveItineraryPriceBtn").hide();
  var $form = $(this).closest("form");
  var isValid = true;
  $form.find("input[required]").each(function () {
    if ($(this).val() === "") {
      isValid = false;
      $(this).addClass("highlight-required");
    } else {
      $(this).removeClass("highlight-required");
    }
  });

  if (isValid) {
    // Submit ajax request

    var formData = $form.serializeArray();
    console.log(`formData`, formData);

    $.ajax({
      type: "POST",
      url: "/api/packages/price/",
      data: formData,
      success: function (response) {
        popUpMessage("center", response.msg, response.status);
        setTimeout(function () {
          $form[0].reset();
          $("html, body").animate({ scrollTop: 0 }, "slow");
          $form.find(".removeDoublePriceBox").remove();
          $form.find(".cloneSlabForm").remove();
          $(".saveItineraryPriceBtn").show();
        }, 1500);
      },
      error: function (error) {
        console.log(`error`, error);
        $(".saveItineraryPriceBtn").show();
        popUpMessage("center", error.responseJSON.msg, "warning");
      },
    });
  } else {
    $(".saveItineraryPriceBtn").show();
    popUpMessage("center", "Please fill all required fields.", "warning");
  }
});

// Edit price

$(".editPrice").click(function (e) {
  selectedItinaryId = $(this).attr("data-id");
  e.preventDefault();

  // Clear form fields first
  var fieldsToClear = [
    "#startDate",
    "#endDate",
    "#adultNet",
    "#adultMRP",
    "#adultSelling",
    "#adultAgent",
    "#childNet",
    "#childMRP",
    "#childSelling",
    "#childAgent",
  ];
  fieldsToClear.forEach(function (field) {
    $(field).val("");
  });

  $(".editPriceBox").show();

  var priceInfo = localStorage.getItem(`packageInfo_${selectedItinaryId}`);
  priceInfo = JSON.parse(priceInfo);

  // Common datepicker initialization function
  function initDatePicker(elementId, selectedDate) {
    $(elementId)
      .daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: false,
        locale: {
          format: "YYYY-MM-DD", // Internal format for daterangepicker
        },
      })
      .on("apply.daterangepicker", function (ev, picker) {
        var date = new Date(picker.startDate.format("YYYY-MM-DD"));
        var options = { day: "numeric", month: "long" }; // Custom format
        $(this).val(date.toLocaleDateString("en-GB", options)); // Set formatted date
      });

    // Set the initial date after initializing the datepicker
    var dateObj = new Date(selectedDate);
    $(elementId).val(
      dateObj.toLocaleDateString("en-GB", { day: "numeric", month: "long" })
    );
  }

  // Set datepicker and formatted values for start and end dates
  initDatePicker("#startDate", priceInfo.tour_package_price_date_start);
  initDatePicker("#endDate", priceInfo.tour_package_price_date_end);

  // Set values for other fields
  $("#adultNet").val(priceInfo.tour_package_price_net_adult);
  $("#adultMRP").val(priceInfo.tour_package_price_mrp_adult);
  $("#adultSelling").val(priceInfo.tour_package_price_selling_adult);
  $("#adultAgent").val(priceInfo.tour_package_price_agent_adult);
  $("#childNet").val(priceInfo.tour_package_price_net_child);
  $("#childMRP").val(priceInfo.tour_package_price_mrp_child);
  $("#childSelling").val(priceInfo.tour_package_price_selling_child);
  $("#childAgent").val(priceInfo.tour_package_price_agent_child);
});

$("#updateItineraryPriceBtn").click(function (e) {
  e.preventDefault();

  const formData = new FormData();

  formData.append("startDate", $("#startDate").val());
  formData.append("endDate", $("#endDate").val());
  formData.append("adultNet", $("#adultNet").val());
  formData.append("adultMRP", $("#adultMRP").val());
  formData.append("adultSelling", $("#adultSelling").val());
  formData.append("adultAgent", $("#adultAgent").val());
  formData.append("childNet", $("#childNet").val());
  formData.append("childMRP", $("#childMRP").val());
  formData.append("childSelling", $("#childSelling").val());
  formData.append("childAgent", $("#childAgent").val());
  formData.append("id", btoa(selectedItinaryId));

  $.ajax({
    type: "POST",
    url: "/api/packages/price-update/",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      popUpMessage("center", response.msg, response.status);
      setTimeout(function () {
        window.location.reload();
      }, 1500);
    },
    error: function (error) {
      console.log(`error`, error);
      $(".saveItineraryPriceBtn").show();
      popUpMessage("center", error.responseJSON.msg, "warning");
    },
  });
});
