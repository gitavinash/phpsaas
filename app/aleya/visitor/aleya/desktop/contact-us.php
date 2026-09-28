<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="<?php echo USER_IMAGES ?>aleya_favicon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo USER_IMAGES ?>aleya_favicon.png" type="image/x-icon" />
    <title>Contact Us | Aleya</title>
    <meta name="description" content="Contact Aleya for demo bookings, onboarding help, or sales support." />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>styles.css" />
</head>

<body>
    <div class="cursor-glow" id="cursorGlow"></div>

    <nav class="nav" id="nav">
        <div class="nav-container">
            <a href="/" class="nav-logo">
                <img src="<?php echo USER_IMAGES ?>aleya_logo.png" alt="Aleya" class="logo" />
            </a>

            <div class="nav-menu" id="navMenu">
                <a href="/#features" class="nav-link">Features</a>
                <a href="/#pricing" class="nav-link">Pricing</a>
                <a href="/about-us/" class="nav-link">About</a>
                <a href="/contact-us/" class="nav-link">Contact</a>

                <div class="nav-actions">
                    <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" class="nav-btn ghost">
                        Book Demo
                    </a>
                    <a href="/onboarding/" class="nav-btn primary">
                        Buy Now
                        <span class="btn-arrow">→</span>
                    </a>
                </div>
            </div>

            <div class="nav-actions">
                <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" class="nav-btn ghost">
                    Book Demo
                </a>
                <a href="/onboarding/" class="nav-btn primary">
                    Buy Now
                    <span class="btn-arrow">→</span>
                </a>
            </div>

            <button class="nav-toggle" id="navToggle" aria-label="Menu">
                <span></span>
            </button>
        </div>
    </nav>

    <section class="inner-hero">
        <div class="container inner-hero-content">
            <span class="section-tag">Contact Us</span>
            <h1>Talk to the Aleya team.</h1>
            <p>
                Need a product walkthrough, onboarding support, or pricing help? Reach out through call, WhatsApp, or
                email and we will get back quickly.
            </p>
        </div>
    </section>

    <section class="inner-content">
        <div class="container">
            <div class="contact-grid">
                <article class="panel-card">
                    <h3>Reach us directly</h3>

                    <div class="contact-list">
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h4>Phone</h4>
                                <a href="tel:+919990026008">+91-9990026008</a>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fab fa-whatsapp"></i>
                            <div>
                                <h4>WhatsApp</h4>
                                <a href="https://wa.me/919990026008?text=Hi,%20I'm%20interested%20in%20Aleya"
                                    target="_blank">Start chat</a>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h4>Email</h4>
                                <a href="mailto:support@aleya.in">support@aleya.in</a>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-location-dot"></i>
                            <div>
                                <h4>Office</h4>
                                <p>Kirti Nagar, New Delhi</p>
                            </div>
                        </div>
                    </div>

                    <div class="contact-actions">
                        <a href="/onboarding/" class="nav-btn primary">
                            Buy Now
                            <span class="btn-arrow">→</span>
                        </a>
                        <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" class="nav-btn ghost">
                            Book Demo
                        </a>
                        <a href="/about-us/" class="nav-btn ghost">Know About Us</a>
                    </div>
                </article>

                <article class="map-box">
                    <iframe src="https://www.google.com/maps?q=Kirti%20Nagar%20New%20Delhi&output=embed" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" title="Aleya office location"></iframe>
                </article>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="/" class="footer-logo">
                        <img src="<?php echo USER_IMAGES ?>aleya_logo.png" alt="Aleya" class="logo" />
                    </a>
                    <p>The modern way to grow your business. From lead to conversion, all in one place.</p>
                    <div class="footer-social">
                        <a href="https://www.facebook.com/aleyaerp" target="_blank"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/aleyaerp" target="_blank"><i
                                class="fab fa-instagram"></i></a>
                        <a href="https://www.linkedin.com/company/aleyaerp/" target="_blank"><i
                                class="fab fa-linkedin-in"></i></a>
                        <a href="https://x.com/aleyaerp" target="_blank"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>

                <div class="footer-links">
                    <h4>Product</h4>
                    <a href="/#features">Features</a>
                    <a href="/#pricing">Pricing</a>
                    <a href="/#workflow">How It Works</a>
                    <a href="/#testimonials">Reviews</a>
                </div>

                <div class="footer-links">
                    <h4>Company</h4>
                    <a href="/about-us/">About Us</a>
                    <a href="/contact-us/">Contact</a>
                </div>

                <div class="footer-links">
                    <h4>Legal</h4>
                    <a href="https://aleya.in/privacy-policy/">Privacy Policy</a>
                    <a href="https://aleya.in/terms-and-conditions/">Terms of Use</a>
                    <a href="https://aleya.in/refund-policy/">Refund Policy</a>
                </div>

                <div class="footer-contact">
                    <h4>Get in Touch</h4>
                    <a href="tel:+919990026008"><i class="fas fa-phone"></i> +91-9990026008</a>
                    <a href="mailto:support@aleya.in"><i class="fas fa-envelope"></i> support@aleya.in</a>
                    <a href="https://maps.app.goo.gl/JoFYSsRLWAwjAg5q8"><i class="fas fa-map-marker-alt"></i> Kirti
                        Nagar, New Delhi</a>
                </div>
            </div>

            <div class="footer-bottom">
                <p>
                    © 2026 Aleya — A product of
                    <a href="https://blumox.com/products/" title="Products by Blumox">Blumox Technologies</a>
                </p>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/919990026008?text=Hi,%20I'm%20interested%20in%20Aleya" class="whatsapp-float"
        target="_blank">
        <i class="fab fa-whatsapp"></i>
        <span>Chat with us</span>
    </a>

    <script src="<?php echo USER_JS ?>script.js"></script>
</body>

</html>