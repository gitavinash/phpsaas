$(document).ready(function() {

    $('#openNav').click(function(e) {
        e.preventDefault();
        document.getElementById("mobileNav").style.width = "100%";
    });

    $('#closeNav').click(function(e) {
        e.preventDefault();
        document.getElementById("mobileNav").style.width = "0%";
    });

    function searchDomain(domain) {
        console.log(domain);
    }

});