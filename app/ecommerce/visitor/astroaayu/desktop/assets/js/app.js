function popUpMessage(msgPostion, msgTxt, msgType) {
  if (msgType == 200) {
    msgType = "success";
  }

  Swal.fire({
    position: msgPostion,
    type: msgType,
    title: msgTxt,
    showConfirmButton: false,
    timer: 1500,
  });
}

const nav = document.querySelector(".nav");
window.addEventListener("scroll", function () {
  if (scrollY > 100) {
    nav.classList.add("nav_active");
  } else {
    nav.classList.remove("nav_active");
  }
});
const toggle = document.querySelector(".toggler");
const nav_res = document.querySelector(".nav_res");
const close = document.querySelector(".close_icon_res");
toggle.addEventListener("click", function () {
  nav_res.classList.toggle("nav_res_active");
});
close.addEventListener("click", function () {
  nav_res.classList.remove("nav_res_active");
});
window.addEventListener("click", function (e) {
  if (e.target == nav_res) {
    nav_res.classList.remove("nav_res_active");
  }
});

/** Add to cart */

$("#addToCart").click(function (e) {
  e.preventDefault();
  $("#addToCart").hide();
  var productInfo = $("#productInfo").attr("value");
  var qty = productInfo.split("_");
  var id = qty[0];
  var price = qty[1];
  var qty = qty[2];

  $.ajax({
    type: "POST",
    dataType: "json",
    data: {
      id: id,
      quantity: 1,
      price: price,
      type: "cart",
      action: "add",
    },
    success: function (data) {
      $("#addToCart").show();

      if (data.status == 200) {
        $("#cartNumber").html(1);
        $("#cartNumber").css({ background: "#219518", color: "#fff" });
        $("#addToCardPopUp").show();

        $("#selectedName").text("");
        $("#selectedName").append(data.productName);
        $("#selectedImg").text("");
        $("#selectedImg").attr("src", data.productImage);
        $("#selectedQty").text("");
        $("#selectedQty").append(data.productQty);

        window.location.href = "/checkout/";
      } else {
        console.log(data.msg);
      }
    },
    error: function (data) {
      console.log(data);
      $("#addToCart").show();
    },
  });
});

/** Remove from cart */

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

/** Checkout form */

$("#continueShipping").click(function (e) {
  e.preventDefault();
  $("#continueShipping").hide();

  var mobile = $("#mobile").val();
  var email = $("#email").val();

  var formData = $(".checkout-form").serializeArray();
  formData.push({ name: "type", value: "cart" });
  formData.push({ name: "action", value: "checkout" });

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
            orderID = data.order;

            window.location = "/shipping/?id=" + orderID;
          } else {
            popUpMessage("center", data.msg, "error");
            $("#continueShipping").show();

            console.log(data);
          }
        },
        error: function (data) {
          popUpMessage("center", data.msg, "error");
          $("#continueShipping").show();
          console.log(data);
        },
      });
    } else {
      popUpMessage("center", "Please enter your email", "error");
      $("#continueShipping").show();
    }
  } else {
    popUpMessage("center", "Please enter your mobile number", "error");
    $("#continueShipping").show();
  }
});

$("#placeOrder").click(function (e) {
  e.preventDefault();
  $("#checkOut").hide();
  $("#placeOrder").hide();
  $("#shippingDetails").show();
  $("#continueShipping").show();
});

$("#continueToPayment").click(function (e) {
  e.preventDefault();
  $("#continueToPayment").hide();

  var couponCode1 = $("#couponCode1").val();
  var couponCode2 = $("#couponCode2").val();

  var couponCode;

  if (couponCode1 != "") {
    couponCode = couponCode1;
  }

  if (couponCode2 != "") {
    couponCode = couponCode2;
  }

  var formData = $(".shipping-box").serializeArray();
  formData.push({ name: "coupon", value: couponCode });
  formData.push({ name: "type", value: "order" });
  formData.push({ name: "action", value: "generate" });

  $.ajax({
    type: "POST",
    dataType: "json",
    data: formData,
    success: function (data) {
      if (data.status == 200) {
        window.location.href = data.url;
      } else {
        console.log(data);
      }
      $("#continueToPayment").show();
    },
    error: function (data) {
      console.log(data);
      $("#continueToPayment").show();
    },
  });

  function getRecaptchaToken() {
    return new Promise(function (resolve, reject) {
      grecaptcha.ready(function () {
        grecaptcha
          .execute("site_key", { action: "subscribe_newsletter" })
          .then(function (token) {
            resolve(token); // <--- this sends token to the .then() below
          })
          .catch(function (error) {
            reject(error); // handles errors if grecaptcha fails
          });
      });
    });
  }

  console.log(`tets recaptcha`);

  if ($(".verifyRecaptcha").lenght) {
    console.log(`Get captcha`);
    getRecaptchaToken(recaptchaSiteKey);
  }
});
