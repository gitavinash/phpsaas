<?php

/** Pagination */

$currentPage = end($urlStructure);

if (is_numeric($currentPage)) {
	/** Remove page no from url */
	$categoryURL = str_replace($currentPage . '/', '', $url);
} else if (isset($_GET['page']) && is_numeric($_GET['page'])) {
	$currentPage = (int)$_GET['page'];
	$categoryURL = $url;
} else {
	$currentPage = 1;
	$categoryURL = $url;
}

if ($checkUrl = mysqli_query($connection, "SELECT link_id, category_id, category_title, category_image, category_description, category_type, link_id, link_url as categoryLink, link_status, link_type, link_relation_id, link_site_id, link_published, link_modified, meta_title, meta_description, meta_keyword, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "category ON link_relation_id=category_id INNER JOIN " . TABLE_PREFIX . "meta ON link_id=meta_relation_id WHERE link_url='{$categoryURL}' AND link_type='category' AND link_status=200 AND link_site_id=" . SITE_ID . " AND meta_type='category' ORDER BY link_is_featured, link_published DESC LIMIT 0, 1")) {

	if (mysqli_num_rows($checkUrl) != 0) {

		extract(mysqli_fetch_assoc($checkUrl));

		/** Check post status */

		if ($link_status == 200) {

			$category_path = USER_PATH . str_replace('/category/', '', rtrim($categoryLink, '/')) . '-category.php';

			// Count total result for pagination

			if ($totalCategoryPost = mysqli_query($connection, "SELECT count(link_id) as total_result FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "{$category_type} ON link_relation_id={$category_type}_id INNER JOIN " . TABLE_PREFIX . "meta ON link_id=meta_relation_id LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id={$category_type}_id WHERE {$category_type}_category='{$category_id}' AND link_status=200 AND link_type='{$category_type}' AND {$category_type}_status=1 ORDER BY link_published DESC")) {

				if (mysqli_num_rows($totalCategoryPost) != 0) {

					extract(mysqli_fetch_assoc($totalCategoryPost));
					$totalResult = $total_result ?? 0;
					$totalPages = ceil($totalResult / PER_PAGE_LIMIT);
					// if (isset($_GET['pg'])) {
					// 	$totalPages = $totalPages - (($currentPage == 1) ? 1 : 0);
					// }
					define('TOTAL_PAGE', $totalPages);
					define('TOTAL_RESULT', $totalResult);
				}
			}

			$sortBy = 'link_published DESC, link_is_featured DESC';
			if (isset($_GET['sort'])) {
				switch ($_GET['sort']) {
					case 'price_low':
						$sortBy = 'inventory_price ASC';
						break;
					case 'price_high':
						$sortBy = 'inventory_price DESC';
						break;
					case 'newest':
						$sortBy = 'link_published DESC';
						break;
					case 'oldest':
						$sortBy = 'link_published ASC';
						break;
				}
			}

			/* * Fetching product of the category */

			$addFilter = "";
			$addTime = "";

			if ($category_type == 'news') {
				$addFilter .= ", news_owner_id, news_paid";
				$addTime = ", news_published";
				$sortBy = "news_published DESC";
			}

			$startFrom = max(0, ($currentPage - 1) * PER_PAGE_LIMIT);
			$paginationQuery = "LIMIT " . $startFrom . ", " . PER_PAGE_LIMIT;

			if ($categoryPost = mysqli_query($connection, "SELECT DISTINCT (link_id) as link_id, link_url, link_published as publishedTime, {$category_type}_id, {$category_type}_image, {$category_type}_title, {$category_type}_description, meta_description, inventory_price, inventory_mrp, inventory_currency_symbol, inventory_in_stock, profile_display_name {$addTime} {$addFilter} FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "{$category_type} ON link_relation_id={$category_type}_id AND {$category_type}_site_id=" . SITE_ID . " INNER JOIN " . TABLE_PREFIX . "meta ON link_id=meta_relation_id LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id={$category_type}_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id={$category_type}_owner_id WHERE {$category_type}_category='{$category_id}' AND link_status=200 AND link_type='{$category_type}' AND {$category_type}_status=1 ORDER BY {$sortBy} {$paginationQuery}")) {

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

				$category_path = USER_PATH . $category_type . '-category.php';

				if (file_exists($category_path)) {

					require_once $category_path;
				} else {
					/** Use parent category template */

					if (file_exists(USER_PATH . 'category.php')) {

						require_once USER_PATH . 'category.php';
					} else {
						http_response_code(404);
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
