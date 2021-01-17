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


/** Fetch device location */

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        msg = "Geolocation is not supported by this browser.";
    }
}
      
function showPosition(position) {

    longitude = position.coords.longitude;
    latitude = position.coords.latitude;

}