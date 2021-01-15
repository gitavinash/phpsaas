$(document).ready(function() {

	var explodeKey = '_isw_';
	var otherValue = '';

	/** SweetAlert Pop Up Box */

	function popUpMessage(msgPostion, msgTxt, msgType) {

		if(msgType==200) {

			msgType='success'
			
		}

		Swal.fire({
			position: msgPostion,
			type: msgType,
			title: msgTxt,
			showConfirmButton: false,
			timer: 1500
		})

	}

	function confirmMessage(id, msgTxt, type, action, otherValue) {

		Swal.fire({
            title: 'Are you sure?',
            text: msgTxt,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if (result.value) {

				/** Calling pangat api */

				$.ajax({
					type: 'POST',
					dataType: 'json',
					data: {
						'type': type,
						'action': action,
						'id': id,
						'key': explodeKey,
						'other': otherValue,
					},
					success: function(data) {
						if(data.status==200) {
							popUpMessage('center', data.msg, data.status)
							setTimeout(function() {
								location.reload();
							}, 1500)
						} else {
							console.log(data);
						}
					},
					error: function(data) {
						console.log(data);
					}
				})

	        }

        })

    }
    
    /** Cancel booking */

    $('.bookingCancelBtn').click(function(e) {
		e.preventDefault();

        // calling confirm message
        confirmMessage($(this).attr('href'), $(this).attr('msg'), 'booking', $(this).attr('action'),  otherValue);

    });

    $('.bookingConfirmBtn').click(function(e) {
		e.preventDefault();
		
		otherValue = {
			'date' : $(this).attr('date'),
			'time' : $(this).attr('time')
		}

        // calling confirm message
        confirmMessage($(this).attr('href'), $(this).attr('msg'), 'booking', $(this).attr('action'), otherValue);

    });

    function cancelPangatFunction(bookingID) {
        console.log(bookingID);
    }

	/** Start Code of Website Configuration Accordion Tabs */

	//  Website Configuration tab

	$('#configurationBtn').click(function() {

		$('#configurationBtn').addClass('active');
		$('#configurationTabBox').css('display', 'block');

		$('#adsTab').removeClass('active');
		$('#adsTabBox').hide();

		$('#robotsTab').removeClass('active');
		$('#robotsTabBox').hide();

		$('#mainfestTab').removeClass('active');
		$('#mainfestTabBox').hide();

	});

	$('#adsTab').click(function() {

		$('#adsTab').addClass('active');
		$('#adsTabBox').css('display', 'block');

		$('#configurationBtn').removeClass('active');
		$('#configurationTabBox').hide();

		$('#robotsTab').removeClass('active');
		$('#robotsTabBox').hide();

		$('#mainfestTab').removeClass('active');
		$('#mainfestTabBox').hide();

	});

	$('#robotsTab').click(function() {

		$('#robotsTab').addClass('active');
		$('#robotsTabBox').css('display', 'block');

		$('#configurationBtn').removeClass('active');
		$('#configurationTabBox').hide();

		$('#adsTab').removeClass('active');
		$('#adsTabBox').hide();

		$('#mainfestTab').removeClass('active');
		$('#mainfestTabBox').hide();

	});

	$('#mainfestTab').click(function() {

		$('#mainfestTab').addClass('active');
		$('#mainfestTabBox').css('display', 'block');

		$('#configurationBtn').removeClass('active');
		$('#configurationTabBox').hide();

		$('#adsTab').removeClass('active');
		$('#adsTabBox').hide();

		$('#robotsTab').removeClass('active');
		$('#robotsTabBox').hide();

	});

	/** Website Configuration */

	$('#updateWebsite').click(function(e) {
		e.preventDefault();

		$('#updateWebsite').hide();

		var websiteName = $('#websiteName').val();
		var websiteDescription = $('#websiteDescription').val();
		var websiteKeyword = $('#websiteKeyword').val();

		if(websiteName=='') {
			popUpMessage('center', 'Please enter website name', 'warning');
			$('#updateWebsite').show();
		} else if(websiteDescription=='') {
			popUpMessage('center', 'Please enter website description', 'warning');
			$('#updateWebsite').show();
		} else if(websiteKeyword=='') {
			popUpMessage('center', 'Please enter website keywords', 'warning');
			$('#updateWebsite').show();
		} else {
			
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('#websiteData').serialize(),
				success: function(data) {
					popUpMessage('center', data.msg, data.status);
					$('#updateWebsite').show();
				},
				error: function(data) {
					console.log(data);
					$('#updateWebsite').show();
				}
			})

		}

	});

	/** Ads.txt */

	$('#updateAdsTxt').click(function(e) {
		e.preventDefault();
		$('#updateAdsTxt').hide();
		$.ajax({
			type: 'POST',
			data: {
				type : 'setting',
				action : 'ads',
				adsData : $('#ads_txt').val()
			},
			success: function(data) {
				popUpMessage('center', data.msg, data.status);
				$('#updateAdsTxt').show();
			}, error : function(data) {
				console.log(data)
				popUpMessage('center', data.msg, data.status);
				$('#updateAdsTxt').show();
			}
		})

	});

	/** Robots.txt Website Configuration tab */

	$('#updateRobotTxt').click(function(e) {
		e.preventDefault();
		$('#updateRobotTxt').hide();
		
		$.ajax({
			type: 'POST',
			data: {
				type : 'setting',
				action : 'robots',
				robotsData : $('#robots_txt').val()
			},
			success: function(data) {
				popUpMessage('center', data.msg, data.status);
				$('#updateRobotTxt').show();
			}, error : function(data) {
				console.log(data);
				popUpMessage('center', data.msg, data.status);
				$('#updateRobotTxt').show();
			}
		})

	});

	/** Mainfest.json Website Configuration tab */

	$('#updateMainfestJson').click(function(e) {
		e.preventDefault();
		$('#updateMainfestJson').hide();
		
		$.ajax({
			type: 'POST',
			data: {
				type : 'setting',
				action : 'mainfest',
				mainfestData : $('#mainfest_json').val()
			},
			success: function(data) {
				popUpMessage('center', data.msg, data.status);
				$('#updateMainfestJson').show();
			}, error : function(data) {
				console.log(data);
				popUpMessage('center', data.msg, data.status);
				$('#updateMainfestJson').show();
			}
		})

	});

	/** End Code of Website Configuration Accordion Tabs */

	/** Update Password */

	$('#updatePasswordBtn').click(function(e) {
		e.preventDefault();
		$('#updatePasswordBtn').hide();

		var password = $('#password').val();
		var confirmPassword = $('#confirmPassword').val();

		var isPasswordSame = password.localeCompare(confirmPassword);

		if(password=='') {
			popUpMessage('center', 'Please enter your password', 'warning');
			$('#updatePasswordBtn').show();
		} else if(confirmPassword=='') {
			popUpMessage('center', 'Please confirm your password', 'warning');
			$('#updatePasswordBtn').show();
		} else if(isPasswordSame===0) {	
			
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('#updatePassword').serialize(),
				success: function(data) {

					popUpMessage('center', data.msg, data.status);

					$('#password').val('');
					$('#confirmPassword').val('');

					$('#updatePasswordBtn').show();
				},
				error: function(data) {
					console.log(data);
					$('#updatePasswordBtn').show();
				}
			})
			
		} else {
			popUpMessage('center', 'Your password not matched', 'warning');
			$('#updatePasswordBtn').show();
		}

	})

	/** Change Site Logo */

	$('#updateSiteLogo').click(function(e) {
		imageUpload('updateSiteLogoForm', true, 'updateSiteLogo');
	});

	/** Change Website Icon */

	$('#updateSiteIcon').click(function(e) {
		imageUpload('updateSiteIconForm', true, 'updateSiteIcon');
	});

	/** Change Profile Picture */

	$('#updateProfileImage').click(function() {
		imageUpload('updateProfileImageForm', true, 'updateProfileImage');
	});

	/** User profile update */

	$('#updateProfile').click(function(e) {
		e.preventDefault();
		$('#updateProfile').hide();
		var firstName = $('#firstName').val();
		var lastName = $('#lastName').val();
		var fullName = $('#fullName').val();
		var mobile = $('#mobile').val();

		if(firstName=='') {
			popUpMessage('center', 'Please enter your first name', 'warning');
			$('#updateProfile').show();
		} else if(lastName=='') {
			popUpMessage('center', 'Please enter your last name', 'warning');
			$('#updateProfile').show();
		} else if(fullName=='') {
			popUpMessage('center', 'Please enter your full name', 'warning');
			$('#updateProfile').show();
		} else if(mobile=='') {
			popUpMessage('center', 'Please enter your mobile number', 'warning');
			$('#updateProfile').show();
		} else {

			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('#profileData').serialize(),
				success: function(data) {
					popUpMessage('center', data.msg, data.status);
					$('#updateProfile').show();
				},
				error: function(data) {
					console.log(data);
					$('#updateProfile').show();
				}
			})			

		}

	});
	
	$('#table').DataTable({
		dom: 'Bfrtip',
		buttons: [{
			extend: 'pdf',
			title: 'Download PDF',
			text: 'Download PDF',
			filename: 'booking_list' + Date.now()
		}, {
			extend: 'excel',
			title: 'Download Excel',
			text: 'Download Excel',
			filename: 'booking_list' + new Date()
		}, {
			extend: 'csv',
			title: 'Download CSV',
			text: 'Download CSV',
			filename: 'booking_list' + new Date()
		}]
	});

	/** Unblock booking date */

    $('.unblockDate').click(function(e) {
        e.preventDefault();
		var date = $(this).attr('id');
		
		$.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                'date' : date,
                'type' : 'booking',
                'action' : 'unblockDate'
            },
			success: function(data) {
				popUpMessage('center', data.msg, data.status);

				if(ret.status==200) {
		
				  window.setTimeout(function() {
					  location.reload()
				  }, 1600);
		
				} else {
		
				  $('#blockDate').show();
		
				}
			},
			error: function(data) {
				console.log(data);
			},

		});

    })
	
});