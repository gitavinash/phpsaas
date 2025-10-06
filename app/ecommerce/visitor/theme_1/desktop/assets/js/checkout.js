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
              $("#shippingName").append(data.name);
              $("#shippingAddress").append(data.address);
              $(".shipping-box").show();
              window.location.href = "/shipping/?id=" + data.order;
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
});
