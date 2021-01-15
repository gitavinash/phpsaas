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
                var previousMonthValue = '';

                if(data.previous_month!='') {
                    previousMonthValue = "<span id='previousMonth' ><a href='?date=" + data.previous_month + "'>&#8678;</a>";
                }

                $(divIdentity).append("<h4>"+ previousMonthValue +"</span>"+ data.current_month +" <span id='nextMonth' ><a href='?date="+ data.next_month +"'>&#8680;</a></span></h4>");
                $.each(availablity, function(key, value) {
                    $(divIdentity).append("</div><div class='date-list' ><div class='date' >" + availablity[key].short_date + "</div><div id='"+ availablity[key].full_date +"_breakfast' class='bookPangat " + ( availablity[key].breakfast==1 ? 'red' : (availablity[key].breakfast==0 ? 'yellow bookingForm' : 'green bookingForm' )  ) + "' >नाश्ता</div><div id='"+ availablity[key].full_date +"_lunch' class='bookPangat " + ( availablity[key].lunch==1 ? 'red' : (availablity[key].lunch==0 ? 'yellow bookingForm' : 'green '+ availablity[key].lunch +' bookingForm' )  ) + "' >दोपहर</div><div id='"+ availablity[key].full_date +"_dinner' class='bookPangat " + ( availablity[key].dinner==1 ? 'red' : (availablity[key].dinner==0 ? 'yellow bookingForm' : 'green bookingForm' )  ) + "' >रात्रि</div></div>")
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

        $('#bookingSlotInfo').append('Date: ' + date + ' | ' + time.toUpperCase());
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

                    popUpMessage('center', 'Please enter your mobile', 'warning');
                    $('#pangatBookBtn').show();

                }
                
            } else {

                popUpMessage('center', 'Please enter your email', 'warning');
                $('#pangatBookBtn').show();

            }

        } else {

            popUpMessage('center', 'Please enter your name', 'warning');
            $('#pangatBookBtn').show();

        }

    })

    $('#selfCheckin').click(function(e) {
        if($(this).prop("checked") == true){
            $('#guest').show();
        }
        else if($(this).prop("checked") == false){
            $('#guest').hide();
        }
    });

});