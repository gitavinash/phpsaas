$(document).ready(function () {
  $("#processToPayment").click(function (e) {
    e.preventDefault();
    $("#processToPayment").hide();

    var couponCode = $("#couponCode").val();
    var orderType = "default";
    if ($("#orderType").length != 0) {
      orderType = $("#orderType").val();
    }
    var appType = "default";
    if ($("#app").length != 0) {
      appType = $("#app").val();
    }
    var formData = $(".shipping-box").serializeArray();
    formData.push({ name: "coupon", value: couponCode });
    formData.push({ name: "type", value: "order" });
    formData.push({ name: "action", value: "generate" });
    formData.push({ name: "order", value: $("#orderId").val() });
    formData.push({ name: "app", value: appType });
    formData.push({ name: "pgRequestType", value: orderType });
    formData.push({ name: "paymentMode", value: $("#paymentMode").val() });

    $.ajax({
      type: "POST",
      dataType: "json",
      data: formData,
      success: function (data) {
        if (data.status == 200) {
          window.location.href = data.url;
        } else {
          console.log(`Success : `, data);
          var errMsg = data.message || data.msg || 'Payment initialization failed. Please verify your shipping info and try again.';
          if (typeof showCheckoutError === 'function') {
            showCheckoutError(errMsg);
          } else {
            alert(errMsg);
          }
        }
        $("#processToPayment").show();
      },
      error: function (xhr, status, error) {
        console.log(`error : `, xhr);
        var errMsg = 'A network error occurred while connecting to the payment gateway. Please check your connection and try again.';
        if (typeof showCheckoutError === 'function') {
          showCheckoutError(errMsg);
        } else {
          alert(errMsg);
        }
        $("#processToPayment").show();
      },
    });
  });

  // Remove item from cart

  $(".removeFromCart").click(function (e) {
    e.preventDefault();
    var id = $(this).attr("id");

    /** Call remove from cart api */

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
          location.reload();
        } else {
          popUpMessage("center", data.msg, "error");
        }
        $("#product_" + id).hide();
      },
      error: function (data) {
        console.log(data);
      },
    });
  });
});
