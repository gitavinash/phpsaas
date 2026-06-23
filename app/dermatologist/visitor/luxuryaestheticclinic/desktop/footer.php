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
                    href="<?php echo ((isset($companyInstagramProfile)) ? $companyInstagramProfile : ''); ?>"
                    class="social-circle"
                    aria-label="Instagram"><i data-lucide="instagram" style="width: 16px"></i></a>
                <a
                    href="<?php echo ((isset($companyFacebookProfile)) ? $companyFacebookProfile : ''); ?>"
                    class="social-circle"
                    aria-label="Facebook"><i data-lucide="facebook" style="width: 16px"></i></a>
                <a
                    href="<?php echo ((isset($companyYoutubeProfile)) ? $companyYoutubeProfile : ''); ?>"
                    class="social-circle"
                    aria-label="YouTube"><i data-lucide="youtube" style="width: 16px"></i></a>
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
        <i data-lucide="message-square" style="width: 20px; height: 20px"></i>
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