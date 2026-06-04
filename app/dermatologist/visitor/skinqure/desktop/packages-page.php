<?php

require_once 'header.php'

?>

<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner"
                    style="background-image: url(<?php echo $page_image ?>); background-size: cover; background-position: 100% 100%">
                    <div class="inner_content">
                        <p>
                            <?php echo ucwords($page_title) ?></p>
                        <div class="text-start"><a href="/">Home</a> <span><i
                                    class="fa fa-chevron-right"></i></span><span>Service</span>
                            <span><i
                                    class="fa fa-chevron-right"></i></span><span><?php echo ucwords($page_title) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section_spacing_top ">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-md-12">
                        <div class="w-100 h100">
                            <div class="section_heading">
                                <h3 class="text-start mb-md-4"><?php echo ucwords($page_title) ?></h3>
                            </div>

                            <div class="description">
                                <?php echo $page_description ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php

$packageList = mysqli_query($connection, "SELECT post_image, post_title, post_description FROM " . TABLE_PREFIX . "post WHERE post_type='package' AND post_site_id=" . SITE_ID);

$backGroundColor = 'dark';

if (mysqli_num_rows($packageList) != 0) {

    while ($packageData = mysqli_fetch_assoc($packageList)) {

        extract($packageData);

        if ($backGroundColor == 'light') { ?>

<section class="technology" style="padding: 0px;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 h-100 formfield relative">
                            <div class="treatment-section-img">
                                <img src="<?php echo $post_image ?>" alt="" class="img-fluid w-100 h100 cover">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 formfield treatment-box text-white">
                            <h4><?php echo $post_title ?></h4>
                            <p><?php echo $post_description ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

            $backGroundColor = 'dark';
        } else { ?>


<section class="technology yellowbg" style="padding: 0px;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 formfield treatment-box text-white">
                            <h4><?php echo $post_title ?></h4>
                            <p><?php echo $post_description ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 h-100 formfield relative">
                            <div class="treatment-section-img">
                                <img src="<?php echo $post_image ?>" alt="" class="img-fluid w-100 h100 cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

            $backGroundColor = 'light';
        }
    }
}

?>


<?php

require_once 'footer.php';

?>