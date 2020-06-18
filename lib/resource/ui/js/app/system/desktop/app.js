$(document).ready(function() {
	
	function popUpMessage(msgPostion, msgTxt, msgType) {
		Swal.fire({
			position: msgPostion,
			type: msgType,
			title: msgTxt,
			showConfirmButton: false,
			timer: 1500
		})
	}

	/* login form */

	$('#loginBtn').on('click', function(event) {
		event.preventDefault();

		$('#loginBtn').hide();

		var email = $('#email').val();
		var password = $('#password').val();

		if(email=='') {
			popUpMessage('center', 'Please enter your email', 'warning');
		} else if(password=='') {
			popUpMessage('center', 'Please enter your password', 'warning');
		} else {

			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('#login').serialize(),
				success: function(data) {
					if(data.status=='success') {
						window.location.href='//'+document.domain+data.url;
					} else {
						popUpMessage('center', data.msg, data.status);
					}
				},
				error: function(data) {
					console.log(data);
				}
			})

		}

		$('#loginBtn').show();
		
	});

	/* register form */

	$('#registerBtn').on('click', function(event) {
		event.preventDefault();
		$('#registerBtn').hide();

		var first_name = $('#first_name').val();
		var last_name = $('#last_name').val();
		var mobile = $('#mobile').val();
		var email = $('#email').val();
		var password = $('#password').val();
		var confirmPassword = $('#confirmPassword').val();

		var isPasswordSame=password.localeCompare(confirmPassword);

		if(first_name=='') {
			popUpMessage('center', 'Please enter your first name', 'warning');
		} else if(last_name=='') {
			popUpMessage('center', 'Please enter your last name', 'warning');
		} else if(mobile=='') {
			popUpMessage('center', 'Please enter your mobile', 'warning');
		} else if(email=='') {
			popUpMessage('center', 'Please enter your email', 'warning');
		} else if(password=='') {
			popUpMessage('center', 'Please enter your password', 'warning');
		} else if(confirmPassword=='') {
			popUpMessage('center', 'Please confirm your password', 'warning');
		} else if(isPasswordSame===0) {
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('#register').serialize(),
				success: function(data) {
					if(data.status=='success'){
						window.location.href='//'+document.domain+data.url;
					} else {
						popUpMessage('center', data.msg, data.status);
					}
					$('#registerBtn').show();
				},
				error: function(data) {
					console.log(data);
					$('#registerBtn').show();
				}
			})
		} else {
			popUpMessage('center', 'Your password not matched', 'warning');
		}

		$('#registerBtn').show();

	});
	
	/* Forgot Password form */

	$('#forgotPassword').on('click', function(event) {
		event.preventDefault();
		$('#forgotPassword').hide();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: $('#forgot').serialize(),
			success: function(data) {
				if(data.status=='success') {
					$('.beforeClick').hide();
					$('.afterClick').show();
					$('#statusMsg').text(data.msg);
					popUpMessage('center', data.msg, data.status);
				} else {
					popUpMessage('center', data.msg, data.status);
				}
			},
			error: function(data) {
				console.log(data);
			}
		})
		$('#forgotPassword').show();
	});
	
	/* Update Forgot Password form */

	$('#updatePassword').on('click', function(event) {
		event.preventDefault();
		$('#updatePassword').hide();

		var password = $('#password').val();
		var confirmPassword = $('#confirmPassword').val();

		var isPasswordSame = password.localeCompare(confirmPassword);

		if(password=='') {
			popUpMessage('center', 'Please enter your password', 'warning');
		} else if(confirmPassword=='') {
			popUpMessage('center', 'Please confirm your password', 'warning');
		} else if(isPasswordSame===0) {	
			
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('#update').serialize(),
				success: function(data) {
					if(data.status=='success') {
						$('.beforeClick').hide();
						$('.afterClick').show();
						$('#statusMsg').text(data.msg);
						popUpMessage('center', data.msg, data.status);
					} else {
						popUpMessage('center', data.msg, data.status);
					}
				},
				error: function(data) {
					console.log(data);
				}
			})
			
		} else {
			popUpMessage('center', 'Your password not matched', 'warning');
		}	

		$('#updatePassword').show();
	});

	$('#resendVerificationMail').click(function(event) {
		
		event.preventDefault();
		$('#resendVerificationMail').hide();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: $('#verifyEmail').serialize(),
			success: function(data) {
				if(data.status=='success') {
					$('.beforeClick').hide();
					$('.afterClick').show();
					$('#statusMsg').text(data.msg);
					popUpMessage('center', data.msg, data.status);
				} else {
					popUpMessage('center', data.msg, data.status);
				}
			},
			error: function(data) {
				console.log(data);
			}
		})
		$('#resendVerificationMail').show();

	});


	/** Website Configuration */

	$('#pos1').click(function() {

		$('#pos1').addClass('active');
		$('#pos1Box').css('display', 'block');

		$('#pos2').removeClass('active');
		$('#pos2Box').hide();

		$('#pos3').removeClass('active');
		$('#pos3Box').hide();

		$('#pos4').removeClass('active');
		$('#pos4Box').hide();

	});

	$('#pos2').click(function() {

		$('#pos2').addClass('active');
		$('#pos2Box').css('display', 'block');

		$('#pos1').removeClass('active');
		$('#pos1Box').hide();

		$('#pos3').removeClass('active');
		$('#pos3Box').hide();

		$('#pos4').removeClass('active');
		$('#pos4Box').hide();

	});

	$('#pos3').click(function() {

		$('#pos3').addClass('active');
		$('#pos3Box').css('display', 'block');

		$('#pos1').removeClass('active');
		$('#pos1Box').hide();

		$('#pos2').removeClass('active');
		$('#pos2Box').hide();

		$('#pos4').removeClass('active');
		$('#pos4Box').hide();

	});

	$('#pos4').click(function() {

		$('#pos4').addClass('active');
		$('#pos4Box').css('display', 'block');

		$('#pos1').removeClass('active');
		$('#pos1Box').hide();

		$('#pos2').removeClass('active');
		$('#pos2Box').hide();

		$('#pos3').removeClass('active');
		$('#pos3Box').hide();

	});

	$('#adsTXT').click(function() {
		
		$.ajax({
			type: 'POST',
			data: {
				type : 'api',
				action : 'setting',
				key : 'ads',
				data : $('#ads_txt').val()
			},
			success: function(data) {
				simplePopUp('success', 'success', data.msg)
			}, error : function(data) {
				simplePopUp('error', 'Error', data.msg)
			}
		})

	});

	$('#robotsTXT').click(function() {
		
		$.ajax({
			type: 'POST',
			data: {
				type : 'api',
				action : 'setting',
				key : 'robots',
				data : $('#robots_txt').val()
			},
			success: function(data) {
				simplePopUp('success', 'success', data.msg)
			}, error : function(data) {
				simplePopUp('error', 'Error', data.msg)
			}
		})

	});

	$('#mainfestJSON').click(function() {
		
		$.ajax({
			type: 'POST',
			data: {
				type : 'api',
				action : 'setting',
				key : 'mainfest',
				data : $('#mainfest_json').val()
			},
			success: function(data) {
				simplePopUp('success', 'success', data.msg)
			}, error : function(data) {
				simplePopUp('error', 'Error', data.msg)
			}
		})

	});

});