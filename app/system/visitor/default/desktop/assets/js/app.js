$(document).ready(function() {
	
	function popUpMessage(msg, status) {
		Swal.fire({
			type: status,
			title: msg,
			showConfirmButton: false,
			timer: 1000
		  })
	}

	/* login form */

	$('#loginBtn').on('click', function(event) {
		event.preventDefault();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: $('#login').serialize(),
			success: function(data) {
				if(data.status==200) {
					window.location.href='//'+document.domain+data.url;
				} else {
					popUpMessage(data.msg, 'error');
				}
			},
			error: function(data) {
				console.log(data);
			}
		})
	});

	/* register form */

	$('#registerBtn').on('click', function(event) {
		event.preventDefault();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: $('#register').serialize(),
			success: function(data) {
				if(data.status==200){
					window.location.href='//'+document.domain+data.url;
				} else {
					popUpMessage(data.msg, 'error');
				}
			},
			error: function(data) {
				console.log(data);
			}
		})
		event.preventDefault();
	});
	
	/* Forgot Password form */

	$('#forgotPassword').on('click', function(event) {
		event.preventDefault();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: $('#forgot').serialize(),
			success: function(data) {
				if(data.status==200) {
					$('.beforeClick').hide();
					$('.afterClick').show();
					$('#statusMsg').text(data.msg);
					popUpMessage(data.msg, 'success');
				} else {
					popUpMessage(data.msg, 'error');
				}
			},
			error: function(data) {
				popUpMessage(data.msg, 'error');
			}
		})
	});
	
	/* Update Forgot Password form */

	$('#updatePassword').on('click', function(event) {
		event.preventDefault();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: $('#update').serialize(),
			success: function(data) {
				if(data.status==200) {
					$('.beforeClick').hide();
					$('.afterClick').show();
					$('#statusMsg').text(data.msg);
				} else {
					popUpMessage(data.msg, 'error');
				}
			},
			error: function(data) {
				popUpMessage(data.msg, 'error');
			}
		})
	});

});