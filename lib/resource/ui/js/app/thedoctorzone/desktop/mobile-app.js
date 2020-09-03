$(document).ready(function(e){

	/** Default Pop Up Box */
	
	function popUpMessage(msgPostion, msgTxt, msgType) {

		Swal.fire({
			position: msgPostion,
			type: msgType,
			title: msgTxt,
			showConfirmButton: false,
			timer: 1500
		})

	}
    
    /** Send OTP */

    $('#sendOTP').click(function(e) {
        e.preventDefault();
        $('#sendOTP').hide();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: $('#otpForm').serialize(),
            success: function(data) {
                if(data.status==200) {
                    window.location.href=data.url;
                } else {
                    // popUpMessage('center', data.msg, data.status);
                }
                console.log(data);
                $('#sendOTP').show();
            },
            error: function(data) {
                console.log(data);
                // popUpMessage('center', data.msg, data.status);
                $('#sendOTP').show();
            }
        })
    })

    /** Verify OTP */

    $('#verifyOTP').click(function(e) {
        e.preventDefault();
        $('#verifyOTP').hide();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: $('#otpForm').serialize(),
            success: function(data) {
                if(data.status==200) {
                    window.location.href=data.url;
                } else {
                    popUpMessage('center', data.msg, data.status);
                }
                console.log(data);
                $('#verifyOTP').show();
            },
            error: function(data) {
                // popUpMessage('center', data.msg, data.status);
                console.log(data);
                $('#verifyOTP').show();
            }
        })
    })

});