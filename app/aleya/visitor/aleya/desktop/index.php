<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="<?php echo USER_IMAGES ?>aleya_favicon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo USER_IMAGES ?>aleya_favicon.png" type="image/x-icon" />
    <!-- Primary Meta Tags -->
    <title>Aleya — The Future of Travel Business Management</title>
    <meta name="title" content="Aleya — The Future of Travel Business Management" />
    <meta name="description"
        content="Stop juggling spreadsheets. Aleya automates your entire travel agency — from lead capture to booking confirmation. Join 100+ agencies already growing with us." />
    <meta name="keywords"
        content="travel CRM, travel ERP, travel agency software, booking management, lead automation" />
    <link rel="canonical" href="https://aleya.in/" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://aleya.in/" />
    <meta property="og:title" content="Aleya — The Future of Travel Business Management" />
    <meta property="og:description"
        content="Stop juggling spreadsheets. Aleya automates your entire travel agency — from lead capture to booking confirmation." />
    <meta property="og:image" content="https://aleya.in/og-image.jpg" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Aleya — The Future of Travel Business Management" />
    <meta name="twitter:description" content="Stop juggling spreadsheets. Aleya automates your entire travel agency." />
    <meta name="twitter:image" content="https://aleya.in/og-image.jpg" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>styles.css" />

    <!-- Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Aleya",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "description": "Travel CRM & ERP Software for travel agencies",
        "url": "https://aleya.in",
        "offers": [{
                "@type": "Offer",
                "name": "CRM",
                "price": "999",
                "priceCurrency": "INR"
            },
            {
                "@type": "Offer",
                "name": "ERP",
                "price": "2499",
                "priceCurrency": "INR"
            }
        ],
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.9",
            "reviewCount": "127"
        }
    }
    </script>
</head>

<body>
    <!-- Cursor Glow Effect -->
    <div class="cursor-glow" id="cursorGlow"></div>

    <!-- Navigation -->
    <nav class="nav" id="nav">
        <div class="nav-container">
            <a href="#" class="nav-logo">
                <img src="<?php echo USER_IMAGES ?>aleya_logo.png" alt="Aleya" class="logo" />
            </a>

            <div class="nav-menu" id="navMenu">
                <a href="#features" class="nav-link">Features</a>
                <a href="#workflow" class="nav-link">How It Works</a>
                <a href="#pricing" class="nav-link">Pricing</a>
                <a href="#testimonials" class="nav-link">Reviews</a>

                <div class="nav-actions">
                    <a href="https://calendar.app.google/TWyKTqQXWfd1oyuK6" target="_blank" class="nav-btn primary">
                        Book Demo
                        <span class="btn-arrow">→</span>
                    </a>
                </div>
            </div>

            <div class="nav-actions">
                <a href="https://calendar.app.google/TWyKTqQXWfd1oyuK6" target="_blank" class="nav-btn primary">
                    Book Demo
                    <span class="btn-arrow">→</span>
                </a>
            </div>

            <button class="nav-toggle" id="navToggle" aria-label="Menu">
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Hero Section - Immersive Full Screen -->
    <section class="hero" id="hero">
        <div class="hero-bg">
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>
            <div class="grid-lines"></div>
        </div>

        <div class="container hero-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="pulse"></span>
                    Now with AI-Powered Lead Scoring
                </div>

                <h1 class="hero-title">
                    <span class="line">Your Business,</span>
                    <span class="line highlight">On Autopilot.</span>
                </h1>

                <p class="hero-subtitle">
                    Aleya captures leads from Facebook, Instagram & Google — then
                    automates follow-ups, tracks conversions, and grows your business.
                    You focus on what matters.
                </p>

                <div class="hero-cta">
                    <a href="https://calendar.app.google/TWyKTqQXWfd1oyuK6" target="_blank"
                        class="cta-primary magnetic">
                        <span>Book Free Demo</span>
                        <div class="cta-glow"></div>
                    </a>
                    <a href="#features" class="cta-secondary">
                        <span class="play-icon">▶</span>
                        Explore Features
                    </a>
                </div>

                <div class="hero-proof">
                    <div class="proof-avatars">
                        <div class="avatar">PK</div>
                        <div class="avatar">RS</div>
                        <div class="avatar">AS</div>
                        <div class="avatar">+</div>
                    </div>
                    <span>Join <strong>100+</strong> businesses already using Aleya</span>
                </div>
            </div>

            <!-- Floating Dashboard Preview -->
            <div class="hero-dashboard">
                <div class="dashboard-window">
                    <div class="window-bar">
                        <div class="dots"><span></span><span></span><span></span></div>
                        <span class="url">app.aleya.in/dashboard</span>
                    </div>
                    <div class="window-content">
                        <!-- Animated Dashboard UI -->
                        <div class="dash-sidebar">
                            <div class="sidebar-item active">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="sidebar-item"><i class="fas fa-users"></i></div>
                            <div class="sidebar-item"><i class="fas fa-calendar"></i></div>
                            <div class="sidebar-item"><i class="fas fa-chart-bar"></i></div>
                        </div>
                        <div class="dash-main">
                            <div class="dash-header">
                                <div class="greeting">Good morning, Rajesh 👋</div>
                                <div class="date">Feb 9, 2026</div>
                            </div>
                            <div class="dash-stats">
                                <div class="stat-card">
                                    <span class="stat-label">New Leads</span>
                                    <span class="stat-value">24</span>
                                    <span class="stat-trend up">↑ 12%</span>
                                </div>
                                <div class="stat-card highlight">
                                    <span class="stat-label">Pending Follow-ups</span>
                                    <span class="stat-value">8</span>
                                    <span class="stat-trend">Due today</span>
                                </div>
                                <div class="stat-card">
                                    <span class="stat-label">Revenue</span>
                                    <span class="stat-value">₹4.2L</span>
                                    <span class="stat-trend up">↑ 23%</span>
                                </div>
                            </div>
                            <div class="dash-chart">
                                <div class="chart-bars">
                                    <div class="bar" style="--h: 40%"></div>
                                    <div class="bar" style="--h: 65%"></div>
                                    <div class="bar" style="--h: 45%"></div>
                                    <div class="bar" style="--h: 80%"></div>
                                    <div class="bar active" style="--h: 95%"></div>
                                    <div class="bar" style="--h: 70%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Notification Cards -->
                <div class="float-card card-1">
                    <div class="card-icon"><i class="fab fa-facebook"></i></div>
                    <div class="card-text">
                        <strong>New Lead from Facebook</strong>
                        <span>Honeymoon Package - Maldives</span>
                    </div>
                </div>

                <div class="float-card card-2">
                    <div class="card-icon green"><i class="fas fa-check"></i></div>
                    <div class="card-text">
                        <strong>Booking Confirmed!</strong>
                        <span>₹85,000 payment received</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="scroll-indicator">
            <span>Scroll to explore</span>
            <div class="mouse">
                <div class="wheel"></div>
            </div>
        </div>
    </section>

    <!-- Logo Marquee -->
    <section class="marquee-section">
        <div class="marquee">
            <div class="marquee-content">
                <span class="marquee-item">✦ Trusted by 100+ agencies</span>
                <span class="marquee-item">✦ 10,000+ leads managed</span>
                <span class="marquee-item">✦ ₹ 1 Cr+ bookings processed</span>
                <span class="marquee-item">✦ 4.9★ average rating</span>
                <span class="marquee-item">✦ Trusted by 100+ agencies</span>
                <span class="marquee-item">✦ 10,000+ leads managed</span>
                <span class="marquee-item">✦ ₹ 1 Cr+ bookings processed</span>
                <span class="marquee-item">✦ 4.9★ average rating</span>
            </div>
        </div>
    </section>

    <!-- Problem → Solution Section -->
    <section class="problem-section" id="problem">
        <div class="container">
            <div class="problem-grid">
                <div class="problem-text">
                    <span class="section-tag">The Problem</span>
                    <h2>You're losing leads while juggling spreadsheets.</h2>
                    <ul class="problem-list">
                        <li>
                            <span class="strike">Leads sitting in Excel for days</span>
                            <span class="fix">→ Auto-captured instantly</span>
                        </li>
                        <li>
                            <span class="strike">Manual follow-up reminders</span>
                            <span class="fix">→ Smart automated nudges</span>
                        </li>
                        <li>
                            <span class="strike">Scattered booking data</span>
                            <span class="fix">→ One unified dashboard</span>
                        </li>
                        <li>
                            <span class="strike">Chasing payments manually</span>
                            <span class="fix">→ Integrated payment links</span>
                        </li>
                    </ul>
                </div>
                <div class="problem-visual">
                    <div class="chaos-stack">
                        <div class="chaos-item spreadsheet">
                            <i class="fas fa-table"></i>
                            <span>leads_may_2026.xlsx</span>
                        </div>
                        <div class="chaos-item whatsapp">
                            <i class="fab fa-whatsapp"></i>
                            <span>99+ unread messages</span>
                        </div>
                        <div class="chaos-item notes">
                            <i class="fas fa-sticky-note"></i>
                            <span>Call Sharma ji tomorrow</span>
                        </div>
                    </div>
                    <div class="arrow-transform">
                        <svg viewBox="0 0 100 40">
                            <path d="M10 20 H85 M75 10 L90 20 L75 30" stroke="currentColor" stroke-width="3"
                                fill="none" />
                        </svg>
                    </div>
                    <div class="solution-preview">
                        <div class="mini-dash">
                            <div class="mini-header">Aleya Dashboard</div>
                            <div class="mini-content">
                                <div class="mini-stat">24 Leads</div>
                                <div class="mini-stat green">8 Hot</div>
                                <div class="mini-stat">₹4.2L</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features - Horizontal Scroll Cards -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Features</span>
                <h2>
                    Everything you need.<br /><span class="gradient-text">Nothing you don't.</span>
                </h2>
            </div>
        </div>

        <div class="features-scroll">
            <div class="features-track">
                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon multi">
                        <i class="fab fa-facebook"></i>
                        <i class="fab fa-instagram"></i>
                        <i class="fab fa-google"></i>
                    </div>
                    <h3>Multi-Channel Lead Capture</h3>
                    <p>
                        Leads from Facebook, Instagram, and Google Ads flow directly into
                        Aleya. Zero manual entry.
                    </p>
                    <div class="feature-tag">Automation</div>
                </div>

                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon single">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>AI Lead Scoring</h3>
                    <p>
                        Our AI ranks leads by conversion probability. Know exactly who to
                        call first.
                    </p>
                    <div class="feature-tag">AI-Powered</div>
                </div>

                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon single">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Smart Follow-up Reminders</h3>
                    <p>
                        Never miss a follow-up. Get notified at the right time via
                        WhatsApp or email.
                    </p>
                    <div class="feature-tag">Productivity</div>
                </div>

                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon single">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3>Itinerary Builder</h3>
                    <p>
                        Create stunning, branded itineraries in minutes. Share via link or
                        PDF.
                    </p>
                    <div class="feature-tag">Design</div>
                </div>

                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon single">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3>Integrated Payments</h3>
                    <p>
                        Collect payments via UPI, cards, or bank transfer. Auto-generate
                        invoices.
                    </p>
                    <div class="feature-tag">Finance</div>
                </div>

                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon single">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Voucher Management</h3>
                    <p>
                        Generate and send booking vouchers automatically after payment
                        confirmation.
                    </p>
                    <div class="feature-tag">Automation</div>
                </div>

                <!-- Duplicated cards for infinite scroll -->
                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon multi">
                        <i class="fab fa-facebook"></i>
                        <i class="fab fa-instagram"></i>
                        <i class="fab fa-google"></i>
                    </div>
                    <h3>Multi-Channel Lead Capture</h3>
                    <p>
                        Leads from Facebook, Instagram, and Google Ads flow directly into
                        Aleya. Zero manual entry.
                    </p>
                    <div class="feature-tag">Automation</div>
                </div>

                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon single">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>AI Lead Scoring</h3>
                    <p>
                        Our AI ranks leads by conversion probability. Know exactly who to
                        call first.
                    </p>
                    <div class="feature-tag">AI-Powered</div>
                </div>

                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon single">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Smart Follow-up Reminders</h3>
                    <p>
                        Never miss a follow-up. Get notified at the right time via
                        WhatsApp or email.
                    </p>
                    <div class="feature-tag">Productivity</div>
                </div>

                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon single">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3>Itinerary Builder</h3>
                    <p>
                        Create stunning, branded itineraries in minutes. Share via link or
                        PDF.
                    </p>
                    <div class="feature-tag">Design</div>
                </div>

                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon single">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3>Integrated Payments</h3>
                    <p>
                        Collect payments via UPI, cards, or bank transfer. Auto-generate
                        invoices.
                    </p>
                    <div class="feature-tag">Finance</div>
                </div>

                <div class="feature-card">
                    <div class="card-glow"></div>
                    <div class="feature-icon single">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Voucher Management</h3>
                    <p>
                        Generate and send booking vouchers automatically after payment
                        confirmation.
                    </p>
                    <div class="feature-tag">Automation</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Workflow Section - Vertical Timeline -->
    <section class="workflow-section" id="workflow">
        <div class="container">
            <div class="section-header center">
                <span class="section-tag">How It Works</span>
                <h2>
                    From lead to booking<br /><span class="gradient-text">in 4 simple steps</span>
                </h2>
            </div>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-number">01</div>
                    <div class="timeline-content">
                        <h3>Lead Arrives</h3>
                        <p>
                            Someone fills your Facebook or Google ad form. Aleya captures it
                            instantly and adds to your pipeline.
                        </p>
                    </div>
                    <div class="timeline-visual">
                        <div class="visual-box">
                            <i class="fab fa-facebook"></i>
                            <span class="arrow">→</span>
                            <div class="aleya-icon">
                                <img src="<?php echo USER_IMAGES ?>aleya-icon.png" alt="" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-number">02</div>
                    <div class="timeline-content">
                        <h3>You Follow Up</h3>
                        <p>
                            Get a WhatsApp notification. One-click to call. Aleya shows you
                            the full context.
                        </p>
                    </div>
                    <div class="timeline-visual">
                        <div class="visual-box">
                            <i class="fab fa-whatsapp"></i>
                            <span class="notification">🔔 New Hot Lead!</span>
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-number">03</div>
                    <div class="timeline-content">
                        <h3>Create & Quote</h3>
                        <p>
                            Build a beautiful itinerary, generate a quote, and share a
                            payment link — all in under 5 minutes.
                        </p>
                    </div>
                    <div class="timeline-visual">
                        <div class="visual-box itinerary">
                            <div class="itin-header">Maldives Package</div>
                            <div class="itin-price">₹1,25,000</div>
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-number">04</div>
                    <div class="timeline-content">
                        <h3>Confirm & Celebrate</h3>
                        <p>
                            Payment received. Vouchers sent. Customer happy. You just closed
                            another deal. 🎉
                        </p>
                    </div>
                    <div class="timeline-visual">
                        <div class="visual-box celebration">
                            <span>✓</span>
                            <span class="confetti">🎊</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section - Modern Cards -->
    <section class="pricing-section" id="pricing">
        <div class="container">
            <div class="section-header center">
                <span class="section-tag">Pricing</span>
                <h2>Simple, transparent pricing.</h2>
                <p class="section-desc">
                    Choose the plan that fits your business. Scale as you grow.
                </p>
            </div>

            <div class="pricing-toggle">
                <span class="toggle-label" data-period="monthly">Monthly</span>
                <label class="toggle">
                    <input type="checkbox" id="pricingToggle" checked />
                    <span class="slider"></span>
                </label>
                <span class="toggle-label active" data-period="yearly">
                    Yearly
                    <span class="save-tag">Save 25% on yearly plan</span>
                </span>
            </div>

            <div class="pricing-grid">
                <div class="pricing-card">
                    <div class="card-header">
                        <h3>CRM Starter</h3>
                        <p>For businesses of any industry getting organized</p>
                    </div>
                    <div class="card-price">
                        <span class="currency">₹</span>
                        <span class="amount" data-yearly="999" data-monthly="1249">999</span>
                        <span class="period" data-monthly="/mo" data-yearly="/mo">/mo</span>
                        <span class="gst-label">+ 18% GST</span>
                    </div>
                    <p class="billing-note" data-yearly="billed annually" data-monthly="billed monthly">billed annually
                    </p>
                    <ul class="card-features">
                        <li>
                            <i class="fas fa-check"></i> Lead capture from FB & Google
                        </li>
                        <li><i class="fas fa-check"></i> Smart follow-up reminders</li>
                        <li><i class="fas fa-check"></i> Performance dashboard</li>
                        <li><i class="fas fa-check"></i> Up to 10 users included</li>
                        <li><i class="fas fa-check"></i> Email support</li>
                        <li class="extra-users">
                            <i class="fas fa-info-circle"></i> +₹99/user after 10 users
                        </li>
                    </ul>
                    <a href="https://calendar.app.google/TWyKTqQXWfd1oyuK6" target="_blank"
                        class="card-btn outline">Book Free Demo</a>
                </div>

                <div class="pricing-card featured">
                    <div class="featured-badge">For Travel Agencies</div>
                    <div class="card-header">
                        <h3>ERP Complete</h3>
                        <p>Full-stack travel agency automation</p>
                    </div>
                    <div class="card-price">
                        <span class="currency">₹</span>
                        <span class="amount" data-yearly="2499" data-monthly="3124">2499</span>
                        <span class="period" data-monthly="/mo" data-yearly="/mo">/mo</span>
                        <span class="gst-label">+ 18% GST</span>
                    </div>
                    <p class="billing-note" data-yearly="billed annually" data-monthly="billed monthly">billed annually
                    </p>
                    <ul class="card-features">
                        <li><i class="fas fa-check"></i> Everything in CRM Starter</li>
                        <li><i class="fas fa-check"></i> AI lead scoring</li>
                        <li><i class="fas fa-check"></i> Itinerary builder</li>
                        <li><i class="fas fa-check"></i> Invoicing & payments</li>
                        <li><i class="fas fa-check"></i> Voucher automation</li>
                        <li><i class="fas fa-check"></i> Supplier management</li>
                        <li><i class="fas fa-check"></i> Up to 10 users included</li>
                        <li><i class="fas fa-check"></i> Priority support</li>
                        <li class="extra-users">
                            <i class="fas fa-info-circle"></i> +₹249/user after 10 users
                        </li>
                    </ul>
                    <a href="https://calendar.app.google/TWyKTqQXWfd1oyuK6" target="_blank"
                        class="card-btn primary">Book Free Demo</a>
                </div>
            </div>

            <!-- Feature Comparison Toggle -->
            <div class="comparison-toggle-wrapper">
                <button class="comparison-toggle" id="comparisonToggle">
                    <span>Compare all features</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>

            <!-- Feature Comparison Table -->
            <div class="comparison-table-wrapper" id="comparisonTable">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Features</th>
                            <th>CRM Starter</th>
                            <th>ERP Complete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="category-row">
                            <td colspan="3">Lead Management</td>
                        </tr>
                        <tr>
                            <td>Lead capture from FB & Google</td>
                            <td><i class="fas fa-check"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>AI Lead Scoring</td>
                            <td><i class="fas fa-times"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>Follow-up reminders</td>
                            <td><i class="fas fa-check"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>WhatsApp integration</td>
                            <td><i class="fas fa-times"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr class="category-row">
                            <td colspan="3">Travel Business Tools</td>
                        </tr>
                        <tr>
                            <td>Itinerary Builder</td>
                            <td><i class="fas fa-times"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>Invoicing & Payments</td>
                            <td><i class="fas fa-times"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>Voucher Automation</td>
                            <td><i class="fas fa-times"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>Supplier Management</td>
                            <td><i class="fas fa-times"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr class="category-row">
                            <td colspan="3">Team & Support</td>
                        </tr>
                        <tr>
                            <td>Users Included</td>
                            <td>Up to 10</td>
                            <td>Up to 10</td>
                        </tr>
                        <tr>
                            <td>Additional User Cost</td>
                            <td>₹99/user/mo</td>
                            <td>₹249/user/mo</td>
                        </tr>
                        <tr>
                            <td>Performance Dashboard</td>
                            <td><i class="fas fa-check"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>Support</td>
                            <td>Email</td>
                            <td>Priority</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Testimonials - Stacked Cards -->
    <section class="testimonials-section" id="testimonials">
        <div class="container">
            <div class="section-header center">
                <span class="section-tag">Reviews</span>
                <h2>
                    Businesses love us.<br /><span class="gradient-text">Like, really love us.</span>
                </h2>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="quote-icon">"</div>
                    <p>
                        Aleya literally changed my life. I used to spend 3 hours daily on
                        lead management. Now it's 20 minutes. I can actually focus on
                        growing the business.
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">RK</div>
                        <div class="author-info">
                            <strong>Rajesh Kumar</strong>
                            <span>Delhi</span>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="quote-icon">"</div>
                    <p>
                        The Facebook integration is magic. Leads appear in my dashboard
                        seconds after they submit. My competitors are still copying from
                        Excel 😂
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">PS</div>
                        <div class="author-info">
                            <strong>Priya Sharma</strong>
                            <span>Mumbai</span>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="quote-icon">"</div>
                    <p>
                        We went from 15 bookings/month to 40+ within 3 months of using
                        Aleya. The ROI is insane. Best ₹2,499 I spend every month.
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">AM</div>
                        <div class="author-info">
                            <strong>Amit Mehta</strong>
                            <span>Bangalore</span>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section" id="faq">
        <div class="container">
            <div class="faq-grid">
                <div class="faq-header">
                    <span class="section-tag">FAQ</span>
                    <h2>Questions?<br />We've got answers.</h2>
                    <p>
                        Can't find what you're looking for?
                        <a href="#contact">Chat with us</a>
                    </p>
                </div>

                <div class="faq-list">
                    <div class="faq-item">
                        <button class="faq-q">
                            <span>Do you offer a free trial?</span>
                            <i class="fas fa-plus"></i>
                        </button>
                        <div class="faq-a">
                            <p>
                                We offer a free demo instead of a trial. Book a personalized
                                demo and our team will walk you through every feature tailored
                                to your business needs.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-q">
                            <span>What's the difference between CRM and ERP plans?</span>
                            <i class="fas fa-plus"></i>
                        </button>
                        <div class="faq-a">
                            <p>
                                CRM Starter is perfect for any business looking to capture and
                                manage leads. ERP Complete is designed specifically for travel
                                agencies, adding itinerary builder, invoicing, voucher
                                automation, and supplier management.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-q">
                            <span>How many users can I add?</span>
                            <i class="fas fa-plus"></i>
                        </button>
                        <div class="faq-a">
                            <p>
                                Both plans include up to 10 users. Need more? Add additional
                                users at ₹99/user/month for CRM or ₹249/user/month for ERP.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-q">
                            <span>How does Facebook lead capture work?</span>
                            <i class="fas fa-plus"></i>
                        </button>
                        <div class="faq-a">
                            <p>
                                We connect to your Facebook Lead Ads via official API. When
                                someone submits a form, the lead appears in Aleya within
                                seconds.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-q">
                            <span>Can I import my existing leads?</span>
                            <i class="fas fa-plus"></i>
                        </button>
                        <div class="faq-a">
                            <p>
                                Absolutely. Upload a CSV file and we'll map all your existing
                                leads into Aleya. Takes 2 minutes.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-q">
                            <span>What payment methods do you accept?</span>
                            <i class="fas fa-plus"></i>
                        </button>
                        <div class="faq-a">
                            <p>
                                We accept UPI, VISA, Mastercard, and AMEX for both one-time
                                and recurring payments. Annual plans also accept bank
                                transfers.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-q">
                            <span>How long does onboarding take?</span>
                            <i class="fas fa-plus"></i>
                        </button>
                        <div class="faq-a">
                            <p>
                                Most businesses are up and running within 24 hours. Our team
                                helps you import data, connect your ad accounts, and train
                                your staff.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-q">
                            <span>Can I cancel anytime?</span>
                            <i class="fas fa-plus"></i>
                        </button>
                        <div class="faq-a">
                            <p>
                                Yes, you can cancel your subscription at any time. Your data
                                remains accessible for 30 days after cancellation.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-q">
                            <span>Is there a mobile app?</span>
                            <i class="fas fa-plus"></i>
                        </button>
                        <div class="faq-a">
                            <p>
                                Aleya is fully responsive and works great on mobile browsers.
                                A dedicated mobile app is coming soon.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-q">
                            <span>Is my data secure?</span>
                            <i class="fas fa-plus"></i>
                        </button>
                        <div class="faq-a">
                            <p>
                                100%. We use bank-grade encryption, regular backups, and
                                comply with data protection standards. Your data is yours.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="cta-section" id="start">
        <div class="cta-bg">
            <div class="gradient-orb orb-4"></div>
            <div class="gradient-orb orb-5"></div>
        </div>
        <div class="container">
            <div class="cta-content">
                <h2>Ready to put your business on autopilot?</h2>
                <p>
                    Join 100+ businesses who've already made the switch. Book a free
                    demo to see how Aleya can transform your workflow.
                </p>

                <a href="https://calendar.app.google/TWyKTqQXWfd1oyuK6" target="_blank" class="cta-primary cta-large">
                    Book Free Demo
                    <span class="btn-arrow">→</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="index.php" class="footer-logo">
                        <img src="<?php echo USER_IMAGES ?>aleya_logo.png" alt="Aleya" class="logo" />
                    </a>
                    <p>
                        The modern way to grow your business. From lead to conversion, all
                        in one place.
                    </p>
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
                    <a href="#features">Features</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#workflow">How It Works</a>
                    <a href="#testimonials">Reviews</a>
                </div>

                <div class="footer-links">
                    <h4>Company</h4>
                    <a href="/about-us/">About Us</a>
                    <a href="/contact-us/">Contact</a>
                </div>

                <div class="footer-links">
                    <h4>Legal</h4>
                    <a href="/privacy-policy/" target="_blank">Privacy Policy</a>
                    <a href="/terms-and-conditions/" target="_blank">Terms of Use</a>
                    <a href="/refund-policy/" target="_blank">Refund Policy</a>
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

    <!-- WhatsApp Float -->
    <a href="https://wa.me/919990026008?text=Hi,%20I'm%20interested%20in%20Aleya" class="whatsapp-float"
        target="_blank">
        <i class="fab fa-whatsapp"></i>
        <span>Chat with us</span>
    </a>

    <!-- Cookie Banner -->
    <div class="cookie-banner" id="cookieBanner">
        <p>
            We use cookies to improve your experience.
            <a href="/privacy-policy/">Learn more</a>
        </p>
        <button onclick="acceptCookies()">Got it</button>
    </div>

    <script src="<?php echo USER_JS ?>script.js"></script>
</body>

</html>