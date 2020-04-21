$(document).ready(function(e){

    $('#menu').click(function(e) {
        $('.full-screen-menu').css('display', 'block');
    });

    $('#closeMenu').click(function(e) {
        $('.full-screen-menu').css('display', 'none');
    })

    $('#search').click(function(e) {
        $('.search-box').css('display', 'block');
        $('#searchBar').focus();
    });

    $('#closeSearch').click(function(e) {
        $('.search-box').css('display', 'none');
    })

    /* Homepage slider */

    $('.homepageSlider').slick({
        infinite: true,
        autoplay: true,
        speed: 1500,
        slidesToShow: 1,
        slidesToScroll: 1,
        nextArrow: false,
        prevArrow: false
    });

    /** PWA Script */

    

});