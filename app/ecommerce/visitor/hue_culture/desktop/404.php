<?php

require_once 'header.php';

?>

<!-- Full Screen Mobile Navigation -->
<div class="mobile-nav" id="mobile-nav">
    <div class="menu-close" id="menu-close">&times;</div>
    <nav>
        <ul>
            <li><a href="about/" class="mobile-link">About Us</a></li>
            <li>
                <a href="/#color-path" class="mobile-link">Hue Color System</a>
            </li>
            <li><a href="service/" class="mobile-link">Color Clinic</a></li>
            <li><a href="blogs/" class="mobile-link">Blogs</a></li>
            <li><a href="training/" class="mobile-link">Training</a></li>
            <li><a href="community/" class="mobile-link">Community</a></li>
            <li>
                <a href="/#portfolio" class="mobile-link">Portfolio</a>
            </li>
            <li>
                <a href="contact/" class="btn-primary mobile-cta">Contact Us</a>
            </li>
        </ul>
    </nav>
</div>

<!-- 404 Hero Section -->
<section class="error-hero">
    <div class="container reveal-on-load">
        <span class="error-hero-badge">Error 404</span>
        <h1 class="hero-premium-headline error-headline">
            Page Not <span class="hero-premium-hues">Found</span>
        </h1>
        <p class="error-desc reveal-on-load">
            The hue you are searching for does not exist on our palette. It may
            have been moved, renamed, or is temporarily unavailable.
        </p>
        <a href="/" class="btn-primary" style="
            padding: 15px 40px;
            border-radius: 40px;
            font-size: 1.1rem;
            display: inline-block;
          ">
            Return to Homepage <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</section>

<?php

require_once 'footer.php';

?>