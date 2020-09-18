$(document).ready(function(e) {
    
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
                $('#orderUpdateData').show();
            },
            error: function(data) {
                console.log(data);
                $('#orderUpdateData').show();
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
                alert(data.msg);
                window.location.href='/dashboard/order/?type=list&status=all';
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