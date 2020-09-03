$(document).ready(function(e){
    
    /** Send OTP */

    $('#sendOTP').click(function(e) {
        e.preventDefault();
        $('#sendOTP').hide();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: $('#otpForm').serialize(),
            success: function(data) {
                console.log(data);
                $('#sendOTP').show();
            },
            error: function(data) {
                console.log(data);
                $('#sendOTP').show();
            }
        })
    })

});