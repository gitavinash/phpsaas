<?php

    include_once 'header.php';

?>


<section>
    <div class="gallery">
        <h1>Publications</h1>

        <?php

        
        if($bookList=mysqli_query($connection, "SELECT link_url, product_id, product_title, product_image, inventory_price FROM ".TABLE_PREFIX."product INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON product_id=inventory_relation_id WHERE link_type='product' AND product_type='book' AND link_status=200 AND link_site_id=".SITE_ID." AND inventory_site_id=".SITE_ID." ORDER BY link_published DESC")) {

            if(mysqli_num_rows($bookList)!=0) {

                while($bookListData=mysqli_fetch_assoc($bookList)) {

                    extract($bookListData);

                    echo "<div class='img-gallery-magnific'>
                        <div class='magnific-img cur-pointer'>
                            <img src='{$product_image}' alt='{$product_title}' />
                            <p style='padding-bottom: 0px !important' >{$product_title}</p>";

                    if($productMeta=mysqli_query($connection, "SELECT product_meta_key, product_meta_value FROM ".TABLE_PREFIX."product_meta WHERE product_meta_site_id=".SITE_ID." AND product_meta_relation_id={$product_id}")) {

                        if(mysqli_num_rows($productMeta)!=0) {

                            while($productMetaData=mysqli_fetch_assoc($productMeta)) {

                                extract($productMetaData);

                                echo "<p style='padding-bottom: 0px !important' ><strong>{$product_meta_key}</strong> {$product_meta_value}</p>";

                            }

                        }

                    } else {

                        echo mysqli_error($connection);

                    }

                    echo "
                        <p><strong>Price</strong> {$inventory_price}</p>
                        </div>
                    </div>";

                }

            } else {

                echo 'No book found';

            }

        } else {

            echo mysqli_error($connection);

        }

        ?>

    </div>
</section>

<?php

    include_once 'footer.php';

?>