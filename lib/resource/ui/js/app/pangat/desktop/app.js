$(document).ready(function(e) {
    
    $('.booking-calender').clndr({
        clickEvents: {
            click: function(target){
                selectedDate = target.date._i;
                slotAvailability(selectedDate);
            }
        },
    });

    function slotAvailability(date) {
        console.log('Selected Date : ' + date);
        $('.booking-calender').hide();
        $('.booking-slots').hide();
    }

});