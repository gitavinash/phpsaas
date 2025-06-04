$(document).ready(function () {
  // Toggle overlay navigation
  $(".top_menu").on("click", function () {
    $(".overlay").toggleClass("show");
  });

  $(".close").on("click", function () {
    $(".overlay").toggleClass("show");
  });

  // Toggle mobile search
  $(".mobile_search").on("click", function () {
    $(".search_overlay").toggleClass("active");
  });

  $(".cancel_search__input").on("click", function () {
    $(".search_overlay").toggleClass("active");
  });

  // Filter toggle
  $(".filter_btn").on("click", function () {
    $(".filter_overlay").toggleClass("myfilter");
  });

  $(".filter_cancel").on("click", function () {
    $(".filter_overlay").toggleClass("myfilter");
  });

  // Image change on click
  window.myFunction = function (smallImg) {
    $("#imgBox").attr("src", $(smallImg).attr("src"));
  };

  // Slick carousel
  if ($(".testimonial_slider").length) {
    $(".testimonial_slider").slick({
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 1,
      autoplay: false,
      autoplaySpeed: 2000,
      arrows: false,
      dots: true,
      responsive: [
        {
          breakpoint: 1200,
          settings: {
            slidesToShow: 2,
          },
        },
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 2,
          },
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1,
          },
        },
        {
          breakpoint: 576,
          settings: {
            slidesToShow: 1,
          },
        },
      ],
    });
  }

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
});
