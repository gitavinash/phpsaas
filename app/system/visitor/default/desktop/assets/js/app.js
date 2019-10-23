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

});