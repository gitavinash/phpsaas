<?php

require_once 'header.php';

?>

<!-- 2. CLINICAL HEADER BANNER & BREADCRUMBS -->
<section class="inner-page-banner" id="banner" style="background-image: url(<?php echo $page_image ?>);">
    <div class="container inner-banner-content">
        <h1><?php echo $page_title; ?></h1>
        <div class="breadcrumb-path">
            <a href="/">Home</a>
            <span><i
                    data-lucide="chevron-right"
                    style="width: 14px; height: 14px"></i></span>
            <span style="color: var(--color-primary-gold)"><?php echo $page_title; ?></span>
        </div>
    </div>
</section>

<!-- 3. CINEMATIC VIDEO SHOWCASE -->
<section class="cinematic-video-section" id="cinematicVideo">
    <div class="container text-center reveal">
        <span class="section-tag">Clinical Excellence Tour</span>
        <h2>Experience Our Scientific Care</h2>
        <p class="max-width-600" style="margin-bottom: 4rem">
            Take a virtual journey inside our private South Delhi suites and watch
            verified patient growth journeys in action:
        </p>

        <div class="video-container-box">
            <div class="widescreen-frame">
                <iframe
                    allowfullscreen
                    allow="autoplay"
                    title="Laser Session Video Vlog"
                    src="https://player.vimeo.com/video/1133358154?color=C5A880&amp;title=0&amp;portrait=0&amp;byline=0"></iframe>
            </div>
        </div>
    </div>
</section>

<!-- 12. CONSULTATION FORM SECTION (Reused lead capture) -->
<?php

require_once 'widgets/recent-articles.php';

require_once 'footer.php';

?>