<?php

require_once 'header.php'

?>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner about_banner_bg">
                    <div class="inner_content">
                        <p><?php echo ucwords($page_title) ?></p>
                        <div class="text-start"><a href="/">Home</a> <span><i
                                    class="fa fa-chevron-right"></i></span><span><?php echo ucwords($page_title) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section_spacing_top section_spacing_bottom">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">

                <div class="row">
                    <div class="col-md-12 text-center mb-lg-5">
                        <div class="section_heading">
                            <h3 class="text-center">About <?php echo ucwords($page_title) ?></h3>
                            <!-- <p class="text-center">Ex-Sr. Dermatologist AIIMS, New Delhi,</p> -->
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="w-100">
                            <img src="<?php echo $page_image ?>" alt="" class="img-fluid w-100">
                        </div>

                        <ul class="about-us-social-media-icon">
                            <li><a href="https://www.facebook.com/drjangid/" target="_blank"><i
                                        class="fa fa-facebook-square" aria-hidden="true"></i></a></li>
                            <li><a href="https://www.instagram.com/doc.jangid/" target="_blank"><i
                                        class="fa fa-instagram" aria-hidden="true"></i></a></li>
                            <li><a href="https://www.youtube.com/@skinqure/" target="_blank"><i
                                        class="fa fa-youtube-play" aria-hidden="true"></i></a></li>
                            <li><a href="https://twitter.com/drjangid/" target="_blank"><i class="fa fa-twitter-square"
                                        aria-hidden="true"></i></a></li>
                            <li><a href="https://linkedin.com/in/dr-jangid-dermatologist-39835269/" target="_blank"><i
                                        class="fa fa-linkedin-square" aria-hidden="true"></i></a></li>
                        </ul>

                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="w-100 p-lg-4 p-md-4 mt-lg-3">
                            <p class="abt_p">
                                <?php echo $page_description ?>
                            </p>


                            <?php

                            if (isset($pageSection['faq'])) {

                                echo '<div class="accordion" id="faqAccordion">';

                                for ($i = 0; $i < sizeof($pageSection['faq']); $i++) {

                                    if ($pageSection['faq'][$i]['question'] != '') { ?>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?php echo $i ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?php echo $i ?>" aria-expanded="false"
                                        aria-controls="collapse<?php echo $i ?>">
                                        <?php echo $pageSection['faq'][$i]['question'] ?>
                                    </button>
                                </h2>
                                <div id="collapse<?php echo $i ?>" class="accordion-collapse collapse"
                                    aria-labelledby="heading<?php echo $i ?>" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <?php echo $pageSection['faq'][$i]['answer'] ?>
                                    </div>
                                </div>
                            </div>

                            <?php
                                    }
                                }

                                echo "</div>";
                            }



                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>


<section>
    <div class="container-fluid">
        <div class="row">

            <?php

            if ($artciles = mysqli_query($connection, "SELECT page_title, page_image, page_section FROM " . TABLE_PREFIX . "page INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = page_id WHERE link_site_id =" . SITE_ID . " AND link_type='page' AND page_type='article' AND page_site_id=" . SITE_ID . " ORDER BY link_modified DESC LIMIT 0, 4")) {

                if (mysqli_num_rows($artciles) != 0) {

                    while ($artcilesData = mysqli_fetch_assoc($artciles)) {

                        extract($artcilesData);

                        echo "<div class='col-12 col-md-3'>
                        <div class='galleryBox'>
                            <a href='{$page_section}'>
                                <img src='{$page_image}' alt='Example Image' />
                            </a>
                            <a href='{$page_section}' target='_blank' class='galleryBoxBtn btn btn_quote'>Read Article</a>
                        </div>
                    </div>";
                    }
                }
            }


            ?>

        </div>
        <div class="row section_spacing_bottom">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <a href="/media/" class="btn green-btn animated slideInUp">More Articles</a>
                </div>
            </div>
        </div>
</section>


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
                        <div class="w-100 reveal-form bg-primary formfield text-white form-box ">
                            <h4>Reveal The NU in You:</h4>
                            <p class="mb-md-4">Book your appointment today with our expert dermatologist for a perfect
                                diagnosis & medical advice at SkinQure Clinic. Consultation is available both online and
                                offline.</p>
                            <form action="" class="w-100">
                                <div class="row ">
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="first_name" id="name" placeholder="First Name"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="last_name" id="name" placeholder="Last Name"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="mobile" id="name" placeholder="Contact Number"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="email" id="name" placeholder="Email Address"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <div class="w-100 form-group">
                                            <textarea name="message" id="" placeholder="Your Message"
                                                class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="w-100 d-flex justify-content-center">
                                            <input type="hidden" name="type" value="enquiry">
                                            <input type="hidden" name="action" value="new">
                                            <input type="hidden" name="subject"
                                                value="Reveal The NU in You: Enquiry Form">
                                            <input type="hidden" name="sender"
                                                value="<?php echo base64_encode("skinqure.saket@gmail.com") ?>">
                                            <input type="hidden" name="cc"
                                                value="<?php echo base64_encode("kushagra.catalyst@gmail.com,info@skinqure.in,care@skinqure.in") ?>">
                                            <input type="hidden" name="redirect_url"
                                                value="/thank-you/?msg=Thank+you+for+your+query%2Ffeedback.+It+is+important+for+us.">
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