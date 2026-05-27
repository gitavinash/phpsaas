<!-- Footer -->
<footer class="bg-gray-900 text-white pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <div>
                <a href="/" class="inline-block mb-6">
                    <img src="https://andblooms.com/upload/2020/10/1602753246_andblooms_logo_mobile_128x128.png"
                        alt="AndBlooms Logo" class="h-8 brightness-0 invert">
                </a>
                <p class="text-gray-400 mb-6">Delivering fresh, beautiful flowers for all your special moments since
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
                    <?php

                    generateMenu('SITE_NAV_FOOTER_1', 'mobile', true, 'text-gray-400 hover:text-white transition');

                    ?>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-6">Product categories</h3>
                <ul class="space-y-3">
                    <?php

                    generateMenu('SITE_NAV_FOOTER_2', 'mobile', true, 'text-gray-400 hover:text-white transition');

                    ?>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-6">Newsletter</h3>
                <p class="text-gray-400 mb-4">Subscribe to receive updates, access to exclusive deals, and more.</p>
                <div class="mb-4">
                    <div class="flex">
                        <input type="email" placeholder="Your email address" d
                            class="flex-1 py-3 px-4 bg-gray-800 border-none rounded-l-button text-white focus:outline-none text-sm">
                        <button
                            class="bg-primary hover:bg-opacity-90 text-white py-3 px-4 rounded-r-button font-medium transition whitespace-nowrap">Subscribe</button>
                    </div>
                </div>
                <h3 class="text-lg font-semibold mb-4 mt-6">Payment Methods</h3>
                <div class="flex space-x-3">
                    <div class="w-10 h-6 flex items-center justify-center text-gray-400">
                        <i class="ri-visa-fill text-xl"></i>
                    </div>
                    <div class="w-10 h-6 flex items-center justify-center text-gray-400">
                        <i class="ri-mastercard-fill text-xl"></i>
                    </div>
                    <div class="w-10 h-6 flex items-center justify-center text-gray-400">
                        <i class="ri-paypal-fill text-xl"></i>
                    </div>
                    <div class="w-10 h-6 flex items-center justify-center text-gray-400">
                        <i class="ri-apple-fill text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm mb-4 md:mb-0">© <?php echo date('Y') . ' ' . SITE_NAME ?>. All
                    rights reserved. | Developed by <a href='https://blumox.com'
                        class="text-gray-500 hover:text-white text-sm transition">Blumox Technologies</a></p>
                <div class="flex space-x-6">
                    <?php

                    generateMenu('SITE_NAV_FOOTER_3', 'non_list', true, 'text-gray-500 hover:text-white text-sm transition');

                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>
<div id="orderPopup"></div>
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
    // Mobile menu is now handled in header.php

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

    // 7. Urgency Timer for Same-Day Delivery
    function updateUrgencyTimer() {
        const now = new Date();
        const cutoffTime = new Date();
        cutoffTime.setHours(11, 0, 0, 0); // 11 AM cutoff

        const timerEl = document.getElementById('urgency-timer');
        if (!timerEl) return;

        if (now < cutoffTime) {
            const diff = cutoffTime - now;
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            timerEl.textContent = `${hours}h ${minutes}m`;
        } else {
            timerEl.parentElement.innerHTML =
                '<i class="ri-information-line text-orange-600 text-lg mr-1"></i><span class="text-sm text-gray-700">Next-day delivery available</span>';
        }
    }

    // Update timer immediately and every minute
    updateUrgencyTimer();
    setInterval(updateUrgencyTimer, 60000);
});
</script>
<script>
let orders = [];
let index = 0;

async function fetchOrders() {
    const res = await fetch("/api/order/recent/");
    const json = await res.json();
    if (json.status === "success") {
        orders = json.data;
    }
}

function showPopup() {
    if (!orders.length) return;

    const item = orders[index];
    const popup = document.getElementById("orderPopup");

    popup.innerHTML = `
        <a class="order-toast-link" href="${item.link_url}" aria-label="View ${item.product_title}">
            <div class="order-toast">
                <img class="toast-img" src="${item.product_image}" alt="${item.product_title}">

                <div class="toast-body">
                    <div class="toast-header">
                        <i class="ri-fire-line text-orange-600"></i>
                        <span class="toast-urgency-title">Hurry! New order just in</span>
                    </div>

                    <div class="toast-body-content">
                        <span class="font-semibold text-primary">${item.profile_display_name.toUpperCase()}</span>
                        <span class="text-gray-600">placed an order for</span>
                        <span class="toast-product">${item.product_title.toUpperCase()}</span>
                    </div>

                    <div class="toast-footer">
                        <i class="ri-time-line text-orange-500"></i>
                        <span>Ordered ${item.order_update}</span>
                    </div>
                </div>

                <button class="toast-close" onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('orderPopup').style.display='none'" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </a>
    `;

    popup.style.display = "block";

    setTimeout(() => {
        popup.style.display = "none";
    }, 6000);

    index = (index + 1) % orders.length;
}

// init
fetchOrders();
setInterval(showPopup, 10000);
setInterval(fetchOrders, 100000);
</script>


</body>

</html>