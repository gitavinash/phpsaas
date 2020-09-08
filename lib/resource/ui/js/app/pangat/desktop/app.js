$(document).ready(function(e) {

    var bookingDate;

    function callAPI(divIdentity) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                'type' : 'booking',
                'action' : 'availability'
            },
            success: function(data) {
                var availablity = data.availablity;
                $(divIdentity).append("<h3>Select Date and Time<span>Choose a date below to place a request for pangat</span></h3>");
                $.each(availablity, function(key, value) {
                    $(divIdentity).append("</div><div class='date-list' ><div class='date' >" + availablity[key].date + "</div><div id='"+ availablity[key].full_date +"_breakfast' class='bookPangat " + ( availablity[key].breakfast==1 ? 'green' : (availablity[key].breakfast==0 ? 'red' : 'yellow' )  ) + "' >नाश्ता</div><div id='"+ availablity[key].full_date +"_lunch' class='bookPangat " + ( availablity[key].lunch==1 ? 'green' : (availablity[key].lunch==0 ? 'red' : 'yellow' )  ) + "' >दोपहर</div><div id='"+ availablity[key].full_date +"_dinner' class='bookPangat " + ( availablity[key].dinner==1 ? 'green' : (availablity[key].dinner==0 ? 'red' : 'yellow' )  ) + "' >रात्रि</div></div>")
                });
            },
            error: function(data) {
                console.log('Error' + data);
            }
        })
    };

    if($('.calender').is(':visible')) {
        callAPI('.calender');
    } else {
        console.log('Page is not load');
    }

    $(document).on("click", "div.bookPangat" , function() {
        var bookingDate=$(this).attr("id");
        $('.form').show();
        $('.booking-form').hide();
    });

});