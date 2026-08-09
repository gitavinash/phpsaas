<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo defined('SITE_URL') ? SITE_URL : '/' ?>">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Website Status Pending | Blumox Cloud</title>

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

        /* Clean Header */
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
            padding-top: calc(var(--header-height) + 4rem);
            padding-bottom: 4rem;
            background: radial-gradient(circle at 50% 0%, rgba(55, 181, 241, 0.14) 0%, transparent 60%);
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
            color: var(--color-brand);
            text-transform: uppercase;
            box-shadow: var(--shadow-sm);
        }

        .hero-title {
            font-size: 3.4rem;
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
            max-width: 620px;
            margin: 0 auto 2.5rem;
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
        }

        /* Help Cards Grid */
        .help-section {
            padding: 4rem 0;
            background: #ffffff;
            border-top: 1px solid var(--border-light);
        }

        .help-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.8rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .help-card {
            background: #fdfdfd;
            padding: 2rem;
            border-radius: var(--border-radius-card);
            border: 1px solid var(--border-light);
            text-align: center;
            transition: var(--transition);
        }

        .help-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-hover);
            box-shadow: var(--shadow-md);
            background: #ffffff;
        }

        .help-icon {
            font-size: 2rem;
            color: var(--color-brand);
            margin-bottom: 1rem;
        }

        .help-card h3 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .help-card p {
            font-size: 0.88rem;
            color: var(--color-secondary);
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

        .separator {
            color: var(--color-secondary);
        }

        @media (max-width: 640px) {
            :root { --header-height: 66px; }
            .logo-img { height: 28px; }
            .header-actions { gap: 6px; }
            .header-actions .btn { padding: 0.45rem 0.8rem; font-size: 0.72rem; letter-spacing: 0.02em; }
            .btn-text-full { display: none; }
            .btn-text-short { display: inline; }
        }

        @media (min-width: 641px) {
            .btn-text-full { display: inline; }
            .btn-text-short { display: none; }
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.2rem; }
            .help-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
            .footer-bottom { flex-direction: column; gap: 1rem; text-align: center; }
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
                    <i class="fa-solid fa-spinner fa-spin"></i> STATUS PENDING • UNDER REVIEW
                </div>

                <h1 class="hero-title">
                    WEBSITE STATUS IS<br />
                    <span class="text-accent-highlight">PENDING</span>
                </h1>

                <p class="hero-subtitle">
                    The account status for <span class="domain-tag"><?php echo htmlspecialchars($site_domain ?? $_SERVER['HTTP_HOST'] ?? 'domain.com') ?></span> is currently pending or under administrative review. Please sign in or contact support for details.
                </p>

                <div class="hero-actions">
                    <a href="<?php echo defined('HTTP_PROTOCOL') && defined('MAIN_DOMAIN') ? HTTP_PROTOCOL . MAIN_DOMAIN . (defined('LOGIN_PATH') ? LOGIN_PATH : '/login/') : '/login/' ?>" class="btn btn-primary">
                        <i class="fa-solid fa-user"></i> Owner Login
                    </a>
                    <a href="mailto:contact@blumox.com" class="btn btn-outline">
                        <i class="fa-solid fa-headset"></i> Contact Support
                    </a>
                </div>
            </div>
        </section>

        <!-- Help Section -->
        <section class="help-section">
            <div class="container">
                <div class="help-grid">
                    <div class="help-card">
                        <div class="help-icon"><i class="fa-solid fa-laptop-code"></i></div>
                        <h3>Account Verification</h3>
                        <p>Sign in to your control panel to verify domain setup and account settings.</p>
                    </div>

                    <div class="help-card">
                        <div class="help-icon"><i class="fa-solid fa-clock"></i></div>
                        <h3>Deployment Pending</h3>
                        <p>Website provisioning takes a few moments to finish propagation.</p>
                    </div>

                    <div class="help-card">
                        <div class="help-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
                        <h3>Support Assistance</h3>
                        <p>Need assistance? Contact our technical team for instant status updates.</p>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Cloned Mega Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
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

                <div class="footer-links">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="https://blumox.com/service-software-development/" target="_blank">Software Development</a></li>
                        <li><a href="https://blumox.com/service-mobile-app-development/" target="_blank">Mobile Applications</a></li>
                        <li><a href="https://blumox.com/service-cloud-devops/" target="_blank">Cloud & DevOps</a></li>
                        <li><a href="https://blumox.com/service-erp-crm/" target="_blank">ERP & CRM Solutions</a></li>
                        <li><a href="https://blumox.com/service-web-design/" target="_blank">Website Designing</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="https://blumox.com/about/" target="_blank">About Blumox</a></li>
                        <li><a href="https://blumox.com/industries/" target="_blank">Industries</a></li>
                        <li><a href="https://blumox.com/clientele/" target="_blank">Clientele</a></li>
                        <li><a href="https://blumox.com/products/" target="_blank">Products</a></li>
                    </ul>
                </div>

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
                                <a href="tel:+919990026008">+91 999 002 6008</a>
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

</body>

</html>
<?php
exit();
?>