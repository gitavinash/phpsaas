<?php

if (file_exists(USER_PATH . 'index.php')) {

	// Fetch homepage meta tags for seo

	if ($checkHomepageMeta = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "page INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=page_id INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id WHERE link_url IN ('/homepage/', '/') AND link_status=200 AND link_site_id=" . SITE_ID)) {

		if (mysqli_num_rows($checkHomepageMeta) != 0) {

			extract(mysqli_fetch_assoc($checkHomepageMeta));
		}
	}

	require_once USER_PATH . 'index.php';
} else {

	if (DEVELOPMENT_MODE == false) {

		echo '<h1>' . SITE_VERSION . ' theme not found</h1>';
	} else {

		echo USER_PATH;
	}
}
