$(document).ready(function() {

    /** Check Coupon */

    $('#checkCoupon').click(function(e) {
        e.preventDefault();
        console.log('i m here to check coupon');
    })

    /** Add to cart */

    $('#addToCart').click(function(e) {
        e.preventDefault();
        $('#addToCardPopUp').show();

        

        setTimeout(function() {
            $("#addToCardPopUp").hide('blind', {}, 500)
        }, 5000);
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