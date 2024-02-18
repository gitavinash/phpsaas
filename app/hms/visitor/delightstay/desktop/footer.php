<footer>
    <section class="horizontal_padding">
        <div class="container">
            <div class="row  footer_padding ">
                <div class="col-12 col-md-12 col-lg-4 ">
                    <a class="navbar-brand main_logo" href="/">
                        <img src="<?php echo USER_IMAGES ?>icon/main_logo.svg " alt="" class="img-fluid">
                    </a>
                    <p class="py-5 delight_footer opacity-50">Delight Hotels and Resorts bring to you hospitality
                        and comfort
                        unmatched
                        by any
                        other chain
                        of
                        hotels. Delight Hotels & Resort is a dynamic and a proactive service provider. We have
                        developed
                        a reputation for being a diversified & professional company.</p>
                </div>

                <div class="col-6 col-md-6 col-lg-2 ps-2 ps-md-3 ps-lg-5 ">
                    <h5 class="orange">TOURS</h5>
                    <ul class="jimcorbett pt-4 opacity-50">
                        <li>
                            <a href="/category/jim-corbett/">Jim Corbett</a>
                        </li>
                        <li>
                            <a href="/category/delhi/">Delhi</a>
                        </li>
                        <li>
                            <a href="/category/agra/">Agra</a>
                        </li>
                        <li>
                            <a href="/category/amritsar/">Amritsar</a>
                        </li>
                        <li>
                            <a href="/category/haridwar/">Haridwar</a>
                        </li>
                        <li>
                            <a href="/category/jaipur/">Jaipur</a>
                        </li>
                        <li>
                            <a href="/category/nainital/">Nainital</a>
                        </li>
                        <li>
                            <a href="/category/naukuchiatal/">Naukuchiatal</a>
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
                            <a href="https://blog.delighthotelsandresort.com/">Blog</a>
                        </li>
                        <li>
                            <a href="/contact-us/">Contact Us</a>
                        </li>
                        <li>
                            <a href="/privacy-policy/">Privacy Policy</a>
                        </li>
                        <li>
                            <a href="/terms-and-conditions/">Terms & COnditions</a>
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
                            Address : 114 , 1st Floor , Kirti Shikhar Tower, Distt Center , Janakpuri , New Delhi
                            110058<br>
                        </address>
                    </div>
                    <div class="d-flex align-items-start py-2 ">
                        <img src="<?php echo USER_IMAGES ?>icon/Call.svg" alt="" class="pe-2">
                        <a href="" class="opacity-50">
                            +91-8929604524,<br />+91-9773889527,<br />+91-9728872206
                        </a>
                    </div>
                    <div class="d-flex align-items-start py-2">
                        <img src="<?php echo USER_IMAGES ?>icon/Message.svg" alt="" class="pe-2">
                        <a href="" class="opacity-50">reservations@delightstay.co.in</a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12 col-md-10 col-lg-8 py-3 opacity-50">
                    &copy; COPYRIGHT <?php echo date('Y') ?> Delight Hotels and Resorts. ALL RIGHT RESERVED | Design
                    and Developed by <a href="" title="Travel Portal Development Company">Blumox
                        Technologies</a>
                </div>
                <div
                    class="col-12 col-md-4 col-lg-4 d-flex  justify-content-center justify-content-md-center justify-content-lg-end   ">
                    <div class="py-3">
                        <a href="">
                            <img src="<?php echo USER_IMAGES ?>icon/insta.svg" alt="" class="px-2">
                        </a>
                        <a href="">
                            <img src="<?php echo USER_IMAGES ?>icon/fec.svg" alt="">
                        </a>
                        <a href="">
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
    $(function() {
        $('input[name="birthday"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'), 10)
        }, function(start, end, label) {
            var years = moment().diff(start, 'years');
            alert("You are " + years + " years old!");
        });
    });
});
</script>

<script>
$(document).ready(function() {
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
});
</script>
<script>
$(document).ready(function() {
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
});
</script>

<script type='text/javascript'>
var select = document.getElementById("be-hotel-select");
select.addEventListener("change", function() {
    var hotel_id = "hotel_id";
    var regex = new RegExp(/hotel_id=\d+/g);
    var getParams = window.location.search;
    var params_str = hotel_id + "=" + this.value;
    var path = "";
    if (getParams.indexOf(hotel_id) != -1) {
        path = getParams.replace(regex, params_str);
    } else {
        if (getParams == "") {
            path = getParams + '?' + params_str;
        } else {
            path = getParams + '&' + params_str;
        }
    }
    window.history.pushState(false, false, path);
});
</script>
<!-- end Booking form script -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cityHotelWrap = document.querySelector('.block-search__city-hotel');
    const citySelect = document.querySelector('.block-search__city-select');
    const hotelSelectList = document.querySelector('.block-search__hotel');
    const searchFormWithoutSelect = document.querySelector('#be-search-form');
    document.querySelector('.cont1').classList.add('visible');
    const processLoadFormObserve = () => {
        const sfObserve = new MutationObserver((e) => {

            if (e[0].removedNodes) {
                setTimeout(() => cityHotelWrap.classList.remove('visible-flex'));
            }
            if (e[0].addedNodes) {
                setTimeout(() => cityHotelWrap.classList.add('visible-flex'), 100);
            }
        });
        sfObserve.observe(searchFormWithoutSelect, {
            childList: true
        });
    }
    const setSelectListeners = () => {
        citySelect.addEventListener('change', (evt) => {


            const hotelSelectNumber = evt.target.value;
            const checkedHotelSelectOption = citySelect.options[evt.target.value - 1];
            const beScenario = checkedHotelSelectOption.getAttribute('data-hotel');

            const visibleHotelSelect = document.querySelector(
                '.block-search__hotel-item.visible');
            const activeHotelSelect = hotelSelectList.querySelector(
                '.block-search__hotel-item.cont' + hotelSelectNumber);

            activeHotelSelect.classList.toggle('visible');
            visibleHotelSelect.classList.toggle('visible');

            loadForm(window, beScenario);

        })

        hotelSelectList.addEventListener('change', (evt) => {
            const beScenario = evt.target.value;
            loadForm(window, beScenario);
        })
    }
    processLoadFormObserve();
    setSelectListeners();
});
</script>
<script src="<?php echo USER_JS ?>navbar.js"></script>
</body>

</html>