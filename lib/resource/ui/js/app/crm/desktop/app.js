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
        $('#orderWaitingBtn').hide();
        var formData = $('#orderData').serializeArray();
        formData.push({name:'action', value:'waiting'})
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data = formData,
            success: function(data) {
                if(data.status==200) {

                    popUpMessage('center', data.msg, 'success');
                    setTimeout(function() {
                        window.location.href='/dashboard/order/?type=list&status=4';
                    }, 1700);
                    

                } else {

                    popUpMessage('center', data.msg, 'error');

                }
                $('#orderWaitingBtn').show();
            },
            error: function(data) {
                console.log(data);
                $('#orderWaitingBtn').show();
            }
        })
    });
    
    $('#orderCancelBtn').click(function(e) {
        e.preventDefault();
        $('#orderCancelBtn').hide();
        var formData = $('#orderData').serializeArray();
        formData.push({name:'action', value:'cancel'})
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data = formData,
            success: function(data) {
                if(data.status==200) {

                    popUpMessage('center', data.msg, 'success');
                    setTimeout(function() {
                        window.location.href='/dashboard/order/?type=list&status=3';
                    }, 1700);
                    

                } else {

                    popUpMessage('center', data.msg, 'error');

                }
                $('#orderCancelBtn').show();
            },
            error: function(data) {
                console.log(data);
                $('#orderCancelBtn').show();
            }
        })
    });
    
    $('#orderReturnBtn').click(function(e) {
        e.preventDefault();
        $('#orderReturnBtn').hide();
        var formData = $('#orderData').serializeArray();
        formData.push({name:'action', value:'rto'})
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data = formData,
            success: function(data) {
                if(data.status==200) {

                    popUpMessage('center', data.msg, 'success');
                    setTimeout(function() {
                        window.location.href='/dashboard/order/?type=list&status=5';
                    }, 1700);
                    

                } else {

                    popUpMessage('center', data.msg, 'error');

                }
                $('#orderReturnBtn').show();
            },
            error: function(data) {
                console.log(data);
                $('#orderReturnBtn').show();
            }
        })
    });
    
    $('#orderDeliverBtn').click(function(e) {
        e.preventDefault();
        $('#orderDeliverBtn').hide();
        var formData = $('#orderData').serializeArray();
        formData.push({name:'action', value:'deliver'})
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data = formData,
            success: function(data) {
                if(data.status==200) {

                    popUpMessage('center', data.msg, 'success');
                    setTimeout(function() {
                        window.location.href='/dashboard/order/?type=list&status=6';
                    }, 1700);
                    

                } else {

                    popUpMessage('center', data.msg, 'error');

                }
                $('#orderDeliverBtn').show();
            },
            error: function(data) {
                console.log(data);
                $('#orderDeliverBtn').show();
            }
        })
    });

});