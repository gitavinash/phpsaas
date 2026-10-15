<?php
/**
 * Aleya SaaS — Global Footer Component
 */
$pageScripts = $pageScripts ?? [];
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <!-- Brand Column -->
            <div class="footer-col-brand">
                <a href="/" aria-label="Aleya Homepage">
                    <img src="/app/aleya/visitor/aleya_new/desktop/assets/images/aleya_white_logo.png" alt="Aleya SaaS" class="footer-logo" width="140" height="40" />
                </a>
                <p class="footer-tagline">
                    Aleya is a unified SaaS business management, CRM, and operations platform. We help service businesses replace scattered spreadsheets with organized workflows, automated follow-ups, and reliable team visibility.
                </p>
                <div class="footer-social-links">
                    <a href="https://www.facebook.com/aleyaerp" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="Aleya on Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/aleyaerp" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="Aleya on Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.linkedin.com/company/aleyaerp/" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="Aleya on LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://x.com/aleyaerp" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="Aleya on X">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>

            <!-- Industries Column -->
            <div class="footer-col">
                <h4>Industries</h4>
                <div class="footer-links-list">
                    <a href="/industries/travel-agencies/">Travel Agencies</a>
                    <a href="/industries/travel-agents/">Travel Agents</a>
                    <a href="/industries/transporters/">Transporters & Fleet</a>
                    <a href="/industries/dms/">DMS (Distribution)</a>
                    <a href="/industries/spa-management/">SPA Management</a>
                </div>
            </div>

            <!-- Product Column -->
            <div class="footer-col">
                <h4>Product</h4>
                <div class="footer-links-list">
                    <a href="/#features">Core Features</a>
                    <a href="/pricing/">Pricing Plans</a>
                    <a href="/#workflow">How It Works</a>
                    <a href="/#testimonials">Customer Reviews</a>
                    <a href="/checkout/">Order Workspace</a>
                    <a href="/login/">Customer Sign In</a>
                </div>
            </div>

            <!-- Company & Legal -->
            <div class="footer-col">
                <h4>Company & Legal</h4>
                <div class="footer-links-list">
                    <a href="/about-us/">About Aleya</a>
                    <a href="/contact-us/">Contact Us</a>
                    <a href="/privacy-policy/">Privacy Policy</a>
                    <a href="/terms-and-conditions/">Terms of Service</a>
                    <a href="/refund-policy/">Refund Policy</a>
                </div>
            </div>

            <!-- Contact & Office -->
            <div class="footer-col">
                <h4>Direct Contact</h4>
                <div class="footer-contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <a href="tel:+919990026008">+91-9990026008</a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <a href="mailto:support@aleya.in">support@aleya.in</a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <i class="fab fa-whatsapp"></i>
                    <div>
                        <a href="https://wa.me/919990026008?text=Hi,%20I'm%20interested%20in%20Aleya" target="_blank" rel="noopener noreferrer">WhatsApp Support</a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-location-dot"></i>
                    <div>
                        <span>Kirti Nagar, New Delhi, India</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© <?php echo date('Y'); ?> Aleya.in — A SaaS Product by <a href="https://blumox.com/products/" target="_blank" rel="noopener noreferrer">Blumox Technologies</a>. All rights reserved.</p>
            <p>Designed for High-Reliability Operations & Conversion Architecture.</p>
        </div>
    </div>
</footer>

<!-- Floating WhatsApp Action -->
<a href="https://wa.me/919990026008?text=Hi,%20I'm%20interested%20in%20Aleya" class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Chat with Aleya on WhatsApp" data-track="whatsapp">
    <i class="fab fa-whatsapp"></i>
    <span>Chat with sales</span>
</a>

<!-- Core Application Scripts -->
<script src="/app/aleya/visitor/aleya_new/desktop/assets/js/main.js"></script>
<?php foreach ($pageScripts as $script): ?>
    <script src="<?php echo htmlspecialchars($script, ENT_QUOTES, 'UTF-8'); ?>"></script>
<?php endforeach; ?>
</body>
</html>
