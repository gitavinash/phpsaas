<?php

    require_once 'header.php'

?>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner about_banner_bg">
                    <div class="inner_content">
                        <p><?php echo ucwords($post_title) ?></p>
                        <div class="text-start"><a href="/">Home</a> <span><i
                                    class="fa fa-chevron-right"></i></span><span><?php echo ucwords($post_title) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container-fluid">
        <div class="row justify-content-center section_spacing_top section_spacing_bottom">
            <div class="col-lg-10">
                <div class="row">
                    <div class="section_heading">
                        <h3 class="text-start mb-md-4"><?php echo ucwords($post_title) ?></h3>
                    </div>
                    <div class="youtube-gallery">
                        <?php echo ucwords($post_description) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

    require_once 'footer.php';

?>