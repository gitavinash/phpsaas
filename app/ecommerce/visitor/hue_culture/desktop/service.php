<?php

require_once 'header.php';

?>
<!-- Full Screen Mobile Navigation -->
<div class="mobile-nav" id="mobile-nav">
    <div class="menu-close" id="menu-close">&times;</div>
    <nav>
        <ul>
            <li><a href="about.html" class="mobile-link">About Us</a></li>
            <li>
                <a href="index.html#color-path" class="mobile-link">Hue Color System</a>
            </li>
            <li><a href="service.html" class="mobile-link">Color Clinic</a></li>
            <li><a href="blogs.html" class="mobile-link">Blogs</a></li>
            <li><a href="training.html" class="mobile-link">Training</a></li>
            <li><a href="community.html" class="mobile-link">Community</a></li>
            <li>
                <a href="index.html#portfolio" class="mobile-link">Portfolio</a>
            </li>
            <li>
                <a href="contact.html" class="btn-primary mobile-cta">Contact Us</a>
            </li>
        </ul>
    </nav>
</div>

<!-- 2-Column Hero Section -->
<section class="service-hero">
    <div class="container hero-split-container">
        <!-- Left Column: Text Content -->
        <div class="service-hero-content">
            <h1 class="hero-premium-headline">
                The <span class="hero-premium-hues">Color Clinic</span>
            </h1>
            <p class="service-hero-desc">
                Our expert team employs scientific color analysis and refined
                intuition to transform personal styling, interior environments, and
                energetic alignments. We empower you to see and utilize color the
                way the experts do.
            </p>
        </div>

        <!-- Right Column: 6 Small Cards Aligned Right -->
        <div class="service-hero-right">
            <div class="service-feature-grid">
                <div class="service-feature-card primary">
                    <div class="service-feature-icon">
                        <i class="fa-solid fa-house"></i>
                    </div>
                    <h3 class="serif-normal">Spaces</h3>
                </div>

                <div class="service-feature-card">
                    <div class="service-feature-icon">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <h3 class="serif-normal">Color Analysis</h3>
                </div>

                <div class="service-feature-card">
                    <div class="service-feature-icon">
                        <i class="fa-solid fa-shirt"></i>
                    </div>
                    <h3 class="serif-normal">Styling</h3>
                </div>

                <div class="service-feature-card">
                    <div class="service-feature-icon">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3 class="serif-normal">Energy Palettes</h3>
                </div>

                <div class="service-feature-card">
                    <div class="service-feature-icon">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <h3 class="serif-normal">Collaborations</h3>
                </div>

                <div class="service-feature-card">
                    <div class="service-feature-icon">
                        <i class="fa-solid fa-shapes"></i>
                    </div>
                    <h3 class="serif-normal">Colorana</h3>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Style Article Content -->
<article class="service-blog-post">
    <div class="container reveal">
        <div class="blog-post-header">
            <h2 class="serif-normal">
                Mastering the Science of Spectral Harmony
            </h2>
            <p class="section-desc">
                Discover how professional color guidance transforms more than just
                your walls—it transforms your experience of space and self.
            </p>
        </div>

        <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=2070&auto=format&fit=crop"
            alt="Color Harmony" class="blog-post-hero-image" />

        <div class="blog-post-body">
            <p>
                Color is not merely a visual adornment; it is a fundamental pillar
                of environmental psychology. At Hue Culture, we approach the "Color
                Clinic" as an laboratory where science meets soul. Every room, every
                outfit, and every brand palette is an opportunity to tune the
                frequency of your daily life.
            </p>

            <h3>The Anatomy of a Perfect Space</h3>
            <p>
                When we evaluate a room, we start with the light. Natural light is
                dynamic, shifting from the cool blues of early morning to the warm
                ambers of sunset. A color that looks impeccable in a north-facing
                studio might feel stark and sterile in an east-facing kitchen. Our
                process involves mapping these shifts to ensure your finishes—tiles,
                fabrics, and paints—remain harmonious throughout the day.
            </p>

            <img src="https://images.unsplash.com/photo-1513161455079-7dc1de15ef3e?q=80&w=1976&auto=format&fit=crop"
                alt="Interior Details" class="blog-post-inline-image" />

            <h3>Identity Through Palette</h3>
            <p>
                In personal styling, your palette is your signature. It's the silent
                communicator that tells the world who you are before you speak. Our
                Personal Color Analysis goes beyond seasonal labels. We look at the
                undertones of your skin, the clarity of your eyes, and the natural
                contrast levels of your features to build a "core" wardrobe palette
                that empowers you with confidence.
            </p>

            <p>
                By integrating these scientific principles with your personal
                aesthetic goals, we create a roadmap for effortless style. No more
                second-guessing; only clarity and confidence in every choice.
            </p>
        </div>

        <!-- Featured Products Section at bottom of Article -->
        <div class="blog-post-footer">
            <h2 class="featured-products-title serif-normal">
                Featured Products
            </h2>
            <div class="related-blog-grid">
                <a href="product-details.html" class="product-card">
                    <img src="https://images.unsplash.com/photo-1544457070-4cd773b4d71e?q=80&w=2130&auto=format&fit=crop"
                        class="product-card-img" alt="Linen Throw" />
                    <div class="product-card-info">
                        <span class="product-card-tag">Textiles</span>
                        <h3 class="product-card-title serif-normal">Raw Linen Throw</h3>
                        <div class="product-card-price">₹120.00</div>
                    </div>
                </a>

                <a href="product-details.html" class="product-card">
                    <img src="https://images.unsplash.com/photo-1610701596007-11502861dcfa?q=80&w=2070&auto=format&fit=crop"
                        class="product-card-img" alt="Ambient Lamp" />
                    <div class="product-card-info">
                        <span class="product-card-tag">Lighting</span>
                        <h3 class="product-card-title serif-normal">
                            Ambient Hue Lamp
                        </h3>
                        <div class="product-card-price">₹210.00</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</article>
<?php

require_once 'footer.php';

?>