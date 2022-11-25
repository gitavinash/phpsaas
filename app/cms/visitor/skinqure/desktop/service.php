<?php

$serviceSection = json_decode(base64_decode($service_section), true);

if ($serviceSection == '') {
    $serviceSection = json_decode($service_section, TRUE);
}


require_once 'header.php';


?>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner"
                    style="background-image: url(<?php echo $service_image ?>); background-size: cover; background-position: 100% 100%">
                    <div class="inner_content">
                        <p>
                            <?php echo ucwords($service_title) ?></p>
                        <div class="text-start"><a href="/">Home</a> <span><i
                                    class="fa fa-chevron-right"></i></span><span>Service</span>
                            <span><i
                                    class="fa fa-chevron-right"></i></span><span><?php echo ucwords($service_title) ?></span>
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

                            <div class="container">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="quote-box">
                                            <p><?php echo stripslashes($serviceSection['quote']['quote']) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="section_heading">
                                            <h3 class="text-start mb-md-4">
                                                <?php echo ucwords(stripslashes($service_title)) ?></h3>
                                        </div>
                                        <div class="description abt_p">
                                            <?php echo stripslashes($service_description) ?>
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


<?php

if (isset($serviceSection['faq'])) { ?>


<section class="sectionpadding">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <div class="row">

                    <div class="col-md-12 text-center">
                        <div class="section_heading mb-md-4">
                            <h3 class="text-center">Frequently Asked Questions ( FAQs )</h3>
                        </div>
                    </div>
                    <div class="accordion" id="faqAccordion">

                        <?php

                            for ($i = 0; $i < sizeof($serviceSection['faq']); $i++) {

                                if ($serviceSection['faq'][$i]['question'] != '') {
                                    if ($i === 0) { ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $i ?>">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?php echo $i ?>" aria-expanded="true"
                                    aria-controls="collapse<?php echo $i ?>">
                                    <?php echo stripslashes($serviceSection['faq'][$i]['question']) ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $i ?>" class="accordion-collapse collapse show"
                                aria-labelledby="heading<?php echo $i ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <?php echo stripslashes(str_replace('\r\n', '<br />', $serviceSection['faq'][$i]['answer'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php
                                    } else { ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $i ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?php echo $i ?>" aria-expanded="false"
                                    aria-controls="collapse<?php echo $i ?>">
                                    <?php echo stripslashes($serviceSection['faq'][$i]['question']) ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $i ?>" class="accordion-collapse collapse"
                                aria-labelledby="heading<?php echo $i ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <?php echo stripslashes(str_replace('\r\n', '<br />', $serviceSection['faq'][$i]['answer'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php
                                    }
                                }
                            }

                            ?>

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

if (isset($serviceSection['treatment_steps'])) { ?>

<section class="section_spacing_top section_spacing_bottom">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="section_heading mb-md-4">
                            <p class="text-center">
                                <?php echo stripslashes($serviceSection['treatment_steps']['header']) ?></p>
                            <h3 class="text-center">
                                <?php echo stripslashes($serviceSection['treatment_steps']['description']) ?></h3>
                        </div>
                    </div>
                    <?php

                        for ($i = 0; $i < sizeof($serviceSection['treatment_steps']['steps']); $i++) { ?>

                    <div class="col-lg-4 col-md-4 mb-lg-0 mb-md-0 mb-4">
                        <div class="w-100 relative service_box">
                            <figure class="m-0">
                                <img src="<?php echo $serviceSection['treatment_steps']['steps'][$i]['image'] ?>" alt=""
                                    class="img-fluid w-100">
                            </figure>
                            <div class="service-title">
                                <h3><?php echo stripslashes($serviceSection['treatment_steps']['steps'][$i]['header']) ?>
                                </h3>
                            </div>
                            <div class="written_text p-3">
                                <p class="m-0">
                                    <?php echo stripslashes($serviceSection['treatment_steps']['steps'][$i]['description']) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php
                        }

                        ?>
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
                                <p class="text-start">
                                    <?php echo stripslashes($serviceSection['why_choose_us']['header']) ?></p>
                                <h3 class="text-start mb-md-5">
                                    <?php echo stripslashes($serviceSection['why_choose_us']['description']) ?>
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
                                    <h5><?php echo stripslashes($serviceSection['why_choose_us']['steps'][$i]['description']) ?>
                                    </h5>
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

<?php

if (isset($serviceSection['before_after'])) { ?>


<section class="greenbg  section_spacing_top sectionpadding">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <div class="row">

                    <div class="col-md-4 position-relative">
                        <div class="section_heading">
                            <h3 class="text-start"><?php echo stripslashes($serviceSection['before_after']['header']) ?>
                            </h3>
                            <h3 class="text-start">
                                <?php echo stripslashes($serviceSection['before_after']['subHeader']) ?></h3>
                        </div>

                        <p class="separate_quote">
                            <img src="<?php echo USER_IMAGES ?>left-quote.png">
                            <?php echo stripslashes($serviceSection['before_after']['description']) ?>
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

if (isset($serviceSection['watch_video'])) { ?>

<section class="sectionpadding youtube_testimonial"
    style="background: linear-gradient(rgba(0,0,0,0.5),rgba(0,0,0,0.5)),url(<?php echo $serviceSection['watch_video']['image'] ?>); background-size: cover; background-position: 100% 100%">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <div class="w-100 video-box relative">
                    <div class="section_heading">
                        <p class="text-center text-white"><?php echo $serviceSection['watch_video']['header'] ?></p>
                        <h3 class="text-center mb-md-4 text-white">
                            <?php echo stripslashes($serviceSection['watch_video']['description']) ?></h3>
                    </div>
                    <a class="btn youtube youtubbtn" target="_blank"
                        data-youtube="<?php echo $serviceSection['watch_video']['videoLink'] ?>"
                        href="<?php echo $serviceSection['watch_video']['videoLink'] ?>">
                        <i class="md hydrated fa fa-play"> </i>
                    </a>

                </div>
            </div>
        </div>
    </div>
</section>

<div class="video-modal d-flex justify-content-center align-items-center d-none v-none" id="video-modal">
    <div class="video-body relative p-md-4 p-1">
        <div class="close-video">X</div>
        <div class="youtub-link w-100 h100">
            <iframe src="<?php echo $serviceSection['watch_video']['videoLink'] ?>" width="100%" height="100%"></iframe>
        </div>
    </div>
</div>

<?php
}

?>

<!-- Contact form -->

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

if (isset($serviceSection['our_commitment'])) { ?>

<section class="section_spacing_top ">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <div class="row">
                    <div class="col-md-6">
                        <div class="w-100 h100 p-md-3">
                            <div class="section_heading">
                                <p class="text-start"><?php echo $serviceSection['our_commitment']['header'] ?></p>
                                <h3 class="text-start mb-md-4">
                                    <?php echo $serviceSection['our_commitment']['subHeader'] ?></h3>
                            </div>
                            <p class="text-start servicep"><?php echo $serviceSection['our_commitment']['descripton'] ?>
                            </p>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="w-100 h100">
                            <img src="<?php echo $serviceSection['our_commitment']['image'] ?>" alt=""
                                class="img-fluid w-100 h100 cover">
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

require_once 'footer.php';

?>