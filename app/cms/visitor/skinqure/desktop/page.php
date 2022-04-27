<?php

    require_once 'header.php'

?>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner contact_banner_bg">
                    <div class="inner_content">
                        <p><?php echo ucwords($page_title) ?></p>
                        <div class="text-start"><a href="index.html">Home</a> <span><i
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
            <div class="col-lg-10 text-center">

                <div class="row">
                    <div class="col-md-12 text-center mb-lg-5">
                        <div class="section_heading">
                            <p class="text-center">About Our Clinic</p>
                            <h3 class="text-center">The more gentle skin care</h3>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="w-100">
                            <img src="<?php echo USER_IMAGES ?>08.jpg" alt="" class="img-fluid w-100">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="w-100 p-lg-4 p-md-4 mt-lg-3">
                            <p class="abt_p">
                                Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis
                                nostrud exrcitation ullamco laboris nisi ut aliquip ex ea commodo consequat
                                auteirure dolor in reprehen derit in volup tate velit esse cillum dolore eu
                                fugiat nulla pariatur.
                            </p>
                            <div class="relative quotebox w-100">
                                <div class="quote">
                                    <i class="fa fa-quote-left" aria-hidden="true"></i>
                                </div>
                                <p class="abt_p">
                                    Since 1991, I have improved the knowledge received at the university and
                                    increased my qualification level.
                                    Watch Our Video
                                </p>
                            </div>
                            <a href="" class="btn btn_quote"><i class="fa fa-play-circle"></i> Watch Full Video</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
<section class="section_spacing_top">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <div class="row justify-content-center">
                    <div class="col-12 mb-md-4">
                        <div class="section_heading">
                            <p class="text-center">Dermatologists</p>
                            <h3 class="text-center">Our Dermatologists Team</h3>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-6 mb-4">
                        <div class="w-100 relative   team_box">
                            <figure class="mb-0">
                                <img src="<?php echo USER_IMAGES ?>Team-01.jpg" alt="" class="img-fluid w-100">
                            </figure>
                            <div class="written">
                                <h4>
                                    Skin Doctor
                                </h4>
                                <h5>
                                    Doctor Reyan
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-6 mb-4">
                        <div class="w-100 relative   team_box">
                            <figure class="mb-0">
                                <img src="<?php echo USER_IMAGES ?>Team-02.jpg" alt="" class="img-fluid w-100">
                            </figure>
                            <div class="written">
                                <h4>
                                    Skin Doctor
                                </h4>
                                <h5>
                                    Doctor Reyan
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-6 mb-4">
                        <div class="w-100 relative   team_box">
                            <figure class="mb-0">
                                <img src="<?php echo USER_IMAGES ?>Team-03.jpg" alt="" class="img-fluid w-100">
                            </figure>
                            <div class="written">
                                <h4>
                                    Skin Doctor
                                </h4>
                                <h5>
                                    Doctor Reyan
                                </h5>
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