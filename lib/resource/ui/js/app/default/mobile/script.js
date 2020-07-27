$(document).ready(function() {

	console.log('i m here');

	/** SweetAlert Pop Up Box */

	function popUpMessage(msgPostion, msgTxt, msgType) {

		Swal.fire({
			position: msgPostion,
			type: msgType,
			title: msgTxt,
			showConfirmButton: false,
			timer: 1500
		})

	}
	
	/** Image Uplaod Function */

	function imageUpload(formName, returnMsg, btnName) {
		$('#'+btnName).hide();
		var data = new FormData(document.getElementById(formName));
		$.ajax({
			type: 'POST',
			data: data,
			processData: false,
			contentType: false,
			dataType: 'JSON',
			encrypt: 'multipart/form-data',
			success: function(data) {
				popUpMessage('center', data.msg, data.status);
				$('#'+btnName).show();
			},
			error: function(data) {
				console.log(data);
				popUpMessage('center', data.msg, data.status);
				$('#'+btnName).show();
			}
		});
	}

	/** Start Image Preview Section */

	// Image Preview Default Function

	function imagePreview(input, imageName) {
		if (input.files && input.files[0]) {
		  	var reader = new FileReader();
		  	reader.onload = function(e) {
				$('.' + imageName + 'PreviewBox').show();
				$('#' + imageName + 'Path').attr('src', e.target.result);
				$('.' + imageName + 'UploadBox').hide();

				/** Only for feature image */

				if(imageName=='featureImage') {

					if($('#facebookPath').attr('src')=='') {
						$('.facebookPreviewBox').show();
						$('#facebookPath').attr('src', e.target.result);
						$('.facebookUploadBox').hide();
					}
	
					if($('#twitterPath').attr('src')=='') {
						$('.twitterPreviewBox').show();
						$('#twitterPath').attr('src', e.target.result);
						$('.twitterUploadBox').hide();
					}

				}

		  	}
		  	reader.readAsDataURL(input.files[0]);
		}
	}

	// Image Replace Default Function

	function imageReplace(idName) {
		$('.' + idName + 'PreviewBox').hide();
		$('#' + idName + 'Path').attr('src', '');
		$('.' + idName + 'UploadBox').show();
	}

	// change image action

	$('#featureImage').change(function() {
		console.log('i m here');
		imagePreview(this, 'featureImage');
	});

	$('#facebookImage').change(function() {
		imagePreview(this, 'facebook');
	});

	$('#twitterImage').change(function() {
		imagePreview(this, 'twitter');
	});

	$('#profileImage').change(function() {
		$('#updateProfileImage').show();
		imagePreview(this, 'profile');
	});

	$('#siteIconImage').change(function() {
		imagePreview(this, 'siteIcon');
	});

	$('#siteLogoImage').change(function() {
		imagePreview(this, 'siteLogo');
	});

	// replace image actions

	$('#featureChangeImage').click(function() {
		imageReplace('featureImage');
	})

	$('#facebookChangeImage').click(function() {
		imageReplace('facebook');
	})

	$('#twitterChangeImage').click(function() {
		imageReplace('twitter');
	})

	$('#profileChangeImage').click(function() {
		imageReplace('profile');
	})

	$('#siteIconChangeImage').click(function() {
		imageReplace('siteIcon');
		$('#updateSiteIcon').show();
	})

	$('#siteLogoChangeImage').click(function() {
		imageReplace('siteLogo');
		$('#updateSiteLogo').show();
	})

	/** End Image Preview Section */

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

});