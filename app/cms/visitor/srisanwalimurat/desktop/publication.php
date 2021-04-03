<?php

    include_once 'header.php';

?>


<section>
    <div class="gallery">
        <h1>Publications</h1>

        <?php
        
        if($bookList=mysqli_query($connection, "SELECT link_url, product_title, product_image FROM ".TABLE_PREFIX."product INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=product_id WHERE link_type='product' AND product_type='book' AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_published DESC")) {

            if(mysqli_num_rows($bookList)!=0) {

                while($bookListData=mysqli_fetch_assoc($bookList)) {

                    extract($bookListData);

                    echo "<div class='img-gallery-magnific'>
                        <div class='magnific-img cur-pointer'>
                            <img src='{$product_image}' alt='{$product_title}' />
                            <p>{$product_title}</p>
                        </div>
                    </div>";

                }

            } else {

                echo 'No book found';

            }

        }

        ?>

    </div>
</section>

<?php

    include_once 'footer.php';

?>