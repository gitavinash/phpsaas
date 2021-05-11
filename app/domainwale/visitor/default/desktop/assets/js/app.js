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

    // Call searchDomain function on search result page by default

    if($('.show-domain-result').length > 0) {

        // If .show-domain-result class is found on page

        var domainName = $('#domain').val();

        searchDomain(domainName);

    }

    // Call searchDomain function when user click on search domain button

    $('#domainSearch').click(function(e) {
        e.preventDefault();

        var domainName = $('#domain').val();
        searchDomain(domainName);

    });

});