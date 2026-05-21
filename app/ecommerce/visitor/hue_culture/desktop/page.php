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
        </div>
    </div>
</section>

<article class="service-blog-post">
    <div class="container reveal">
        <div class="blog-post-body">
            <?php echo $page_description ?>
        </div>
    </div>
</article>

<?php

require_once 'footer.php';

?>