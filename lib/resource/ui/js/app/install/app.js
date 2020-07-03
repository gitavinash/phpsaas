$(document).ready(function() {

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

    /** Website Installation */

    $('#installBtn').click(function(e) {
        e.preventDefault();
        $('#installBtn').hide();

        var siteName = $('#siteName').val();
        var siteDesc = $('#siteDesc').val();
        var email = $('#email').val();
        var password = $('#password').val();
        var confirmPassword = $('#confirmPassword').val();
        var lang = $('#siteLang').val();

        var isPasswordSame=password.localeCompare(confirmPassword);

        if(siteName=='') {
            popUpMessage('center', 'Please enter website name', 'warning');
            $('#installBtn').show();
        } else if(siteDesc=='') {
            popUpMessage('center', 'Please enter website description', 'warning');
            $('#installBtn').show();
        } else if(email=='') {
            popUpMessage('center', 'Please enter your email', 'warning');
            $('#installBtn').show();
        } else if(lang=='') {
            popUpMessage('center', 'Please select website language', 'warning');
            $('#installBtn').show();
        } else if(password=='') {
            popUpMessage('center', 'Please enter your password', 'warning');
            $('#installBtn').show();
        } else if(confirmPassword=='') {
            popUpMessage('center', 'Please confirm your password', 'warning');
            $('#installBtn').show();
        } else if(isPasswordSame===0) {

            /** Call the API using AJAX */
            
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: $('#installWebsite').serialize(),
                success: function(data) {
                    if(data.status=='success'){
                        $('#loginBox').hide();
                        $('#thankYouBox').show();
                        popUpMessage('center', data.msg, data.status);
                        window.setTimeout(function(){
                            window.location.href='//'+document.domain+data.url;
                        }, 7000);
                    } else {
                        popUpMessage('center', data.msg, data.status);
                        $('#installBtn').show();
                    }
                },
                error: function(data) {
                    console.log(data);
                    $('#installBtn').show();
                }
            })

        } else {
            popUpMessage('center', 'Your password not matched', 'warning');
        }

    })

})