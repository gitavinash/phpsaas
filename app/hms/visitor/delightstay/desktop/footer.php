<footer>
    <section class="horizontal_padding">
        <div class="container">
            <div class="row  footer_padding ">
                <div class="col-12 col-md-12 col-lg-4 ">
                    <a class="navbar-brand main_logo" href="/">
                        <img src="<?php echo USER_IMAGES ?>mgi_logo.jpeg" alt="" class="img-fluid">
                    </a>
                    <p class="py-5 delight_footer opacity-50"><?php echo SITE_DESC ?></p>
                </div>

                <div class="col-6 col-md-6 col-lg-2 ps-2 ps-md-3 ps-lg-5 ">
                    <h5 class="orange">TOURS</h5>
                    <ul class="jimcorbett pt-4 opacity-50">
                        <li>
                            <a href="/manali/hotel-in-manali/">Manali</a>
                        </li>
                        <li>
                            <a href="/shimla/hotel-in-shimla/">Shimla</a>
                        </li>
                        <li>
                            <a href="/jim-corbett/hotel-in-jim-corbett/">Jim Corbett</a>
                        </li>
                        <li>
                            <a href="/mussoorie/hotel-in-mussoorie/">Mussoorie</a>
                        </li>
                        <li>
                            <a href="/goa/hotel-in-goa/">Goa</a>
                        </li>
                        <li>
                            <a href="/kashmir/hotel-in-kashmir/">Kashmir</a>
                        </li>
                        <li>
                            <a href="/jaipur/hotel-in-jaipur/">Jaipur</a>
                        </li>
                        <li>
                            <a href="/leh/hotel-in-leh/">Leh</a>
                        </li>
                    </ul>
                </div>
                <div class="col-6 col-md-6 col-lg-2">
                    <h5 class="orange ">COMPANY</h5>
                    <ul class="jimcorbett pt-4 opacity-50">
                        <li>
                            <a href="/">Home</a>
                        </li>
                        <li>
                            <a href="/about-us/">About Us</a>
                        </li>
                        <li>
                            <a href="/contact-us/">Contact Us</a>
                        </li>
                        <li>
                            <a href="/privacy-policy/">Privacy Policy</a>
                        </li>
                        <li>
                            <a href="/terms-and-conditions/">Terms & Conditions</a>
                        </li>
                        <li>
                            <a href="/refund-policy/">Refund Policy</a>
                        </li>

                    </ul>
                </div>
                <div class="col-12 col-md-12 col-lg-4 pt-5 pt-md-5 pt-lg-0 p-0">
                    <h5 class="orange">CONTACT INFO</h5>
                    <div class="d-flex align-items-start pt-4 ">
                        <img src="<?php echo USER_IMAGES ?>icon/Location.svg" alt="" class="pe-2">
                        <address class="opacity-50">
                            Musafir Go India<br />212 A, Suneja Tower 1, District Center Janakpuri,<br />New Delhi -
                            110058<br>
                        </address>
                    </div>
                    <div class="d-flex align-items-start py-2 ">
                        <img src="<?php echo USER_IMAGES ?>icon/Call.svg" alt="" class="pe-2">
                        <a href="tel:+91-7827515902" class="opacity-50">
                            +91-7827515902
                        </a>
                    </div>
                    <div class="d-flex align-items-start py-2">
                        <img src="<?php echo USER_IMAGES ?>icon/Message.svg" alt="" class="pe-2">
                        <a href="" class="opacity-50">reservations@mgihotels.com</a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12 col-md-10 col-lg-8 py-3 opacity-50">
                    &copy; COPYRIGHT <?php echo date('Y') ?> <?php echo SITE_NAME ?>. ALL RIGHT RESERVED | Design
                    and Developed by <a href="https://blumox.com" title="Travel Portal Development Company">Blumox
                        Technologies</a>
                </div>
                <div
                    class="col-12 col-md-4 col-lg-4 d-flex  justify-content-center justify-content-md-center justify-content-lg-end   ">
                    <div class="py-3">
                        <a href="https://www.instagram.com/musafirgoindia212/" target="_blank">
                            <img src="<?php echo USER_IMAGES ?>icon/insta.svg" alt="" class="px-2">
                        </a>
                        <a href="https://www.facebook.com/GoIndiaMusafir/" target="_blank">
                            <img src="<?php echo USER_IMAGES ?>icon/fec.svg" alt="">
                        </a>
                        <a href="https://x.com/musafirgoindia" target="_blank">
                            <img src="<?php echo USER_IMAGES ?>icon/tiwitter.svg" alt="" class="px-2">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"
    integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(document).ready(function() {
    $('.datepicker').daterangepicker({
        singleDatePicker: false, // Allow date range selection
        showDropdowns: true,
        minDate: moment(), // Set minimum date to the current date
        maxDate: moment().add(1, 'year'), // Set maximum date to one year from the current date
        startDate: moment(), // Default start date is today
        endDate: moment().add(1, 'days'), // Default end date is tomorrow (minimum 1-day gap)
        autoApply: true, // Auto close the picker when a date range is selected
        maxSpan: {
            days: 365
        }, // Limit the maximum span to 1 year
        showCustomRangeLabel: true, // Always show the custom range option
        ranges: {}, // No predefined ranges
        locale: {
            format: 'DD-MM-YYYY',
            customRangeLabel: 'Custom Range'
        },
        isInvalidDate: function(date) {
            // Get the selected start date
            var startDate = $('.datepicker').data('daterangepicker').startDate;
            // If start date is defined and date is within 1 day of the start date, disable it
            if (startDate && date.diff(startDate, 'days') < 1 && date.diff(startDate, 'days') > -
                1) {
                return true;
            }
            return false;
        }
    });

    // Hide the predefined ranges when the calendar is shown
    $('.datepicker').on('show.daterangepicker', function(ev, picker) {
        picker.container.find('.ranges').hide();
        picker.container.find('.drp-calendar.left').show();
        picker.container.find('.drp-calendar.right').show();
    });


    // Hide the predefined ranges when the calendar is shown
    $('.datepicker').on('show.daterangepicker', function(ev, picker) {
        picker.container.find('.ranges').hide();
        picker.container.find('.drp-calendar.left').show();
        picker.container.find('.drp-calendar.right').show();
    });

});

$('.testimonial').slick({
    infinite: true,
    speed: 300,
    autoplay: true,
    autoplaySpeed: 2500,
    slidesToShow: 2,
    slidesToScroll: 1,
    responsive: [

        {
            breakpoint: 992,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            }
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            }
        },
    ]
});
$('.offer_slider').slick({
    infinite: true,
    speed: 300,
    autoplay: true,
    autoplaySpeed: 2000,
    slidesToShow: 4,
    slidesToScroll: 1,
    responsive: [{
            breakpoint: 992,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
            }
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
            }
        },
        {
            breakpoint: 576,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            }
        },
    ]
});
</script>
<script src="<?php echo USER_JS ?>navbar.js"></script>
</body>

</html>