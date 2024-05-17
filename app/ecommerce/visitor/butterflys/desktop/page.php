<?php

include_once 'header.php';

?>


<!-- header end -->
<section class="banner-section pro-galery">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="brdcrum">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $page_title ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h2 class="inter-heading cont-hed"><?php echo $page_title ?></h2>
                <div class="account-backend">
                    <p><?php echo $page_description; ?></p>
                </div>
            </div>

        </div>
</section>

<?php

include_once 'footer.php';

?>