<?php

if (ACTIVE_URL != '/contact-us/') {
    require_once 'widgets/consultation-section.php';
}

?>
<footer class="main-footer" id="mainFooter">
    <div class="container footer-top-grid">
        <!-- Col 1: About & Social -->
        <div>
            <img
                src="<?php echo SITE_LOGO ?>"
                alt="<?php echo SITE_NAME ?>"
                class="footer-logo-img" />
            <p class="footer-about-text">
                <?php echo SITE_DESC ?>
            </p>
            <div class="footer-socials">
                <a
                    href="<?php echo isset($companyInstagramProfile) ? $companyInstagramProfile : ''; ?>"
                    class="social-circle"
                    aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>

                <a
                    href="<?php echo isset($companyFacebookProfile) ? $companyFacebookProfile : ''; ?>"
                    class="social-circle"
                    aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>

                <a
                    href="<?php echo isset($companyYoutubeProfile) ? $companyYoutubeProfile : ''; ?>"
                    class="social-circle"
                    aria-label="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
            </div>
        </div>

        <!-- Col 2: Treatments Links -->
        <div>
            <h4 class="footer-heading">Treatments</h4>
            <ul class="footer-links-list">
                <?php
                generateMenu('SITE_NAV_FOOTER_1', 'links', true, 'footer-link');
                ?>
            </ul>
        </div>

        <!-- Col 3: Quick Navigation -->
        <div>
            <h4 class="footer-heading">Quick Links</h4>
            <ul class="footer-links-list">
                <?php
                generateMenu('SITE_NAV_FOOTER_2', 'links', true, 'footer-link');
                ?>
            </ul>
        </div>

        <!-- Col 4: Newsletter -->
        <div>
            <h4 class="footer-heading">Newsletter</h4>
            <p class="footer-newsletter-text">
                Subscribe to receive exclusive skincare advice and treatment
                invitations from Dr. Reed.
            </p>
            <form
                class="newsletter-form"
                onsubmit="
              event.preventDefault();
              alert('Subscribed successfully!');
              this.reset();
            ">
                <input
                    type="email"
                    class="newsletter-input"
                    placeholder="Your Email Address"
                    aria-label="Email Address"
                    required />
                <button
                    type="submit"
                    class="newsletter-submit"
                    aria-label="Subscribe">
                    <i data-lucide="arrow-right" style="width: 18px"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="container footer-bottom">
        <span class="copyright-text">&copy; 2026 Luxury Aesthetic Clinic. All Rights Reserved. Design and Developed by <a href="https://blumox.com" target="_blank">Blumox Technologies</a></span>
        <ul class="footer-bottom-links">
            <?php
            generateMenu('SITE_NAV_FOOTER_3', 'links', true, 'footer-bottom-link');
            ?>
        </ul>
    </div>
</footer>

<!-- STICKY FLOATING CONTACT WIDGET -->
<div class="sticky-contact-widget" id="stickyContact">
    <a
        href="tel:+919999999999"
        class="sc-btn sc-btn-call"
        aria-label="Call Clinic Now">
        <i data-lucide="phone" style="width: 20px; height: 20px"></i>
        <span class="sc-label">Call Now</span>
    </a>
    <a
        href="https://wa.me/919999999999"
        class="sc-btn sc-btn-whatsapp"
        target="_blank"
        rel="noopener"
        aria-label="Chat via WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        <span class="sc-label">WhatsApp</span>
    </a>
</div>

<!-- Lucide Icons CDN -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    // Initialize Lucide Icons dynamically
    lucide.createIcons();
</script>

<!-- JavaScript Operations -->
<script src="<?php echo USER_JS ?>main.js?v=<?php echo time(); ?>"></script>
<!-- Social Proof Plugin -->
<script src="<?php echo PLUGIN_LIBRARY ?>social_proof/social_proof.js?v=<?php echo time(); ?>"></script>
</body>

</html>