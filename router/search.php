<?php

    $search_type = '';
    $tag_type='';
    $tag_description='';

    /** Search query */

    $searchQuery=str_replace('-', ' ', mysqli_real_escape_string($connection, $_GET['q']));

    if($queryList=mysqli_query($connection, "SELECT tag_title, tag_relation_id, tag_image, tag_description, tag_type FROM ".TABLE_PREFIX."tag WHERE tag_title LIKE '%{$searchQuery}%' AND tag_site_id=".SITE_ID." ORDER BY tag_time DESC")) {

        if(mysqli_num_rows($queryList)!=0) {

            while($queryPostData=mysqli_fetch_assoc($queryList)) {

                $queryPost[]=$queryPostData;

            }

            extract($queryPost[0]); // Extract first result for page meta's

        } else {

            $productListType=TRUE;

            if($queryList=mysqli_query($connection, "SELECT product_title, product_image, link_url, link_published, inventory_price, inventory_mrp, inventory_in_stock FROM ".TABLE_PREFIX."product INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id WHERE product_title LIKE '%{$searchQuery}%' AND product_site_id=".SITE_ID." AND link_status=200 AND link_type='product' ORDER BY product_time DESC")) {

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

        $search_path=USER_PATH."{$search_type}-search.php";

        /** Include Tag view template */

        if(file_exists($search_path)) {

            require_once $search_path;

        } else {
            
            require_once USER_PATH.'search.php';

        }

    } else {

        require_once ERROR_404;

    }