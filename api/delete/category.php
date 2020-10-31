<?php

    function category($connection, $data) {

        extract($data);

        if(isset($id)) {

            if($checkCategory=mysqli_query($connection, "SELECT category_id, category_type, link_id FROM ".TABLE_PREFIX."category INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=category_id WHERE link_type='category' AND category_id='{$id}' AND link_site_id=".SITE_ID)) {

                if(mysqli_num_rows($checkCategory)!=0) {

                    extract(mysqli_fetch_assoc($checkCategory));

                    /** Check this category is link with any other data */

                    if($checkCategoryLinks=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."{$category_type} WHERE {$category_type}_category='{$category_id}'")) {

                        if(mysqli_num_rows($checkCategoryLinks)==0) {

                            // Category is not linked with any other data

                            mysqli_query($connection, "START TRANSACTION");

                            $deleteCategory=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."category WHERE category_id='{$category_id}'");
                            $deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_id='{$link_id}' AND link_type='category'");
                            $deleteMeta=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."meta WHERE meta_relation_id='{$link_id}' AND meta_type='category' AND meta_site_id=".SITE_ID);
                            $deleteTag=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_id='{$category_id}' AND tag_type='category' AND tag_site_id=".SITE_ID);

                            if($deleteCategory && $deleteLink && $deleteMeta && $deleteTag) {

                                $status=200;
                                $msg='Category delete successfully';
                                mysqli_query($connection, "COMMIT");

                            } else {

                                $msg='Something is wrong while deleting category';
                                mysqli_query($connection, "ROLLBACK");

                            }


                        } else {

                            // Category is link with other data's

                            $status='warning';
                            $msg="Category is link with other data's";

                        }

                    }


                } else {

                    $msg='No category found';

                }

            } else {

                $msg=mysqli_error($connection);

            }

        }
        
        $result = array(
            'status' => (isset($status)) ? $status : 'error',
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );

        echo json_encode($result, TRUE);

    }