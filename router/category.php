<?php

/** Pagination */

$currentPage = end($urlStructure);

if (is_numeric($currentPage)) {

	/** Remove page no from url */
	$categoryURL = str_replace($currentPage . '/', '', $url);
} else {

	$currentPage = 1;
	$categoryURL = $url;
}

if ($checkUrl = mysqli_query($connection, "SELECT link_id, category_id, category_title, category_image, category_description, category_type, link_id, link_url as categoryLink, link_status, link_type, link_relation_id, link_site_id, link_published, link_modified, meta_title, meta_description, meta_keyword, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "category ON link_relation_id=category_id INNER JOIN " . TABLE_PREFIX . "meta ON link_id=meta_relation_id WHERE link_url='{$categoryURL}' AND link_type='category' AND link_site_id=" . SITE_ID . " AND meta_type='category' LIMIT 0, 1")) {

	if (mysqli_num_rows($checkUrl) != 0) {

		extract(mysqli_fetch_assoc($checkUrl));

		/** Check post status */

		if ($link_status == 200) {

			$category_path = USER_PATH . str_replace('/category/', '', rtrim($categoryLink, '/')) . '-category.php';

			// Count total result for pagination

			if ($totalCategoryPost = mysqli_query($connection, "SELECT count(link_id) as total_result FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "{$category_type} ON link_relation_id={$category_type}_id INNER JOIN " . TABLE_PREFIX . "meta ON link_id=meta_relation_id WHERE {$category_type}_category='{$category_id}' AND link_status=200 AND link_type='{$category_type}' AND {$category_type}_status=1 ORDER BY link_published DESC")) {

				if (mysqli_num_rows($totalCategoryPost) != 0) {

					extract(mysqli_fetch_assoc($totalCategoryPost));

					define('TOTAL_PAGE', round($total_result / PER_PAGE_LIMIT));
					define('TOTAL_RESULT', $total_result);
				}
			}

			/** Fetching post of the category */

			if ($categoryPost = mysqli_query($connection, "SELECT link_url, {$category_type}_image, {$category_type}_title, meta_description FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "{$category_type} ON link_relation_id={$category_type}_id INNER JOIN " . TABLE_PREFIX . "meta ON link_id=meta_relation_id WHERE {$category_type}_category='{$category_id}' AND link_status=200 AND link_type='{$category_type}' AND {$category_type}_status=1 ORDER BY link_published DESC " . PAGINATION)) {

				if (mysqli_num_rows($categoryPost) != 0) {
					while ($categoryPostData = mysqli_fetch_assoc($categoryPost)) {

						$categorySinglePost[] = $categoryPostData;
					}
				}
			}

			if (file_exists($category_path)) {

				/** Use custome template of this category */

				require_once $category_path;
			} else {

				$category_path = USER_PATH . str_replace('_', '-', str_replace(' ', '-', $category_type)) . '-category.php';

				if (file_exists($category_path)) {

					/** Use custome template of this category */

					require_once $category_path;
				} else {

					/** Use parent category template */

					if (file_exists(USER_PATH . 'category.php')) {

						require_once USER_PATH . 'category.php';
					} else {

						echo '<h1>Unable to find the template path</h1>';
					}
				}
			}
		} else {

			/** Add Error page  */

			require_once ERROR_403;
		}
	} else {

		http_response_code(404);
		include_once ERROR_404;
	}
}