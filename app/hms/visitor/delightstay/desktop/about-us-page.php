<?php

    require_once 'header.php';

?>
<main>
    <section class="about_wrapper">
        <div class="container">
            <div class="about_wrapper__heading">
                <h3>Confident travel starts at <span>DELIGHT STAY.</span></h3>
            </div>
        </div>
    </section>

    <section class="since_wrapper">
        <div class="container">
            <div class="row since_flex">
                <div class="col-lg-7">
                    <div class="since_wrapper__left">
                        <?php echo $page_description ?>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="since_wrapper__right">
                        <img src="<?php echo USER_IMAGES ?>aboutright.jpeg" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- what we do -->
    <section class="do_wrapper">
        <div class="container">
            <div class="do_wrapper__heading">
                <h3>Our Vision</h3>
                <p>OUR VISION IS TO ESTABLISH A REPUTATION OF OFFERING INNOVATION AND CREATIVE BUSINESS SOLUTIONS BASED
                    ON THE NEEDS OF OUR CLIENTS. ADDITIONALLY TO BE KNOWN AS A VALUE FOR MONEY SERVICE PROVIDER AND A
                    LEADER IN THE BUSINESS VERTICALS SERVICED BY US.</p>
            </div>
        </div>
    </section>




    <!-- tesimonial -->
    <section class="ourTeam">
        <div class="container">
            <div class="row">
                <div class="main--heading">
                    <h3>Some numbers</h3>
                </div>
            </div>
            <div class="ourTeam_content">
                <div class="row">
                    <div class="col-lg-4 col-6">
                        <div class="team_box">
                            <div class="team_box__img">
                                <img src="<?php echo USER_IMAGES ?>happy-clients.png" class="img-fluid" alt="">
                            </div>
                            <div class="team_box__title">
                                <h3>10000+</h3>
                                <h6>Happy clients</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6">
                        <div class="team_box">
                            <div class="team_box__img">
                                <img src="<?php echo USER_IMAGES ?>hotels.png" class="img-fluid" alt="">
                            </div>
                            <div class="team_box__title">
                                <h3>15+</h3>
                                <h6>Hotel associated</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="team_box office_box">
                            <div class="team_box__img">
                                <img src="<?php echo USER_IMAGES ?>locations.png" class="img-fluid" alt="">
                            </div>
                            <div class="team_box__title">
                                <h3>10+</h3>
                                <h6>Locations</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
</main>
<?php

    require_once 'footer.php';

?>