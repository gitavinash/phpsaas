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


    /** Add to cart */

    $('#addToCart').click(function(e) {
      e.preventDefault();
      $('#addToCart').hide();
      var productInfo = $('#productInfo').text();
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

                  console.log(data);

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
      console.log(id);
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

                      console.log(data);
                                  
                      $('#addToCart').show();
                      
                      if(data.status==200) {
                          
                          $('.checkout-form').hide();
                          // $('#shippingName').append(data.name);
                          // $('#shippingAddress').append(data.address);
                          // $('.shipping-box').show();
                          window.location.href='/shipping/?id=' + data.order;

                      } else {
                          popUpMessage('center', data.msg, 'error');
                      }
     
                      $('#continueShipping').show();
                      
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

});