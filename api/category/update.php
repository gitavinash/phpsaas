<?php

    require_once LOGIN_CHECK;

    function update($connection, $data) {

        extract($data);

        if($name!='') {

            if($category!=0) {
				$category=explode('_', $category);
				$categoryURL=$category[1];
				$categoryParentID=$category[0];
			} else {
				$categoryParentID=0;
				$categoryURL='/';
			}

			$url=strtolower('/'.$postType.$categoryURL.preg_replace('~[^\p{M}\w-]+~u', '-', $url).'/');

			mysqli_query($connection, 'START TRANSACTION');

			$updateCategory=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."category SET category_parent_id='{$categoryParentID}', category_title='{$name}', category_description='{$description}' WHERE category_site_id='".APP_SITE_ID."' AND category_id='{$categoryID}'");
			
			if($oldURL!=$url) {
				
				/** Update URL if user change old url */

				$redirectURL=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=301 WHERE link_url='{$oldURL}' AND link_relation_id='{$categoryID}' AND link_type='category' AND link_status=200 AND link_site_id=".APP_SITE_ID);
				$insertNewURL=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$categoryID}', 200, 'category', '".USER_ID."', '".SITE_ID."', NOW(), NOW())");

				if($updateCategory && $redirectURL && $insertNewURL) {
					
					$status=200;
					$msg='Category update successfully';
					mysqli_query($connection, 'COMMIT');
					
				} else {
					
					$status='error';
					$msg='Something is wrong when try to update category';
					mysqli_query($connection, 'ROLLBACK');

				}

			} else {

				/** Only update category */

				$modifyTime=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_modified=NOW() WHERE link_relation_id='{$categoryID}' AND link_status=200 AND link_type='category' AND link_site_id=".SITE_ID);

				if($updateCategory && $modifyTime) {
					
					$status=200;
					$msg='Category update successfully';
					mysqli_query($connection, 'COMMIT');
					
				} else {
					mysqli_query($connection, 'ROLLBACK');
					
					$msg='Something is wrong when try to update category';

				}

			}

        } else {

            $msg='Please enter category name';

        }
    
        $result = array(
            'status' => (isset($status)) ? $status : 'error',
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
			'categoryID' => ((isset($categoryID)) ? $categoryID : 0 ),
			'postType' => ((isset($postType)) ? $postType : 'post' )
        );

        echo json_encode($result, TRUE);

    }