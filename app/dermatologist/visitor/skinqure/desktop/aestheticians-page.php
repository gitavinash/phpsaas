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
    <div class="container">


        <?php

        if ($checkTeams = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "page WHERE page_type='team' AND page_status=1 AND page_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($checkTeams) != 0) {

                while ($teamsData = mysqli_fetch_assoc($checkTeams)) {

                    extract($teamsData);

                    echo "<div class='row section_spacing_bottom'>
                    <div class='col-12 col-md-4'>
                        <img src='{$page_image}' alt='' style='width: 100%' >
                    </div>
                    <div class='col-12 col-md-8'>
                        <div class='w-100 formfield text-white form-box-non-bg'>
                            <div class='section_heading'>
                                <h3 class='text-start'>{$page_title}</h3>
                            </div>
                            <p class='abt_p'>{$page_description}</p>
                        </div>
                    </div>
                </div>";
                }
            }
        }

        ?>


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