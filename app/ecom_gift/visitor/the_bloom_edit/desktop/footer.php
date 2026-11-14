</main>

<!-- Footer -->
<footer class="bg-[#183028] text-[#F4EFE6] border-t border-[#B48E54]/25 pt-12 sm:pt-16 pb-8 sm:pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-10 pb-10 sm:pb-12 border-b border-white/15">
            <!-- Brand Atelier Info -->
            <div class="lg:col-span-4 space-y-5">
                <a href="/" class="inline-flex items-center bg-[#FAF7F2] px-3.5 py-2 rounded-md border border-[#E6DDD3]">
                    <img src="<?php echo SITE_LOGO; ?>"
                        alt="<?php echo safe_html(SITE_NAME); ?>" class="h-7 w-auto">
                </a>
                <p class="text-[#DED6CB] text-sm leading-relaxed max-w-sm">
                    Founded in 2018 across Chhatarpur and Gurugram, Crafting expressive, seasonal floral compositions and bespoke luxury gifting delivered by hand across Delhi NCR.
                </p>
                <div class="text-xs text-[#DED6CB] space-y-1.5 pt-1">
                    <p>
                        <i class="ri-phone-line text-[#B48E54] mr-1.5" aria-hidden="true"></i>
                        <a href="tel:+919910744119" class="hover:text-white transition-colors inline-block py-0.5">+91-9910744119</a>
                    </p>
                    <p>
                        <i class="ri-mail-line text-[#B48E54] mr-1.5" aria-hidden="true"></i>
                        <a href="mailto:sd@andblooms.com" class="hover:text-white transition-colors inline-block py-0.5">sd@andblooms.com</a>
                    </p>
                </div>
                <div class="flex items-center gap-3 pt-1">
                    <a href="https://www.instagram.com/andblooms/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
                        class="w-11 h-11 flex items-center justify-center rounded-full border border-white/20 text-[#F4EFE6] hover:bg-[#9B5637] hover:border-[#9B5637] transition-colors">
                        <i class="ri-instagram-line text-lg" aria-hidden="true"></i>
                    </a>
                    <a href="https://www.facebook.com/andblooms/" target="_blank" rel="noopener noreferrer" aria-label="Facebook"
                        class="w-11 h-11 flex items-center justify-center rounded-full border border-white/20 text-[#F4EFE6] hover:bg-[#9B5637] hover:border-[#9B5637] transition-colors">
                        <i class="ri-facebook-fill text-lg" aria-hidden="true"></i>
                    </a>
                    <a href="https://www.youtube.com/@andblooms" target="_blank" rel="noopener noreferrer" aria-label="YouTube"
                        class="w-11 h-11 flex items-center justify-center rounded-full border border-white/20 text-[#F4EFE6] hover:bg-[#9B5637] hover:border-[#9B5637] transition-colors">
                        <i class="ri-youtube-line text-lg" aria-hidden="true"></i>
                    </a>
                    <a href="https://wa.me/919910744119" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Concierge"
                        class="w-11 h-11 flex items-center justify-center rounded-full border border-white/20 text-[#F4EFE6] hover:bg-[#9B5637] hover:border-[#9B5637] transition-colors">
                        <i class="ri-whatsapp-line text-lg" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="lg:col-span-2">
                <h2 class="font-serif text-lg font-medium text-white mb-3.5 sm:mb-5">Ateliers &amp; Guide</h2>
                <ul class="space-y-1.5 text-sm">
                    <?php
                    generateMenu('SITE_NAV_FOOTER_1', 'mobile', true, 'text-[#DED6CB] hover:text-white transition-colors inline-block py-1');
                    ?>
                </ul>
            </div>

            <!-- Collections -->
            <div class="lg:col-span-2">
                <h2 class="font-serif text-lg font-medium text-white mb-3.5 sm:mb-5">Collections</h2>
                <ul class="space-y-1.5 text-sm">
                    <?php
                    generateMenu('SITE_NAV_FOOTER_2', 'mobile', true, 'text-[#DED6CB] hover:text-white transition-colors inline-block py-1');
                    ?>
                </ul>
            </div>

            <!-- Editorial Newsletter -->
            <div class="lg:col-span-4">
                <h2 class="font-serif text-lg font-medium text-white mb-3">The Atelier Journal</h2>
                <p class="text-[#DED6CB] text-sm mb-4 leading-relaxed">
                    Receive seasonal floral dispatches, private workshop invitations, and limited edition gifting releases.
                </p>
                <form id="newsletterForm" class="space-y-2.5" novalidate>
                    <label for="newsletterEmail" class="sr-only">Email address</label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input id="newsletterEmail" name="email" type="email" required autocomplete="email"
                            placeholder="Enter your email address"
                            class="flex-1 min-h-[48px] py-3 px-4 bg-white/10 border border-white/20 rounded-md text-white placeholder-[#DED6CB]/75 text-base sm:text-sm focus:bg-white/15 focus:border-[#B48E54]">
                        <button type="submit"
                            class="min-h-[48px] bg-[#9B5637] hover:bg-[#7D4127] text-white py-3 px-5 rounded-md text-xs font-semibold uppercase tracking-widest transition-colors whitespace-nowrap">
                            Join List
                        </button>
                    </div>
                    <p id="newsletterStatus" class="text-xs min-h-[1rem]" aria-live="polite"></p>
                </form>

                <div class="pt-5">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-[#DED6CB]/80 mb-3 font-medium">
                        Encrypted Payment Partners
                    </p>
                    <div class="flex flex-wrap items-center gap-3 text-[#DED6CB]">
                        <span class="inline-flex items-center gap-1.5 text-xs border border-white/15 rounded px-2.5 py-1 bg-white/5">
                            <i class="ri-visa-fill text-base" aria-hidden="true"></i> Visa
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs border border-white/15 rounded px-2.5 py-1 bg-white/5">
                            <i class="ri-mastercard-fill text-base" aria-hidden="true"></i> Mastercard
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs border border-white/15 rounded px-2.5 py-1 bg-white/5">
                            <i class="ri-bank-card-line text-base" aria-hidden="true"></i> UPI &amp; Cards
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs border border-white/15 rounded px-2.5 py-1 bg-white/5">
                            <i class="ri-lock-line text-base text-[#B48E54]" aria-hidden="true"></i> Razorpay Secure
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-[#DED6CB]">
            <p class="text-center md:text-left">
                &copy; <?php echo date('Y') . ' ' . safe_html(SITE_NAME); ?>. All rights reserved.
                <span class="mx-1.5 text-white/30">|</span>
                Crafted with <a href="https://blumox.com" target="_blank" rel="noopener noreferrer"
                    class="text-white hover:text-[#EFE8DC] underline decoration-white/30 underline-offset-4 transition-colors">Blumox Technologies</a>
            </p>
            <div class="flex flex-wrap justify-center gap-x-6 gap-y-2">
                <?php
                generateMenu('SITE_NAV_FOOTER_3', 'non_list', true, 'text-[#DED6CB] hover:text-white text-xs transition-colors inline-block py-1');
                ?>
            </div>
        </div>
    </div>
</footer>

<div id="orderPopup" role="status" aria-live="polite"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.js"></script>
<script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
<!-- Social Proof Plugin -->
<script src="<?php echo PLUGIN_LIBRARY ?>social_proof/social_proof.js?v=<?php echo time(); ?>"></script>
<script type="text/javascript" src="<?php echo USER_JS ?>app.js?v=<?php echo assetVersion('js/app.js'); ?>"></script>
<?php
if (ACTIVE_URL == '/checkout/' || strpos(ACTIVE_URL, '/checkout') !== false) {
    echo '<script type="text/javascript" src="' . USER_JS . 'checkout.js?v=' . assetVersion('js/checkout.js') . '"></script>';
}
?>
</body>

</html>
