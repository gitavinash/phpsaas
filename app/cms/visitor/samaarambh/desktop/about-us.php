<?php

require_once 'header.php';

?>

<!--Page Header Start-->
<section class="page-header">
    <div class="page-header-bg" style="background-image: url(<?php echo $page_image ?>)">
    </div>
    <div class="container">
        <div class="page-header__inner">
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="index.html">Home</a></li>
                <li><span>/</span></li>
                <li class="active"><?php echo $page_title ?></li>
            </ul>
            <h2><?php echo $page_title ?></h2>
        </div>
    </div>
</section>
<!--Page Header End-->

<?php echo $page_description ?>

<?php

require_once 'footer.php';

?>