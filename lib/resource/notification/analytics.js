$(document).ready(function(e) {

    /** Fetch device location */

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            msg = "Geolocation is not supported by this browser.";
        }
    }
      
    function showPosition(position) {
        msg = "Latitude: " + position.coords.latitude +
        "<br>Longitude: " + position.coords.longitude;
        console.log(msg);
    }

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