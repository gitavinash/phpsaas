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
    // ===== FORCE IST TIME (DO NOT USE new Date() DIRECTLY) =====
    function getISTDate() {
      const now = new Date();
      const istOffset = 330; // IST = UTC + 5:30 (in minutes)
      const localOffset = now.getTimezoneOffset();
      return new Date(now.getTime() + (istOffset + localOffset) * 60000);
    }

    const now = getISTDate();
    const todayStr = formatDate(now);

    let deliveryDateStr = "";
    let deliveryDisplayStr = "";
    let deliveryDateObj = null;

    if (dateInput === "today") {
      deliveryDateObj = new Date(now);
    } else if (dateInput === "tomorrow") {
      deliveryDateObj = new Date(now);
      deliveryDateObj.setDate(deliveryDateObj.getDate() + 1);
    } else {
      // Expected format: MM/DD/YYYY
      const parts = dateInput.split("/");
      deliveryDateObj = new Date(
        parseInt(parts[2], 10),
        parseInt(parts[0], 10) - 1,
        parseInt(parts[1], 10)
      );
    }

    // Normalize time to avoid DST / edge issues
    deliveryDateObj.setHours(12, 0, 0, 0);

    deliveryDateStr = formatDate(deliveryDateObj);
    deliveryDisplayStr = formatDateForDisplay(deliveryDateObj);

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

  /** Remove from cart */
  $(".removeFromCart").click(function (e) {
    e.preventDefault();
    var totalCartItems = parseInt($("#cartItems").text()) || 0;
    var id = $(this).attr("id");
    var $item = $("#product_" + id);
    var itemPrice = parseFloat($item.data("price"));
    var itemMRP = parseFloat($item.data("mrp"));

    $.ajax({
      type: "POST",
      dataType: "json",
      data: {
        type: "cart",
        action: "remove",
        id: id,
      },
      success: function (data) {
        if (data.status == 200) {
          $item.slideUp("fast", function () {
            $(this).remove();

            // Update cart summary manually
            var totalPrice = 0;
            var totalMRP = 0;

            $(".cart-item").each(function () {
              totalPrice += parseFloat($(this).data("price"));
              totalMRP += parseFloat($(this).data("mrp"));
            });

            var hasDiscount = totalMRP > totalPrice && totalMRP != 0;
            var discount = totalMRP - totalPrice;
            var discountPercent = hasDiscount
              ? Math.round((discount / totalMRP) * 100)
              : 0;

            // Update DOM
            $("#grandTotal").text("₹" + totalPrice.toLocaleString());

            if (hasDiscount) {
              $("#totalMRP")
                .text("₹" + totalMRP.toLocaleString())
                .parent()
                .show();
              $("#youSave")
                .html(
                  "₹" +
                    discount.toLocaleString() +
                    " (" +
                    discountPercent +
                    "% OFF)"
                )
                .parent()
                .show();
            } else {
              $("#totalMRP").parent().hide();
              $("#youSave").parent().hide();
            }

            if ($(".cart-item").length === 0) {
              location.reload();
            }
          });
          $("#cartItems").html(totalCartItems - 1);
        } else {
          popUpMessage("center", data.msg, "error");
        }
      },
      error: function (data) {
        console.log(data);
      },
    });
  });

  $("#placeOrder").click(function (e) {
    e.preventDefault();
    const contactInfo = JSON.parse(localStorage.getItem("shippingInfo"));
    if (contactInfo) {
      $("#first_name").val(contactInfo.first_name);
      $("#last_name").val(contactInfo.last_name);
      $("#email").val(contactInfo.email);
      $("#mobile").val(contactInfo.mobile);
      $("#address_first_name").val(contactInfo.address_first_name);
      $("#address_last_name").val(contactInfo.address_last_name);
      $("#address_mobile").val(contactInfo.address_mobile);
      $("#house_no").val(contactInfo.house_no);
      $("#area").val(contactInfo.area);
      $("#landmark").val(contactInfo.landmark);
      $("#city").val(contactInfo.city);
      $("#state").val(contactInfo.state);
      $("#pincode").val(contactInfo.pincode);
    }
    $("#checkOut").hide();
    $("#placeOrder").hide();
    $("#shippingDetails").removeClass("hidden");
    $("#continueShipping").removeClass("hidden");
  });

  /** Checkout form */

  $("#continueShipping").click(function (e) {
    e.preventDefault();
    const $btn = $(this);
    $btn.prop("disabled", true).text("Processing...");

    $("#placeOrder").addClass("hidden");

    var mobile = $("#mobile").val();
    var email = $("#email").val();

    var formData = $(".checkout-form").serializeArray();
    formData.push({ name: "type", value: "cart" });
    formData.push({ name: "action", value: "checkout" });

    // Save to localStorage
    const contactInfo = {
      first_name: $("#first_name").val(),
      last_name: $("#last_name").val(),
      email: email,
      mobile: mobile,
      address_first_name: $("#address_first_name").val(),
      address_last_name: $("#address_last_name").val(),
      address_mobile: $("#address_mobile").val(),
      house_no: $("#house_no").val(),
      area: $("#area").val(),
      landmark: $("#landmark").val(),
      city: $("#city").val(),
      state: $("#state").val(),
      pincode: $("#pincode").val(),
    };

    localStorage.setItem("shippingInfo", JSON.stringify(contactInfo));

    if (mobile != "") {
      if (email != "") {
        $.ajax({
          type: "POST",
          dataType: "json",
          data: formData,
          success: function (data) {
            $("html, body").animate({ scrollTop: 0 }, "fast");

            $("#addToCart").show();

            if (data.status == 200) {
              $(".checkout-form").hide();
              // $('#shippingName').append(data.name);
              // $('#shippingAddress').append(data.address);
              // $('.shipping-box').show();
              //   window.location.href='/shipping/?id=' + data.order;
              $("#shippingDetails").addClass("hidden");
              $("#placeOrder").addClass("hidden");

              $("#deliveryDetails").show();
              $("#continueDelivery").show();

              orderID = data.order;
              $("#continueShipping").addClass("hidden");
              $("#continueCheckout").removeClass("hidden");
            } else {
              popUpMessage("center", data.msg, data.error);
              $("#continueShipping").removeClass("hidden");
              $btn
                .prop("disabled", false)
                .text("Continue to Delivery date & time");
              console.log(data);
            }
          },
          error: function (data) {
            popUpMessage("center", data.msg, "error");
            $("#continueShipping").removeClass("hidden");
            $btn
              .prop("disabled", false)
              .text("Continue to Delivery date & time");
            console.log(data);
          },
        });
      } else {
        popUpMessage("center", "Please enter your email", "error");
        $("#continueShipping").removeClass("hidden");
        $btn.prop("disabled", false).text("Continue to Delivery date & time");
      }
    } else {
      popUpMessage("center", "Please enter your mobile number", "error");
      $("#continueShipping").removeClass("hidden");
      $btn.prop("disabled", false).text("Continue to Delivery date & time");
    }
  });

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
          $btn.prop("disabled", false).text("Review Order");
        }
      },
      error: function (errorMsg) {
        console.log(errorMsg);
        popUpMessage(
          "center",
          errorMsg.responseJSON.msg,
          errorMsg.responseJSON.status
        );
        $btn.prop("disabled", false).text("Review Order");
      },
    });
  });
});
