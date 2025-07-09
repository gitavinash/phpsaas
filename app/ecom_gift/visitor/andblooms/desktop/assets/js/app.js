$(document).ready(function () {
  /** SweetAlert Pop Up Box */

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

  /** Default variable for booking deliveries */

  var todayDateCheck = "";
  var deliveryDate = "";
  var deliveryTime = "";
  var currentTimeInHour = new Date().getHours(); //get hours of the day in 24Hr format (0-23)
  var deliveryType = "";
  var deliverySlot = "";
  var orderID = "";

  // Cart stick

  var cartPurchase = $(".cart-purchase");

  function handleSticky() {
    if ($(window).width() > 768) {
      // Only apply on screens wider than 768px (desktop)
      $(window).on("scroll", function () {
        if ($(window).scrollTop() > 200) {
          cartPurchase.addClass("sticky");
        } else {
          cartPurchase.removeClass("sticky");
        }
      });
    } else {
      cartPurchase.removeClass("sticky"); // Remove sticky class on smaller screens
      $(window).off("scroll"); // Remove scroll event on mobile
    }
  }

  handleSticky(); // Run on page load

  $(window).on("resize", function () {
    handleSticky(); // Re-run when window is resized
  });
  /** Add to cart */

  $("#addToCart").click(function (e) {
    e.preventDefault();
    $("#addToCart").hide();
    var productInfo = $("#productInfo").attr("value");
    var qty = productInfo.split("_");
    var id = qty[0];
    var price = qty[1];
    var qty = parseInt(qty[2]) || 1; // Default to 1 if not specified

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
          // alert('Product added to cart successfully');
          // Get the element
          const cartCircle = document.querySelector(".crt-crcl");

          // Increment the value
          if (cartCircle) {
            let currentValue = parseInt(cartCircle.textContent.trim(), 10) || 0;
            cartCircle.textContent = currentValue + 1;
          }

          Swal.fire({
            icon: "success",
            title: "Product Added!",
            text: "Do you want to go to the checkout page?",
            showCancelButton: true,
            confirmButtonText: "Go to Checkout",
            cancelButtonText: "Continue Shopping",
            reverseButtons: true, // Puts the cancel button first
          }).then((result) => {
            if (result.value) {
              console.log("Redirecting to checkout..."); // Debugging output
              window.location.href = "/checkout/";
            } else if (result.dismiss === Swal.DismissReason.cancel) {
              console.log("User chose to continue shopping.");
            } else {
              console.log("Unexpected action:", result);
            }
          });

          var currentCartCount = parseInt($("#cartItems").text()) || 0;
          $("#cartItems").text(currentCartCount + qty);

          $("#addToCart").html("Added to cart");
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

  /** Close add to cart popup */

  $("#closeAddToCartPopUp").click(function (e) {
    e.preventDefault();
    $("#addToCardPopUp").hide();
  });

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
            } else {
              popUpMessage("center", data.msg, data.error);
              $("#continueShipping").removeClass("hidden");

              console.log(data);
            }
          },
          error: function (data) {
            popUpMessage("center", data.msg, "error");
            $("#continueShipping").removeClass("hidden");
            console.log(data);
          },
        });
      } else {
        popUpMessage("center", "Please enter your email", "error");
        $("#continueShipping").removeClass("hidden");
      }
    } else {
      popUpMessage("center", "Please enter your mobile number", "error");
      $("#continueShipping").removeClass("hidden");
    }
  });

  $("#online").click(function (e) {
    $(".shippingCharge").html("Free");
    var totaPrice = $("#subTotal").html();
    $("#finalPrice").html(totaPrice);
    $("#finalPrice2").html(totaPrice);
  });

  $("#cod").click(function (e) {
    $(".shippingCharge").html('<i class="fas fa-rupee-sign"></i> 50/-');
    var totaPrice = $("#finalPrice").html();
    finalPrice = parseInt(totaPrice) + parseInt(50);
    $("#finalPrice").html(finalPrice);
    $("#finalPrice2").html(finalPrice);
  });

  $("#returnToCheckout").click(function (e) {
    $(".shipping-box").hide();
    $(".checkout-form").show();
  });

  $("#continueToPayment").click(function (e) {
    e.preventDefault();
    $("#continueToPayment").hide();

    var couponCode = $("#couponCode").val();

    var formData = $(".shipping-box").serializeArray();
    formData.push({ name: "coupon", value: couponCode });
    formData.push({ name: "type", value: "order" });
    formData.push({ name: "action", value: "generate" });
    formData.push({ name: "order", value: $("#orderId").val() });
    formData.push({ name: "paymentMode", value: "online" });

    console.log(formData);

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
  });

  function showDeliveryTimeSlots(date) {
    switch (date) {
      case "today":
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, "0");
        var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
        var yyyy = today.getFullYear();
        deliveryDate = mm + "/" + dd + "/" + yyyy;
        todayDateCheck = mm + "/" + dd + "/" + yyyy;
        break;

      case "tomorrow":
        var today = new Date(new Date().getTime() + +24 * 60 * 60 * 1000);
        var dd = String(today.getDate()).padStart(2, "0");
        var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
        var yyyy = today.getFullYear();
        deliveryDate = mm + "/" + dd + "/" + yyyy;
        break;

      default:
        $(".calenderBox").hide();
        deliveryDate = date;
        break;
    }
    $(".deliveryDateBox").show();
    $(".deliverySchedule").show();

    if (deliveryDate == todayDateCheck) {
      if (currentTimeInHour >= "07") {
        $("#morningDeliveryBox").hide();

        if (currentTimeInHour > "19") {
          $("#standardDeliveryBox").hide();
        }
      } else {
        $("#morningDeliveryBox").show();
        $("#standardDeliveryBox").show();
      }
    } else {
      $("#morningDeliveryBox").show();
      $("#standardDeliveryBox").show();
    }

    $(".showTimeBox").show();
  }

  $("#deliverByToday").click(function (e) {
    e.preventDefault();
    $(".calenderBox").hide();
    showDeliveryTimeSlots("today");
  });

  $("#deliverByTomorrow").click(function (e) {
    e.preventDefault();
    $(".calenderBox").hide();
    showDeliveryTimeSlots("tomorrow");
  });

  $("#deliverByOther").click(function (e) {
    e.preventDefault();
    $(".showTimeBox").hide();
    $(".deliveryDateBox").show();
    $(".calenderBox").show();
  });

  $(".showCalender").click(function (e) {
    e.preventDefault();
    $(".deliveryDateBox").show();
    $(".calenderBox").show();
    $(".showTimeBox").hide();
  });

  $(".closeCalender").click(function (e) {
    e.preventDefault();
    $(".deliveryDateBox").hide();
    $(".showDeliveryTimeBox").hide();
  });

  $("#calender")
    .datepicker({
      todayHighlight: true,
      startDate: "0",
      maxViewMode: 0,
    })
    .on("changeDate", function (e) {
      showDeliveryTimeSlots(e.format());
    });

  $(".deliverySlot").click(function (e) {
    var deliverySlot = $(this).attr("id").replace("Slots", "");

    $(".showTimeBox").hide();
    $(".showDeliveryTimeBox").show();
    $(".morningDeliveryTimeSlots").hide();
    $(".standardDeliveryTimeSlots").hide();
    $(".fixedDeliveryTimeSlots").hide();
    $(".midnightDeliveryTimeSlots").hide();
    $("." + deliverySlot + "TimeSlots").show();
  });

  $(".visibleShowTimeBox").click(function (e) {
    e.preventDefault();

    $(".showDeliveryTimeBox").hide();
    $(".morningTimeSlots").hide();
    $(".standardTimeSlots").hide();
    $(".fixedTimeSlots").hide();
    $(".midnightTimeSlots").hide();
    $(".showTimeBox").show();

    console.log("i m here");
  });

  /** Special notes */

  $(".specialNotesBox").click(function (e) {
    e.preventDefault();
    deliveryType = $("input:radio[name='deliveryType']:checked").val();
    deliveryTime = $("input:radio[name='deliveryTime']:checked").val();

    $(".showDeliveryTimeBox").hide();
    $(".specialNotesBox").show();
  });

  $(".reviewBtn").click(function (e) {
    e.preventDefault();

    var occasion = $("#occasion").val();
    var message = $("#message").val();

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
        deliverySlot: deliveryType,
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

  // Product Gallery
  if ($("#productGalleryImages").length) {
    if ($("#productGalleryImages").length) {
      $("#productGallery").ezPlus({
        gallery: "productGalleryImages",
        cursor: "pointer",
        galleryActiveClass: "active",
        imageCrossfade: true,
        // loadingIcon: "images/spinner.gif"
      });

      $("#productGallery").bind("click", function (e) {
        var ez = $("#productGalleryZoom").data("ezPlus");
        ez.closeAll();
        return false;
      });
    }

    $(".piclist li").on("click", function (event) {
      var $pic = $(this).find("img");
      $(".picZoomer-pic").attr("src", $pic.attr("src"));
    });

    $(".piclist li").on("touchstart", function (event) {
      var $pic = $(this).find("img");
      $(".touchedPic").attr("src", $pic.attr("src"));
    });

    // // Product Zoom

    $(".picZoomer").picZoomer();

    $(".sorting").on("change", function () {
      var sorting = $(this).find(":selected").val();
      var url = window.location.href.split("?")[0];
      if (window.location.href.indexOf("?") > 0) {
        window.location = url + "?sort=" + sorting;
        // console.log(url + "?sort=" + sorting);
      } else {
        window.location = url + "?sort=" + sorting;
        // console.log(url + "?sort=" + sorting);
      }
      return false;
    });
  }
  $(".checkCoupon").hide();

  $("#couponCode").on("input", function () {
    code = $(this).val();
    if (code.length != 0) {
      $(".checkCoupon").show();
    } else {
      $(".checkCoupon").hide();
    }
  });

  /** Check Coupon */

  $(".checkCoupon").click(function (e) {
    e.preventDefault();
    var couponCode = $("#couponCode").val();

    var paymentMode = "online";
    var cartPrice = $("#cartPrice").text();

    if (couponCode != "") {
      $(".checkCoupon").hide();

      $.ajax({
        type: "POST",
        dataType: "json",
        data: {
          coupon: couponCode,
          paymentMode: paymentMode,
          cartPrice: cartPrice,
          type: "coupon",
          action: "validate",
        },
        success: function (data) {
          if (data.status == 200) {
            $(".couponMsg").css("color", "#28a745");
            $(".couponMsg").html(data.msg);

            var totalValue = $("#finalPrice").text();

            totalValue = totalValue - data.discount;
            totalValue = totalValue.toFixed(2);

            $("#finalPrice").text(totalValue);
          } else {
            $(".couponMsg").css("color", "#ff0000");
            $(".couponMsg").html(data.msg);
          }

          $(".checkCoupon").show();
        },
        error: function (data) {
          $("#checkCoupon").show();
        },
      });
    } else {
      popUpMessage("center", "Please enter coupon code", "warning");
    }
  });

  if ($(".product-gallery").length) {
    let $mainImg = $(".picZoomer .touchedPic");
    let $thumbnails = $(".piclist li img");
    let $piclist = $(".piclist");
    let scrollAmount = 100; // Scroll speed

    // Change main image on thumbnail click
    $thumbnails.click(function () {
      $(".piclist li img.active").removeClass("active");
      $(this).addClass("active");
      $mainImg.attr("src", $(this).attr("src"));
    });

    // Zoom effect with cursor movement
    $(".picZoomer")
      .on("mousemove", function (e) {
        let offset = $(this).offset();
        let x = ((e.pageX - offset.left) / $(this).width()) * 100;
        let y = ((e.pageY - offset.top) / $(this).height()) * 100;

        $mainImg.css({
          "transform-origin": `${x}% ${y}%`,
          transform: "scale(2)", // Adjust scale as needed
          cursor: "zoom-out",
        });
      })
      .on("mouseleave", function () {
        $mainImg.css({
          transform: "scale(1)",
          cursor: "zoom-in",
        });
      });

    // Zoom effect on click (Mobile)
    $mainImg.on("click", function () {
      if ($(this).hasClass("zoomed")) {
        $(this).removeClass("zoomed").css("transform", "scale(1)");
      } else {
        $(this).addClass("zoomed").css("transform", "scale(2)");
      }
    });

    // Scroll left/right functionality
    $("#prev-img").click(function () {
      $piclist.animate({ scrollLeft: "-=" + scrollAmount }, 300);
    });

    $("#next-img").click(function () {
      $piclist.animate({ scrollLeft: "+=" + scrollAmount }, 300);
    });
  }

  // Added to Cart to cart functionality

  /** Add to cart */
  $(document).on("click", ".quickAddToCart", function (e) {
    e.preventDefault();

    var $btn = $(this);
    $btn.prop("disabled", true).text("Adding...");

    var productInfo = $btn.data("info"); // read data-info
    if (!productInfo) return;

    var [id, price, qty] = productInfo.split("_");

    qty = parseInt(qty) || 1; // Default to 1 if not specified

    $.ajax({
      type: "POST",
      dataType: "json",
      data: {
        id: id,
        quantity: qty || 1,
        price: price,
        type: "cart",
        action: "add",
      },
      success: function (data) {
        $btn.prop("disabled", false).text("Added to Cart");

        if (data.status == 200) {
          // Update cart icon counter
          const $cartCircle = $(".crt-crcl");
          let currentValue = parseInt($cartCircle.text().trim(), 10) || 0;
          $cartCircle.text(currentValue + 1);

          // Show success popup
          $("#cartNumber")
            .html(currentValue + 1)
            .css({ background: "#219518", color: "#fff" });
          $("#addToCardPopUp").show();

          $("#selectedName").text(data.productName || "");
          $("#selectedImg").attr("src", data.productImage || "");
          $("#selectedQty").text(data.productQty || "");

          Swal.fire({
            icon: "success",
            title: "Product Added!",
            text: "Product has been added to your cart",
            showCancelButton: true,
            confirmButtonText: "Go to Checkout",
            cancelButtonText: "Continue Shopping",
            reverseButtons: true,
          }).then((result) => {
            if (result.value) {
              window.location.href = "/checkout/";
            }
          });

          var currentCartCount = parseInt($("#cartItems").text()) || 0;
          $("#cartItems").text(currentCartCount + qty);

          $btn.text("Added to Cart");
        } else {
          console.error("Cart Error:", data.msg);
        }
      },
      error: function (xhr) {
        console.error("Ajax Error:", xhr.responseText);
        $btn.prop("disabled", false).text("Added to Cart");
      },
    });
  });
});
