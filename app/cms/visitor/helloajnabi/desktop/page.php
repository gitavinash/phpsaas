<?php

    include_once 'header.php';

?>

<div class="container">
            <div class="about__text">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="breadcrumb__text">
                            <h2><?php echo $page_title ?></h2>
                            <div class="breadcrumb__option">
                                <a href="/">Home</a>
                                <span><?php echo $page_title ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
						<img src="<?php echo $page_image ?>" alt="">
                    </div>
                    <div class="col-lg-6">
                        <div class="about__right__text">
                            <h2><?php echo $page_title ?></h2>
							<p><?php echo $page_description ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php

    include_once 'footer.php';

?>