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

	/** Event Box */

	/** Covid 19 Box */

	$.ajax({
		url: 'https://api.covid19india.org/data.json',
		success : function(data) {
			
			var covidData = data.cases_time_series;
			var dataLenght = covidData.length;
			var latestData=covidData[dataLenght-1];
			
			$('#confirmedCases').text(latestData.totalconfirmed);
			$('#activeCases').text(latestData.totalconfirmed-latestData.totalrecovered-latestData.totaldeceased);
			$('#recoveredCases').text(latestData.totalrecovered);
			$('#deathCases').text(latestData.totaldeceased);
			$('#lastUpdate').text(latestData.date + ' 2020');

		},
		error :function() {
			console.log('Error while fetching cobid data');
		},

	})

});