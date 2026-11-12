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
  let lastFocusedTrigger = null;

  function syncMobileCheckoutBar(activeStep, customLabel, customDisabled) {
    const $barTrigger = $(".tbe-mobile-checkout-bar [data-checkout-trigger]");
    if (!$barTrigger.length) return;

    let targetBtn = "#placeOrder";
    let label = "Proceed to Shipping";

    if (activeStep === 2) {
      targetBtn = "#continueShipping";
      const hasEvent =
        $("#continueShipping").data("has-event") === true ||
        $("#continueShipping").attr("data-has-event") === "true";
      label = hasEvent ? "Proceed to Payment" : "Continue to Delivery";
    } else if (activeStep === 3) {
      targetBtn = "#continueCheckout";
      label = "Select Delivery Window";
    }

    if (customLabel) {
      label = customLabel;
    }

    $barTrigger.attr("data-target-btn", targetBtn).prop("disabled", Boolean(customDisabled));
    const $span = $barTrigger.find("span").last();
    if ($span.length) {
      $span.text(label);
    } else {
      $barTrigger.text(label);
    }
  }

  let currentCheckoutStep = 1;

  function setCheckoutStep(activeStep) {
    currentCheckoutStep = activeStep;
    $("[data-checkout-step]").each(function () {
      const stepNum = parseInt($(this).attr("data-checkout-step"), 10);
      $(this)
        .toggleClass("is-active", stepNum === activeStep)
        .toggleClass("is-complete", stepNum < activeStep);
      if (stepNum === activeStep) {
        $(this).attr("aria-current", "step");
      } else {
        $(this).removeAttr("aria-current");
      }
    });
    syncMobileCheckoutBar(activeStep);
  }

  setCheckoutStep(1);

  $(document).on("click", "[data-checkout-trigger]", function (e) {
    e.preventDefault();
    const targetSelector = $(this).attr("data-target-btn") || "#placeOrder";
    const $target = $(targetSelector);
    if ($target.length) {
      $target.trigger("click");
    }
  });

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

  function resetModalPanels() {
    $(".deliveryDateBox, .showTimeBox, .calenderBox, .showDeliveryTimeBox, .specialNotesBox").addClass("hidden");
  }

  function openDeliveryModal(triggerEl) {
    lastFocusedTrigger = triggerEl || document.activeElement;
    const modalEl = document.getElementById("deliveryModal");
    if (modalEl && typeof modalEl.showModal === "function") {
      if (!modalEl.open) {
        modalEl.showModal();
      }
    } else if (modalEl) {
      $("#deliveryModal").removeClass("hidden").attr("aria-hidden", "false");
    }
    const closeBtn = document.querySelector("#deliveryModal .closeCalender");
    if (closeBtn) {
      setTimeout(function () {
        closeBtn.focus();
      }, 30);
    }
  }

  function closeDeliveryModal() {
    resetModalPanels();
    const modalEl = document.getElementById("deliveryModal");
    if (modalEl && typeof modalEl.close === "function") {
      if (modalEl.open) {
        modalEl.close();
      }
    } else if (modalEl) {
      $("#deliveryModal").addClass("hidden").attr("aria-hidden", "true");
      if (lastFocusedTrigger && typeof lastFocusedTrigger.focus === "function") {
        lastFocusedTrigger.focus();
      }
    }
  }

  const deliveryDialogEl = document.getElementById("deliveryModal");
  if (deliveryDialogEl && typeof deliveryDialogEl.showModal === "function") {
    deliveryDialogEl.addEventListener("close", function () {
      resetModalPanels();
      deliveryDialogEl.returnValue = "";
    });

    if (!("closedBy" in HTMLDialogElement.prototype)) {
      const outside = function (ev, dialog) {
        const r = dialog.getBoundingClientRect();
        return ev.clientX < r.left || ev.clientX > r.right || ev.clientY < r.top || ev.clientY > r.bottom;
      };
      let pressedOutside = false;
      deliveryDialogEl.addEventListener("pointerdown", function (ev) {
        pressedOutside = outside(ev, deliveryDialogEl);
      });
      deliveryDialogEl.addEventListener("pointerup", function (ev) {
        if (pressedOutside && outside(ev, deliveryDialogEl)) {
          deliveryDialogEl.close();
        }
      });
    }
  }

  function showDeliveryTimeSlots(dateInput, triggerEl) {
    function getISTDate() {
      const now = new Date();
      const istOffset = 330;
      const localOffset = now.getTimezoneOffset();
      return new Date(now.getTime() + (istOffset + localOffset) * 60000);
    }

    const now = getISTDate();
    let deliveryDateObj = null;

    if (dateInput === "today") {
      deliveryDateObj = new Date(now);
    } else if (dateInput === "tomorrow") {
      deliveryDateObj = new Date(now);
      deliveryDateObj.setDate(deliveryDateObj.getDate() + 1);
    } else {
      const parts = dateInput.split("/");
      deliveryDateObj = new Date(
        parseInt(parts[2], 10),
        parseInt(parts[0], 10) - 1,
        parseInt(parts[1], 10)
      );
    }

    deliveryDateObj.setHours(12, 0, 0, 0);
    selectedDeliveryDateStr = formatDate(deliveryDateObj);

    $("#selectedDeliveryDate, #selectedDeliveryDateConfirm").text(
      formatDateForDisplay(deliveryDateObj)
    );

    openDeliveryModal(triggerEl);
    $(".deliveryDateBox, .showTimeBox").removeClass("hidden");
    $(".calenderBox, .showDeliveryTimeBox, .specialNotesBox").addClass("hidden");
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
      slots += `<label class="flex items-center gap-2 border border-[#E6DDD3] rounded cursor-pointer hover:bg-[#FAF7F2]">
        <input type="radio" name="timeSlot" value="${slot}" class="peer sr-only">
        <div class="peer-checked:border-[#183028] peer-checked:bg-[#183028] peer-checked:text-white w-full min-h-[44px] flex items-center justify-center text-center p-2.5 text-xs sm:text-sm rounded transition-colors">${slot}</div>
      </label>`;
    }
    $("#timeSlots").html(
      slots || "<p class='text-[#991B1B] text-sm col-span-full py-2'>No same-day fixed time slots remain for today. Please select Tomorrow or another date.</p>"
    );
  }

  function styleDatepicker() {
    const $dp = $(".datepicker");
    $dp.addClass("p-2 sm:p-4 bg-white rounded");
    $dp.find("table").addClass("w-full text-xs sm:text-sm");
    $dp
      .find("td, th")
      .addClass("p-1.5 sm:p-3 text-center hover:bg-[#EFE8DC] cursor-pointer rounded");
    $dp.find(".today").addClass("border border-[#9B5637]");
    $dp.find(".prev, .next").addClass("text-forest font-bold");

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
      $dp.find(".prev").addClass("opacity-40 pointer-events-none");
    } else {
      $dp.find(".prev").removeClass("opacity-40 pointer-events-none");
    }

    if (
      calendarMonth.getMonth() === threeMonthsLater.getMonth() &&
      calendarMonth.getFullYear() === threeMonthsLater.getFullYear()
    ) {
      $dp.find(".next").addClass("opacity-40 pointer-events-none");
    } else {
      $dp.find(".next").removeClass("opacity-40 pointer-events-none");
    }

    $dp.find(".datepicker-switch").addClass("pointer-events-none text-[#1C1A18] font-semibold");
  }

  // Remove item from cart
  $(".removeFromCart").on("click", function (e) {
    e.preventDefault();
    var id = $(this).attr("id");
    var $item = $("#product_" + id);

    $.ajax({
      type: "POST",
      dataType: "json",
      data: {
        type: "cart",
        action: "remove",
        id: id,
      },
      success: function (data) {
        if (data && data.status == 200) {
          $item.slideUp("fast", function () {
            $(this).remove();

            var totalPrice = 0;
            var totalMRP = 0;
            var remainingUnits = 0;

            $(".cart-item").each(function () {
              var unitQty = Math.max(1, parseInt($(this).attr("data-qty"), 10) || 1);
              var unitPrice = parseFloat($(this).data("price")) || 0;
              var unitMrp = parseFloat($(this).data("mrp")) || 0;
              totalPrice += unitPrice * unitQty;
              totalMRP += (unitMrp > 0 ? unitMrp : unitPrice) * unitQty;
              remainingUnits += unitQty;
            });

            var hasDiscount = totalMRP > totalPrice && totalMRP != 0;
            var discount = totalMRP - totalPrice;
            var discountPercent = hasDiscount
              ? Math.round((discount / totalMRP) * 100)
              : 0;

            $("#grandTotal, #mobileGrandTotal").text("₹" + totalPrice.toLocaleString("en-IN"));
            $("#checkoutItemCount").text(remainingUnits + (remainingUnits === 1 ? " Item" : " Items"));

            var $cartBadge = $("#cartItems");
            if ($cartBadge.length) {
              $cartBadge.text(remainingUnits);
              if (remainingUnits > 0) {
                $cartBadge
                  .removeClass("bg-[#EFE8DC] text-[#5E5650] border border-[#D4C8BA]")
                  .addClass("bg-[#183028] text-white");
              } else {
                $cartBadge
                  .removeClass("bg-[#183028] text-white")
                  .addClass("bg-[#EFE8DC] text-[#5E5650] border border-[#D4C8BA]");
              }
              $cartBadge
                .siblings('a[href="/checkout/"]')
                .attr("aria-label", "Shopping bag, " + remainingUnits + (remainingUnits === 1 ? " item" : " items"));
            }

            $(".tbe-cart-count-pill").text(remainingUnits + (remainingUnits === 1 ? " item" : " items"));

            var $statusEl = $("#tbeCartDropdownStatus");
            if ($statusEl.length && remainingUnits > 0) {
              $statusEl.text("");
              var icon = document.createElement("i");
              icon.className = "ri-checkbox-circle-line text-[#183028] mr-1";
              icon.setAttribute("aria-hidden", "true");
              var strong = document.createElement("strong");
              strong.className = "text-[#1C1A18] font-semibold";
              strong.textContent = String(remainingUnits);
              $statusEl[0].appendChild(icon);
              $statusEl[0].appendChild(document.createTextNode("You have "));
              $statusEl[0].appendChild(strong);
              $statusEl[0].appendChild(
                document.createTextNode(" curated " + (remainingUnits === 1 ? "item" : "items") + " ready for checkout.")
              );
            }

            if (hasDiscount) {
              $("#totalMRP")
                .text("₹" + totalMRP.toLocaleString("en-IN"))
                .parent()
                .show();
              $("#youSave")
                .text(
                  "₹" +
                    discount.toLocaleString("en-IN") +
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
        } else {
          popUpMessage("center", (data && data.msg) || "Could not remove item.", "error");
        }
      },
      error: function () {
        popUpMessage("center", "Unable to remove item right now.", "error");
      },
    });
  });

  $("#shippingForm").on("submit", function (e) {
    e.preventDefault();
    $("#continueShipping").trigger("click");
  });

  // Transition Step 1 (Bag) -> Step 2 (Shipping)
  $("#placeOrder").on("click", function (e) {
    e.preventDefault();
    try {
      const contactInfo = JSON.parse(localStorage.getItem("shippingInfo"));
      if (contactInfo) {
        $("#first_name").val(contactInfo.first_name || "");
        $("#last_name").val(contactInfo.last_name || "");
        $("#email").val(contactInfo.email || "");
        $("#mobile").val(contactInfo.mobile || "");
        $("#address_first_name").val(contactInfo.address_first_name || "");
        $("#address_last_name").val(contactInfo.address_last_name || "");
        $("#address_mobile").val(contactInfo.address_mobile || "");
        $("#house_no").val(contactInfo.house_no || "");
        $("#area").val(contactInfo.area || "");
        $("#landmark").val(contactInfo.landmark || "");
        $("#city").val(contactInfo.city || "");
        $("#state").val(contactInfo.state || "");
        $("#pincode").val(contactInfo.pincode || "");
      }
    } catch (_) {}

    $("#checkOut").hide();
    $("#placeOrder").hide();
    $("#shippingDetails").removeClass("hidden");
    $("#continueShipping").removeClass("hidden");
    setCheckoutStep(2);
    $("html, body").animate({ scrollTop: 0 }, "fast");
    const shippingHeading = document.getElementById("shippingHeading");
    if (shippingHeading) shippingHeading.focus({ preventScroll: true });
  });

  // Submit Shipping Form -> Step 3 (Delivery) or Event Payment
  $("#continueShipping").on("click", function (e) {
    e.preventDefault();
    const $btn = $(this);
    const shippingFormEl = document.getElementById("shippingForm");
    if (shippingFormEl && shippingFormEl.checkValidity && !shippingFormEl.checkValidity()) {
      const firstInvalid = shippingFormEl.querySelector(":invalid");
      if (firstInvalid) firstInvalid.focus();
      return;
    }

    const hasEvent =
      $btn.data("has-event") === true || $btn.attr("data-has-event") === "true";
    const defaultLabel = hasEvent
      ? "Proceed to Payment"
      : "Continue to Delivery Date & Time";

    var mobile = String($("#mobile").val() || "").trim();
    var email = String($("#email").val() || "").trim();

    if (!mobile) {
      popUpMessage("center", "Please enter your mobile number.", "error");
      $("#mobile").focus();
      return;
    }

    if (!email) {
      popUpMessage("center", "Please enter your email address.", "error");
      $("#email").focus();
      return;
    }

    $btn.prop("disabled", true).text("Saving Details...");
    syncMobileCheckoutBar(2, "Saving Details...", true);
    $("#placeOrder").addClass("hidden");

    var formData = $(".checkout-form").serializeArray();
    formData.push({ name: "type", value: "cart" });
    formData.push({ name: "action", value: "checkout" });

    try {
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
    } catch (_) {}

    $.ajax({
      type: "POST",
      dataType: "json",
      data: formData,
      success: function (data) {
        $("html, body").animate({ scrollTop: 0 }, "fast");
        $("#addToCart").show();

        if (data && data.status == 200) {
          orderID = data.order;
          if (hasEvent) {
            $.ajax({
              type: "POST",
              dataType: "json",
              data: {
                type: "order",
                action: "meta",
                order_id: orderID,
              },
              success: function (res) {
                if (res && res.redirect) {
                  window.location.href = res.redirect;
                } else {
                  window.location.href = "/shipping/?id=" + orderID;
                }
              },
              error: function () {
                window.location.href = "/shipping/?id=" + orderID;
              },
            });
          } else {
            $(".checkout-form").hide();
            $("#shippingDetails").addClass("hidden");
            $("#placeOrder").addClass("hidden");

            $("#deliveryDetails").removeClass("hidden").show();
            $("#continueDelivery").show();
            $("#continueShipping").addClass("hidden");
            $("#continueCheckout").removeClass("hidden");
            setCheckoutStep(3);
            const deliveryHeading = document.getElementById("deliveryHeading");
            if (deliveryHeading) deliveryHeading.focus({ preventScroll: true });
          }
        } else {
          popUpMessage("center", (data && data.msg) || "Could not save shipping details.", (data && data.error) || "error");
          $("#continueShipping").removeClass("hidden");
          $btn.prop("disabled", false).text(defaultLabel);
          syncMobileCheckoutBar(2, "", false);
        }
      },
      error: function (xhr) {
        var msg = (xhr && xhr.responseJSON && xhr.responseJSON.msg) || "Unable to continue checkout. Please try again.";
        popUpMessage("center", msg, "error");
        $("#continueShipping").removeClass("hidden");
        $btn.prop("disabled", false).text(defaultLabel);
        syncMobileCheckoutBar(2, "", false);
      },
    });
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
      showDeliveryTimeSlots(e.format(), this);
    })
    .on("show", styleDatepicker)
    .on("changeMonth", function () {
      setTimeout(styleDatepicker, 10);
    })
    .on("changeYear", function () {
      setTimeout(styleDatepicker, 10);
    });

  setTimeout(styleDatepicker, 10);

  $("#deliverByToday").on("click", function (e) {
    e.preventDefault();
    showDeliveryTimeSlots("today", this);
  });

  $("#deliverByTomorrow").on("click", function (e) {
    e.preventDefault();
    showDeliveryTimeSlots("tomorrow", this);
  });

  $(".deliverByOther, #goToCalendar").on("click", function (e) {
    e.preventDefault();
    openDeliveryModal(this);
    $(".deliveryDateBox, .calenderBox").removeClass("hidden");
    $(".showTimeBox, .showDeliveryTimeBox, .specialNotesBox").addClass("hidden");
  });

  $(".closeCalender").on("click", function (e) {
    e.preventDefault();
    closeDeliveryModal();
  });

  if (!deliveryDialogEl || typeof deliveryDialogEl.showModal !== "function") {
    $(document).on("keydown", function (e) {
      if (e.key === "Escape" && !$("#deliveryModal").hasClass("hidden")) {
        closeDeliveryModal();
      }
    });

    $("#deliveryModal").on("click", function (e) {
      if (e.target === this) {
        closeDeliveryModal();
      }
    });
  }

  $(".deliverySlot").on("click", function () {
    const slotType = $(this).val().split("_")[0];
    if (slotType === "fixed") {
      generateTimeSlots(slotType, selectedDeliveryDateStr);
      $(".showTimeBox").addClass("hidden");
      $(".showDeliveryTimeBox").removeClass("hidden");
    }
  });

  $(".visibleShowTimeBox").on("click", function (e) {
    e.preventDefault();
    $(".showDeliveryTimeBox").addClass("hidden");
    $(".showTimeBox").removeClass("hidden");
  });

  $("#confirmDelivery, #confirmTimeSlot").on("click", function (e) {
    e.preventDefault();

    deliverySlot = $("input:radio[name='deliveryType']:checked").val();
    deliveryTime = $("input:radio[name='timeSlot']:checked").val() || deliverySlot;
    deliveryDate = selectedDeliveryDateStr;

    if (!deliveryDate) {
      popUpMessage("center", "Please select a delivery date.", "warning");
      return;
    }

    if (!deliverySlot) {
      popUpMessage("center", "Please select a delivery window.", "warning");
      return;
    }

    if (
      deliverySlot.startsWith("fixed") &&
      !$("input:radio[name='timeSlot']:checked").val()
    ) {
      popUpMessage(
        "center",
        "Please select a time slot for fixed-time delivery.",
        "warning"
      );
      return;
    }

    $(".deliveryDateBox").addClass("hidden");
    $(".specialNotesBox").removeClass("hidden").css("display", "");
    const notesHeading = document.getElementById("specialNotesHeading");
    if (notesHeading) notesHeading.focus();
  });

  $(".closeNotes, .backToDelivery").on("click", function (e) {
    e.preventDefault();
    $(".specialNotesBox").addClass("hidden").css("display", "");
    $(".deliveryDateBox").removeClass("hidden");
  });

  $(".reviewBtn").on("click", function (e) {
    e.preventDefault();
    const $btn = $(this);
    $btn.prop("disabled", true).text("Preparing Summary...");
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
        if (data && data.status == 200 && data.redirect) {
          window.location.href = data.redirect;
        } else {
          popUpMessage("center", (data && data.msg) || "Could not save delivery notes.", (data && data.status) || "error");
          $btn.prop("disabled", false).text("Review Order");
        }
      },
      error: function (errorMsg) {
        var msg = (errorMsg && errorMsg.responseJSON && errorMsg.responseJSON.msg) || "Unable to review order. Please try again.";
        var status = (errorMsg && errorMsg.responseJSON && errorMsg.responseJSON.status) || "error";
        popUpMessage("center", msg, status);
        $btn.prop("disabled", false).text("Review Order");
      },
    });
  });
});
