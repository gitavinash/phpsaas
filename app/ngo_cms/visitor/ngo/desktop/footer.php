<!-- ======================== CTA ======================== -->
<section class="donation-mature">
    <div class="container">
        <h2>Join Us in Serving Humanity</h2>
        <p>
            Whether you wish to donate, volunteer your time, or simply spread the
            word — every act of support helps us serve 100 more meals, educate one
            more child, and plant one more tree. Together, we can write a brighter
            story for India's villages.
        </p>
        <div style="
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
          ">
            <a href="donation.html" class="btn btn-secondary" style="padding: 1rem 2.5rem; font-size: 1.1rem">
                <i class="fas fa-gift" style="margin-right: 8px"></i>Make a Donation
            </a>
            <a href="contact.html" class="btn btn-outline-white" style="padding: 1rem 2.5rem; font-size: 1.1rem">
                <i class="fas fa-hands-helping" style="margin-right: 8px"></i>Become
                a Volunteer
            </a>
        </div>
    </div>
</section>

<div id="footer-placeholder">
    <footer class="footer-mega">
        <div class="container footer-grid">
            <!-- Col 1: About & Social -->
            <div class="footer-col">
                <a href="index.html" class="brand-logo" style="margin-bottom: 1.5rem;">
                    <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>" class="logo-img-footer">
                </a>
                <p><?php echo SITE_DESC ?></p>
                <div class="social-icons">
                    <?php

                    if (!empty($companyFacebookProfile)) {
                        echo "<a href='{$companyFacebookProfile}' class='social-icon'><i class='fab fa-facebook-f'></i></a>";
                    }

                    if (!empty($companyInstagramProfile)) {
                        echo "<a href='{$companyInstagramProfile}' class='social-icon'><i class='fab fa-instagram'></i></a>";
                    }

                    if (!empty($companyYoutubeProfile)) {
                        echo "<a href='{$companyYoutubeProfile}' class='social-icon'><i class='fab fa-youtube'></i></a>";
                    }

                    if (!empty($companyTwitterProfile)) {
                        echo "<a href='{$companyTwitterProfile}' class='social-icon'><i class='fab fa-twitter'></i></a>";
                    }

                    if (!empty($companyLinkedInProfile)) {
                        echo "<a href='{$companyLinkedInProfile}' class='social-icon'><i class='fab fa-linkedin-in'></i></a>";
                    }

                    ?>
                </div>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links-mega">
                    <?php
                    generateMenu('SITE_NAV_FOOTER_1', 'dropdown', true, '');
                    ?>
                </ul>
            </div>

            <!-- Col 3: Contact Info -->
            <div class="footer-col">
                <h3>Contact Us</h3>
                <?php

                if (isset($companyAddress)) {
                    echo "<div class='footer-contact-item'>
                    <i class='fas fa-map-marker-alt'></i>
                    <span>{$companyAddress}</span>
                </div>";
                }

                if (isset($companyMobile)) {
                    echo "<div class='footer-contact-item'>
                    <i class='fas fa-phone-alt'></i>
                    <span>{$companyMobile}</span>
                </div>";
                }

                if (isset($companyEmail)) {
                    echo "<div class='footer-contact-item'>
                    <i class='fas fa-envelope'></i>
                    <span>{$companyEmail}</span>
                </div>";
                }


                if (isset($companyTiming)) {
                    echo "<div class='footer-contact-item'>
                    <i class='fas fa-clock'></i>
                    <span>{$companyTiming}</span>
                </div>";
                }

                ?>
            </div>

            <!-- Col 4: Newsletter -->
            <div class="footer-col">
                <h3>Stay Connected</h3>
                <p style="margin-bottom: 1rem;">Join our newsletter to receive updates on our latest projects and
                    events.</p>
                <form class="footer-newsletter" onsubmit="event.preventDefault(); alert('Thank you for subscribing!');">
                    <input type="email" placeholder="Your Email Address"
                        style="width: 100%; padding: 10px; border: none; border-radius: 4px; margin-bottom: 10px; font-family: inherit;">
                    <button type="submit" class="btn btn-secondary"
                        style="width: 100%; border: none; cursor: pointer;">Subscribe</button>
                </form>
                <div
                    style="margin-top: 2rem; background-color: rgba(244, 211, 94, 0.15); border: 1px solid var(--secondary-color); border-radius: 8px; padding: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <h3 style="margin-bottom: 10px; font-size: 1.1rem; color: var(--secondary-color);">Choose Your
                        Language</h3>
                    <div id="google_translate_element" style="border-radius: 4px; overflow: hidden;"></div>
                </div>
            </div>
        </div>

        <div class="container footer-bottom">
            <p>&copy; <?php echo date('Y') ?> <?php echo SITE_NAME ?>. All rights reserved.
                <?php echo (isset($companyRegisterationNo) ? " | Reg. No: {$companyRegisterationNo}" : '') ?></p>
            <p style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.8;">Designed and Developed by <a
                    href="https://blumox.com" target="_blank" style="color: inherit; text-decoration: underline;">Blumox
                    Technologies</a></p>
            <div class="footer-legal-links">
                <?php
                generateMenu('SITE_NAV_FOOTER_2', 'dropdown', true, '');
                ?>
            </div>
        </div>
    </footer>
</div>
<script src="<?php echo USER_JS ?>main.js?v=<?= time() ?>"></script>
</body>

</html>