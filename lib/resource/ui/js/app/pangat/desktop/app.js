$(document).ready(function(e) {

    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const date = urlParams.get('date');

    function callAPI(divIdentity) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                'date' : date,
                'type' : 'booking',
                'action' : 'availability'
            },
            success: function(data) {
                var availablity = data.availablity;
                $(divIdentity).append("<h4>"+ availablity[0].current_month +" <a href='?date="+ availablity[0].next_month +"'>></a></h4>");
                $.each(availablity, function(key, value) {
                    $(divIdentity).append("</div><div class='date-list' ><div class='date' >" + availablity[key].date + "</div><div id='"+ availablity[key].full_date +"_breakfast' class='bookPangat " + ( availablity[key].breakfast==1 ? 'green' : (availablity[key].breakfast==0 ? 'red' : 'yellow' )  ) + "' >नाश्ता</div><div id='"+ availablity[key].full_date +"_lunch' class='bookPangat " + ( availablity[key].lunch==1 ? 'green' : (availablity[key].lunch==0 ? 'red' : 'yellow' )  ) + "' >दोपहर</div><div id='"+ availablity[key].full_date +"_dinner' class='bookPangat " + ( availablity[key].dinner==1 ? 'green' : (availablity[key].dinner==0 ? 'red' : 'yellow' )  ) + "' >रात्रि</div></div>")
                });
            },
            error: function(data) {
                console.log(data);
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