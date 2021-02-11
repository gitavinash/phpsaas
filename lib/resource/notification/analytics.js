$(document).ready(function(e) {

    var longitude = '';
    var latitude = '';

    getLocation();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
            'url' : window.location.pathname + window.location.search,
            'type' : 'analytics',
            'action' : 'record',
            'longitude' : longitude,
            'latitude' : latitude,
        },
        error: function(data) {
            console.log(data);
        }
    });

});