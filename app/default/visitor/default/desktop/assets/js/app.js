$(document).ready(function () {
  $("#processToPayment").click(function (e) {
    e.preventDefault();
    $("#processToPayment").hide();

    var couponCode = $("#couponCode").val();

    var formData = $(".shipping-box").serializeArray();
    // formData.push({ name: "coupon", value: couponCode });
    formData.push({ name: "type", value: "order" });
    formData.push({ name: "action", value: "generate" });
    formData.push({ name: "order", value: $("#orderId").val() });
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
        }
        $("#processToPayment").show();
      },
      error: function (data) {
        console.log(`error : `, data);
        $("#processToPayment").show();
      },
    });
  });
});
