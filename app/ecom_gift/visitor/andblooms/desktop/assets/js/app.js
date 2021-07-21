$(document).ready(function() {

  /** SweetAlert Pop Up Box */

	function popUpMessage(msgPostion, msgTxt, msgType) {

		if(msgType==200) {

			msgType='success'

		}

		Swal.fire({
			position: msgPostion,
			type: msgType,
			title: msgTxt,
			showConfirmButton: false,
			timer: 1500
		})

    }
    
    /** Default variable for booking deliveries */

    var todayDateCheck = '';
    var deliveryDate = '';
    var deliveryTime = '';
    var currentTimeInHour = (new Date()).getHours(); //get hours of the day in 24Hr format (0-23)
    var deliveryType = '';
    var deliverySlot = '';
    var orderID = '';

    /** Add to cart */

    $('#addToCart').click(function(e) {
      e.preventDefault();
      $('#addToCart').hide();
      var productInfo = $("#productInfo").attr('value');
      var qty = productInfo.split('_');
      var id = qty[0];
      var price = qty[1];
      var qty = qty[2];

      $.ajax({
          type: 'POST',
          dataType: 'json',
          data: {
              'id' : id,
              'quantity' : 1,
              'price' : price,
              'type' : 'cart',
              'action' : 'add'
          },
          success: function(data) {
                          
              $('#addToCart').show();
              
              if(data.status==200) {
                  $('#cartNumber').html(1)
                  $('#cartNumber').css({'background': '#219518', 'color': '#fff'});
                  $('#addToCardPopUp').show();

                  $('#selectedName').text('');
                  $('#selectedName').append(data.productName);
                  $('#selectedImg').text('');
                  $('#selectedImg').attr('src', data.productImage);
                  $('#selectedQty').text('');
                  $('#selectedQty').append(data.productQty);

                  window.location.href='/checkout/';

              } else {
                  console.log(data.msg);
              }

          },
          error: function(data) {
              console.log(data);
              $('#addToCart').show();
          }
      })

  });

  /** Close add to cart popup */

  $('#closeAddToCartPopUp').click(function(e) {
      e.preventDefault();
      $('#addToCardPopUp').hide();
  })

  /** Remove from cart */

  $('.removeFromCart').click(function(e) {
      e.preventDefault();
      var id = $(this).attr('id');
      
      /** Call remove from cart api */

      $.ajax({
          type: 'POST',
          dataType: 'json',
          data = {
              type: 'cart',
              action: 'remove',
              id: id
          },
          success: function(data) {

              if(data.status==200) {

                  location.reload();

              } else {

                  popUpMessage('center', data.msg, 'error');

              }
              $('#product_'+id).hide();

          },
          error: function(data) {
              console.log(data);
          }
      })

  });

  $('#placeOrder').click(function(e) {
    e.preventDefault();
    $('#checkOut').hide();
    $('#placeOrder').hide();
    $('#shippingDetails').show();
    $('#continueShipping').show();
  });

  /** Checkout form */ 

  $('#continueShipping').click(function(e) {
      e.preventDefault();
      $('#continueShipping').hide();

      var mobile = $('#mobile').val();
      var email = $('#email').val();

      var formData = $('.checkout-form').serializeArray();
      formData.push({name:'type', value:'cart'})
      formData.push({name:'action', value:'checkout'})

      if(mobile!='') {

          if(email!='') {

              $.ajax({
                  type: 'POST',
                  dataType: 'json',
                  data: formData,
                  success: function(data) {

                      $('html, body').animate({ scrollTop: 0 }, 'fast');

                      $('#addToCart').show();
                      
                      if(data.status==200) {
                          
                        $('.checkout-form').hide();
                          // $('#shippingName').append(data.name);
                          // $('#shippingAddress').append(data.address);
                          // $('.shipping-box').show();
                        //   window.location.href='/shipping/?id=' + data.order;
                        $('#shippingDetails').hide();
                        $('#continueShipping').hide();
                        $('#deliveryDetails').show();
                        $('#continueDelivery').show();

                        orderID = data.order;

                      } else {
                          popUpMessage('center', data.msg, 'error');
                          $('#continueShipping').show();

                          console.log(data);

                      }
                      
                  },
                  error: function(data) {
                      popUpMessage('center', data.msg, 'error');
                      $('#continueShipping').show();
                      console.log(data);
                  }
              })

          } else {

              popUpMessage('center', 'Please enter your email', 'error');
              $('#continueShipping').show();

          }

      } else {

          popUpMessage('center', 'Please enter your mobile number', 'error');
          $('#continueShipping').show();

      }
      
  });

  $('#online').click(function(e) {
      $('.shippingCharge').html('Free');
      var totaPrice = $('#subTotal').html();
      $('#finalPrice').html(totaPrice);
      $('#finalPrice2').html(totaPrice);
  });

  $('#cod').click(function(e) {
      $('.shippingCharge').html('<i class="fas fa-rupee-sign"></i> 50/-');
      var totaPrice = $('#finalPrice').html();
      finalPrice = parseInt(totaPrice) + parseInt(50);
      $('#finalPrice').html(finalPrice);
      $('#finalPrice2').html(finalPrice);
  });

  $('#returnToCheckout').click(function(e) {
      $('.shipping-box').hide();
      $('.checkout-form').show();
  });

  $('#continueToPayment').click(function(e) {
      e.preventDefault();
      $('#continueToPayment').hide();

      var couponCode1 = $('#couponCode1').val();
      var couponCode2 = $('#couponCode2').val();

      var couponCode;

      if(couponCode1!='') {
          couponCode=couponCode1;
      }

      if(couponCode2!='') {
          couponCode=couponCode2;
      }

      var formData = $('.shipping-box').serializeArray();
      formData.push({name:'coupon', value: couponCode});
      formData.push({name:'type', value:'order'});
      formData.push({name:'action', value:'generate'});

      $.ajax({
          type: 'POST',
          dataType: 'json',
          data = formData,
          success: function(data) {
              
              if(data.status==200) {

                  window.location.href = data.url;

              } else {

                  console.log(data);

              }
              $('#continueToPayment').show();
          },
          error: function(data) {
              console.log(data);
              $('#continueToPayment').show();
          }
      })

  });

  function showDeliveryTimeSlots(date) {

    switch (date) {

        case 'today' :
            
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            deliveryDate = mm + '/' + dd + '/' + yyyy;
            todayDateCheck = mm + '/' + dd + '/' + yyyy;
            break;
            
        case 'tomorrow' :
            
            var today = new Date( new Date().getTime() + + 24 * 60 * 60 * 1000 );
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            deliveryDate = mm + '/' + dd + '/' + yyyy;
            break;

        default : 
            
            $('.calenderBox').hide();
            deliveryDate = date;
            break;
            
        }
        $('.deliveryDateBox').show();
        $('.deliverySchedule').show();
    
        if(deliveryDate==todayDateCheck) {
            
            if(currentTimeInHour >= '07') {

                $('#morningDeliveryBox').hide();

                if(currentTimeInHour > '19') {

                    $('#standardDeliveryBox').hide();
                    
                }
                
            } else {

                $('#morningDeliveryBox').show();
                $('#standardDeliveryBox').show();

            }

        } else {

            $('#morningDeliveryBox').show();
            $('#standardDeliveryBox').show();

        }
        
        $('.showTimeBox').show();

  }

  $('#deliverByToday').click(function(e) {
      e.preventDefault();
      $('.calenderBox').hide();
      showDeliveryTimeSlots('today');
  });

  $('#deliverByTomorrow').click(function(e) {
      e.preventDefault();
      $('.calenderBox').hide();
      showDeliveryTimeSlots('tomorrow');
  });

  $('#deliverByOther').click(function(e) {
      e.preventDefault();
      $('.showTimeBox').hide();
        $('.deliveryDateBox').show();
        $('.calenderBox').show();
    });

    $('.showCalender').click(function(e) {
        e.preventDefault();
        $('.deliveryDateBox').show();
        $('.calenderBox').show();
        $('.showTimeBox').hide();
    });

    $('.closeCalender').click(function(e) {
        e.preventDefault();
        $('.deliveryDateBox').hide();
        $('.showDeliveryTimeBox').hide();
    });

    $('#calender').datepicker({
        todayHighlight: true,
        startDate: '0',
        maxViewMode: 0
    }).on('changeDate', function(e) {
        showDeliveryTimeSlots(e.format());
    });

    $('.deliverySlot').click(function(e) {

        var deliverySlot = $(this).attr('id').replace('Slots', '');
        
        $('.showTimeBox').hide();
        $('.showDeliveryTimeBox').show();
        $('.morningDeliveryTimeSlots').hide();
        $('.standardDeliveryTimeSlots').hide();
        $('.fixedDeliveryTimeSlots').hide();
        $('.midnightDeliveryTimeSlots').hide();
        $('.'+deliverySlot+'TimeSlots').show();

    });

    $('.visibleShowTimeBox').click(function(e) {
        e.preventDefault();
        
        $('.showDeliveryTimeBox').hide();
        $('.morningTimeSlots').hide();
        $('.standardTimeSlots').hide();
        $('.fixedTimeSlots').hide();
        $('.midnightTimeSlots').hide();
        $('.showTimeBox').show();

        console.log('i m here');
    });

    /** Special notes */

    $('.specialNotesBox').click(function(e) {

        e.preventDefault();
        deliveryType = $("input:radio[name='deliveryType']:checked").val();
        deliveryTime = $("input:radio[name='deliveryTime']:checked").val();

        $('.showDeliveryTimeBox').hide();
        $('.specialNotesBox').show();

    });

    $('.reviewBtn').click(function(e) {
        e.preventDefault();

        var occasion = $('#occasion').val();
        var message = $('#message').val();

        $.ajax({

            type: 'POST',
            dataType: 'json',
            data = {
                type: 'order',
                action: 'meta',
                order_id: orderID,
                occasion: occasion,
                message: message,
                deliveryDate: deliveryDate,
                deliveryTime: deliveryTime,
                deliverySlot: deliveryType
            },
            success: function(data) {
  
                if(data.status==200) {

                    window.location.href=data.redirect;

                } else {

                    popUpMessage('center', data.msg, data.status);

                }
  
            },
            error: function(data) {

                console.log(data);

            }

        });

    });

    // Product Gallery

    if($('#productGalleryImages').length) {

        $('#productGallery').ezPlus({
            gallery: 'productGalleryImages',
            cursor: 'pointer',
            galleryActiveClass: "active",
            imageCrossfade: true,
            // loadingIcon: "images/spinner.gif"
        });

        $("#productGallery").bind("click", function (e) {
            var ez = $('#productGalleryZoom').data('ezPlus');
            ez.closeAll();
            return false;
        });

    }

//     if($('#productGallery').length) {

//         $('#productGallery').zoom({
            
//             // the width/height of the zoomed image
//             source_image_width: 1500, 
//             source_image_height: 1500, 
//             // zoom_area_width: 500,
//             // zoom_area_height: 500,
//             zoom_area_distance: 300,

//             // enable fade in/out transitions    
//             // zoom_easing: true,  

//             // click to zoom or hover to zoom
//             // click_to_zoom: true,

//             // zoom_element: "auto",
//             // show_descriptions: true,
//             // description_location: "bottom",
//             // description_opacity: 0.7,
// //             hide_cursor: false, 
// // speed: 600, 
// // autoplay: false,      
// // autoplay_interval: 6000,
// // keyboard: true,
// // right_to_left: false,
// small_thumbs: 1,  
//         })

//     }

// $(".xzoom, .xzoom-gallery").xzoom({
//     smoothZoomMove: 3,
//     defaultScale: '50%',
//     lensOpacity: 0.5,
//     lensShape: 'circle'
// });

// Product gallery image zoom

function imageZoom(imgID, resultID, previewBoxMagnifierId) {
    
  var img, lens, result, cx, cy;
  img = document.getElementById(imgID);
  result = document.getElementById(resultID);
  /*create lens:*/
  lens = document.createElement("DIV");
  lens.setAttribute("class", "img-zoom");
//   lens.setAttribute("id", previewBoxMagnifierId);
  /*insert lens:*/
  img.parentElement.insertBefore(lens, img);
  /*calculate the ratio between result DIV and lens:*/

  cx = result.offsetWidth / lens.offsetWidth;
  cy = result.offsetHeight / lens.offsetHeight;
  /*set background properties for the result DIV:*/
  result.style.backgroundImage = "url('" + img.src + "')";
  result.style.backgroundSize = (img.width * cx) + "px " + (img.height * cy) + "px";
  /*execute a function when someone moves the cursor over the image, or the lens:*/
  lens.addEventListener("mousemove", moveLens);
  img.addEventListener("mousemove", moveLens);
  /*and also for touch screens:*/
  lens.addEventListener("touchmove", moveLens);
  img.addEventListener("touchmove", moveLens);

  function moveLens(e) {
    var pos, x, y;
    /*prevent any other actions that may occur when moving over the image:*/
    e.preventDefault();
    /*get the cursor's x and y positions:*/
    pos = getCursorPos(e);
    /*calculate the position of the lens:*/
    x = pos.x - (lens.offsetWidth / 2);
    y = pos.y - (lens.offsetHeight / 2);
    /*prevent the lens from being positioned outside the image:*/
    if (x > img.width - lens.offsetWidth) {x = img.width - lens.offsetWidth;}
    if (x < 0) {x = 0;}
    if (y > img.height - lens.offsetHeight) {y = img.height - lens.offsetHeight;}
    if (y < 0) {y = 0;}
    /*set the position of the lens:*/
    lens.style.left = x + "px";
    lens.style.top = y + "px";
    /*display what the lens "sees":*/
    result.style.backgroundPosition = "-" + (x * cx) + "px -" + (y * cy) + "px";
  }

  function getCursorPos(e) {
    var a, x = 0, y = 0;
    e = e || window.event;
    /*get the x and y positions of the image:*/
    a = img.getBoundingClientRect();
    /*calculate the cursor's x and y coordinates, relative to the image:*/
    x = e.pageX - a.left;
    y = e.pageY - a.top;
    /*consider any page scrolling:*/
    x = x - window.pageXOffset;
    y = y - window.pageYOffset;
    return {x : x, y : y};
  }

}

// for (let galleryImages = 1; galleryImages <= $('.zoomImage').length; galleryImages++) {
    
//     // Call image zoom preview function

//     $('.img').hover(function(e) {
//         e.preventDefault();
//         $('.productZoomImagePreview'+galleryImages).show();
//         imageZoom("mainProductGalleryImage"+galleryImages, "productZoomImagePreview"+galleryImages, galleryImages);
//         $('.img-lens'+galleryImages).show();
//     }, function() {
//         $('.productZoomImagePreview'+galleryImages).hide();
//         // $('.img-zoom-lens').hide();
//         $('.img-lens'+galleryImages).remove();
//     });
        
// }

$('.img').hover(function(e) {
    e.preventDefault();
    var previewBoxId = $('.img :first-child').attr('id');
    var previewBoxZoomId = previewBoxId+'Preview';
    var previewBoxMagnifierId = previewBoxId+'Magnifier';
    $('#'+previewBoxZoomId).show();
    $('#'+previewBoxMagnifierId).show();
    $('.img-zoom').show();
    imageZoom(previewBoxId, previewBoxZoomId, previewBoxMagnifierId);
}, function(e) {
    $('.productZoomImagePreview').hide();
    $('.img-zoom').remove();
});

});