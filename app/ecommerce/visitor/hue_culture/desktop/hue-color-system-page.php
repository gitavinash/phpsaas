<?php

require_once 'header.php';

?>

<!-- About Hero Section -->
<section class="hero"
    style="background-color: var(--pepper-white); padding-top: 150px; padding-bottom: 80px; align-items: center; min-height: 70vh;">
    <div class="container"
        style="display: flex; flex-wrap: wrap; align-items: center; gap: 60px; margin: 0 auto; width: 100%;">

        <!-- Left: Text Content & CTA -->
        <div class="hero-content reveal-on-load" style="flex: 1; min-width: 320px;">
            <h1 class="hero-premium-headline" style="margin-bottom: 20px;">
                <?php echo $page_title ?>
            </h1>
            <p style="font-size: 1.25rem; color: #555; line-height: 1.6; max-width: 550px; margin-bottom: 35px;">
                <?php echo $page_description ?>
            </p>
        </div>

        <!-- Right: Image -->
        <div class="hero-image-container reveal-on-load" style="flex: 1; min-width: 320px; padding-left: 0;">
            <div class="hero-img-wrapper" style="transform: rotate(-1.5deg)">
                <img src="<?php echo $page_image ?>" alt="Hue Color System Experts"
                    style="height: 500px; object-fit: cover; width: 100%; border-radius: 20px;" />
            </div>
        </div>


    </div>
</section>

<?php

require_once 'footer.php';

?>