<?php

    include_once 'header.php';

?>


    <!-- Slider -->
    <div class="mbs-slider-section">
        <div class="mbs-profile-info">
            <div class="d-flex align-items-center w-100">
                <div class="mx-auto mbs-profile-inner">
                    <h2>Makeup by Shiivi<span>Makeup Artist</span></h2> </div>
            </div>
        </div>
        <div class="owl-carousel mbs-main-slider">
            <div class="slide-item" style="background-image: url(<?php echo USER_IMAGES ?>shiivi-bg-2.jpg);"></div>
        </div>
    </div>
    <!-- About Me -->
    <section class="mbs-about-section-home mbs-about-inner-page bg-black">
        <div class="container">
            <div class="row mbs-about-inner">
                <div class="col-md-8 offset-md-2 text-center">
                    <div class="mbs-about-info">
                        <h1>About Me</h1>
                        <h3>Hi, I'm Shivani Gupta a Professional Makeup Artist</h3>
                        <p>Hey, I am Shivani, a professional makeup artist possessing dedicated passion towards makeup and art. I have always been fascinated by the tempting cosmetics and their implementations that do make one look confident, fresh and most importantly, flawless. </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Services -->
    <section class="mbs-homepage-services-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center mb-40">
                    <h2>Services</h2>
                    <p>Here are a few selections from my portfolio. Please view my expanded portfolio further below. Nam rhoncus euismo.</p>
                </div>
            </div>
            <div class="row mbs-portfolio-items">
                <?php
                
                    /** Service list */

                    if($serviceList=mysqli_query($connection, "SELECT service_image, service_title, link_url FROM ".TABLE_PREFIX."service INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=service_id WHERE link_site_id=".SITE_ID)) {

                        if(mysqli_num_rows($serviceList)!=0) {

                            while($serviceData=mysqli_fetch_assoc($serviceList)) {

                                extract($serviceData); ?>

                                    <div class="col-md-6 single-item">
                                        <div class="mbs-portfolio-wrap">
                                            <a href="<?php echo $link_url ?>"><img src="<?php echo $service_image ?>" alt=""></a>
                                            <div class="mbs-portfolio-content">
                                                <!-- <p>Vestibulume</p> -->
                                                <h3><a href="<?php echo $link_url ?>"><?php echo $service_title ?></a></h3>
                                                <a href="<?php echo $link_url ?>" class="mbs-portfolio-link"></a>
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
    </section>

<?php

    include_once 'footer.php';

?>