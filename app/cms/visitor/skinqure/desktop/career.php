<?php

    require_once 'header.php'

?>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner about_banner_bg">
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

<section class="section_spacing_top section_spacing_bottom">
    <div class="container">

        <?php
        
            $careerList = mysqli_query($connection, "SELECT page_title, link_url FROM ".TABLE_PREFIX."page INNER JOIN ".TABLE_PREFIX."link on link_relation_id=page_id WHERE link_type='page' AND page_type='career' AND link_status=200 AND link_site_id=".SITE_ID);

            if(mysqli_num_rows($careerList)!=0) {

                while($careerData = mysqli_fetch_assoc($careerList)) {

                    extract($careerData); ?>

        <div class="row section_spacing_bottom">
            <div class="col-12">
                <div class="w-100 formfield text-white form-box-non-bg">
                    <div class="section_heading">
                        <h3 class="text-start"><?php echo $page_title ?></h3>
                    </div>
                    <a href="<?php echo $link_url ?>" target="_blank" class="btn btn_quote">View Now</a>
                </div>
            </div>
        </div>

        <?php
        }

        } else { ?>

        <div class="row section_spacing_bottom">
            <div class="col-12">
                <div class="w-100 formfield text-white form-box-non-bg">
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
</section>

<?php

    require_once 'footer.php';

?>