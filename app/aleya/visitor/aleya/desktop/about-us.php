<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="<?php echo USER_IMAGES ?>aleya_favicon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo USER_IMAGES ?>aleya_favicon.png" type="image/x-icon" />
    <title>About Us | Aleya</title>
    <meta name="description"
        content="Learn about Aleya, our mission, and how we help businesses automate lead management and bookings." />

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
                    <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" class="nav-btn primary">
                        Book Demo
                        <span class="btn-arrow">→</span>
                    </a>
                </div>
            </div>

            <div class="nav-actions">
                <a href="https://calendar.app.google/wKtLAUxZmsnph5PM6" target="_blank" class="nav-btn primary">
                    Book Demo
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
            <span class="section-tag">About Us</span>
            <h1>We built Aleya to remove chaos from growth.</h1>
            <p>
                Aleya is a business automation platform focused on lead capture, follow-up, bookings, and team
                productivity.
                We help agencies and service businesses move from spreadsheets to a single actionable dashboard.
            </p>
        </div>
    </section>

    <section class="inner-content">
        <div class="container">
            <div class="about-grid">
                <article class="panel-card">
                    <h3>Our Story</h3>
                    <p>
                        We saw teams lose revenue not because they lacked leads, but because operations were fragmented.
                        Aleya was created to connect lead sources, sales follow-ups, payments, and post-booking
                        workflows into one reliable system.
                    </p>
                </article>

                <article class="panel-card">
                    <h3>Our Values</h3>
                    <ul class="value-list">
                        <li><strong>Clarity First:</strong> Every feature should reduce confusion and save time.</li>
                        <li><strong>Automation with Control:</strong> Smart defaults with full business flexibility.
                        </li>
                        <li><strong>Real Support:</strong> Fast onboarding and human help when teams need it.</li>
                    </ul>
                </article>
            </div>

            <div class="stats-grid">
                <div class="stat-panel">
                    <strong>100+</strong>
                    <span>Businesses onboarded</span>
                </div>
                <div class="stat-panel">
                    <strong>10,000+</strong>
                    <span>Leads managed</span>
                </div>
                <div class="stat-panel">
                    <strong>4.9/5</strong>
                    <span>Average rating</span>
                </div>
                <div class="stat-panel">
                    <strong>24h</strong>
                    <span>Typical onboarding time</span>
                </div>
            </div>

            <div class="contact-actions" style="justify-content:center; margin-top: 28px;">
                <a href="/contact-us/" class="nav-btn primary">Contact Our Team <span class="btn-arrow">→</span></a>
                <a href="/#pricing" class="nav-btn ghost">View Pricing</a>
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