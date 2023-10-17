<?php

$mediaURL = SITE_URL.$media_url;
header("Location: {$mediaURL}", TRUE, 301);
exit();

require_once 'header.php';

?>
<section class="hero side-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-7">
                <h2 class="isfont-40 text-white my-3 is-font600 text-center text-lg-start">
                    <?php echo $media_title ?>
                </h2>
            </div>
            <div class="col-12 col-lg-5 my-4 my-lg-0">
                <img src="<?php echo USER_IMAGES ?>r11.png" alt=""
                    class="img-fluid d-none d-lg-block m-auto ms-lg-auto" />
            </div>
        </div>
    </div>
</section>

<main>
    <section class="vartical-padding">
        <div class="container">
            <div class="row align-items-center text-justify">

            </div>
        </div>
    </section>

</main>

<?php

require_once 'footer.php';

?>