<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo defined('SITE_URL') ? SITE_URL : '/' ?>">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Domain Not Found | Blumox Technologies SaaS Applications</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-page: #f0f9ff;
            --bg-card: #ffffff;
            --bg-glass: rgba(255, 255, 255, 0.88);
            --border-light: #e2e8f0;
            --border-hover: rgba(55, 181, 241, 0.35);
            --color-primary: #1d1d1b;
            --color-brand: #37b5f1;
            --color-secondary: #475569;
            --accent-solid: #37b5f1;
            --accent-hover: #298ebd;
            --accent-text: #ffffff;
            --font-heading: "Plus Jakarta Sans", sans-serif;
            --font-body: "Inter", sans-serif;
            --container-width: 1240px;
            --header-height: 76px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            --shadow-glow: 0 10px 25px -5px rgba(55, 181, 241, 0.25);
            --border-radius-btn: 50px;
            --border-radius-card: 16px;
            --transition: all 0.25s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-page);
            color: var(--color-primary);
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
            line-height: 1.5;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.02em;
            text-transform: uppercase;
            color: var(--color-primary);
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: var(--transition);
        }

        ul {
            list-style: none;
        }

        .container {
            max-width: var(--container-width);
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Clean Responsive Header with Logo */
        .glass-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            z-index: 1000;
            background: var(--bg-glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-light);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 12px;
        }

        .logo-img {
            height: 38px;
            width: auto;
            display: block;
            flex-shrink: 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        @media (max-width: 640px) {
            :root {
                --header-height: 66px;
            }
            .logo-img {
                height: 28px;
            }
            .header-actions {
                gap: 6px;
            }
            .header-actions .btn {
                padding: 0.45rem 0.8rem;
                font-size: 0.72rem;
                letter-spacing: 0.02em;
            }
            .btn-text-full {
                display: none;
            }
            .btn-text-short {
                display: inline;
            }
        }

        @media (min-width: 641px) {
            .btn-text-full {
                display: inline;
            }
            .btn-text-short {
                display: none;
            }
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.65rem 1.4rem;
            border-radius: var(--border-radius-btn);
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid transparent;
            font-family: var(--font-heading);
        }

        .btn-primary {
            background: var(--accent-solid);
            color: var(--accent-text);
            border-color: var(--accent-solid);
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--color-primary);
            color: var(--color-primary);
        }

        .btn-outline:hover {
            background: var(--color-primary);
            color: #fff;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section {
            padding-top: calc(var(--header-height) + 3rem);
            padding-bottom: 3.5rem;
            background: radial-gradient(circle at 50% 0%, rgba(55, 181, 241, 0.12) 0%, transparent 60%);
            text-align: center;
        }

        .hero-badge {
            display: inline-block;
            margin: 0 auto 1.2rem auto;
            padding: 0.4rem 1.1rem;
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--border-radius-btn);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--color-secondary);
            text-transform: uppercase;
            box-shadow: var(--shadow-sm);
        }

        .hero-badge span {
            color: #ef4444;
            font-weight: 800;
            margin-right: 4px;
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.2rem;
            letter-spacing: -0.03em;
        }

        .text-accent-highlight {
            color: var(--color-primary);
            position: relative;
            z-index: 1;
            display: inline-block;
        }

        .text-accent-highlight::after {
            content: "";
            position: absolute;
            bottom: 3px;
            left: 0;
            width: 100%;
            height: 10px;
            background: var(--color-brand);
            z-index: -1;
            opacity: 0.4;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: var(--color-secondary);
            max-width: 600px;
            margin: 0 auto 2rem;
            line-height: 1.5;
        }

        .domain-tag {
            background: #ffffff;
            border: 1px solid var(--border-light);
            padding: 3px 10px;
            border-radius: 6px;
            color: var(--color-brand);
            font-weight: 700;
            font-family: monospace;
            font-size: 1rem;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2.5rem;
        }

        /* Domain Checker Utility */
        .checker-card {
            max-width: 520px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid var(--border-light);
            padding: 6px 6px 6px 18px;
            border-radius: var(--border-radius-btn);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--shadow-lg);
        }

        .checker-card input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 0.9rem;
            color: var(--color-primary);
            font-family: var(--font-body);
            background: transparent;
        }

        .checker-card input::placeholder {
            color: #94a3b8;
        }

        /* Products Grid Section */
        .products-section {
            padding: 4.5rem 0;
            background: #ffffff;
            border-top: 1px solid var(--border-light);
        }

        .section-header {
            text-align: center;
            max-width: 650px;
            margin: 0 auto 3rem auto;
        }

        .section-tag {
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            color: var(--color-brand);
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            display: block;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
        }

        .section-subtitle {
            color: var(--color-secondary);
            font-size: 1rem;
        }

        .challenges-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.8rem;
        }

        .challenge-card {
            background: #fdfdfd;
            padding: 1.8rem;
            border-radius: var(--border-radius-card);
            border: 1px solid var(--border-light);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: var(--shadow-sm);
        }

        .challenge-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-glow);
            border-color: var(--border-hover);
            background: #ffffff;
        }

        .card-icon-wrapper {
            height: 44px;
            display: flex;
            align-items: center;
            margin-bottom: 1.2rem;
        }

        .card-icon-img {
            max-height: 38px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
        }

        .card-icon-box {
            width: 44px;
            height: 44px;
            background: rgba(55, 181, 241, 0.1);
            color: var(--color-brand);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 1px solid rgba(55, 181, 241, 0.15);
        }

        .product-status-badge {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            padding: 0.25rem 0.65rem;
            border-radius: 6px;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .status-live { background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2); }
        .status-app { background: rgba(99, 102, 241, 0.1); color: #6366f1; border: 1px solid rgba(99, 102, 241, 0.2); }
        .status-popular { background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.2); }

        .challenge-card h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
            color: var(--color-primary);
        }

        .challenge-card p {
            color: var(--color-secondary);
            font-size: 0.9rem;
            margin-bottom: 1.25rem;
            line-height: 1.5;
            flex-grow: 1;
        }

        .solution-box {
            border-top: 1px solid var(--border-light);
            padding-top: 1rem;
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .link-ext {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--color-brand);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .link-ext:hover {
            text-decoration: underline;
        }

        /* FAQ Section */
        .faq-section {
            padding: 4rem 0;
            background: var(--bg-page);
            border-top: 1px solid var(--border-light);
        }

        .faq-container {
            max-width: 720px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .faq-item {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: 12px;
            overflow: hidden;
            transition: var(--transition);
        }

        .faq-question {
            padding: 1.1rem 1.4rem;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            font-family: var(--font-heading);
            text-transform: none;
        }

        .faq-question i {
            color: var(--color-brand);
            transition: var(--transition);
        }

        .faq-answer {
            padding: 0 1.4rem 1.1rem;
            color: var(--color-secondary);
            font-size: 0.9rem;
            display: none;
            line-height: 1.5;
        }

        .faq-item.active {
            border-color: var(--color-brand);
            box-shadow: var(--shadow-sm);
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-item.active .faq-answer {
            display: block;
        }

        /* Subscription Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            opacity: 0;
            pointer-events: none;
            transition: var(--transition);
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--border-radius-card);
            width: 100%;
            max-width: 500px;
            padding: 2.2rem;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: translateY(15px);
            transition: var(--transition);
        }

        .modal-overlay.active .modal-card {
            transform: translateY(0);
        }

        .modal-close {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            background: #f1f5f9;
            border: none;
            color: var(--color-secondary);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-close:hover {
            background: var(--color-primary);
            color: #fff;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 0.4rem;
        }

        .modal-subtitle {
            color: var(--color-secondary);
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            text-transform: none;
        }

        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--color-primary);
            margin-bottom: 0.35rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 0.9rem;
            background: #f8fafc;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            color: var(--color-primary);
            font-size: 0.9rem;
            font-family: var(--font-body);
            outline: none;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--color-brand);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(55, 181, 241, 0.15);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        /* Cloned Mega Footer of Blumox.com */
        footer {
            border-top: 1px solid var(--border-light);
            padding: 4rem 0 2rem;
            background: #ffffff;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
            gap: 3rem;
            margin-bottom: 4rem;
        }

        .footer-brand p {
            color: var(--color-secondary);
            max-width: 320px;
            margin-bottom: 1.5rem;
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .social-links {
            display: flex;
            gap: 0.75rem;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--bg-page);
            border: 1px solid var(--border-light);
            color: var(--color-secondary);
            transition: var(--transition);
        }

        .social-links a:hover {
            background: var(--color-primary);
            color: #fff;
            border-color: var(--color-primary);
            transform: translateY(-2px);
        }

        .footer-links h4 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            color: var(--color-primary);
        }

        .footer-links ul li {
            margin-bottom: 0.6rem;
        }

        .footer-links ul li a {
            color: var(--color-secondary);
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .footer-links ul li a:hover {
            color: var(--color-brand);
        }

        .contact-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .contact-item {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 0.85rem;
            color: var(--color-secondary);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .contact-item .icon {
            flex-shrink: 0;
            margin-top: 2px;
            color: var(--color-brand);
        }

        .contact-item a {
            color: var(--color-secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        .contact-item a:hover {
            color: var(--color-brand);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border-light);
            padding-top: 2rem;
            font-size: 0.85rem;
            color: var(--color-secondary);
        }

        .footer-legal {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .footer-legal a:hover {
            color: var(--color-brand);
        }

        .separator {
            color: var(--color-secondary);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .challenges-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.2rem; }
            .hero-subtitle { font-size: 0.98rem; }
            .challenges-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
            .footer-bottom { flex-direction: column; gap: 1rem; text-align: center; }
            .checker-card { flex-direction: column; border-radius: 12px; padding: 10px; }
            .checker-card input { width: 100%; text-align: center; }
            .checker-card .btn { width: 100%; }
        }
    </style>
</head>

<body>

    <!-- Header Navigation (Logo + Action Buttons) -->
    <header class="glass-header">
        <div class="container" style="width: 100%;">
            <div class="header-content">
                <a href="https://blumox.com/" target="_blank" style="display: flex; align-items: center;">
                    <img src="https://blumox.com/assets/images/logo.png" alt="Blumox Technologies" class="logo-img">
                </a>

                <div class="header-actions">
                    <a href="<?php echo defined('HTTP_PROTOCOL') && defined('MAIN_DOMAIN') ? HTTP_PROTOCOL . MAIN_DOMAIN . (defined('LOGIN_PATH') ? LOGIN_PATH : '/login/') : '/login/' ?>" class="btn btn-outline">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span class="btn-text-full">Site Owner Login</span>
                        <span class="btn-text-short">Login</span>
                    </a>
                    <button onclick="openModal('General Application Subscription')" class="btn btn-primary">
                        <i class="fa-solid fa-cube"></i>
                        <span class="btn-text-full">Subscribe App</span>
                        <span class="btn-text-short">Subscribe</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-badge">
                    <span>404</span> DOMAIN NOT CONFIGURED
                </div>

                <h1 class="hero-title">
                    REQUESTED WEBSITE<br />
                    <span class="text-accent-highlight">NOT FOUND ON SERVER</span>
                </h1>

                <p class="hero-subtitle">
                    The domain <span class="domain-tag"><?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'this-domain.com') ?></span> is pointed here, but no application is active yet. Select a software application below to subscribe and launch instantly.
                </p>

                <div class="hero-actions">
                    <a href="#apps-store" class="btn btn-primary">
                        <i class="fa-solid fa-rocket"></i> Subscribe Our Applications
                    </a>
                    <a href="<?php echo defined('HTTP_PROTOCOL') && defined('MAIN_DOMAIN') ? HTTP_PROTOCOL . MAIN_DOMAIN . (defined('LOGIN_PATH') ? LOGIN_PATH : '/login/') : '/login/' ?>" class="btn btn-outline">
                        <i class="fa-solid fa-user-lock"></i> Are You Site Owner? Login
                    </a>
                </div>

                <!-- Domain Checker Bar -->
                <div class="checker-card">
                    <i class="fa-solid fa-magnifying-glass" style="color: var(--color-secondary);"></i>
                    <input type="text" id="domainSearchInput" placeholder="Check domain status..." value="<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? '') ?>">
                    <button onclick="checkDomainStatus()" class="btn btn-primary">
                        Check Status <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
                <div id="domainStatusResult" style="margin-top: 0.8rem; font-size: 0.9rem; font-weight: 600; display: none;"></div>
            </div>
        </section>

        <!-- Product Subscriptions Grid -->
        <section id="apps-store" class="products-section">
            <div class="container">
                <div class="section-header">
                    <span class="section-tag">SAAS & ENTERPRISE APPLICATIONS</span>
                    <h2 class="section-title">
                        SELECT & SUBSCRIBE <span class="text-accent-highlight">APPLICATION</span>
                    </h2>
                    <p class="section-subtitle">
                        Turn this domain into a full-featured web platform. Pick an application below to start your subscription.
                    </p>
                </div>

                <div class="challenges-grid">

                    <!-- 1. Aleya Travel CRM & ERP -->
                    <div class="challenge-card">
                        <span class="product-status-badge status-live">Live Product</span>
                        <div class="card-icon-wrapper">
                            <img src="https://blumox.com/assets/images/products/aleya_logo.png" alt="Aleya Logo" class="card-icon-img">
                        </div>
                        <h3>Aleya Travel CRM & ERP</h3>
                        <p>Complete tour management, itinerary builder, dynamic booking engine, and customer lead pipeline.</p>
                        
                        <div class="solution-box">
                            <a href="https://aleya.in/" target="_blank" class="link-ext">Visit Aleya.in <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                            <button onclick="openModal('Aleya Travel CRM & ERP')" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.75rem;">
                                Subscribe
                            </button>
                        </div>
                    </div>

                    <!-- 2. Oxteal Web Hosting & AMC -->
                    <div class="challenge-card">
                        <span class="product-status-badge status-live">Live Cloud</span>
                        <div class="card-icon-wrapper">
                            <img src="https://blumox.com/assets/images/products/oxteal_logo.svg" alt="Oxteal Logo" class="card-icon-img">
                        </div>
                        <h3>Oxteal Hosting & Infrastructure</h3>
                        <p>Premium cloud hosting, SSL certificates, 24/7 technical AMC, and automated backups.</p>
                        
                        <div class="solution-box">
                            <a href="https://oxteal.com/" target="_blank" class="link-ext">Visit Oxteal.com <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                            <button onclick="openModal('Oxteal Hosting & Infrastructure')" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.75rem;">
                                Subscribe
                            </button>
                        </div>
                    </div>

                    <!-- 3. E-Commerce Website -->
                    <div class="challenge-card">
                        <span class="product-status-badge status-popular">Popular</span>
                        <div class="card-icon-wrapper">
                            <img src="https://blumox.com/assets/images/products/cartt_logo.png" alt="Cartt E-Commerce Logo" class="card-icon-img">
                        </div>
                        <h3>E-Commerce Website</h3>
                        <p>All-in-one online store with product catalog, cart, checkout, payment gateway & inventory control.</p>
                        
                        <div class="solution-box">
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-secondary);">Instant Store</span>
                            <button onclick="openModal('E-Commerce Website')" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.75rem;">
                                Subscribe
                            </button>
                        </div>
                    </div>

                    <!-- 4. Website ( CMS ) -->
                    <div class="challenge-card">
                        <span class="product-status-badge status-live">Enterprise</span>
                        <div class="card-icon-wrapper">
                            <img src="https://blumox.com/assets/images/logo.png" alt="Website CMS Logo" class="card-icon-img">
                        </div>
                        <h3>Website ( CMS )</h3>
                        <p>Custom content management system & dynamic corporate website with SEO page builder.</p>
                        
                        <div class="solution-box">
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-secondary);">Dynamic CMS</span>
                            <button onclick="openModal('Website ( CMS )')" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.75rem;">
                                Subscribe
                            </button>
                        </div>
                    </div>

                    <!-- 5. CRM System -->
                    <div class="challenge-card">
                        <span class="product-status-badge status-popular">High Growth</span>
                        <div class="card-icon-wrapper">
                            <div class="card-icon-box"><i class="fa-solid fa-users-gear"></i></div>
                        </div>
                        <h3>CRM System</h3>
                        <p>Customer relationship manager, lead pipeline tracking, quotation generator & sales team analytics.</p>
                        
                        <div class="solution-box">
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-secondary);">Sales Pipeline</span>
                            <button onclick="openModal('CRM System')" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.75rem;">
                                Subscribe
                            </button>
                        </div>
                    </div>

                    <!-- 6. Flight Ticket Website -->
                    <div class="challenge-card">
                        <span class="product-status-badge status-live">API Ready</span>
                        <div class="card-icon-wrapper">
                            <div class="card-icon-box"><i class="fa-solid fa-plane"></i></div>
                        </div>
                        <h3>Flight Ticket Website</h3>
                        <p>Flight booking engine with GDS/API integration, real-time fare search & automated ticket issuance.</p>
                        
                        <div class="solution-box">
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-secondary);">GDS Booking</span>
                            <button onclick="openModal('Flight Ticket Website')" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.75rem;">
                                Subscribe
                            </button>
                        </div>
                    </div>

                    <!-- 7. Travel Portal -->
                    <div class="challenge-card">
                        <span class="product-status-badge status-popular">B2B & B2C</span>
                        <div class="card-icon-wrapper">
                            <div class="card-icon-box"><i class="fa-solid fa-suitcase-rolling"></i></div>
                        </div>
                        <h3>Travel Portal</h3>
                        <p>Complete B2B & B2C travel portal for tours, hotels, transfers, package itineraries & agent logins.</p>
                        
                        <div class="solution-box">
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-secondary);">Travel Portal</span>
                            <button onclick="openModal('Travel Portal')" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.75rem;">
                                Subscribe
                            </button>
                        </div>
                    </div>

                    <!-- 8. Inventory Management Software -->
                    <div class="challenge-card">
                        <span class="product-status-badge status-live">ERP System</span>
                        <div class="card-icon-wrapper">
                            <img src="https://blumox.com/assets/images/products/batchno_logo.png" alt="BatchNo Logo" class="card-icon-img">
                        </div>
                        <h3>Inventory Management</h3>
                        <p>Multi-warehouse stock control, batch & serial tracking, supplier ledgers & automated purchase orders.</p>
                        
                        <div class="solution-box">
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-secondary);">Stock Control</span>
                            <button onclick="openModal('Inventory Management Software')" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.75rem;">
                                Subscribe
                            </button>
                        </div>
                    </div>

                    <!-- 9. Aleya SPA Management Software -->
                    <div class="challenge-card">
                        <span class="product-status-badge status-app">Android App</span>
                        <div class="card-icon-wrapper">
                            <img src="https://blumox.com/assets/images/products/aleya_logo.png" alt="Aleya SPA Logo" class="card-icon-img">
                        </div>
                        <h3>Aleya SPA Management</h3>
                        <p>Spa & salon appointment scheduling, therapist management, service packages & POS billing app.</p>
                        
                        <div class="solution-box">
                            <a href="https://play.google.com/store/apps/details?id=com.blumox.aleyaspa" target="_blank" class="link-ext">
                                Play Store <i class="fa-brands fa-google-play"></i>
                            </a>
                            <button onclick="openModal('Aleya SPA Management Software')" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.75rem;">
                                Subscribe
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
            <div class="container">
                <div class="section-header" style="margin-bottom: 2rem;">
                    <span class="section-tag">HELP & SUPPORT</span>
                    <h2 class="section-title">FREQUENTLY ASKED QUESTIONS</h2>
                </div>

                <div class="faq-container">
                    <div class="faq-item" onclick="toggleFaq(this)">
                        <div class="faq-question">
                            <span>Why am I seeing this "Domain Not Found" notice?</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            This page is displayed when a domain's DNS points to our servers, but no active application has been mapped to this domain yet.
                        </div>
                    </div>

                    <div class="faq-item" onclick="toggleFaq(this)">
                        <div class="faq-question">
                            <span>How do I subscribe and activate an application on my domain?</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            Simply click "Subscribe" on any application card above. Select your application, enter your requirements, and our team will activate your application within minutes.
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Cloned Mega Footer of Blumox.com -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <!-- Brand Column -->
                <div class="footer-brand">
                    <img src="https://blumox.com/assets/images/logo.png" alt="Blumox Logo" style="display: block; height: 34px; margin-bottom: 1.2rem" />
                    <p>
                        Building the digital future with premium IT solutions. We transform complex challenges into elegant growth engines.
                    </p>
                    <div class="social-links">
                        <a href="https://www.linkedin.com/company/blumoxofficial/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                                <rect x="2" y="9" width="4" height="12"></rect>
                                <circle cx="4" cy="4" r="2"></circle>
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/BlumoxIndia" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                            </svg>
                        </a>
                        <a href="https://x.com/blumoxofficial" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z" />
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/blumoxofficial" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                            </svg>
                        </a>
                        <a href="https://www.youtube.com/@BlumoxTechnologies" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.33z" />
                                <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Services Column -->
                <div class="footer-links">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="https://blumox.com/service-software-development/" target="_blank">Software Development</a></li>
                        <li><a href="https://blumox.com/service-mobile-app-development/" target="_blank">Mobile Applications</a></li>
                        <li><a href="https://blumox.com/service-cloud-devops/" target="_blank">Cloud & DevOps</a></li>
                        <li><a href="https://blumox.com/service-erp-crm/" target="_blank">ERP & CRM Solutions</a></li>
                        <li><a href="https://blumox.com/service-web-design/" target="_blank">Website Designing</a></li>
                        <li><a href="https://blumox.com/service-tech-support/" target="_blank">Tech Support</a></li>
                    </ul>
                </div>

                <!-- Company Column -->
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="https://blumox.com/about/" target="_blank">About Blumox</a></li>
                        <li><a href="https://blumox.com/industries/" target="_blank">Industries</a></li>
                        <li><a href="https://blumox.com/clientele/" target="_blank">Clientele</a></li>
                        <li><a href="https://blumox.com/products/" target="_blank">Products</a></li>
                        <li><a href="https://blumox.com/contact/" target="_blank">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Contact Column -->
                <div class="footer-links footer-contact">
                    <h4>Contact Us</h4>
                    <ul class="contact-list">
                        <li class="contact-item">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </span>
                            <span>A2/5A, WHS Block 2,<br />Kirti Nagar, New Delhi - 110015</span>
                        </li>
                        <li class="contact-item">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </span>
                            <div>
                                <a href="tel:+919990026008">+91 999 002 6008</a><br>
                                <a href="tel:+919990026009">+91 999 002 6009</a>
                            </div>
                        </li>
                        <li class="contact-item">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                </svg>
                            </span>
                            <a href="mailto:contact@blumox.com">contact@blumox.com</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y') ?> Blumox Technologies. All rights reserved.</p>
                <div class="footer-legal">
                    <a href="https://blumox.com/privacy-policy/" target="_blank">Privacy Policy</a>
                    <span class="separator">•</span>
                    <a href="https://blumox.com/terms-of-service/" target="_blank">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Subscription Modal -->
    <div id="inquiryModal" class="modal-overlay">
        <div class="modal-card">
            <button onclick="closeModal()" class="modal-close">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h3 class="modal-title">Subscribe Application</h3>
            <p class="modal-subtitle">Select an application and tell us about your project requirements.</p>

            <form id="subscriptionForm" onsubmit="handleSubscriptionSubmit(event)">
                <div class="form-group">
                    <label class="form-label">Select Application</label>
                    <select id="modalAppSelect" class="form-control" style="font-weight: 600; color: var(--color-primary);" required>
                        <option value="Aleya Travel CRM & ERP">Aleya Travel CRM & ERP</option>
                        <option value="Oxteal Hosting & Infrastructure">Oxteal Hosting & Infrastructure</option>
                        <option value="E-Commerce Website">E-Commerce Website</option>
                        <option value="Website ( CMS )">Website ( CMS )</option>
                        <option value="CRM System">CRM System</option>
                        <option value="Flight Ticket Website">Flight Ticket Website</option>
                        <option value="Travel Portal">Travel Portal</option>
                        <option value="Inventory Management Software">Inventory Management Software</option>
                        <option value="Aleya SPA Management Software">Aleya SPA Management Software</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Your Name</label>
                    <input type="text" id="subscriberName" class="form-control" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="subscriberEmail" class="form-control" placeholder="name@company.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Mobile / Phone Number</label>
                    <input type="tel" id="subscriberPhone" class="form-control" placeholder="+91 99900 00000" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Project Requirements / Notes</label>
                    <textarea id="subscriberRequirement" class="form-control" placeholder="Describe your business needs, features, or custom requirements..." rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; margin-top: 0.4rem;">
                    Submit Subscription Request <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div id="modalSuccess" style="display: none; text-align: center; padding: 1.2rem 0;">
                <i class="fa-solid fa-circle-check" style="font-size: 3rem; color: #10b981; margin-bottom: 0.8rem;"></i>
                <h4 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 0.4rem;">Subscription Submitted!</h4>
                <p style="color: var(--color-secondary); font-size: 0.9rem; margin-bottom: 1.2rem;">
                    Thank you! Our technical onboarding team will contact you shortly to activate your selected application.
                </p>
                <button onclick="closeModal()" class="btn btn-outline">Close</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function openModal(appName) {
            const select = document.getElementById('modalAppSelect');
            if (appName && select) {
                // Set matching option if available
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].value === appName) {
                        select.selectedIndex = i;
                        break;
                    }
                }
            }
            document.getElementById('subscriptionForm').style.display = 'block';
            document.getElementById('modalSuccess').style.display = 'none';
            document.getElementById('inquiryModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('inquiryModal').classList.remove('active');
        }

        document.getElementById('inquiryModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        function toggleFaq(element) {
            const isActive = element.classList.contains('active');
            document.querySelectorAll('.faq-item').forEach(item => item.classList.remove('active'));
            if (!isActive) {
                element.classList.add('active');
            }
        }

        function checkDomainStatus() {
            const input = document.getElementById('domainSearchInput').value.trim();
            const resultDiv = document.getElementById('domainStatusResult');

            if (!input) {
                resultDiv.style.color = '#ef4444';
                resultDiv.innerText = 'Please enter a valid domain name.';
                resultDiv.style.display = 'block';
                return;
            }

            resultDiv.style.color = 'var(--color-brand)';
            resultDiv.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Checking domain status for <strong>${input}</strong>...`;
            resultDiv.style.display = 'block';

            setTimeout(() => {
                resultDiv.style.color = '#059669';
                resultDiv.innerHTML = `<i class="fa-solid fa-circle-check"></i> Domain <strong>${input}</strong> is ready for setup! Select an application below to subscribe.`;
            }, 600);
        }

        function handleSubscriptionSubmit(e) {
            e.preventDefault();
            document.getElementById('subscriptionForm').style.display = 'none';
            document.getElementById('modalSuccess').style.display = 'block';
        }
    </script>
</body>

</html>
<?php
exit();
?>