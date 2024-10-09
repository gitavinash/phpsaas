function addRemoveButton(box, removeClass) {
  if (!$(box).first().find(removeClass).length) {
    $(box)
      .first()
      .append(
        `<button type="button" class="${removeClass.slice(1)}">Remove</button>`
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

// Submit form
