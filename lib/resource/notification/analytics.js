$(document).ready(function(e) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
            'url' : window.location.href,
            'type' : 'analytics',
            'action' : 'record'
        },
        success: function(data) {
            console.log(data.msg);
        },
        error: function(data) {
            console.log(data);
        }
    })
});