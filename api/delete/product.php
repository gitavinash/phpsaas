<?php

    function product($connection, $data) {

        extract($data);

        if(isset($id)) {

            if($checkProduct=mysqli_query($connection, "SELECT product_id, link_id FROM ".TABLE_PREFIX."product INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=product_id WHERE link_type='product' AND link_site_id=".SITE_ID." AND product_id='{$id}'")) {

                if(mysqli_num_rows($checkProduct)!=0) {

                    extract(mysqli_fetch_assoc($checkProduct));

                    mysqli_query($connection, "START TRANSACTION");

                    $deleteProduct=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."product WHERE product_id='{$product_id}' AND product_site_id=".SITE_ID);
                    $deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_id='{$link_id}' AND link_type='product' AND link_site_id=".SITE_ID);
                    $deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."inventory WHERE inventory_relation_id='{$product_id}' AND inventory_type='product' AND inventory_site_id=".SITE_ID);
                    $deleteMeta=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."meta WHERE meta_relation_id='{$link_id}' AND meta_type='product' AND meta_site_id=".SITE_ID);
                    $deleteTag=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_id='{$product_id}' AND tag_type='product' AND tag_site_id=".SITE_ID);

                    if($deleteProduct && $deleteLink && $deleteMeta && $deleteTag) {

                        $status=200;
                        $msg='Product delete successfully';
                        mysqli_query($connection, "COMMIT");

                    } else {

                        $msg='Something is wrong while deleting product';
                        mysqli_query($connection, "ROLLBACK");

                    }

                } else {

                    $msg='Invalid request found';

                }

            } else {

                $msg='Technical error';
    
            }

        } else {

            $msg='Something is wrong while sending request to server';

        }
        
        $result = array(
            'status' => (isset($status)) ? $status : 'error',
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );

        echo json_encode($result, TRUE);

    }