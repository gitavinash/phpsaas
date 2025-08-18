<!-- Footer -->
<footer class="bg-primary text-white pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <div>
                <a href="/" class="inline-block mb-6">
                    <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?> Logo"
                        class="h-8 brightness-0 invert">
                </a>
                <p class="text-white-400 mb-6">Delivering fresh, beautiful flowers for all your special moments since
                    2019.</p>
                <div class="flex space-x-4">
                    <a href="#"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-white hover:bg-primary transition">
                        <i class="ri-facebook-fill"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-white hover:bg-primary transition">
                        <i class="ri-instagram-line"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-white hover:bg-primary transition">
                        <i class="ri-pinterest-line"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-white hover:bg-primary transition">
                        <i class="ri-twitter-x-line"></i>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-6">Quick Links</h3>
                <ul class="space-y-3">
                    <li><a href="/" class="text-white-400 hover:text-white transition">Home</a></li>
                    <li><a href="/about-us/" class="text-white-400 hover:text-white transition">About us</a></li>
                    <li><a href="/contact-us/" class="text-white-400 hover:text-white transition">Contact us</a></li>

                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-6">Product categories</h3>
                <ul class="space-y-3">
                    <li><a href="/category/wall-decor/" class="text-white-400 hover:text-white transition">Wall
                            Décor</a>
                    </li>
                    <li><a href="/category/furniture-and-accents/"
                            class="text-white-400 hover:text-white transition">Furniture & Accents</a></li>
                    <li><a href="/category/lighting/" class="text-white-400 hover:text-white transition">Lighting</a>
                    </li>
                    <li><a href="/category/showpieces-and-idols/"
                            class="text-white-400 hover:text-white transition">Showpieces & Idols</a></li>
                    <li><a href="/category/paintings/" class="text-white-400 hover:text-white transition">Paintings</a>
                    </li>
                    <li><a href="/category/water-fountains/" class="text-white-400 hover:text-white transition">Water
                            Fountains</a></li>
                </ul>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 space-y-4">
                <h3 class="text-xl font-semibold text-gray-800 border-b pb-3">Contact Us</h3>

                <!-- Address -->
                <div>
                    <p class="text-gray-600 font-medium mb-1">Address</p>
                    <address class="not-italic text-gray-700 leading-relaxed">
                        House No. 51, Shop No. 1, Banjara Market,<br />
                        Sector 58, Ghata, Haryana 122011
                    </address>
                </div>

                <!-- Phone -->
                <div>
                    <p class="text-gray-600 font-medium mb-1">Call</p>
                    <a href="tel:+919810494275" class="text-blue-600 hover:underline">
                        +91&nbsp;98104&nbsp;94275
                    </a>
                </div>

                <!-- WhatsApp -->
                <div>
                    <p class="text-gray-600 font-medium mb-1">WhatsApp</p>
                    <a href="https://wa.me/919810494275" target="_blank" rel="noopener"
                        class="text-green-600 hover:underline">
                        +91&nbsp;98104&nbsp;94275
                    </a>
                </div>
            </div>

        </div>
        <div class="border-t border-gray-800 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-white-500 text-sm mb-4 md:mb-0">© <?php echo date('Y') . ' ' . SITE_NAME ?>. All
                    rights reserved. | Developed by <a href='https://blumox.com'
                        class="text-white-500 hover:text-white text-sm transition">Blumox Technologies</a></p>
                <div class="flex space-x-6">
                    <a href="/terms-and-conditions/" class="text-white-500 hover:text-white text-sm transition">Terms
                        and
                        Conditions</a>
                    <a href="/privacy-policy/" class="text-white-500 hover:text-white text-sm transition">Privacy
                        Policy</a>
                    <a href="/cancellation---refund/"
                        class="text-white-500 hover:text-white text-sm transition">Cancellation & Refund</a>
                    <a href="/shipping-policy/" class="text-white-500 hover:text-white text-sm transition">Shipping
                        policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.js"></script>
<script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo USER_JS ?>app.min.js"></script>
<?php

if (ACTIVE_URL == '/checkout/') {
    echo '<script type="text/javascript" src="' . USER_JS . 'checkout.min.js"></script>';
}
?>
<script>
$(document).ready(function() {
    // 1. Mobile Menu Toggle
    if ($('#mobile-menu-button').length && $('#mobile-menu').length) {
        $('#mobile-menu-button').on('click', function() {
            $('#mobile-menu').toggleClass('hidden');
        });
    }

    // 2. Quantity Control
    const $decrease = $('#decrease-quantity');
    const $increase = $('#increase-quantity');
    const $quantityInput = $('#quantity');

    if ($decrease.length && $increase.length && $quantityInput.length) {
        $decrease.on('click', function() {
            const val = parseInt($quantityInput.val());
            if (val > 1) $quantityInput.val(val - 1);
        });

        $increase.on('click', function() {
            const val = parseInt($quantityInput.val());
            $quantityInput.val(val + 1);
        });

        $quantityInput.on('change', function() {
            const val = parseInt($(this).val());
            if (val < 1 || isNaN(val)) {
                $(this).val(1);
            }
        });
    }

    // 3. Checkbox Styling (chocolate, card, vase)
    const $checkboxLabels = $('label[for="chocolate"], label[for="card"], label[for="vase"]');

    $checkboxLabels.each(function() {
        $(this).on('click', function(e) {
            const forAttr = $(this).attr('for');
            const $checkbox = $('#' + forAttr);
            const $checkIcon = $(this).find('.ri-check-line');
            const $customCheckbox = $(this).find('.checkbox-custom');

            $checkbox.prop('checked', !$checkbox.prop('checked'));

            if ($checkbox.prop('checked')) {
                $customCheckbox.addClass('bg-primary border-primary');
                $checkIcon.removeClass('hidden');
            } else {
                $customCheckbox.removeClass('bg-primary border-primary');
                $checkIcon.addClass('hidden');
            }

            e.preventDefault();
        });
    });

    // 4. Tab Control
    const $tabButtons = $('.tab-button');
    const $tabContents = $('.tab-content');

    if ($tabButtons.length) {
        $tabButtons.on('click', function() {
            const tabName = $(this).data('tab');

            $tabButtons.removeClass('border-primary text-primary')
                .addClass('border-transparent text-gray-500');

            $(this).removeClass('border-transparent text-gray-500')
                .addClass('border-primary text-primary');

            $tabContents.removeClass('active');
            $('#' + tabName + '-content').addClass('active');
        });
    }

    // 5. Header Scroll + Mobile Slide Menu + Hero Slider
    const $header = $('header');
    const $menuButton = $('#menuButton');
    const $closeMenu = $('#closeMenu');
    const $mobileMenu = $('#mobileMenu');
    const $mobileMenuOverlay = $('#mobileMenuOverlay');
    const $mobileShopButton = $('#mobileShopButton');
    const $mobileShopContent = $('#mobileShopContent');
    const $slider = $('#heroSlider');
    const $slides = $('.slide');
    const $dots = $('.slider-dot');
    const $prevBtn = $('.slider-nav-btn.prev');
    const $nextBtn = $('.slider-nav-btn.next');

    let currentSlide = 0;
    let slideInterval;

    function updateSlides() {
        $slides.each(function(index) {
            if (index === currentSlide) {
                $(this).css('transform', 'translateX(0)');
            } else if (index < currentSlide) {
                $(this).css('transform', 'translateX(-100%)');
            } else {
                $(this).css('transform', 'translateX(100%)');
            }
        });

        $dots.each(function(index) {
            $(this).toggleClass('bg-opacity-100', index === currentSlide)
                .toggleClass('bg-opacity-50', index !== currentSlide);
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % $slides.length;
        updateSlides();
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + $slides.length) % $slides.length;
        updateSlides();
    }

    function startSlideShow() {
        slideInterval = setInterval(nextSlide, 5000);
    }

    function stopSlideShow() {
        clearInterval(slideInterval);
    }

    $prevBtn.on('click', function() {
        stopSlideShow();
        prevSlide();
        startSlideShow();
    });

    $nextBtn.on('click', function() {
        stopSlideShow();
        nextSlide();
        startSlideShow();
    });

    $dots.on('click', function() {
        stopSlideShow();
        currentSlide = $(this).index();
        updateSlides();
        startSlideShow();
    });

    $slider.on('mouseenter', stopSlideShow).on('mouseleave', startSlideShow);

    startSlideShow();

    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 50) {
            $header.removeClass('header-transparent').addClass('header-solid');
        } else {
            $header.addClass('header-transparent').removeClass('header-solid');
        }
    });

    function openMobileMenu() {
        $mobileMenu.removeClass('translate-x-full');
        $mobileMenuOverlay.removeClass('hidden');
        $('body').css('overflow', 'hidden');
    }

    function closeMobileMenu() {
        $mobileMenu.addClass('translate-x-full');
        $mobileMenuOverlay.addClass('hidden');
        $('body').css('overflow', '');
    }

    $menuButton.on('click', openMobileMenu);
    $closeMenu.on('click', closeMobileMenu);
    $mobileMenuOverlay.on('click', closeMobileMenu);

    $mobileShopButton.on('click', function() {
        $mobileShopContent.toggleClass('hidden');
        const $icon = $(this).find('i');
        const isRotated = $icon.css('transform') === 'matrix(-1, 0, 0, -1, 0, 0)';
        $icon.css('transform', isRotated ? '' : 'rotate(180deg)');
    });

    // 6. Product Zoom and Thumbnail Switcher
    $('.thumbnail img').on('click', function() {
        const newSrc = $(this).attr('src');
        $('#mainProductImage').attr('src', newSrc);
        $('#zoomOverlay').css('background-image', 'url(' + newSrc + ')');

        $('.thumbnail img').parent().removeClass('border-primary');
        $(this).parent().addClass('border-primary');
    });

    $('#zoomOverlay').css('background-image', 'url(' + $('#mainProductImage').attr('src') + ')');

    $('.product-image-container').on('mouseenter', function() {
        $('#zoomOverlay').addClass('active');
    }).on('mouseleave', function() {
        $('#zoomOverlay').removeClass('active');
    }).on('mousemove', function(e) {
        const $this = $(this);
        const offset = $this.offset();
        const x = e.pageX - offset.left;
        const y = e.pageY - offset.top;
        const width = $this.width();
        const height = $this.height();

        const xPercent = (x / width) * 100;
        const yPercent = (y / height) * 100;

        $('#zoomOverlay').css('background-position', xPercent + '% ' + yPercent + '%');
    });
});
</script>

</body>

</html>