$(document).ready(function () {
  var orderID = "";
  const deliverySlotHours = {
    morning: [7, 11],
    afternoon: [12, 16],
    evening: [17, 20],
    fixed: [7, 25], // 7 AM to 2 AM next day
  };

  let selectedDeliveryDateStr = "";
  let deliveryDate = "";
  let deliveryTime = "";
  let deliverySlot = "";

  function formatDate(dateObj) {
    const dd = String(dateObj.getDate()).padStart(2, "0");
    const mm = String(dateObj.getMonth() + 1).padStart(2, "0");
    const yyyy = dateObj.getFullYear();
    return `${mm}/${dd}/${yyyy}`;
  }

  function formatDateForDisplay(dateObj) {
    return dateObj.toLocaleDateString("en-IN", {
      day: "2-digit",
      month: "long",
      year: "numeric",
    });
  }

  function formatTimeAMPM(hour) {
    const adjustedHour = hour % 24;
    const period = adjustedHour >= 12 ? "PM" : "AM";
    const hour12 = adjustedHour % 12 || 12;
    return `${hour12}:00 ${period}`;
  }

  function showDeliveryTimeSlots(dateInput) {
    const now = new Date();
    const todayStr = formatDate(now);

    let deliveryDateStr = "";
    let deliveryDisplayStr = "";

    if (dateInput === "today") {
      deliveryDateStr = todayStr;
      deliveryDisplayStr = formatDateForDisplay(now);
    } else if (dateInput === "tomorrow") {
      const tomorrow = new Date(now);
      tomorrow.setDate(now.getDate() + 1);
      deliveryDateStr = formatDate(tomorrow);
      deliveryDisplayStr = formatDateForDisplay(tomorrow);
    } else {
      deliveryDateStr = dateInput;
      const parts = deliveryDateStr.split("/");
      const selectedDate = new Date(`${parts[2]}-${parts[0]}-${parts[1]}`);
      deliveryDisplayStr = formatDateForDisplay(selectedDate);
    }

    selectedDeliveryDateStr = deliveryDateStr;

    $("#selectedDeliveryDate, #selectedDeliveryDateConfirm").text(
      deliveryDisplayStr
    );
    $("#deliveryModal").removeClass("hidden");
    $(".deliveryDateBox").removeClass("hidden");
    $(".showTimeBox").removeClass("hidden");
    $(".calenderBox, .showDeliveryTimeBox").addClass("hidden");
  }

  function generateTimeSlots(slotType, dateStr) {
    const [startHour, endHour] = deliverySlotHours[slotType];
    const now = new Date();
    const parts = dateStr.split("/");
    const selectedDate = new Date(`${parts[2]}-${parts[0]}-${parts[1]}`);
    const isToday = now.toDateString() === selectedDate.toDateString();

    let slots = "";
    for (let hour = startHour; hour <= endHour; hour++) {
      const adjustedHour = hour % 24;
      if (isToday && adjustedHour <= now.getHours() + 2) continue;
      const slot = `${formatTimeAMPM(adjustedHour)} - ${formatTimeAMPM(
        adjustedHour + 2
      )}`;
      slots += `<label class='flex items-center gap-2 border rounded cursor-pointer hover:bg-gray-100'>
        <input type='radio' name='timeSlot' value='${slot}' class='peer hidden'>
        <div class='peer-checked:border-primary peer-checked:bg-primary/10 w-full p-2'>${slot}</div>
      </label>`;
    }
    $("#timeSlots").html(
      slots || "<p class='text-red-500'>No slots available.</p>"
    );
  }

  function styleDatepicker() {
    const $dp = $(".datepicker");
    $dp.addClass("p-4 bg-white rounded");
    $dp.find("table").addClass("w-full text-sm");
    $dp
      .find("td, th")
      .addClass("p-3 text-center hover:bg-gray-100 cursor-pointer");
    $dp.find(".today").addClass("border border-primary");
    $dp.find(".prev, .next").addClass("text-primary font-bold");

    const currentDate = new Date();
    const calendarTitle = $dp.find(".datepicker-switch").first().text();
    const [monthName, year] = calendarTitle.split(" ");
    const calendarMonth = new Date(`${monthName} 1, ${year}`);

    const threeMonthsLater = new Date();
    threeMonthsLater.setMonth(currentDate.getMonth() + 2);

    if (
      calendarMonth.getMonth() === currentDate.getMonth() &&
      calendarMonth.getFullYear() === currentDate.getFullYear()
    ) {
      $dp.find(".prev").addClass("opacity-50 pointer-events-none");
    } else {
      $dp.find(".prev").removeClass("opacity-50 pointer-events-none");
    }

    if (
      calendarMonth.getMonth() === threeMonthsLater.getMonth() &&
      calendarMonth.getFullYear() === threeMonthsLater.getFullYear()
    ) {
      $dp.find(".next").addClass("opacity-50 pointer-events-none");
    } else {
      $dp.find(".next").removeClass("opacity-50 pointer-events-none");
    }

    $dp.find(".datepicker-switch").addClass("pointer-events-none opacity-50");
  }

  $("#inlineCalendar")
    .datepicker({
      todayHighlight: true,
      startDate: new Date(),
      endDate: "+3m",
      format: "mm/dd/yyyy",
      autoclose: true,
    })
    .on("changeDate", function (e) {
      showDeliveryTimeSlots(e.format());
    })
    .on("show", function () {
      styleDatepicker();
    })
    .on("changeMonth", function () {
      setTimeout(styleDatepicker, 10);
    })
    .on("changeYear", function () {
      setTimeout(styleDatepicker, 10);
    });

  setTimeout(styleDatepicker, 10);

  $("#deliverByToday").click((e) => {
    e.preventDefault();
    showDeliveryTimeSlots("today");
  });
  $("#deliverByTomorrow").click((e) => {
    e.preventDefault();
    showDeliveryTimeSlots("tomorrow");
  });
  $(".deliverByOther, #goToCalendar").click((e) => {
    e.preventDefault();
    $("#deliveryModal").removeClass("hidden");
    $(".deliveryDateBox, .calenderBox").removeClass("hidden");
    $(".showTimeBox, .showDeliveryTimeBox").addClass("hidden");
  });

  $(".closeCalender").click((e) => {
    e.preventDefault();
    $(
      "#deliveryModal, .deliveryDateBox, .showTimeBox, .calenderBox, .showDeliveryTimeBox"
    ).addClass("hidden");
  });

  $(".deliverySlot").click(function () {
    const slotType = $(this).val().split("_")[0];
    if (slotType === "fixed") {
      generateTimeSlots(slotType, selectedDeliveryDateStr);
      $(".showTimeBox").addClass("hidden");
      $(".showDeliveryTimeBox").removeClass("hidden");
    }
  });

  $(".visibleShowTimeBox").click((e) => {
    e.preventDefault();
    $(".showDeliveryTimeBox").addClass("hidden");
    $(".showTimeBox").removeClass("hidden");
  });

  // $("#confirmDelivery, #confirmTimeSlot").click(function (e) {
  //   e.preventDefault();

  //   deliverySlot = $("input:radio[name='deliveryType']:checked").val();
  //   deliveryTime =
  //     $("input:radio[name='timeSlot']:checked").val() || deliverySlot;
  //   deliveryDate = selectedDeliveryDateStr;

  //   console.log("Delivery Details:", {
  //     deliveryDate,
  //     deliverySlot,
  //     deliveryTime,
  //   });

  //   $(".deliveryDateBox").addClass("hidden");
  //   $(".specialNotesBox").removeClass("hidden").fadeIn(200);
  // });

  $("#confirmDelivery, #confirmTimeSlot").click(function (e) {
    e.preventDefault();

    deliverySlot = $("input:radio[name='deliveryType']:checked").val();
    deliveryTime =
      $("input:radio[name='timeSlot']:checked").val() || deliverySlot;
    deliveryDate = selectedDeliveryDateStr;

    // Validation
    if (!deliveryDate) {
      popUpMessage("center", "Please select a delivery date.", "warning");
      return;
    }

    if (!deliverySlot) {
      popUpMessage("center", "Please select a delivery slot.", "warning");
      return;
    }

    if (
      deliverySlot.startsWith("fixed") &&
      !$("input:radio[name='timeSlot']:checked").val()
    ) {
      popUpMessage(
        "center",
        "Please select a time slot for fixed delivery.",
        "warning"
      );
      return;
    }

    console.log("Delivery Details:", {
      deliveryDate,
      deliverySlot,
      deliveryTime,
    });

    // Proceed only if all validations pass
    $(".deliveryDateBox").addClass("hidden");
    $(".specialNotesBox").removeClass("hidden").fadeIn(200);
  });

  $(".closeNotes, .backToDelivery").click(function () {
    $(".specialNotesBox").fadeOut(200, function () {
      $(this).addClass("hidden");
      $(".deliveryDateBox").removeClass("hidden");
    });
  });

  $(".reviewBtn").click(function (e) {
    e.preventDefault();
    const $btn = $(this);
    $btn.prop("disabled", true).text("Processing...");
    var occasion = $("#occasionSelect").val();
    var message = $("#specialNoteInput").val();

    $.ajax({
      type: "POST",
      dataType: "json",
      data: {
        type: "order",
        action: "meta",
        order_id: orderID,
        occasion: occasion,
        message: message,
        deliveryDate: deliveryDate,
        deliveryTime: deliveryTime,
        deliverySlot: deliverySlot,
      },
      success: function (data) {
        if (data.status == 200) {
          window.location.href = data.redirect;
        } else {
          popUpMessage("center", data.msg, data.status);
        }
      },
      error: function (data) {
        console.log(data);
      },
    });
  });
});
