<?php
$siteLogoUrl = defined('SITE_LOGO') && !empty(SITE_LOGO) ? SITE_LOGO : '';
$siteNameDisplay = defined('SITE_NAME') && !empty(SITE_NAME) ? html_entity_decode(SITE_NAME, ENT_QUOTES, 'UTF-8') : 'AndBlooms';
$siteDescDisplay = defined('SITE_DESCRIPTION') && !empty(SITE_DESCRIPTION) 
    ? strip_tags(html_entity_decode(SITE_DESCRIPTION, ENT_QUOTES, 'UTF-8')) 
    : 'Fresh, hand-tied luxury flowers, floral arrangements, gourmet gift hampers, and indoor planters delivered with care across Delhi, Gurgaon, and NCR.';
?>
    </main>

    <!-- Veloura Boutique Footer -->
    <footer class="bg-[#242220] text-[#D5C4B7] pt-14 sm:pt-20 pb-10 border-t border-[#3D3A37]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-12 pb-12 sm:pb-16 border-b border-white/10">
                <!-- Col 1: Brand & Philosophy (4 cols) -->
                <div class="lg:col-span-4 space-y-5">
                    <a href="/" class="inline-block group" aria-label="<?php echo safe_html($siteNameDisplay); ?>">
                        <?php if (!empty($siteLogoUrl)): ?>
                            <span class="inline-flex items-center bg-[#FAF7F2] px-3.5 py-2 rounded-md border border-[#E6DDD3]">
                                <img src="<?php echo htmlspecialchars($siteLogoUrl); ?>"
                                    alt="<?php echo safe_html($siteNameDisplay); ?>"
                                    class="h-8 w-auto max-w-[170px] object-contain">
                            </span>
                        <?php else: ?>
                            <span class="font-serif text-2xl sm:text-3xl font-medium tracking-[0.08em] text-white group-hover:text-[#C47C68] transition-colors">
                                <?php echo safe_html($siteNameDisplay); ?>
                            </span>
                            <span class="block text-[10px] uppercase tracking-[0.25em] text-[#D5C4B7] font-sans mt-0.5">
                                Fresh Flowers &amp; Luxury Gifts
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <p class="text-sm text-[#A39B93] leading-relaxed max-w-sm font-light">
                        <?php echo safe_html($siteDescDisplay); ?>
                    </p>

                    <div class="flex items-center gap-3 pt-2">
                        <a href="https://www.instagram.com/andblooms/" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-[#C47C68] hover:border-[#C47C68] transition-all"
                            aria-label="Follow on Instagram">
                            <i class="ri-instagram-line" aria-hidden="true"></i>
                        </a>
                        <a href="https://www.facebook.com/andblooms/" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-[#C47C68] hover:border-[#C47C68] transition-all"
                            aria-label="Follow on Facebook">
                            <i class="ri-facebook-fill" aria-hidden="true"></i>
                        </a>
                        <a href="https://www.youtube.com/@andblooms" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-[#C47C68] hover:border-[#C47C68] transition-all"
                            aria-label="Watch on YouTube">
                            <i class="ri-youtube-line" aria-hidden="true"></i>
                        </a>
                        <a href="tel:+919910744119"
                            class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-[#C47C68] hover:border-[#C47C68] transition-all"
                            aria-label="Call customer care">
                            <i class="ri-phone-line" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Store Locations & Timings (3 cols) -->
                <div class="lg:col-span-3 space-y-4">
                    <h3 class="font-serif text-lg text-white font-medium">Our Stores &amp; Timings</h3>
                    <div class="space-y-3.5 text-sm text-[#A39B93] font-light">
                        <div>
                            <p class="font-semibold text-white text-xs uppercase tracking-wider mb-0.5">Chhatarpur Store (Flagship)</p>
                            <p class="text-xs sm:text-sm">Shop No. A179/2, Mehrauli-Gurgaon Rd, Andheria Mor Village, Chhatarpur, New Delhi 110030</p>
                        </div>
                        <div>
                            <p class="font-semibold text-white text-xs uppercase tracking-wider mb-0.5">Gurugram Studio</p>
                            <p class="text-xs sm:text-sm">LG-K3, South Point Mall, Golf Course Rd, Sector 53, Gurugram, Haryana 122002</p>
                        </div>
                        <div class="pt-1 text-xs text-[#D9BA8F] space-y-1">
                            <p><i class="ri-time-line mr-1 text-[#C47C68]" aria-hidden="true"></i> Open Daily: 9:00 AM – 9:00 PM</p>
                            <p><i class="ri-phone-line mr-1 text-[#C47C68]" aria-hidden="true"></i> <a href="tel:+919910744119" class="hover:text-white transition-colors">+91 99107 44119</a></p>
                            <p><i class="ri-mail-line mr-1 text-[#C47C68]" aria-hidden="true"></i> <a href="mailto:sales@andblooms.com" class="hover:text-white transition-colors">sales@andblooms.com</a></p>
                        </div>
                    </div>
                </div>

                <!-- Col 3: Collections (2 cols) -->
                <div class="lg:col-span-2 space-y-4">
                    <h3 class="font-serif text-lg text-white font-medium">Collections</h3>
                    <ul class="space-y-2 text-sm text-[#A39B93] font-light">
                        <?php
                        $footerNavItems = get_object_from_constant('SITE_NAV_FOOTER_2');
                        if (!empty($footerNavItems)) {
                            foreach ($footerNavItems as $navItem) {
                                $label = safe_html($navItem['label'] ?? '');
                                $url = htmlspecialchars($navItem['url'] ?? '#');
                                echo '<li><a href="' . $url . '" class="hover:text-white transition-colors inline-block py-0.5">' . $label . '</a></li>';
                            }
                        } else {
                        ?>
                            <li><a href="/category/flower/" class="hover:text-white transition-colors inline-block py-0.5">Fresh Bouquets</a></li>
                            <li><a href="/category/cake/" class="hover:text-white transition-colors inline-block py-0.5">Artisan Hampers</a></li>
                            <li><a href="/category/planters/" class="hover:text-white transition-colors inline-block py-0.5">Ceramic Planters</a></li>
                            <li><a href="/events/" class="hover:text-white transition-colors inline-block py-0.5">Floral Workshops</a></li>
                            <li><a href="/shop/" class="hover:text-white transition-colors inline-block py-0.5">All Creations</a></li>
                        <?php } ?>
                    </ul>
                </div>

                <!-- Col 4: Customer Support & Newsletter (3 cols) -->
                <div class="lg:col-span-3 space-y-4">
                    <h3 class="font-serif text-lg text-white font-medium">Customer Support &amp; Offers</h3>
                    <p class="text-sm text-[#A39B93] font-light leading-relaxed">
                        Subscribe for seasonal flower guides, festive gifting updates, and exclusive discount offers.
                    </p>
                    <form id="newsletterForm" class="space-y-2" novalidate>
                        <div class="flex gap-2">
                            <input type="email" name="email" placeholder="Your email address" required
                                class="flex-1 bg-white/5 border border-white/15 rounded-lg px-3.5 py-2.5 text-xs text-white placeholder-[#8E8780] focus:outline-none focus:border-[#C47C68]">
                            <button type="submit" class="vl-btn-primary py-2.5 px-4 text-xs">
                                <span>Join</span>
                            </button>
                        </div>
                        <span id="newsletterStatus" class="block text-xs" aria-live="polite"></span>
                    </form>
                    <div class="pt-2 text-xs text-[#A39B93] space-y-2">
                        <div class="flex items-center gap-2">
                            <i class="ri-shield-check-line text-[#C47C68]" aria-hidden="true"></i>
                            <span>100% Secure Razorpay &amp; UPI Payments</span>
                        </div>
                        <div class="flex items-center gap-2 text-[#D9BA8F]">
                            <i class="ri-customer-service-2-line text-[#C47C68]" aria-hidden="true"></i>
                            <span>Help &amp; Queries: <a href="tel:+919910744119" class="hover:underline">+91 99107 44119</a></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Legal Bar -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#8E8780]">
                <p>&copy; <?php echo date('Y'); ?> <?php echo safe_html($siteNameDisplay); ?>. All rights reserved.</p>
                <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2">
                    <?php
                    $footerPolicyItems = get_object_from_constant('SITE_NAV_FOOTER_3');
                    if (!empty($footerPolicyItems)) {
                        foreach ($footerPolicyItems as $policyItem) {
                            $label = safe_html($policyItem['label'] ?? '');
                            $url = htmlspecialchars($policyItem['url'] ?? '#');
                            echo '<a href="' . $url . '" class="hover:text-white transition-colors">' . $label . '</a>';
                        }
                    } else {
                    ?>
                        <a href="/terms-and-conditions/" class="hover:text-white transition-colors">Terms &amp; Conditions</a>
                        <a href="/privacy-policy/" class="hover:text-white transition-colors">Privacy Policy</a>
                        <a href="/cancellation---refund/" class="hover:text-white transition-colors">Cancellation &amp; Refund</a>
                        <a href="/shipping-policy/" class="hover:text-white transition-colors">Shipping Policy</a>
                    <?php } ?>
                    <a href="/contact-us/" class="hover:text-white transition-colors">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Support Pill -->
    <a href="https://wa.me/919910744119?text=Hello%20AndBlooms%20Team,%20I%20would%20like%20assistance%20with%20a%20flower%20order"
        target="_blank" rel="noopener noreferrer"
        class="fixed bottom-5 right-5 z-40 bg-[#25D366] text-white px-3.5 py-2.5 rounded-full shadow-lg flex items-center gap-2 text-xs font-semibold hover:bg-[#1EBE5D] transition-transform hover:scale-105"
        aria-label="Chat with Customer Support on WhatsApp">
        <i class="ri-whatsapp-line text-lg" aria-hidden="true"></i>
        <span class="hidden sm:inline">WhatsApp Support</span>
    </a>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.js"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo USER_JS ?>header-nav.js?v=<?php echo time(); ?>"></script>
    <script src="<?php echo USER_JS ?>app.js?v=<?php echo time(); ?>"></script>
    <?php if (ACTIVE_URL == '/checkout/' || strpos(ACTIVE_URL, '/checkout') !== false): ?>
        <script src="<?php echo USER_JS ?>checkout.js?v=<?php echo time(); ?>"></script>
    <?php endif; ?>
    <?php if (strpos(ACTIVE_URL, '/product/') !== false || strpos(ACTIVE_URL, '/subscription-product/') !== false): ?>
        <script src="<?php echo USER_JS ?>product-gallery.js?v=<?php echo time(); ?>"></script>
    <?php endif; ?>
    <?php if (strpos(ACTIVE_URL, '/workshop-events/') !== false || strpos(ACTIVE_URL, '/events/') !== false): ?>
        <script src="<?php echo USER_JS ?>events.js?v=<?php echo time(); ?>"></script>
    <?php endif; ?>
</body>

</html>
