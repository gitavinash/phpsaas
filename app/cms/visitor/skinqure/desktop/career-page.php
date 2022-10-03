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
                            <h3 class="text-center"><?php echo $page_title ?></h3>
                        </div>
                    </div>
                    <div class="col-md-12 mb-lg-5 text-justify">
                        <?php echo $page_description ?>
                    </div>
                    <a href="#" target="_blank" class="btn btn_quote">Apply Now</a>
                </div>

            </div>
        </div>
    </div>

</section>

<?php

    require_once 'footer.php';

?>