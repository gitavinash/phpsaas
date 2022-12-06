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


<section>
    <div class="container-fluid">
        <div class="row">

            <?php

            if ($artciles = mysqli_query($connection, "SELECT page_title, page_image, page_section FROM " . TABLE_PREFIX . "page INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = page_id WHERE link_site_id =" . SITE_ID . " AND link_type='page' AND page_type='article' AND page_site_id=" . SITE_ID . " ORDER BY link_modified DESC")) {

                if (mysqli_num_rows($artciles) != 0) {

                    while ($artcilesData = mysqli_fetch_assoc($artciles)) {

                        extract($artcilesData);

                        echo "<div class='col-12 col-md-4'>
                        <div class='galleryBox'>
                            <a href='{$page_section}'>
                                <img src='{$page_image}' alt='Example Image' />
                            </a>
                            <a href='{$page_section}' target='_blank' class='galleryBoxBtn btn btn_quote'>Read Article</a>
                        </div>
                    </div>";
                    }
                }
            }


            ?>

        </div>
    </div>
</section>

<?php

require_once 'footer.php';

?>