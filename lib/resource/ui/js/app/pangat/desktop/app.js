$(document).ready(function(e) {

    var bookingDate;

    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const date = urlParams.get('date');


	function popUpMessage(msgPostion, msgTxt, msgType) {

		Swal.fire({
			position: msgPostion,
			type: msgType,
			title: msgTxt,
			showConfirmButton: false,
			timer: 1500
		})

	}

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
                    $(divIdentity).append("</div><div class='date-list' ><div class='date' >" + availablity[key].date + "</div><div id='"+ availablity[key].full_date +"_breakfast' class='bookPangat " + ( availablity[key].breakfast==1 ? 'red' : (availablity[key].breakfast==0 ? 'yellow bookingForm' : 'green bookingForm' )  ) + "' >नाश्ता</div><div id='"+ availablity[key].full_date +"_lunch' class='bookPangat " + ( availablity[key].lunch==1 ? 'red' : (availablity[key].lunch==0 ? 'yellow bookingForm' : 'green bookingForm' )  ) + "' >दोपहर</div><div id='"+ availablity[key].full_date +"_dinner' class='bookPangat " + ( availablity[key].dinner==1 ? 'red' : (availablity[key].dinner==0 ? 'yellow bookingForm' : 'green bookingForm' )  ) + "' >रात्रि</div></div>")
                });
                $('.loader').hide();
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

    $(document).on("click", "div.bookingForm" , function() {
        bookingDate=$(this).attr("id");

        var date = bookingDate.split('_');
        var time = date[1];
        var date = date[0];

        $('#bookingSlotInfo').append('Date: ' + date + ' | ' + time);
        $('.form').show();
        $('.booking-form').hide();
    });

    /** Pangat Booking Form */

    $('#pangatBookBtn').click(function(e) {
        e.preventDefault();
        $('#pangatBookBtn').hide();
        var name = $('#first_name').val();
        var email = $('#email').val();
        var mobile = $('#mobile').val();
        var checkin = $('#selfCheckin').val();

        if(name!='') {

            if(email!='') {

                if(mobile!='') {

                    /** Submit form through API */

                    var date = bookingDate.split('_');
                    var time = date[1];
                    var date = date[0];

                    var formData = $('#enquiryForm').serializeArray();
                    formData.push({name:'bookingDate', value:date})
                    formData.push({name:'bookingTime', value:time})
                    formData.push({name:'type', value:'booking'})
                    formData.push({name:'action', value:'request'})

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        data = formData,
                        success: function(data) {
                            console.log(data);
                            $('#pangatBookBtn').show();
                            $('.form').hide();
                            if(data.status==200) {

                                $('#thankYou').show();
                                $('#mobilenumber').append(data.mobile);

                            } else {

                                $('#error').show();

                            }
                        },
                        error: function(data) {
                            console.log(data);
                            $('#pangatBookBtn').show();
                            $('.form').hide();
                            $('#error').show();
                        }
                    })

                } else {

                    popUpMessage('center', 'Please enter your mobile', 'alert');

                }
                
            } else {

                console.log('Please enter your email');

            }

        } else {

            popUpMessage('center', 'Please enter your name', 'alert');

        }

    })

});