<?php

$serviceSection = json_decode(base64_decode($service_section), true);

if ($serviceSection == '') {
    $serviceSection = json_decode($service_section, TRUE);
}

require_once 'header.php';

?>

<section class="section_spacing_top ">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    <iframe width="100%" height="315"
                        src="https://www.youtube.com/embed/<?php echo str_replace('https://www.youtube.com/watch?v=', '', $serviceSection['feature_video']['url']) ?>"
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

                            <div class="container">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="quote-box">
                                            <p><?php echo $serviceSection['quote']['quote'] ?></p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="section_heading">
                                            <h3 class="text-start mb-md-4"><?php echo ucwords($service_title) ?></h3>
                                        </div>
                                        <div class="description abt_p">
                                            <?php echo $service_description ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                <h3 class="text-start mb-md-12 text-center"
                                    style="text-transform: uppercase;color: #0d415f">Treatment options</h3>
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

for ($i = 0; $i < sizeof($serviceSection['treatment_option']); $i++) {

    if ($serviceSection['treatment_option'][$i]['image'] != '' && $serviceSection['treatment_option'][$i]['header'] != '') {

        if ($backGroundColor == 'light') { ?>

<section class="treatment" style="padding: 0px;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 formfield treatment-box text-white">
                            <h4><?php echo $serviceSection['treatment_option'][$i]['header'] ?></h4>
                            <p><img src="<?php echo USER_IMAGES ?>icon/forward.png"
                                    class="treatment-icon"><strong>Indications
                                </strong><?php echo $serviceSection['treatment_option'][$i]['indications'] ?></p>
                            <p><img src="<?php echo USER_IMAGES ?>icon/process.png"
                                    class="treatment-icon"><strong>Process
                                </strong><?php echo $serviceSection['treatment_option'][$i]['process'] ?></p>
                            <p><img src="<?php echo USER_IMAGES ?>icon/clock.png"
                                    class="treatment-icon"><strong>Duration
                                </strong><?php echo $serviceSection['treatment_option'][$i]['duration'] ?></p>
                            <p><img src="<?php echo USER_IMAGES ?>icon/calender.png" class="treatment-icon"><strong>No.
                                    of sittings
                                </strong><?php echo $serviceSection['treatment_option'][$i]['sitting'] ?>
                            </p>
                            <div class="row">
                                <div class="col-6">
                                    <a href="<?php echo $serviceSection['treatment_option'][$i]['bookNow'] ?>"><img
                                            src="<?php echo USER_IMAGES ?>icon/book-now.png" class="treatment-btn"
                                            alt=""></a>
                                </div>
                                <div class="col-6">
                                    <a href="<?php echo $serviceSection['treatment_option'][$i]['watchVideo'] ?>"><img
                                            src="<?php echo USER_IMAGES ?>icon/watch-video.png" class="treatment-btn"
                                            alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 h-100 formfield relative">
                            <div class="treatment-section-img">
                                <img src="<?php echo $serviceSection['treatment_option'][$i]['image'] ?>" alt=""
                                    class="img-fluid w-100 h100 cover">
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
        } else { ?>


<section class="treatment yellowbg" style="padding: 0px;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 h-100 formfield relative">
                            <div class="treatment-section-img">
                                <img src="<?php echo $serviceSection['treatment_option'][$i]['image'] ?>" alt=""
                                    class="img-fluid w-100 h100 cover">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 formfield treatment-box text-white">
                            <h4><?php echo $serviceSection['treatment_option'][$i]['header'] ?></h4>
                            <p><img src="<?php echo USER_IMAGES ?>icon/forward.png"
                                    class="treatment-icon"><strong>Indications
                                </strong><?php echo $serviceSection['treatment_option'][$i]['indications'] ?></p>
                            <p><img src="<?php echo USER_IMAGES ?>icon/process.png"
                                    class="treatment-icon"><strong>Process
                                </strong><?php echo $serviceSection['treatment_option'][$i]['process'] ?></p>
                            <p><img src="<?php echo USER_IMAGES ?>icon/clock.png"
                                    class="treatment-icon"><strong>Duration
                                </strong><?php echo $serviceSection['treatment_option'][$i]['duration'] ?></p>
                            <p><img src="<?php echo USER_IMAGES ?>icon/calender.png" class="treatment-icon"><strong>No.
                                    of sittings
                                </strong><?php echo $serviceSection['treatment_option'][$i]['sitting'] ?>
                            </p>
                            <div class="row">
                                <div class="col-6">
                                    <a href="<?php echo $serviceSection['treatment_option'][$i]['bookNow'] ?>"><img
                                            src="<?php echo USER_IMAGES ?>icon/book-now.png" class="treatment-btn"
                                            alt=""></a>
                                </div>
                                <div class="col-6">
                                    <a href="<?php echo $serviceSection['treatment_option'][$i]['watchVideo'] ?>"><img
                                            src="<?php echo USER_IMAGES ?>icon/watch-video.png" class="treatment-btn"
                                            alt=""></a>
                                </div>
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

if (isset($serviceSection['before_after'])) { ?>

<section class="bluebg beforeAfterSection section_spacing_top sectionpadding">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <div class="row">

                    <div class="col-md-4 position-relative">
                        <div class="section_heading">
                            <h3 class="text-start"><?php echo $serviceSection['before_after']['header'] ?></h3>
                            <h3 class="text-start"><?php echo $serviceSection['before_after']['subHeader'] ?></h3>
                        </div>

                        <p class="separate_quote">
                            <img src="<?php echo USER_IMAGES ?>left-quote.png">
                            <?php echo $serviceSection['before_after']['description'] ?>
                        </p>
                        <a href="<?php echo $serviceSection['before_after']['videoLink'] ?>" target="_blank"
                            class="btn btn_quote"><i class="fa fa-play-circle"></i> Watch Full Video</a>
                    </div>

                    <div class="col-md-8 col-12">
                        <div class="slider beforeAfterSlider">

                            <?php

                                if ($service_gallery != '') {

                                    $serviceGalleryList = json_decode($service_gallery, TRUE);

                                    $oldGalleryImages = array();

                                    for ($i = 0; $i < sizeof($serviceGalleryList); $i++) {

                                        echo "<div><img src='{$serviceGalleryList[$i]['path']}'></div>";
                                    }
                                }

                                ?>

                        </div>
                        <div class="slider beforeAfterSliderNav">
                            <?php

                                if ($service_gallery != '') {

                                    $serviceGalleryList = json_decode($service_gallery, TRUE);

                                    $oldGalleryImages = array();

                                    for ($i = 0; $i < sizeof($serviceGalleryList); $i++) {

                                        echo "<div class='slick-navigation' ><img src='{$serviceGalleryList[$i]['path']}'></div>";
                                    }
                                }

                                ?>
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

<?php

if (isset($serviceSection['why_choose_us'])) { ?>


<section class="section_spacing_top">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <div class="row">
                    <div class="col-lg-6 col-md-6 p-lg-0 p-md-0">
                        <div class="w-100 second_img service_first relative">
                            <img src="<?php echo $serviceSection['why_choose_us']['image'] ?>" alt=""
                                class="img-fluid ">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 p-lg-0 p-md-0">
                        <div class="w-100 secondaboutpage ">
                            <div class="section_heading">
                                <p class="text-start"><?php echo $serviceSection['why_choose_us']['header'] ?></p>
                                <h3 class="text-start mb-md-5">
                                    <?php echo $serviceSection['why_choose_us']['description'] ?>
                                </h3>
                            </div>
                            <?php

                                for ($i = 0; $i < sizeof($serviceSection['why_choose_us']['steps']); $i++) { ?>

                            <div class="d-flex w-100 align-items-center mb-5">
                                <div class="icon me-2">
                                    <img src="<?php echo $serviceSection['why_choose_us']['steps'][$i]['icon'] ?>"
                                        alt="" class="img-fluid">
                                </div>
                                <div class="text text-start">
                                    <h5><?php echo $serviceSection['why_choose_us']['steps'][$i]['description'] ?></h5>
                                </div>
                            </div>

                            <?php
                                }

                                ?>
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