<?php

    if($queryList=mysqli_query($connection, "SELECT tag_title, tag_relation_id, tag_image, tag_description, tag_type FROM ".TABLE_PREFIX."tag WHERE tag_title LIKE '%{$searchKeyword}%' AND tag_site_id=".SITE_ID." ORDER BY tag_time DESC")) {

        if(mysqli_num_rows($queryList)!=0) {

            while($queryPostData=mysqli_fetch_assoc($queryList)) {

                $queryPost[]=$queryPostData;

            }

            extract($queryPost);

        } else {

            switch(SITE_APP) {
                
                case 'cms' :
                    $searchQuery="SELECT post_title, post_image, link_url FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON
                    link_relation_id=post_id WHERE post_title
                    LIKE '%{$searchKeyword}%' AND post_site_id=".SITE_ID." AND link_status=200 AND link_type='post' ORDER BY
                    post_time DESC";
                    break;

                default :

                    $productListType=TRUE;
                    $searchQuery = "SELECT product_title, product_image, link_url, link_published, inventory_price,
                    inventory_mrp, inventory_in_stock FROM ".TABLE_PREFIX."product INNER JOIN ".TABLE_PREFIX."link ON
                    link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id WHERE product_title
                    LIKE '%{$searchKeyword}%' AND product_site_id=".SITE_ID." AND link_status=200 AND link_type='product' ORDER BY
                    product_time DESC";
                    break;

            }

            if($queryList=mysqli_query($connection, $searchQuery)) {

                if(mysqli_num_rows($queryList)!=0) {

                    while($queryPostData=mysqli_fetch_assoc($queryList)) {

                    $queryPost[]=$queryPostData;

                }

                extract($queryPost[0]); // Extract first result for page meta's

                } else {

                    $queryPost='';

                }

            }

        }

        /** Include dedicated tag theme template */

        $search_path=USER_PATH."search.php";

        /** Include Tag view template */

        if(file_exists($search_path)) {

            require_once $search_path;

        } else {
            
            require_once USER_PATH.'search.php';

        }

    } else {
        
        http_response_code(404);
        require_once ERROR_404;

    }