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

                        <a href="/media/" class="btn green-btn animated slideInUp">Featured Articles</a>

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

<?php

require_once 'footer.php';

?>