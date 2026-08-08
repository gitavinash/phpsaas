<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo defined('SITE_URL') ? SITE_URL : '/' ?>">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Website Expired | Renew Subscription - Blumox Cloud</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-page: #fff1f2;
            --bg-card: #ffffff;
            --bg-glass: rgba(255, 255, 255, 0.9);
            --border-light: #fecdd3;
            --border-hover: rgba(244, 63, 94, 0.4);
            --color-primary: #1d1d1b;
            --color-brand: #37b5f1;
            --color-danger: #e11d48;
            --color-danger-hover: #be123c;
            --color-secondary: #475569;
            --accent-solid: #37b5f1;
            --accent-hover: #298ebd;
            --font-heading: "Plus Jakarta Sans", sans-serif;
            --font-body: "Inter", sans-serif;
            --container-width: 1240px;
            --header-height: 76px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 25px -5px rgba(225, 29, 72, 0.15);
            --shadow-glow: 0 10px 25px -5px rgba(55, 181, 241, 0.3);
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

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
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

        /* Clean Responsive Header */
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
            box-shadow: 0 2px 15px rgba(225, 29, 72, 0.05);
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
            color: #ffffff;
            border-color: var(--accent-solid);
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow);
        }

        .btn-danger {
            background: var(--color-danger);
            color: #ffffff;
            border-color: var(--color-danger);
        }

        .btn-danger:hover {
            background: var(--color-danger-hover);
            border-color: var(--color-danger-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(225, 29, 72, 0.4);
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

        /* Hero Expired Section */
        .hero-section {
            padding-top: calc(var(--header-height) + 3rem);
            padding-bottom: 3.5rem;
            background: radial-gradient(circle at 50% 0%, rgba(244, 63, 94, 0.15) 0%, transparent 65%);
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
            color: var(--color-danger);
            text-transform: uppercase;
            box-shadow: var(--shadow-sm);
        }

        .hero-badge i {
            margin-right: 4px;
            animation: blink-dot 1.2s infinite;
        }

        @keyframes blink-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.2rem;
            letter-spacing: -0.03em;
        }

        .text-accent-highlight {
            color: var(--color-danger);
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
            background: #fca5a5;
            z-index: -1;
            opacity: 0.5;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: var(--color-secondary);
            max-width: 620px;
            margin: 0 auto 2rem;
            line-height: 1.5;
        }

        /* Expiry Warning Box */
        .expiry-box {
            max-width: 650px;
            margin: 0 auto 2.5rem;
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--border-radius-card);
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow-lg);
            text-align: left;
        }

        .expiry-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px dashed var(--border-light);
            font-size: 0.95rem;
        }

        .expiry-row:last-child {
            border-bottom: none;
        }

        .expiry-label {
            color: var(--color-secondary);
            font-weight: 600;
        }

        .expiry-value {
            font-weight: 700;
            color: var(--color-primary);
        }

        .domain-tag {
            background: rgba(55, 181, 241, 0.1);
            border: 1px solid rgba(55, 181, 241, 0.3);
            padding: 2px 8px;
            border-radius: 4px;
            color: var(--color-brand);
            font-family: monospace;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* Renewal Plans Grid */
        .plans-section {
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
            color: var(--color-danger);
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            display: block;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
        }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 1050px;
            margin: 0 auto;
        }

        .plan-card {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--border-radius-card);
            padding: 2.2rem 1.8rem;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: var(--shadow-sm);
        }

        .plan-card.featured {
            border-color: var(--color-brand);
            box-shadow: 0 10px 30px rgba(55, 181, 241, 0.2);
            transform: scale(1.03);
        }

        .plan-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--color-brand);
            color: #ffffff;
            padding: 0.25rem 0.85rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            min-width: 200px;
            text-align: center;
        }

        .plan-title {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .plan-price-wrapper {
            text-align: center;
            margin: 1rem 0 1.5rem;
        }

        .plan-price {
            font-size: 2.4rem;
            font-weight: 800;
            color: var(--color-primary);
            line-height: 1;
        }

        .plan-period {
            font-size: 0.85rem;
            color: var(--color-secondary);
            margin-top: 4px;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 1.8rem;
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 0.9rem;
        }

        .plan-features li {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--color-secondary);
        }

        .plan-features i {
            color: #10b981;
        }

        /* Payment Methods Strip */
        .payment-strip {
            background: var(--bg-page);
            border-top: 1px solid var(--border-light);
            padding: 2.5rem 0;
            text-align: center;
        }

        .payment-icons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.8rem;
            flex-wrap: wrap;
            margin-top: 1rem;
            font-size: 1.8rem;
            color: var(--color-secondary);
        }

        /* Renewal Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
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

        /* Cloned Mega Footer */
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

        /* Responsive */
        @media (max-width: 1024px) {
            .plans-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }

            .plans-grid {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .expiry-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
        }
    </style>
</head>

<body>

    <!-- Header Navigation -->
    <header class="glass-header">
        <div class="container" style="width: 100%;">
            <div class="header-content">
                <a href="https://blumox.com/" target="_blank" style="display: flex; align-items: center;">
                    <img src="https://blumox.com/assets/images/logo.png" alt="Blumox Technologies" class="logo-img">
                </a>

                <div class="header-actions">
                    <a href="<?php echo defined('HTTP_PROTOCOL') && defined('MAIN_DOMAIN') ? HTTP_PROTOCOL . MAIN_DOMAIN . (defined('LOGIN_PATH') ? LOGIN_PATH : '/login/') : '/login/' ?>" class="btn btn-outline">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span class="btn-text-full">Owner Login</span>
                        <span class="btn-text-short">Login</span>
                    </a>
                    <button onclick="openRenewModal('1 Year Instant Renewal')" class="btn btn-danger">
                        <i class="fa-solid fa-bolt"></i>
                        <span class="btn-text-full">Instant Renew</span>
                        <span class="btn-text-short">Renew</span>
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
                    <i class="fa-solid fa-circle-exclamation"></i> WEBSITE SUBSCRIPTION EXPIRED
                </div>

                <h1 class="hero-title">
                    SUBSCRIPTION <span class="text-accent-highlight">EXPIRED</span>
                </h1>

                <p class="hero-subtitle">
                    The subscription for <span class="domain-tag"><?php echo htmlspecialchars($site_domain ?? $_SERVER['HTTP_HOST'] ?? 'this domain') ?></span> has expired. Renew your plan today to instantly restore full website access.
                </p>

                <!-- Expiry Information Summary Box -->
                <div class="expiry-box">
                    <div class="expiry-row">
                        <span class="expiry-label">Website Domain</span>
                        <span class="expiry-value"><?php echo htmlspecialchars($site_domain ?? $_SERVER['HTTP_HOST'] ?? 'domain.com') ?></span>
                    </div>

                    <?php if (isset($site_installation_expiry_date)): ?>
                        <div class="expiry-row">
                            <span class="expiry-label">Expired On</span>
                            <span class="expiry-value" style="color: var(--color-danger);">
                                <?php echo date('d F Y', strtotime($site_installation_expiry_date)); ?>
                            </span>
                        </div>
                        <div class="expiry-row">
                            <span class="expiry-label">Grace Period Until</span>
                            <span class="expiry-value" style="color: #d97706;">
                                <?php echo date('d F Y', strtotime('+1 Month', strtotime($site_installation_expiry_date))); ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="expiry-row">
                        <span class="expiry-label">Service Status</span>
                        <span class="expiry-value" style="color: var(--color-danger);">
                            <i class="fa-solid fa-triangle-exclamation"></i> Pending Renewal
                        </span>
                    </div>
                </div>

                <div class="hero-actions">
                    <button onclick="openRenewModal('Instant Subscription Renewal')" class="btn btn-danger" style="padding: 0.8rem 2rem; font-size: 0.9rem;">
                        <i class="fa-solid fa-credit-card"></i> Pay & Renew Online Now
                    </button>
                    <a href="<?php echo defined('HTTP_PROTOCOL') && defined('MAIN_DOMAIN') ? HTTP_PROTOCOL . MAIN_DOMAIN . (defined('LOGIN_PATH') ? LOGIN_PATH : '/login/') : '/login/' ?>" class="btn btn-outline">
                        <i class="fa-solid fa-user-lock"></i> Owner Login
                    </a>
                </div>
            </div>
        </section>

    </main>

    <!-- Cloned Mega Footer -->
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
                    <h4>Contact Support</h4>
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

    <!-- Instant Renewal Modal -->
    <div id="renewModal" class="modal-overlay">
        <div class="modal-card">
            <button onclick="closeRenewModal()" class="modal-close">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h3 class="modal-title">Instant Renewal Payment</h3>
            <p class="modal-subtitle">Renew your subscription to un-suspend your website immediately.</p>

            <form id="renewalForm" onsubmit="handleRenewalSubmit(event)">
                <div class="form-group">
                    <label class="form-label">Target Website Domain</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($site_domain ?? $_SERVER['HTTP_HOST'] ?? 'domain.com') ?>" readonly style="font-weight: 700; color: var(--color-brand);">
                </div>

                <div class="form-group">
                    <label class="form-label">Selected Renewal Plan</label>
                    <input type="text" id="modalPlanName" class="form-control" readonly style="font-weight: 700;">
                </div>

                <div class="form-group">
                    <label class="form-label">Owner Name</label>
                    <input type="text" id="renewOwnerName" class="form-control" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="renewOwnerEmail" class="form-control" placeholder="name@company.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone / WhatsApp Number</label>
                    <input type="tel" id="renewOwnerPhone" class="form-control" placeholder="+91 99900 00000" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select class="form-control" id="paymentMethodSelect" style="font-weight: 600;">
                        <option value="UPI / QR Code Scan">Instant UPI / QR Code Scan</option>
                        <option value="Razorpay Online Payment">Razorpay (Cards, Net Banking, Wallets)</option>
                        <option value="WhatsApp Direct Support Renewal">WhatsApp Support Assistant</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-danger" style="width: 100%; padding: 0.85rem; margin-top: 0.4rem;">
                    <i class="fa-solid fa-bolt"></i> Proceed to Pay & Activate
                </button>
            </form>

            <div id="modalRenewSuccess" style="display: none; text-align: center; padding: 1.2rem 0;">
                <i class="fa-solid fa-circle-check" style="font-size: 3rem; color: #10b981; margin-bottom: 0.8rem;"></i>
                <h4 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 0.4rem;">Payment Request Generated!</h4>
                <p style="color: var(--color-secondary); font-size: 0.9rem; margin-bottom: 1.2rem;">
                    Thank you! Our automated system will activate your website immediately upon payment verification.
                </p>
                <a href="https://api.whatsapp.com/send?phone=919990026008&text=Hi,%20I%20want%20to%20renew%20my%20website%20subscription%20for%20<?php echo urlencode($site_domain ?? $_SERVER['HTTP_HOST'] ?? '') ?>" target="_blank" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">
                    <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp to Pay Instant
                </a>
                <button onclick="closeRenewModal()" class="btn btn-outline" style="width: 100%;">Close</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function openRenewModal(planName) {
            document.getElementById('modalPlanName').value = planName;
            document.getElementById('renewalForm').style.display = 'block';
            document.getElementById('modalRenewSuccess').style.display = 'none';
            document.getElementById('renewModal').classList.add('active');
        }

        function closeRenewModal() {
            document.getElementById('renewModal').classList.remove('active');
        }

        document.getElementById('renewModal').addEventListener('click', function(e) {
            if (e.target === this) closeRenewModal();
        });

        function handleRenewalSubmit(e) {
            e.preventDefault();
            document.getElementById('renewalForm').style.display = 'none';
            document.getElementById('modalRenewSuccess').style.display = 'block';
        }
    </script>
</body>

</html>
<?php
exit();
?>