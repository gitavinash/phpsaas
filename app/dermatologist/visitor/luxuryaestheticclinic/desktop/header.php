<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        Luxury Aesthetic Clinic | Premium Skin, Hair & Body Treatments in Delhi
    </title>

    <!-- SEO Meta Tags -->
    <meta
        name="description"
        content="Delhi's premier dermatologist-led aesthetic clinic. Discover celebrity-grade treatments, advanced FDA-approved technologies, and customized rejuvenation plans for naturally beautiful skin, hair, and body results." />
    <meta
        name="keywords"
        content="Luxury Aesthetic Clinic, Dermatology Delhi, Laser Hair Removal Delhi, Botox, Fillers, HydraFacial Delhi, PRP Hair Treatment, GFC Therapy, CoolSculpting Delhi" />
    <meta name="author" content="Luxury Aesthetic Clinic" />
    <meta name="robots" content="index, follow" />

    <!-- Open Graph (Facebook/LinkedIn) -->
    <meta property="og:type" content="website" />
    <meta
        property="og:title"
        content="Luxury Aesthetic Clinic | Delhi's Premier Skincare & Dermatology Center" />
    <meta
        property="og:description"
        content="Advanced dermatologist-led treatments powered by world-class FDA-approved technologies. Experience the ultimate luxury in clinical aesthetic care." />
    <meta property="og:image" content="assets/images/hero_clinic.png" />

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta
        name="twitter:title"
        content="Luxury Aesthetic Clinic | Delhi's Premier Skincare Center" />
    <meta
        name="twitter:description"
        content="Experience celebrity-grade clinical care. Advanced skin, hair, and body contouring by Delhi's top dermatologists." />

    <!-- Favicon -->
    <link
        rel="icon"
        type="image/png"
        href="https://luxuryaestheticclinic.com/coolsculpting/logo.png" />

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>styles.css" />
</head>

<body>
    <!-- 1. PREMIUM STICKY HEADER -->
    <header class="main-header" id="mainHeader">
        <div class="container header-container">
            <!-- Logo -->
            <a href="#hero" class="logo-link" id="logoLink">
                <img
                    src="https://luxuryaestheticclinic.com/coolsculpting/logo.png"
                    alt="Luxury Aesthetic Clinic Logo" />
            </a>

            <!-- Desktop/Mobile Navigation Menu -->
            <nav class="header-nav" aria-label="Main Navigation">
                <ul class="nav-menu" id="navMenu">
                    <li><a href="#hero" class="nav-link">Home</a></li>
                    <li><a href="about.html" class="nav-link">About Us</a></li>
                    <li><a href="team.html" class="nav-link">Our Team</a></li>
                    <li><a href="#treatments" class="nav-link">Treatments</a></li>
                    <li><a href="#before-after" class="nav-link">Results</a></li>
                    <li><a href="contact.html" class="nav-link">Contact</a></li>

                    <!-- Mobile Drawer Contacts (only visible inside mobile navigation menu overlay) -->
                    <li class="mobile-menu-contacts">
                        <a
                            href="tel:+919999999999"
                            class="btn btn-secondary btn-call"
                            id="mobileMenuCall">
                            <i
                                data-lucide="phone"
                                style="width: 16px; margin-right: 8px"></i>
                            Call Clinic
                        </a>
                        <a
                            href="https://wa.me/919999999999"
                            class="btn btn-whatsapp"
                            id="mobileMenuWhatsapp"
                            target="_blank"
                            rel="noopener">
                            <i
                                data-lucide="message-square"
                                style="width: 16px; margin-right: 8px"></i>
                            Chat via WhatsApp
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Action Buttons (Desktop Minimalist Concierge Icons + CTA) -->
            <div class="header-actions">
                <a
                    href="tel:+919999999999"
                    class="btn-icon-round btn-icon-call"
                    id="headerCall"
                    aria-label="Call Clinic Now">
                    <i data-lucide="phone"></i>
                </a>
                <a
                    href="https://wa.me/919999999999"
                    class="btn-icon-round btn-icon-whatsapp"
                    id="headerWhatsapp"
                    target="_blank"
                    rel="noopener"
                    aria-label="WhatsApp Clinic Now">
                    <i data-lucide="message-square"></i>
                </a>
                <a
                    href="book-consultation.html"
                    class="btn btn-primary btn-book"
                    id="headerBook">Book Consultation</a>
            </div>

            <!-- Mobile Menu Toggle -->
            <button
                class="mobile-nav-toggle"
                id="mobileNavToggle"
                aria-label="Toggle navigation menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>