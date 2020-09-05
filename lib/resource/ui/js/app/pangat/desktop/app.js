$(document).ready(function(e) {

    var bookingDate;
    var bookingTime;

    $('.booking-calender').clndr({
        clickEvents: {
            click: function(target){
                bookingDate = target.date._i;
                slotAvailability(bookingDate);
            }
        },
    });

    function slotAvailability(date) {
        
        $('#booking-calender-title').hide();
        $('.booking-calender-box').hide();
        $('.booking-slot').show();

    }

    $('#breakfast').click(function(e) {
        var bookingTime = $(this).attr('id');
        $('.booking-slot').hide();
        $('.form').show();
    });

    $('#lunch').click(function(e) {
        var bookingTime = $(this).attr('id');
        $('.booking-slot').hide();
        $('.form').show();
    });

    $('#dinner').click(function(e) {
        var bookingTime = $(this).attr('id');
        $('.booking-slot').hide();
        $('.form').show();
    });

    $('#submitForm').click(function(e) {
        e.preventDefault();
    });

});