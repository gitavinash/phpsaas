<!-- Footer -->
<footer class="bg-gray-900 text-white pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <div>
                <h3 class="text-xl font-bold mb-4">About Us</h3>
                <p class="text-gray-400 mb-4">
                    JourneyFinders is your gateway to extraordinary travel experiences
                    around the world. We specialize in creating memorable adventures
                    tailored to your preferences.
                </p>
                <div class="flex space-x-4">
                    <a href="https://www.facebook.com/profile.php?id=61575311421582" target="_blank"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-primary transition">
                        <i class="ri-facebook-fill"></i>
                    </a>
                    <a href="https://www.instagram.com/journeyfinders.in" target="_blank"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-primary transition">
                        <i class="ri-instagram-fill"></i>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4">Popular Destinations</h3>
                <ul class="space-y-2">
                    <li><a href="/packages/india/kerala/" class="text-gray-400 hover:text-white transition">Kerala</a>
                    </li>
                    <li><a href="/packages/india/himachal-pradesh/"
                            class="text-gray-400 hover:text-white transition">Himachal pradesh</a></li>
                    <li><a href="/packages/india/goa/" class="text-gray-400 hover:text-white transition">Goa</a></li>
                    <li><a href="/packages/maldives/male" class="text-gray-400 hover:text-white transition">Maldives</a>
                    </li>
                    <li><a href="/packages/mauritius/mauritius/"
                            class="text-gray-400 hover:text-white transition">Mauritius</a>
                    </li>
                    <li><a href="/search/?q=thailand" class="text-gray-400 hover:text-white transition">Thailand</a>
                    </li>
                    <li><a href="/packages/united-arab-emirates/dubai/"
                            class="text-gray-400 hover:text-white transition">Dubai</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="/" class="text-gray-400 hover:text-white transition">Home</a>
                    </li>
                    <li>
                        <a href="/about-us/" class="text-gray-400 hover:text-white transition">About Us</a>
                    </li>
                    <li>
                        <a href="/holiday/" class="text-gray-400 hover:text-white transition">Destinations</a>
                    </li>
                    <li>
                        <a href="/contact-us/" class="text-gray-400 hover:text-white transition">Contact Us</a>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4">Contact Us</h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <div class="w-5 h-5 flex items-center justify-center mt-1 mr-3 text-gray-400">
                            <i class="ri-map-pin-line"></i>
                        </div>
                        <span class="text-gray-400">New Delhi, India</span>
                    </li>
                    <li class="flex items-center">
                        <div class="w-5 h-5 flex items-center justify-center mr-3 text-gray-400">
                            <i class="ri-phone-line"></i>
                        </div>
                        <span class="text-gray-400">+91 92112 87555</span>
                    </li>
                    <li class="flex items-center">
                        <div class="w-5 h-5 flex items-center justify-center mr-3 text-gray-400">
                            <i class="ri-mail-line"></i>
                        </div>
                        <span class="text-gray-400">care@journeyfinders.in</span>
                    </li>
                    <li class="flex items-center">
                        <div class="w-5 h-5 flex items-center justify-center mr-3 text-gray-400">
                            <i class="ri-time-line"></i>
                        </div>
                        <span class="text-gray-400">Mon-Sat: 9:00 AM - 7:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">
                    &copy; <?php echo date('Y') . ' ' . SITE_NAME ?>. All rights reserved. | Powered by
                    <a href="https://aleya.in" target="_blank" title="Travel ERP Software">Aleya</a>
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <div class="h-8 flex items-center justify-center rounded">
                        <a href="/terms-and-conditions/" class="text-gray-400 hover:text-white">Terms and Conditions</a>
                    </div>
                    <div class="h-8 flex items-center justify-center rounded">
                        <a href="/privacy-policy/" class="text-gray-400 hover:text-white">Privacy policy</a>
                    </div>
                    <div class="h-8 flex items-center justify-center rounded">
                        <a href="/shipping-and-delivery/" class="text-gray-400 hover:text-white">Shipping and
                            Delivery</a>
                    </div>
                    <div class="h-8 flex items-center justify-center rounded">
                        <a href="/refund-policy/" class="text-gray-400 hover:text-white">Refund policy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Floating Elements -->
<div class="fixed bottom-6 right-6 z-40 flex flex-col gap-4">

    <?php

    if ($link_type == 'tour_package' && ACTIVE_URL != '/') {
        $whatsappLink = "https://api.whatsapp.com/send?phone=919211287555&text=Hello%20JourneyFinders%2C%0AI%20am%20interested%20in" . urlencode($tour_package_title) . ".%0A" . urlencode(ACTIVE_URL_NON_QUERY);
    } else {
        $whatsappLink = "https://api.whatsapp.com/send?phone=919211287555&text=Hello%20JourneyFinders%2C%20I%20am%20interested.";
    }

    ?>

    <a href="<?php echo $whatsappLink ?>" target="_blank"
        class="w-12 h-12 flex items-center justify-center bg-green-500 text-white rounded-full shadow-lg hover:bg-green-600 transition">
        <i class="ri-whatsapp-line ri-lg"></i>
    </a>
    <button id="back-to-top"
        class="w-12 h-12 flex items-center justify-center bg-gray-800 text-white rounded-full shadow-lg hover:bg-gray-700 transition">
        <i class="ri-arrow-up-line ri-lg"></i>
    </button>
</div>
<?php require_once NOTIFICATION . 'index.php'; ?>
<script src="<?php echo USER_JS ?>app.min.js"></script>
<script src="<?php echo USER_JS ?>tailwind.config.min.js"></script>
<script>
// Back to top button
document.getElementById("back-to-top").addEventListener("click", () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});
// Add event listeners to form elements
document.addEventListener("DOMContentLoaded", function() {
    // Destination select styling
    const selects = document.querySelectorAll("select");
    selects.forEach((select) => {
        const parent = select.parentElement;
        if (!parent.querySelector(".ri-arrow-down-s-line")) {
            const icon = document.createElement("div");
            icon.className =
                "absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none";
            icon.innerHTML =
                '<i class="ri-arrow-down-s-line text-gray-500"></i>';
            parent.style.position = "relative";
            parent.appendChild(icon);
        }
    });
});
</script>
</body>

</html>