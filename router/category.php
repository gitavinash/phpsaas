<?php

	/** Pagination */

	$currentPage=end($urlStructure);

	if(is_numeric($currentPage)) {

		/** Remove page no from url */
		$categoryURL=str_replace($currentPage.'/', '', BASE_URL);

	} else {
		
		$currentPage=1;
		$categoryURL=BASE_URL;

	}

	$postFrom=(($currentPage==0) ? 0 : ($currentPage-1)*PER_PAGE_LIMIT );

	if($checkUrl=mysqli_query($connection, "SELECT category_id, category_title, category_image, category_description, category_type, category_total_count, link_id, link_url as categoryLink, link_status, link_type, link_relation_id, link_site_id, link_published, link_modified, meta_title, meta_description, meta_keyword, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc, GROUP_CONCAT(DISTINCT tag_name SEPARATOR ', ') as tagName FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link, ".TABLE_PREFIX."meta, ".TABLE_PREFIX."tag WHERE category_id=link_relation_id AND  category_id=meta_relation_id AND category_id=tag_relation_id AND link_url='{$categoryURL}' AND link_type='category' AND meta_type='category' AND tag_type='category' AND category_site_id=link_site_id")) {

		if(mysqli_num_rows($checkUrl)!=0) {

			extract(mysqli_fetch_assoc($checkUrl));

			/** Check post status */

			if($link_status==200) {

				$category_path=USER_VIEW_PATH.str_replace('/category/', '', rtrim($categoryLink, '/')).'-category.php';
				
				if($categoryPost=mysqli_query($connection, "SELECT link_url, post_image, post_title, meta_description FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."meta ON post_id=meta_relation_id WHERE post_category='{$category_id}' AND link_status=200 AND link_type = 'post' AND post_status=1 ORDER BY link_modified DESC LIMIT {$postFrom}, ".PER_PAGE_LIMIT)) {

					if(mysqli_num_rows($categoryPost)!=0) {
						while($categoryPostData=mysqli_fetch_assoc($categoryPost)) {

							$categorySinglePost[]=$categoryPostData;

						}
					}
				}

				if(file_exists($category_path)) {

					require_once $category_path;

				} else {

					require_once USER_VIEW_PATH.'category.php';

				}


			} else {

				/** Add Error page  */

				require_once ERROR_403;

			}


		} else {

			include_once ERROR_404;

		}

	}
	
	exit();