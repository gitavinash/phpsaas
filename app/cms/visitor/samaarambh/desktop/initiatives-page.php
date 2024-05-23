<?php

require_once 'header.php';

?>

<!--Page Header Start-->
<section class="page-header">
    <!-- background-image: url('path_of_image') of event/archivment -->
    <div class="page-header-bg" style="background-image: url(<?php echo $page_image ?>)">
    </div>
    <div class="container">
        <div class="page-header__inner">
            <h2>Our-initiatives</h2>
        </div>
    </div>
</section>
<!--Page Header End-->

<!--Events Page Start-->
<section class="events-page">
    <div class="container">
        <div class="row">
            <?php

            if ($categoryList = mysqli_query($connection, "SELECT category_image, category_title, link_url FROM " . TABLE_PREFIX . "category INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=category_id WHERE category_type='service' AND link_status=200 AND link_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($categoryList) != 0) {
                    while ($categoryData = mysqli_fetch_assoc($categoryList)) {
                        extract($categoryData);
                        echo "
                            <div class='col-xl-4 col-lg-6 col-md-6'>
                <div class='events-one__single'>
                    <div class='events-one__img'>
                        <img src='{$category_image}' alt='{$category_title}'>
            <div class='events-one__content'>
                <h3 class='events-one__title'><a href='{$link_url}'>{$category_title}</a></h3>
            </div>
        </div>
    </div>
    </div>
    ";
                    }
                }
            }

            ?>
        </div>
    </div>
</section>
<!--Events Page End-->
<?php

require_once 'footer.php';

?>