<?php

    include_once 'header.php';

?>


<section>
    <div class="gallery">
        <h1>Publications</h1>

        <?php
        
        if($bookList=mysqli_query($connection, "SELECT link_url, book_title, book_image FROM ".TABLE_PREFIX."book INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=book_id WHERE link_type='book' AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_published DESC")) {

            if(mysqli_num_rows($bookList)!=0) {

                while($bookListData=mysqli_fetch_assoc($bookList)) {

                    extract($bookListData);

                    echo "<div class='img-gallery-magnific'>
                        <div class='magnific-img cur-pointer'>
                            <a class='image-popup-vertical-fit' href='{$link_url}' title='{$book_title}'>
                                <img src='{$book_image}' alt='{$book_title}' />
                            </a>
                            <a href='{$link_url}' class='gallery-title' >{$book_title}</a>
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