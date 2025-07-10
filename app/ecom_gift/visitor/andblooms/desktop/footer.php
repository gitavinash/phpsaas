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
                    <li><a href="/" class="text-gray-400 hover:text-white transition">Home</a></li>
                    <li><a href="/about-us/" class="text-gray-400 hover:text-white transition">About Us</a></li>
                    <li><a href="/category/workshop/" class="text-gray-400 hover:text-white transition">Workshop</a>
                    </li>
                    <li><a href="/franchise/" class="text-gray-400 hover:text-white transition">Franchise</a></li>
                    <li><a href="/contact-us/" class="text-gray-400 hover:text-white transition">Contact Us</a></li>

                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-6">Product categories</h3>
                <ul class="space-y-3">
                    <li><a href="/tag/luxury/" class="text-gray-400 hover:text-white transition">Luxury
                            Collections</a></li>
                    <li><a href="/category/flower/" class="text-gray-400 hover:text-white transition">Fresh
                            Flowers</a>
                    <li><a href="/category/and-blooms-express/" class="text-gray-400 hover:text-white transition">&
                            Blooms Express</a>
                    <li><a href="/category/artistic-artificial/"
                            class="text-gray-400 hover:text-white transition">Artistic artificial</a>
                    <li><a href="/category/cake/" class="text-gray-400 hover:text-white transition">Cake</a>
                    <li><a href="/category/infinity-everlasting-flowers/"
                            class="text-gray-400 hover:text-white transition">Infinity Everlasting Flowers</a>
                    <li><a href="/category/planters/" class="text-gray-400 hover:text-white transition">Planters</a>
                    <li><a href="/category/vases-and-ceramics/" class="text-gray-400 hover:text-white transition">Vases
                            & Ceramics</a>
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
                    <a href="/terms-and-conditions/" class="text-gray-500 hover:text-white text-sm transition">Terms
                        and
                        Conditions</a>
                    <a href="/privacy-policy/" class="text-gray-500 hover:text-white text-sm transition">Privacy
                        Policy</a>
                    <a href="/cancellation---refund/"
                        class="text-gray-500 hover:text-white text-sm transition">Cancellation & Refund</a>
                    <a href="/shipping-policy/" class="text-gray-500 hover:text-white text-sm transition">Shipping
                        policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.js"></script>
<script type="text/javascript" src="<?php echo USER_JS ?>app.min.js"></script>
<script id="mobile-menu-script">
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenuButton.addEventListener('click', function() {
        mobileMenu.classList.toggle('hidden');
    });
});
</script>
<script id="quantity-control-script">
document.addEventListener('DOMContentLoaded', function() {
    const decreaseButton = document.getElementById('decrease-quantity');
    const increaseButton = document.getElementById('increase-quantity');
    const quantityInput = document.getElementById('quantity');
    decreaseButton.addEventListener('click', function() {
        const currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });
    increaseButton.addEventListener('click', function() {
        const currentValue = parseInt(quantityInput.value);
        quantityInput.value = currentValue + 1;
    });
    quantityInput.addEventListener('change', function() {
        if (parseInt(this.value) < 1 || isNaN(parseInt(this.value))) {
            this.value = 1;
        }
    });
});
</script>
<script id="tab-control-script">
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            // Update active tab button
            tabButtons.forEach(btn => {
                btn.classList.remove('border-primary', 'text-primary');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-primary', 'text-primary');
            // Show active tab content
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(`${tabName}-content`).classList.add('active');
        });
    });
});
</script>
<script id="checkbox-script">
document.addEventListener('DOMContentLoaded', function() {
    const checkboxLabels = document.querySelectorAll(
        'label[for="chocolate"], label[for="card"], label[for="vase"]');
    checkboxLabels.forEach(label => {
        label.addEventListener('click', function(e) {
            const checkbox = document.getElementById(this.getAttribute('for'));
            const checkIcon = this.querySelector('.ri-check-line');
            const customCheckbox = this.querySelector('.checkbox-custom');
            checkbox.checked = !checkbox.checked;
            if (checkbox.checked) {
                customCheckbox.classList.add('bg-primary', 'border-primary');
                checkIcon.classList.remove('hidden');
            } else {
                customCheckbox.classList.remove('bg-primary', 'border-primary');
                checkIcon.classList.add('hidden');
            }
            e.preventDefault();
        });
    });
});
</script>
<script id="header-scroll">
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('header');
    const menuButton = document.getElementById('menuButton');
    const closeMenu = document.getElementById('closeMenu');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileShopButton = document.getElementById('mobileShopButton');
    const mobileShopContent = document.getElementById('mobileShopContent');
    const slider = document.getElementById('heroSlider');
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.querySelector('.slider-nav-btn.prev');
    const nextBtn = document.querySelector('.slider-nav-btn.next');
    let currentSlide = 0;
    let slideInterval;

    function updateSlides() {
        slides.forEach((slide, index) => {
            if (index === currentSlide) {
                slide.style.transform = 'translateX(0)';
            } else if (index < currentSlide) {
                slide.style.transform = 'translateX(-100%)';
            } else {
                slide.style.transform = 'translateX(100%)';
            }
        });
        dots.forEach((dot, index) => {
            dot.classList.toggle('bg-opacity-100', index === currentSlide);
            dot.classList.toggle('bg-opacity-50', index !== currentSlide);
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        updateSlides();
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        updateSlides();
    }

    function startSlideShow() {
        slideInterval = setInterval(nextSlide, 5000);
    }

    function stopSlideShow() {
        clearInterval(slideInterval);
    }
    prevBtn.addEventListener('click', () => {
        stopSlideShow();
        prevSlide();
        startSlideShow();
    });
    nextBtn.addEventListener('click', () => {
        stopSlideShow();
        nextSlide();
        startSlideShow();
    });
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            stopSlideShow();
            currentSlide = index;
            updateSlides();
            startSlideShow();
        });
    });
    slider.addEventListener('mouseenter', stopSlideShow);
    slider.addEventListener('mouseleave', startSlideShow);
    startSlideShow();
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.remove('header-transparent');
            header.classList.add('header-solid');
        } else {
            header.classList.add('header-transparent');
            header.classList.remove('header-solid');
        }
    });

    function openMobileMenu() {
        mobileMenu.classList.remove('translate-x-full');
        mobileMenuOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        mobileMenu.classList.add('translate-x-full');
        mobileMenuOverlay.classList.add('hidden');
        document.body.style.overflow = '';
    }
    menuButton.addEventListener('click', openMobileMenu);
    closeMenu.addEventListener('click', closeMobileMenu);
    mobileMenuOverlay.addEventListener('click', closeMobileMenu);
    mobileShopButton.addEventListener('click', function() {
        mobileShopContent.classList.toggle('hidden');
        const icon = this.querySelector('i');
        icon.style.transform = icon.style.transform === 'rotate(180deg)' ? '' : 'rotate(180deg)';
    });
});
</script>
<script>
$(document).ready(function() {
    // Thumbnail click to update main image & border
    $('.thumbnail img').on('click', function() {
        var newSrc = $(this).attr('src');
        $('#mainProductImage').attr('src', newSrc);
        $('#zoomOverlay').css('background-image', 'url(' + newSrc + ')');

        $('.thumbnail img').parent().removeClass('border-primary');
        $(this).parent().addClass('border-primary');
    });

    // Setup initial zoom overlay image
    $('#zoomOverlay').css('background-image', 'url(' + $('#mainProductImage').attr('src') + ')');

    // Zoom on hover
    $('.product-image-container').on('mouseenter', function() {
        $('#zoomOverlay').addClass('active');
    }).on('mouseleave', function() {
        $('#zoomOverlay').removeClass('active');
    }).on('mousemove', function(e) {
        var $this = $(this);
        var offset = $this.offset();
        var x = e.pageX - offset.left;
        var y = e.pageY - offset.top;
        var width = $this.width();
        var height = $this.height();

        // Calculate background position % (reverse because background moves opposite direction)
        var xPercent = (x / width) * 100;
        var yPercent = (y / height) * 100;

        $('#zoomOverlay').css('background-position', xPercent + '% ' + yPercent + '%');
    });
});
</script>
</body>

</html>