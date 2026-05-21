<?php

require_once 'header.php';

?>

<!-- Hero Section -->
<section class="hero" style="padding-top: 150px; padding-bottom: 80px">
    <div class="container" style="
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          justify-content: space-between;
          gap: 60px;
          max-width: 1400px;
          margin: 0 auto;
          width: 100%;
        ">
        <!-- Left: Text Content -->
        <div class="hero-content reveal-on-load" style="flex: 1; min-width: 340px; max-width: 650px">
            <h1 class="hero-premium-headline" style="margin-bottom: 30px; line-height: 1.1">
                Tools To See <span class="hero-premium-hues">Hues</span><br />
                The Way Color <span class="hero-premium-experts">Experts</span> Do
            </h1>
            <p style="
              font-size: 1.3rem;
              color: #555;
              line-height: 1.7;
              margin-bottom: 40px;
            ">
                Our systematic approach helps you see undertones clearly, choose
                colour confidently, and create spaces you'll love for years to come.
            </p>

            <div class="hero-btns" style="display: flex; gap: 20px; align-items: center">
                <a href="index.html#color-path" class="btn-primary pink-bg" style="
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
                width: fit-content;
                transition: all 0.3s;
              " onmouseover="
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15)';
              " onmouseout="
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.1)';
              ">
                    Hue Color System
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Right: Image -->
        <div class="hero-image-container reveal-on-load" style="flex: 1; min-width: 340px; padding: 0">
            <div class="hero-img-wrapper" style="
              height: 650px;
              border-radius: 30px;
              box-shadow: 0 40px 80px rgba(0, 0, 0, 0.1);
              transform: rotate(1.5deg);
              transition: transform 0.5s ease-out;
            " onmouseover="this.style.transform = 'rotate(0deg) scale(1.02)'"
                onmouseout="this.style.transform = 'rotate(1.5deg)'">
                <img src="<?php echo USER_IMAGES ?>hero.jpg" alt="Color Design Experts" style="
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 30px;
              " />
            </div>
        </div>
    </div>
</section>

<!-- Color Path Section -->
<section id="color-path" class="color-path">
    <div class="container">
        <div class="section-header reveal">
            <h2 class="serif-normal">Choose Your Color Path</h2>
            <p class="section-desc">
                Whether you're decorating your home, designing for clients, brands
                or looking for guidance, start here to find your color journey.
            </p>
        </div>

        <div class="path-grid reveal">
            <div class="path-card light-teal-bg">
                <div class="card-icon"></div>
                <div class="card-content">
                    <h3>I Am A Space Owner</h3>
                </div>
            </div>

            <div class="path-card soft-green-bg">
                <div class="card-icon"></div>
                <div class="card-content">
                    <h3>I'm a Designer</h3>
                </div>
            </div>

            <div class="path-card blue-bg">
                <div class="card-icon"></div>
                <div class="card-content">
                    <h3>I Want One On One Assistance</h3>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Personal Color Shopper Section -->
<section id="personal-shopper" class="personal-shopper beige-bg">
    <div class="container">
        <div class="section-header reveal">
            <h2 class="serif-normal">Personal Color Shopper</h2>
            <p class="section-desc">
                Whether you're decorating your home, designing for clients, or
                looking for guidance, start here to find your color journey.
            </p>
        </div>
    </div>
</section>

<!-- Personal Color Shopper Section -->
<section id="personal-shopper" class="personal-shopper beige-bg" style="padding: 0px; border: none">
    <div class="shopper-grid reveal">
        <div class="shopper-card card-large" id="furnishing-stores">
            <div class="card-overlay"></div>
            <div class="card-content">
                <h3>Furnishing Stores</h3>
                <p>
                    Discover the perfect furniture pieces that complement your
                    unique color palette and style.
                </p>
            </div>
        </div>

        <div class="shopper-card card-medium" id="interiors">
            <div class="card-overlay"></div>
            <div class="card-content">
                <h3>Interiors</h3>
                <p>
                    Transform your living spaces with expert-led color coordination
                    and interior design advice.
                </p>
            </div>
        </div>

        <div class="shopper-card card-small" id="paints">
            <div class="card-overlay"></div>
            <div class="card-content">
                <h3>Paints</h3>
                <p>
                    Choose the right paint colors and finishes to bring your vision
                    to life with confidence.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Color Clinic Section -->
<section id="color-clinic" class="color-clinic white-bg">
    <div class="container">
        <div class="section-header reveal">
            <h2 class="serif-normal">Color Clinic</h2>
            <!-- <p class="section-desc">
            Solving your color dilemmas with professional, expert-led advice.
            Master the system to find your color journey.
          </p> -->
        </div>

        <div class="clinic-grid reveal">
            <!-- Row 1: Icon Cards -->
            <div class="clinic-card help-card">
                <div class="card-icon">
                    <img src="<?php echo USER_IMAGES ?>color-clinic/spaces.png" alt="Spaces" />
                </div>
                <h3>Spatial Hues</h3>
                <p>
                    Optimizing the flow and psychology of your environment through
                    expert color placement and selection.
                </p>
            </div>

            <div class="clinic-card help-card">
                <div class="card-icon">
                    <img src="<?php echo USER_IMAGES ?>color-clinic/personal-colour-analysis.png"
                        alt="Personal Color Analysis" />
                </div>
                <h3>Personal Color Analysis</h3>
                <p>
                    Discover the hues that harmonize with your unique profile,
                    bringing clarity and confidence to your palette.
                </p>
            </div>

            <div class="clinic-card help-card">
                <div class="card-icon">
                    <img src="<?php echo USER_IMAGES ?>color-clinic/styling.png" alt="Styling" />
                </div>
                <h3>Styling</h3>
                <p>
                    Elevate your space with curated finishes and textures that align
                    with your chosen color journey.
                </p>
            </div>

            <!-- Row 2: Fixed Icon Cards -->
            <div class="clinic-card help-card">
                <div class="card-icon">
                    <img src="<?php echo USER_IMAGES ?>color-clinic/energy.png" alt="Energy Aligned Palettes" />
                </div>
                <h3>Energy Aligned Palettes</h3>
                <p>
                    Identify undertones in your paint, flooring, tile, and fabrics in
                    seconds. The essential first step for any home or project.
                </p>
            </div>

            <div class="clinic-card help-card">
                <div class="card-icon">
                    <img src="<?php echo USER_IMAGES ?>color-clinic/collaborations.png" alt="Collaborations" />
                </div>
                <h3>Collaborations</h3>
                <p>
                    Identify undertones in your paint, flooring, tile, and fabrics in
                    seconds. The essential first step for any home or project.
                </p>
            </div>

            <div class="clinic-card help-card">
                <div class="card-icon">
                    <img src="<?php echo USER_IMAGES ?>color-clinic/color-dna.png" alt="Color DNA" />
                </div>
                <h3>Color DNA</h3>
                <p>
                    Learn how to choose timeless colours, find the perfect white, and
                    coordinate finishes that flow throughout your home.
                </p>
            </div>
        </div>
    </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials blue-bg">
    <div class="container">
        <div class="section-header reveal">
            <h2 class="serif-normal">Testimonials</h2>
            <p class="section-desc">
                Real stories from homeowners and designers who discovered the power
                of the Hue Culture System.
            </p>
        </div>

        <!-- Panel B: Horizontal Scroll Slider -->
        <div class="testimonial-panel active" id="testi-b">
            <div class="testi-slider-wrap">
                <div class="testi-slider" id="testiSlider">
                    <!-- Slide 1: Video -->
                    <div class="testi-slide">
                        <div class="testi-video-wrap">
                            <div class="testi-video-overlay" title="Click to play audio"></div>
                            <iframe
                                src="https://www.youtube.com/embed/D61BvxAOxm0?autoplay=1&mute=1&controls=0&enablejsapi=1"
                                title="Sarah Jenkins - Interior Design Case Study" allow="
                      accelerometer;
                      autoplay;
                      clipboard-write;
                      encrypted-media;
                      gyroscope;
                      picture-in-picture;
                      web-share;
                    " allowfullscreen></iframe>
                        </div>
                        <div class="slide-author">
                            <div class="slide-avatar av1">SJ</div>
                            <div class="slide-author-info">
                                <span class="slide-name">Sarah Jenkins</span>
                                <span class="slide-role">Homeowner</span>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2: Video -->
                    <div class="testi-slide">
                        <div class="testi-video-wrap">
                            <div class="testi-video-overlay" title="Click to play audio"></div>
                            <iframe
                                src="https://www.youtube.com/embed/KO4wcgpWT6Q?autoplay=1&mute=1&controls=0&enablejsapi=1"
                                title="Marcus Thorne - Architect Testimonial" allow="
                      accelerometer;
                      autoplay;
                      clipboard-write;
                      encrypted-media;
                      gyroscope;
                      picture-in-picture;
                      web-share;
                    " allowfullscreen></iframe>
                        </div>
                        <div class="slide-author">
                            <div class="slide-avatar av2">MT</div>
                            <div class="slide-author-info">
                                <span class="slide-name">Marcus Thorne</span>
                                <span class="slide-role">Interior Architect</span>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3: Text Only -->
                    <div class="testi-slide">
                        <span class="quote-accent">“</span>
                        <p class="slide-text">
                            No more second-guessing. The color wheel and guides are
                            essential for anyone who wants to get their renovation right
                            the first time.
                        </p>
                        <div class="slide-author">
                            <div class="slide-avatar av3">ER</div>
                            <div class="slide-author-info">
                                <span class="slide-name">Elena Rodriguez</span>
                                <span class="slide-role">Space Owner</span>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 4: Text Only -->
                    <div class="testi-slide">
                        <span class="quote-accent">“</span>
                        <p class="slide-text">
                            Working with Hue Culture transformed the way I approach every
                            new project. My clients now immediately feel the harmony in
                            every room.
                        </p>
                        <div class="slide-author">
                            <div class="slide-avatar av4">AP</div>
                            <div class="slide-author-info">
                                <span class="slide-name">Anika Patel</span>
                                <span class="slide-role">Interior Designer</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-nav">
                <button class="slider-dot active" data-idx="0"></button>
                <button class="slider-dot" data-idx="1"></button>
                <button class="slider-dot" data-idx="2"></button>
                <button class="slider-dot" data-idx="3"></button>
            </div>
        </div>
    </div>
</section>

<!-- Blogs Section -->

<?php

if ($checkArticles = mysqli_query($connection, "SELECT post_image, post_title, link_url, meta_description, category_title FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "post ON link_relation_id=post_id AND post_site_id=" . SITE_ID . " INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id INNER JOIN " . TABLE_PREFIX . "category ON category_id=post_category WHERE link_status=200 AND link_type='post' AND link_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 3")) {
    if (mysqli_num_rows($checkArticles) != 0) {

        echo "<section class='blogs light-pink-bg'>
    <div class='container'>
        <div class='section-header reveal'>
            <h2 class='serif-normal'>Blogs</h2>
            <p class='section-desc'>
                Expert insights, trend analysis, and practical advice to help you
                master the art of color coordination.
            </p>
        </div>
        <div class='blog-grid reveal'>";

        while ($articleDetails = mysqli_fetch_assoc($checkArticles)) {
            extract($articleDetails);
            echo "<a href='{$link_url}' title='{$post_title}'>
            <div class='blog-card'>
                <div class='blog-img'>
                    <div class='img-placeholder' style='background-color: #f2efed'>
                        <img src='{$post_image}'' alt='{$post_title}'>
                    </div>
                </div>
                <div class='blog-content'>
                    <span class='blog-category'>{$category_title}</span>
                    <h3>{$post_title}</h3>
                    <p>
                        {$meta_description}
                    </p>
                    <a href='{$link_url}' class='read-more'>Read Entry →</a>
                </div>
            </div></a>";
        }

        echo "</div>
    </div>
</section>";
    }
}

?>

<!-- Reach Us Section -->
<section class="reach-us-section peanut-butter-bg">
    <div class="container">
        <div class="reach-us-grid">
            <div class="reach-us-image">
                <img src="<?php echo USER_IMAGES ?>reach-us-banner.jpg" alt="Reach Us" />
            </div>

            <div class="reach-us-content">
                <h2>Reach Us</h2>

                <div class="reach-block">
                    <h3>For Color Selection</h3>

                    <a href="tel:+919811577280" class="reach-item color-selection-color">
                        <span class="reach-icon color-selection-color">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <span>+91-9910441788</span>
                    </a>

                    <a href="mailto:mamta@hueculture.in" class="reach-item color-selection-color">
                        <span class="reach-icon color-selection-color">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <span>info@hueculture.in</span>
                    </a>
                </div>

                <div class="reach-block">
                    <h3>For Collaborations</h3>

                    <a href="tel:+919811577280" class="reach-item collab-color">
                        <span class="reach-icon collab-color">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <span>+91-9811577280</span>
                    </a>

                    <a href="mailto:mamta@hueculture.in" class="reach-item collab-color">
                        <span class="reach-icon collab-color">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <span>mamta@hueculture.in</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>