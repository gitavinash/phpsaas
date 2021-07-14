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

		$('#'+id).hide();

		Swal.fire({
            title: 'Are you sure?',
            text: msgTxt,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
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
						$('#'+id).show();
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
				$('#blockDate').show();
			},
			error: function(data) {
				console.log(data);
			},

		});

    });


	/** Unblock month */

    $('.unblockMonth').click(function(e) {
        e.preventDefault();
		
		confirmMessage('unblockMonth', 'Are you sure to unblock month?', 'booking', 'unblockMonth', $(this).attr('id'));

    });

	/** Calender month visible */

	$('#updateCalenderMonths').click(function(e) {
		e.preventDefault();
		$('#updateCalenderMonths').hide();

		var month = $('#month').val();

		if(month=='' || month==0) {

			popUpMessage('center', 'Please enter a valid number', 'warning');

		} else {

			/** Send data to server using API */

			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('form').serialize(),
				success: function (data) {
					
					if(data.status==200) {

						popUpMessage('center', data.msg, 'success');
						
					} else {
						
						popUpMessage('center', data.msg, data.status);

					}

					$('#updateCalenderMonths').show();

				},
				error: function(data) {
					console.log(data);
					$('#updateCalenderMonths').show();
				}
			});

		}

	});

	// Block multiple dates

	// $('#multipleBlockDate').click(function() {

	// 	$('#multipleBlockDate').hide();
	// 	confirmMessage('dateBlockRange', 'Are you sure to block dates?', 'booking', 'multipleBlockDates', $('#dateBlockRange').val());
	// 	$('#multipleBlockDate').show();

	// });

	// Block month

	$('#blockMonthBookingDates').click(function(e) {
		e.preventDefault();

		var month = '01' + '-' +$('#blockMonth').val() + '-' + $('#blockYear').val();

		confirmMessage('blockMonthBookingDates', 'Are you sure to block month?', 'booking', 'blockMonth', month);
	});
	
});