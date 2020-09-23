$(document).ready(function(e) {

    function popUpMessage(msgPostion, msgTxt, msgType) {

		Swal.fire({
			position: msgPostion,
			type: msgType,
			title: msgTxt,
			showConfirmButton: false,
			timer: 1500
        })

	}
    
    $('#orderUpdateData').click(function(e) {
        e.preventDefault();
        $('#orderUpdateData').hide();
        var formData = $('#orderData').serializeArray();
        formData.push({name:'action', value:'update'})
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data = formData,
            success: function(data) {
                console.log(data);
                if(data.status==200) {
                    popUpMessage('center', data.msg, 'success');
                } else {
                    popUpMessage('center', data.msg, 'error');
                }
                $('#orderUpdateData').show();
            },
            error: function(data) {
                console.log(data);
                $('#orderUpdateData').show();
                popUpMessage('center', data.msg, 'error');
            }
        })
        
    });
    
    $('#orderConfirmBtn').click(function(e) {
        e.preventDefault();
        $('#orderConfirmBtn').hide();
        var formData = $('#orderData').serializeArray();
        formData.push({name:'action', value:'confirm'})
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data = formData,
            success: function(data) {
                if(data.status==200) {

                    popUpMessage('center', data.msg, 'success');
                    setTimeout(function() {
                        window.location.href='/dashboard/order/?type=list&status=all';
                    }, 1700);
                    

                } else {

                    popUpMessage('center', data.msg, 'error');

                }
                $('#orderConfirmBtn').show();
            },
            error: function(data) {
                console.log(data);
                $('#orderConfirmBtn').show();
            }
        })
    });
    
    $('#orderWaitingBtn').click(function(e) {
        e.preventDefault();
    });
    
    $('#orderCancelBtn').click(function(e) {
        e.preventDefault();
    });
    
    $('#orderReturnBtn').click(function(e) {
        e.preventDefault();
    });
    
    $('#orderDeliverBtn').click(function(e) {
        e.preventDefault();
    });

});