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
		url: 'https://api.apify.com/v2/datasets/58a4VXwBBF0HtxuQa/items?format=json&clean=1',
		success : function(data) {
			
			var covidData = data;
			var dataLenght = covidData.length;
			var previousData=covidData[dataLenght-2];
			var latestData=covidData[dataLenght-1];

			var growthCases=latestData.totalCases - previousData.totalCases;
			var growthRecovery=latestData.recovered - previousData.recovered;
			var growthDeath=latestData.deaths - previousData.deaths;
			
			$('#confirmedCases').text(latestData.totalCases);
			$('#activeCases').text(latestData.activeCases);
			$('#recoveredCases').text(latestData.recovered);
			$('#deathCases').text(latestData.deaths);
			$('#dailyCases').text('+ ' + growthCases);
			$('#dailyRecovery').text('+ ' + growthRecovery);
			$('#dailyDeath').text('+ ' + growthDeath);

		},
		error :function() {
			console.log('Error while fetching cobid data');
		},

	})

});