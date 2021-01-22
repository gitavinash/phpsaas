$(document).ready(function() {

    /** Pop up */

    // $('#popUp').show();

    // $('#popUpClose').click(function(e) {

    //     $('#popUp').hide();

    // });

    // document.onkeydown = function(evt) {
    //     evt = evt || window.event;
    //     if (evt.keyCode == 27) {
            
    //         $('#popUp').hide();
            
    //     }
    // };

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

    /** Check Coupon */

    $('.checkCoupon').click(function(e) {
        e.preventDefault();
 
        var couponCode1 = $('#couponCode1').val();
        var couponCode2 = $('#couponCode2').val();

        var paymentMode = $('input[name="paymentMode"]:checked').val();
        var cartPrice = $('#cartPrice').text();
        
        var couponCode='';

        if(couponCode1!='') {
            couponCode=couponCode1;
        }

        if(couponCode2!='') {
            couponCode=couponCode2;
        }

        if(couponCode!='') {

            $('.checkCoupon').hide();

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    'coupon' : couponCode,
                    'paymentMode' : paymentMode,
                    'cartPrice' : cartPrice,
                    'type' : 'coupon',
                    'action' : 'validate'
                },
                success: function(data) {
                    
                    if(data.status==200) {

                        $('.couponMsg').css('color', '#28a745');
                        $('.couponMsg').html(data.msg);

                        var totalValue=$('#finalPrice2').text();

                        totalValue=totalValue-data.discount;
                        totalValue=totalValue.toFixed(2);

                        $('#finalPrice').text(totalValue);
                        $('#finalPrice2').text(totalValue);

                    } else {
                        $('.couponMsg').css('color', '#ff0000');
                        $('.couponMsg').html(data.msg);
                    }
                    
                    $('.checkCoupon').show();
    
                },
                error: function(data) {
                    console.log(data);
                    $('#checkCoupon').show();
                }
            });

        } else {
            
            popUpMessage('center', 'Please enter coupon code', 'warning');

        }
        
    })

    /** Add to cart */

    $('#addToCart').click(function(e) {
        e.preventDefault();
        $('#addToCart').hide();
        var qty = $('#qty').val().split('_');
        console.log(qty);
        var id = qty[0];
        var price = qty[2];
        var qty = qty[1];

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                'id' : id,
                'quantity' : qty,
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

                        $('#addToCart').show();
                        
                        if(data.status==200) {
                            
                            $('.checkout-form').hide();
                            // $('#shippingName').append(data.name);
                            // $('#shippingAddress').append(data.address);
                            // $('.shipping-box').show();
                            window.location.href='/shipping/?id=' + data.order;

                        } else {
                            popUpMessage('center', data.msg, 'error');
                            $('#continueShipping').show();
                            console.log(data);
                        }
                        
                    },
                    error: function(data) {
                        console.log(data);
                        popUpMessage('center', data.msg, 'error');
                        $('#continueShipping').show();
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

        $('#couponCode1').val('');
        $('#couponCode2').val('');
        $('.couponMsg').html('');

    });

    $('#cod').click(function(e) {
        $('.shippingCharge').html('<i class="fas fa-rupee-sign"></i> 50/-');
        var totaPrice = $('#subTotal').html();
        finalPrice = parseInt(totaPrice) + parseInt(50);
        finalPrice = finalPrice.toFixed(2);
        $('#finalPrice').html(finalPrice);
        $('#finalPrice2').html(finalPrice);

        $('#couponCode1').val('');
        $('#couponCode2').val('');
        $('.couponMsg').html('');

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

    /** Contact us form */

    $('#submitContactUsForm').click(function(e) {
        e.preventDefault(e);
    });

});