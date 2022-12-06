<?php

require_once 'header.php';

?>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner"
                    style="background-image: url(<?php echo $service_image ?>); background-size: cover; background-position: 100% 100%">
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
<?php

if (isset($pageSection['why_choose_us'])) { ?>


<section class="section_spacing_top">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <div class="row">
                    <div class="col-lg-6 col-md-6 p-lg-0 p-md-0">
                        <div class="w-100 second_img service_first relative">
                            <img src="<?php echo $pageSection['why_choose_us']['image'] ?>" alt="" class="img-fluid ">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 p-lg-0 p-md-0">
                        <div class="w-100 secondaboutpage ">
                            <div class="section_heading">
                                <p class="text-start"><?php echo $pageSection['why_choose_us']['header'] ?></p>
                                <h3 class="text-start mb-md-5">
                                    <?php echo $pageSection['why_choose_us']['description'] ?>
                                </h3>
                            </div>
                            <?php

                                for ($i = 0; $i < sizeof($pageSection['why_choose_us']['steps']); $i++) { ?>

                            <div class="d-flex w-100 align-items-center mb-5">
                                <div class="icon me-2">
                                    <img src="<?php echo $pageSection['why_choose_us']['steps'][$i]['icon'] ?>" alt=""
                                        class="img-fluid">
                                </div>
                                <div class="text text-start">
                                    <h5><?php echo $pageSection['why_choose_us']['steps'][$i]['description'] ?></h5>
                                </div>
                            </div>

                            <?php
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
}

require_once 'footer.php';

?>