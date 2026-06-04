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
                        <p>Career</p>
                        <div class="text-start"><a href="/">Home</a> <span><i
                                    class="fa fa-chevron-right"></i></span><span>Career</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sectionpadding">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <div class="row">

                    <div class="col-md-12 text-center">
                        <div class="section_heading mb-md-4">
                            <h3 class="text-center">Open Positions</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<section class="section_spacing_bottom">
    <div class="container">
        <div class="row">

            <?php

            $careerList = mysqli_query($connection, "SELECT page_title, page_section FROM " . TABLE_PREFIX . "page WHERE page_type='career' AND page_site_id=" . SITE_ID);

            if (mysqli_num_rows($careerList) != 0) {

                while ($careerData = mysqli_fetch_assoc($careerList)) {

                    extract($careerData);

                    $pageSection = json_decode(base64_decode($page_section), true);

            ?>

            <div class="col-12 col-md-4">
                <div class="career-box">
                    <div class="w-100 formfield text-white">
                        <div class="section_heading">
                            <h3 class="text-start"><?php echo $page_title ?></h3>
                            <p><?php echo $pageSection['career']['location'] ?></p>
                        </div>
                        <a href="<?php echo $pageSection['career']['job_link'] ?>" target="_blank"
                            class="btn btn_quote">Apply Now</a>
                    </div>
                </div>
            </div>

            <?php
                }
            } else { ?>

            <div class="row section_spacing_bottom">
                <div class="col-12">
                    <div class="w-100 formfield text-white">
                        <div class="section_heading">
                            <h3 class="text-start">No vacancy available.</h3>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            }

            ?>

        </div>
    </div>
</section>

<?php

require_once 'footer.php';

?>