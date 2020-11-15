(function ($) {
    "use strict";
    $(window).on('load', function () {
        $('body').addClass('loaded');
    });
    /* *** Menu Navigation *** */
    var OnePageNav = function () {
        var navToggler = $('.ann-js-ann-site-nav-toggle');
        $(".smoothscroll[href^='#'], #ann-navbar ul li a[href^='#']").on('click', function (e) {
            e.preventDefault();
            var hash = this.hash;
            $('html, body').animate({
                scrollTop: $(hash).offset().top
            }, 700, 'easeInOutExpo', function () {
                window.location.hash = hash;
            });
        });
        $("#ann-navbar ul li a[href^='#']").on('click', function (e) {
            if (navToggler.is(':visible')) {
                navToggler.click();
            }
        });
    };
    OnePageNav();
    $(window).scroll(function () {
        var $this = $(this)
            , st = $this.scrollTop()
            , navbar = $('.ann-site-header');
        if (st > 150) {
            if (!navbar.hasClass('scrolled')) {
                navbar.addClass('scrolled');
            }
        }
        if (st < 150) {
            if (navbar.hasClass('scrolled')) {
                navbar.removeClass('scrolled sleep');
            }
        }
        if (st > 350) {
            if (!navbar.hasClass('awake')) {
                navbar.addClass('awake');
            }
        }
        if (st < 350) {
            if (navbar.hasClass('awake')) {
                navbar.removeClass('awake');
                navbar.addClass('sleep');
            }
        }
    });
    $('.ann-js-ann-site-nav-toggle').on('click', function (e) {
        var $this = $(this);
        e.preventDefault();
        if ($('body').hasClass('menu-open')) {
            $this.removeClass('active');
            $('.ann-site-menu .ann-site-menu-inner > ul > li').each(function (i) {
                var that = $(this);
                setTimeout(function () {
                    that.removeClass('is-show');
                }, i * 100);
            });
            setTimeout(function () {
                $('.ann-site-menu').removeClass('ann-site-menu-show');
            }, 800);
            $('body').removeClass('menu-open');
        }
        else {
            $('.ann-site-menu').addClass('ann-site-menu-show');
            $this.addClass('active');
            $('body').addClass('menu-open');
            setTimeout(function () {
                $('.ann-site-menu .ann-site-menu-inner > ul > li').each(function (i) {
                    var that = $(this);
                    setTimeout(function () {
                        that.addClass('is-show');
                    }, i * 100);
                });
            }, 500);
        }
    });
    $('nav .dropdown').hover(function () {
        var $this = $(this);
        $this.addClass('show');
        $this.find('> a').attr('aria-expanded', true);
        $this.find('.dropdown-menu').addClass('show');
    }, function () {
        var $this = $(this);
        $this.removeClass('show');
        $this.find('> a').attr('aria-expanded', false);
        $this.find('.dropdown-menu').removeClass('show');
    });
    $('#dropdown04').on('show.bs.dropdown', function () {
        console.log('show');
    });
    /* *** Main Slider *** */
    $('.ann-main-slider').owlCarousel({
        loop: true
        , margin: 10
        , nav: false
        , autoplay: true
        , items: 1
        , animateIn: "fadeIn"
        , animateOut: "fadeOut"
    });
    /* *** Gallery *** */
    (function () {
        $(document).on("click", ".click-to-expand", function () {
            var imageSrc = $(this).parents(".image-grid").find("img").attr("src");
            $(".js-modal-image").attr("src", imageSrc);
        });
    })();
    /* *** Isotope Active *** */
    $('.gallery-items').imagesLoaded(function () {
        // Add isotope click function
        $('.gallery-filter li').on('click', function () {
            $(".gallery-filter li").removeClass("active");
            $(this).addClass("active");
            var selector = $(this).attr('data-filter');
            $(".gallery-items").isotope({
                filter: selector
                , animationOptions: {
                    duration: 750
                    , easing: 'linear'
                    , queue: false
                , }
            });
            return false;
        });
        $(".gallery-items").isotope({
            itemSelector: '.single-item'
            , layoutMode: 'masonry'
        , });
    });
    /* *** Testimonial Carousel *** */
    $('#testimonial-carousel').owlCarousel({
        loop: true
        , autoplay: true
        , smartSpeed: 500
        , items: 1
        , nav: false
    });
    /* *** Button *** */
    var buttons = document.querySelectorAll(".ann-btn .ann-btn2");
    for (var i = 0; i < buttons.length; i++) {
        var button = buttons[i];
        button.addEventListener("click", function () {
            if (!button.classList.contains("active")) button.classList.add("active");
            else button.classList.remove("active");
        });
    }
})(jQuery);