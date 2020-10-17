$(document).ready(function(){

    $("a").on('click', function(event) {
        if (this.hash !== "") {
            event.preventDefault();
            var hash = this.hash;
            $('html, body').animate({
                scrollTop: $(hash).offset().top
            }, 1000, function(){
                window.location.hash = hash;
            });
        }
    });

    $('.homepageSlider').slick({
        arrows: false,
        autoplay: true,
        autoplaySpeed: 2000,
        dots: false,
        infinite: true,
    });

    $('.testimonial-slider').slick({
        arrows: false,
        autoplay: true,
        autoplaySpeed: 3000,
        dots: false,
        infinite: true,
        slidesToShow: 2,
        slidesToScroll: 1
    });
    
});