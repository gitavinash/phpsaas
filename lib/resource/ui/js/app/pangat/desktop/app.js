$(document).ready(function(e) {

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
                $(divIdentity).append("<div class='heading'><h2>Select Date and Time</h2>");
                $.each(availablity, function(key, value) {
                    $(divIdentity).append("</div><div class='date-list' ><div class='date' >" + availablity[key].date + "</div><div class='bookPangat " + ( availablity[key].breakfast==1 ? 'green' : (availablity[key].breakfast==0 ? 'red' : 'yellow' )  ) + "' >नाश्ता</div><div class='bookPangat " + ( availablity[key].lunch==1 ? 'green' : (availablity[key].lunch==0 ? 'red' : 'yellow' )  ) + "' >दोपहर</div><div class='bookPangat " + ( availablity[key].dinner==1 ? 'green' : (availablity[key].dinner==0 ? 'red' : 'yellow' )  ) + "' >रात्रि</div></div>")
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

});