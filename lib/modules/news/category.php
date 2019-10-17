<?php

	/** Add New Category Function */

	function add($connection, $data) {

		global $table_prefix;

		extract($data);

		if($name=='') {

			/** check if category name is empty */

			$result = array(
				'status' => 206,
				'msg' => 'Please enter category name'
			);

		} else {

			if($category!=0) {
				$category=explode('_', $category);
				$categoryURL=$category[1];
				$categoryParentID=$category[0];
			} else {
				$categoryParentID=0;
				$categoryURL='/';
			}

			$url=strtolower('/category'.$categoryURL.str_replace(' ', '-', str_replace(array(',', '/', '?', '#', '@', '!', '$', '%', '^', '&', '*', '(', ')', '{', '}', '[', ']', '.', '"', "'", ':', ';'), '', ltrim(rtrim($url, '/'), '/'))).'/');

			if($checkURL=mysqli_query($connection, "SELECT link_url FROM ".TABLE_PREFIX."link WHERE link_url='{$url}'")) {

				/* Check url is dublicate or not */

				if(mysqli_num_rows($checkURL)==0) {

					/** Add category details to database */

					mysqli_query($connection, "START TRANSACTION");

					$addCategory=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."category(category_parent_id, category_title, category_description, category_site_id, category_owner_id, category_type) VALUES('{$categoryParentID}', '{$name}', '{$description}', '{$website_id}', '{$website_user_id}', '{$postType}')");
					$newCategoryID=mysqli_insert_id($connection);

					$addMetaData=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_type) VALUES('{$newCategoryID}', 'category')");
					$addLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$newCategoryID}', '200', 'category', '{$website_user_id}', '{$website_id}', NOW(), NOW())");

					if($addCategory && $addMetaData && $addLink) {

							mysqli_query($connection, "COMMIT");

							$result = array(
								'status' => 200,
								'msg' => 'New category create successfully',
								'categoryID' => $newCategoryID,
								'postType' => $postType
							);

					} else {

						mysqli_query($connection, "ROLLBACK");

						$result = array(
							'status' => 500,
							'msg' => 'Something is wrong when creating a new category'
						);

					}

				} else {

					/** If url is found in database */
					
					$result = array(
						'status' => 206,
						'msg' => 'Please change the category url'
					);

				}
			}

		}

		echo json_encode($result, TRUE);

	}

	/** Update Category Function */

	function update($connection, $data) {

		global $table_prefix;

		extract($data);
		
		if($name=='') {

			/** check if category name is empty */

			$result = array(
				'status' => 206,
				'msg' => 'Please enter category name'
			);

		} else {
		
			if($category!=0) {
				$category=explode('_', $category);
				$categoryURL=$category[1];
				$categoryParentID=$category[0];
			} else {
				$categoryParentID=0;
				$categoryURL='/';
			}

			$url=strtolower('/category'.$categoryURL.str_replace(' ', '-', str_replace(array(',', '/', '?', '#', '@', '!', '$', '%', '^', '&', '*', '(', ')', '{', '}', '[', ']', '.', '"', "'", ':', ';'), '', ltrim(rtrim($url, '/'), '/'))).'/');

			echo $url;

			exit();

			mysqli_query($connection, 'START TRANSACTION');

			$updateCategory=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."category SET category_parent_id='{$categoryParentID}', category_title='{$name}', category_description='{$description}' WHERE category_site_id='{$website_id}' AND category_id='{$categoryID}'");
			
			if($oldURL!=$url) {
				
				/** Update URL if user change old url */

				$redirectURL=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=301 WHERE link_url='{$oldURL}' AND link_relation_id='{$categoryID}' AND link_type='category' AND link_status=200");
				$insertNewURL=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$categoryID}', 200, 'category', '{$website_user_id}', '{$website_id}', NOW(), NOW())");

				if($updateCategory && $redirectURL && $insertNewURL) {
					mysqli_query($connection, 'COMMIT');
					$result = array(
						'status' => 200,
						'msg' => 'Category update successfully',
						'categoryID' => $categoryID,
						'postType' => $postType
					);
				} else {
					mysqli_query($connection, 'ROLLBACK');
					$result = array(
						'status' => 500,
						'msg' => 'Something is wrong when try to update category'
					);
				}

			} else {

				/** Only update category */

				$modifyTime=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_modified=NOW() WHERE link_relation_id='{$categoryID}' AND link_status=200 AND link_type='category'");

				if($updateCategory && $modifyTime) {
					mysqli_query($connection, 'COMMIT');
					$result = array(
						'status' => 200,
						'msg' => 'Category update successfully',
						'categoryID' => $categoryID,
						'postType' => $postType
					);
				} else {
					mysqli_query($connection, 'ROLLBACK');
					$result = array(
						'status' => 500,
						'msg' => 'Something is wrong when try to update category'
					);
				}

			}

		}

		echo json_encode($result, TRUE);

	}

?>