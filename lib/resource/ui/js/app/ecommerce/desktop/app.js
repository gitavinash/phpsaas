$(document).ready(function() {

    /** Check Coupon */

    $('#checkCoupon').click(function(e) {
        e.preventDefault();
        console.log('i m here to check coupon');
    })

    /** Add to cart */

    $('#addToCart').click(function(e) {
        e.preventDefault();
        $('#addToCart').hide();
        var qty = $('#qty').val().split('_');
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

    $('#removeFromCart').click(function(e) {
        e.preventDefault();
        $('#addToCardPopUp').hide();
    });

});