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
                            <h3 class="text-center">About Dr Jangid</h3>
                            <!-- <p class="text-center">Ex-Sr. Dermatologist AIIMS, New Delhi,</p> -->
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="w-100">
                            <img src="<?php echo USER_IMAGES ?>dr_jangid.png" alt="" class="img-fluid w-100">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="w-100 p-lg-4 p-md-4 mt-lg-3">
                            <p class="abt_p">
                                Dr. Jangid swears by his knowledge of the “3D’s of Hair Transplant”, which emphasizes
                                three important cornerstones to this procedure. It includes the design of the hairline,
                                the Direction of the transplanted hair follicles, and the Density of the transplanted
                                hair for optimal coverage. With just a few years of expertise in the field of hair
                                transplant and restoration, Dr. Jangid has managed to establish himself as a leading
                                name, relinquishing SkinQure as a renowned clinic for every hair restoration procedure.
                            </p>


                            <div class="accordion" id="faqAccordion">


                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?php echo $i ?>">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse<?php echo $i ?>" aria-expanded="true"
                                            aria-controls="collapse<?php echo $i ?>">
                                            <?php echo $serviceSection['faq'][$i]['question'] ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo $i ?>" class="accordion-collapse collapse show"
                                        aria-labelledby="heading<?php echo $i ?>" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <?php echo $serviceSection['faq'][$i]['answer'] ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?php echo $i ?>">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $i ?>"
                                            aria-expanded="false" aria-controls="collapse<?php echo $i ?>">
                                            <?php echo $serviceSection['faq'][$i]['question'] ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo $i ?>" class="accordion-collapse collapse"
                                        aria-labelledby="heading<?php echo $i ?>" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <?php echo $serviceSection['faq'][$i]['answer'] ?>
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

    require_once 'footer.php';

?>