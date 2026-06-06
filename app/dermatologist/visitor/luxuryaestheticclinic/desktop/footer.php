<!-- 15. CONSULTATION FORM SECTION -->
<section class="consultation-section" id="consultation">
    <div class="container consult-grid">
        <!-- Left Content Column -->
        <div class="consult-info reveal">
            <span class="section-tag">Elevate Your Self Care</span>
            <h2>Begin Your Journey to Radiant Skin</h2>
            <p class="consult-info-desc">
                Schedule a private clinical evaluation with Dr. Elizabeth Reed and
                discover a bespoke roadmap to your ultimate skin, hair, and body
                confidence.
            </p>

            <ul class="consult-trust-points">
                <li class="consult-trust-item">
                    <i data-lucide="lock" style="width: 20px"></i> 100% Confidential
                    Scientific Evaluation
                </li>
                <li class="consult-trust-item">
                    <i data-lucide="award" style="width: 20px"></i> Zero Hard-sell
                    Upsell Policy
                </li>
                <li class="consult-trust-item">
                    <i data-lucide="check-circle" style="width: 20px"></i> Safe
                    FDA-Approved Medical Care
                </li>
            </ul>
        </div>

        <!-- Right Form Card -->
        <div class="consult-form-card reveal">
            <h3 class="form-title-editorial">Schedule Consultation</h3>

            <form id="consultationForm">
                <!-- Name & Phone -->
                <div class="form-group-row">
                    <div class="form-group">
                        <input
                            type="text"
                            id="formName"
                            class="form-input"
                            placeholder=" "
                            required />
                        <label for="formName" class="form-label">Full Name *</label>
                    </div>
                    <div class="form-group">
                        <input
                            type="tel"
                            id="formPhone"
                            class="form-input"
                            placeholder=" "
                            required />
                        <label for="formPhone" class="form-label">Phone Number *</label>
                    </div>
                </div>

                <!-- Email & Concern -->
                <div class="form-group-row">
                    <div class="form-group">
                        <input
                            type="email"
                            id="formEmail"
                            class="form-input"
                            placeholder=" " />
                        <label for="formEmail" class="form-label">Email Address</label>
                    </div>
                    <div class="form-group">
                        <select id="formConcern" class="form-select" required>
                            <option value="" disabled selected hidden></option>
                            <option value="Skin Pigmentation">
                                Skin Pigmentation / Glow
                            </option>
                            <option value="Acne Scars">Acne Scars / Texture</option>
                            <option value="Anti-Aging">Anti-Aging / Wrinkles</option>
                            <option value="Hair Restoration">Hair Fall / Thinning</option>
                            <option value="Body Contouring">
                                Fat Reduction / Contouring
                            </option>
                        </select>
                        <label for="formConcern" class="form-label">Primary Concern *</label>
                    </div>
                </div>

                <!-- Message -->
                <div class="form-group">
                    <textarea
                        id="formMessage"
                        class="form-textarea"
                        placeholder=" "></textarea>
                    <label for="formMessage" class="form-label">Brief Message (Optional)</label>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary form-btn-submit"
                    id="formSubmit">
                    Confirm Booking
                </button>

                <a
                    href="https://wa.me/919999999999"
                    class="btn btn-whatsapp form-btn-whatsapp"
                    target="_blank"
                    rel="noopener">
                    <i
                        data-lucide="message-square"
                        style="width: 16px; margin-right: 8px"></i>
                    WhatsApp Consultation
                </a>

                <p class="form-privacy-note">
                    Your data is strictly secured. We never share contact details.
                </p>
            </form>
        </div>
    </div>
</section>

<!-- 18. LUXURY FOOTER -->
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
                generateMenu('SITE_NAV_FOOTER_1', 'links', true);
                ?>
            </ul>
        </div>

        <!-- Col 3: Quick Navigation -->
        <div>
            <h4 class="footer-heading">Quick Links</h4>
            <ul class="footer-links-list">
                <?php
                generateMenu('SITE_NAV_FOOTER_2', 'links', true);
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
        <span class="copyright-text">&copy; 2026 Luxury Aesthetic Clinic. All Rights Reserved. Made with
            medical precision.</span>
        <ul class="footer-bottom-links">
            <?php
            generateMenu('SITE_NAV_FOOTER_3', 'links', true);
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
<script src="<?php echo USER_JS ?>main.js"></script>
</body>

</html>