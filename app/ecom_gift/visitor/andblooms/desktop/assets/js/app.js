/** SweetAlert Pop Up Box */

let isMainProductAddedToCart = false;

function popUpMessage(msgPostion, msgTxt, msgType) {
  if (msgType == 200) {
    msgType = "success";
  }

  Swal.fire({
    position: msgPostion || "center",
    icon: msgType,
    title: msgTxt,
    showConfirmButton: false,
    timer: 1500,
  });
}

$(document).ready(function () {
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
          isMainProductAddedToCart = true;
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

          if (data.relationType === "event" || window.location.pathname.indexOf("/event") !== -1 || $("#productInfo").attr("data-item-type") === "event" || $("#productInfo").data("item-type") === "event") {
            window.location.href = "/checkout/";
          } else {
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
          }

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

  $(".addAddOnProductToCart").click(function (e) {
    e.preventDefault();
    $(".addAddOnProductToCart").hide();

    var addonProductInfo = $(this).data("info");
    var addonQty = addonProductInfo.split("_");
    var addonId = addonQty[0];
    var addonPrice = addonQty[1];
    var addonQtyVal = parseInt(addonQty[2]) || 1;

    // If main product not added, add both products
    if (!isMainProductAddedToCart) {
      const mainProductInfo = $("#productInfo").attr("value");
      var mainQty = mainProductInfo.split("_");
      var mainId = mainQty[0];
      var mainPrice = mainQty[1];
      var mainQtyVal = parseInt(mainQty[2]) || 1;

      // Make both API calls simultaneously
      Promise.all([
        $.ajax({
          type: "POST",
          dataType: "json",
          data: {
            id: mainId,
            quantity: 1,
            price: mainPrice,
            type: "cart",
            action: "add",
          },
        }),
        $.ajax({
          type: "POST",
          dataType: "json",
          data: {
            id: addonId,
            quantity: 1,
            price: addonPrice,
            type: "cart",
            action: "add",
          },
        }),
      ])
        .then(([mainData, addonData]) => {
          if (mainData.status == 200 && addonData.status == 200) {
            isMainProductAddedToCart = true;
            const cartCircle = document.querySelector(".crt-crcl");
            if (cartCircle) {
              let currentValue = parseInt(cartCircle.textContent.trim(), 10) || 0;
              cartCircle.textContent = currentValue + 2;
            }

            var currentCartCount = parseInt($("#cartItems").text()) || 0;
            $("#cartItems").text(currentCartCount + 2);

            Swal.fire({
              icon: "success",
              title: "Products Added!",
              text: "Both main and add-on products added to cart",
              showCancelButton: true,
              confirmButtonText: "Go to Checkout",
              cancelButtonText: "Continue Shopping",
              reverseButtons: true,
            }).then((result) => {
              if (result.value) {
                window.location.href = "/checkout/";
              }
            });
          }
          $(".addAddOnProductToCart").show();
        })
        .catch((err) => {
          console.error("Error adding products:", err);
          $(".addAddOnProductToCart").show();
        });
    } else {
      // Main product already added, just add addon
      $.ajax({
        type: "POST",
        dataType: "json",
        data: {
          id: addonId,
          quantity: 1,
          price: addonPrice,
          type: "cart",
          action: "add",
        },
        success: function (data) {
          $(".addAddOnProductToCart").show();

          if (data.status == 200) {
            const cartCircle = document.querySelector(".crt-crcl");
            if (cartCircle) {
              let currentValue = parseInt(cartCircle.textContent.trim(), 10) || 0;
              cartCircle.textContent = currentValue + 1;
            }

            var currentCartCount = parseInt($("#cartItems").text()) || 0;
            $("#cartItems").text(currentCartCount + addonQtyVal);

            Swal.fire({
              icon: "success",
              title: "Product Added!",
              text: "Add-on product added to cart",
              showCancelButton: true,
              confirmButtonText: "Go to Checkout",
              cancelButtonText: "Continue Shopping",
              reverseButtons: true,
            }).then((result) => {
              if (result.value) {
                window.location.href = "/checkout/";
              }
            });
          }
        },
        error: function (data) {
          console.error(data);
          $(".addAddOnProductToCart").show();
        },
      });
    }
  });

  /** Close add to cart popup */

  $("#closeAddToCartPopUp").click(function (e) {
    e.preventDefault();
    $("#addToCardPopUp").hide();
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
    const $btn = $(this);
    $("#continueToPayment").hide();
    $btn.prop("disabled", true).text("Processing...");

    var couponCode = $("#couponCode").val();

    var formData = $(".shipping-box").serializeArray();
    formData.push({ name: "coupon", value: couponCode });
    formData.push({ name: "type", value: "order" });
    formData.push({ name: "action", value: "generate" });
    formData.push({ name: "order", value: $("#orderId").val() });
    formData.push({ name: "paymentMode", value: "online" });

    $.ajax({
      type: "POST",
      dataType: "json",
      data: formData,
      success: function (data) {
        if (data.status == 200) {
          window.location.href = data.url;
        } else {
          $("#continueToPayment").show();
          $btn.prop("disabled", false).text("Process to payment");
        }
      },
      error: function (data) {
        $("#continueToPayment").show();
        $btn.prop("disabled", false).text("Process to payment");
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
  if ($(".selectJS").length) {
    $(".selectJS").each(function () {
      const placeholder = $(this).data("title") || "Select";
      $(this).select2({
        placeholder: placeholder,
        width: "100%",
      });
    });
  }
});
