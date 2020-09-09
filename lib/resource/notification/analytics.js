$(document).ready(function(e) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
            'url' : window.location.pathname + window.location.search,
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