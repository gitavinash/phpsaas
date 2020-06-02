$(document).ready(function() {
	
	function popUpMessage(msg) {
		Swal.fire({
			position: 'top-end',
			type: success,
			title: msg,
			showConfirmButton: false,
			timer: 1500
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
					popUpMessage(data.msg);
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