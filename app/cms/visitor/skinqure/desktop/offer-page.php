<?php

require_once 'header.php'

?>

<section class="section_spacing_top ">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    <iframe width="100%" height="315"
                        src="https://www.youtube.com/embed/<?php echo str_replace('https://www.youtube.com/watch?v=', '', $page_highlight) ?>"
                        title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
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

$backGroundColor = 'dark';

$offerList = mysqli_query($connection, "SELECT page_image, page_title, page_description FROM " . TABLE_PREFIX . "page INNER JOIN " . TABLE_PREFIX . "link on link_relation_id=page_id WHERE page_type='offer' AND link_status=200 AND page_status=1 AND link_type='page' AND link_site_id=" . SITE_ID . " LIMIT 0, 10");

if (mysqli_num_rows($offerList) != 0) {

    while ($offerData = mysqli_fetch_assoc($offerList)) {

        extract($offerData);

        if ($backGroundColor === 'light') { ?>


<section class="treatment" style="padding: 0px;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 h-100 formfield relative">
                            <div class="treatment-section-img">
                                <img src="<?php echo $page_image ?>" alt="" class="img-fluid w-100 h100 cover">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 formfield treatment-box">
                            <h4><?php echo $page_title ?></h4>
                            <p><?php echo $page_description ?></p>
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

<section class="treatment yellowbg" style="padding: 0px;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 h-100 formfield relative">
                            <div class="treatment-section-img">
                                <img src="<?php echo $page_image ?>" alt="" class="img-fluid w-100 h100 cover">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 formfield treatment-box text-white">
                            <h4><?php echo $page_title ?></h4>
                            <p><?php echo $page_description ?></p>
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
} else { ?>
<section class="treatment yellowbg" style="padding: 0px;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-12 p-0 p-lg-0 p-md-0">
                        <div class="w-100 formfield treatment-box text-white">
                            <h4>No offer found</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
}

?>

<section class="">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 h-100 formfield relative">
                            <img src="<?php echo USER_IMAGES ?>08.jpg" alt="" class="img-fluid w-100 h100 cover">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 bg-primary formfield text-white form-box ">
                            <h4>Reveal The NU in You:</h4>
                            <p class="mb-md-4">Book your appointment today with our expert dermatologist for a perfect
                                diagnosis & medical advice at SkinQure Clinic. Consultation is available both online and
                                offline.</p>
                            <form action="" class="w-100">
                                <div class="row ">
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="" id="name" placeholder="First Name"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="" id="name" placeholder="Last Name"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="" id="name" placeholder="Contact Number"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="" id="name" placeholder="Email Address"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <div class="w-100 form-group">
                                            <textarea name="" id="" placeholder="Your Message"
                                                class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="w-100 d-flex justify-content-center">
                                            <input type="submit" value="Book Consultation" class="btn text-white">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>


<?php

require_once 'footer.php';

?>