$(document).ready(function() {
	
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
					alert(data.msg);
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
					alert(data.msg);
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
					console.log(data.msg);
				} else {
					alert(data.msg);
				}
			},
			error: function(data) {
				console.log(data);
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
					alert(data.msg);
				}
			},
			error: function(data) {
				console.log(data);
			}
		})
	});

});