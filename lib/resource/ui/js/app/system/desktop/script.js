$(document).ready(function() {

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

});