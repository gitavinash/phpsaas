<?php

/** Including database connection file */

require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php';

/** This script run only when Request method is not POST */

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

	if (SITE_DOMAIN == MAIN_DOMAIN) {

		require_once 'checkMainDomain.php';
	} else {

		require_once 'checkSubDomain.php';
	}
} else {

	require_once 'getDomainData.php';
}

if (isset($site_id)) {

	/** Fetch global website configuration settings */

	if ($websiteConfigutration = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_status=1 AND ( site_configuration_site_id={$site_id} OR site_configuration_site_id={$site_owner_id})")) {

		if (mysqli_num_rows($websiteConfigutration) != 0) {

			while ($websiteConfigutrationData = mysqli_fetch_assoc($websiteConfigutration)) {

				if ($websiteConfigutrationData['site_configuration_key'] != '') {

					$constantName = strtoupper($websiteConfigutrationData['site_configuration_key']);
					$constantValue = $websiteConfigutrationData['site_configuration_value'];

					if (!defined($constantName)) {
						define($constantName, $constantValue);
					}
				}
			}
		}
	}

	require_once __DIR__ . '/themePreview.php';

	$iswLiveThemeId = isset($site_theme_id) ? (int) $site_theme_id : (isset($theme_id) ? (int) $theme_id : 0);
	$iswLiveThemeTitle = isset($theme_title) ? (string) $theme_title : '';
	$iswLiveThemePath = isset($theme_path) ? (string) $theme_path : '';

	if (!defined('ISW_THEME_LIVE_ID')) {
		define('ISW_THEME_LIVE_ID', $iswLiveThemeId);
	}
	if (!defined('ISW_THEME_LIVE_TITLE')) {
		define('ISW_THEME_LIVE_TITLE', $iswLiveThemeTitle);
	}
	if (!defined('ISW_THEME_LIVE_PATH')) {
		define('ISW_THEME_LIVE_PATH', $iswLiveThemePath);
	}

	$iswPreviewState = iswResolveSiteThemePreview(
		$connection,
		(int) $site_id,
		(int) ($site_app_id ?? 0),
		(string) ($app_path ?? 'system'),
		$iswLiveThemeId
	);

	if (!empty($iswPreviewState['active']) && !empty($iswPreviewState['theme'])) {
		$iswPreviewTheme = $iswPreviewState['theme'];
		if (!defined('ISW_THEME_PREVIEW_ACTIVE')) {
			define('ISW_THEME_PREVIEW_ACTIVE', true);
		}
		if (!defined('ISW_THEME_PREVIEW_ID')) {
			define('ISW_THEME_PREVIEW_ID', (int) $iswPreviewTheme['theme_id']);
		}
		if (!defined('ISW_THEME_PREVIEW_TITLE')) {
			define('ISW_THEME_PREVIEW_TITLE', (string) $iswPreviewTheme['theme_title']);
		}
		if (!defined('ISW_THEME_PREVIEW_PATH')) {
			define('ISW_THEME_PREVIEW_PATH', (string) $iswPreviewTheme['theme_path']);
		}

		if (!empty($iswPreviewState['eligible_route'])) {
			$theme_id = (string) $iswPreviewTheme['theme_id'];
			$theme_app_id = (string) $iswPreviewTheme['theme_app_id'];
			$theme_package_id = (string) $iswPreviewTheme['theme_package_id'];
			$theme_title = (string) $iswPreviewTheme['theme_title'];
			$theme_description = (string) $iswPreviewTheme['theme_description'];
			$theme_image = (string) $iswPreviewTheme['theme_image'];
			$theme_path = (string) $iswPreviewTheme['theme_path'];
			$theme_version = (string) $iswPreviewTheme['theme_version'];

			if (!defined('ISW_THEME_PREVIEW_RENDERING')) {
				define('ISW_THEME_PREVIEW_RENDERING', true);
			}
			if (!defined('ISW_THEME_PREVIEW_CAN_APPLY')) {
				define('ISW_THEME_PREVIEW_CAN_APPLY', iswHasPrivilegedSiteSession($connection, (int) $site_id));
			}
		}
	} else {
		if (!defined('ISW_THEME_PREVIEW_ACTIVE')) {
			define('ISW_THEME_PREVIEW_ACTIVE', false);
		}
		if (!defined('ISW_THEME_PREVIEW_ID')) {
			define('ISW_THEME_PREVIEW_ID', 0);
		}
		if (!defined('ISW_THEME_PREVIEW_TITLE')) {
			define('ISW_THEME_PREVIEW_TITLE', '');
		}
		if (!defined('ISW_THEME_PREVIEW_PATH')) {
			define('ISW_THEME_PREVIEW_PATH', '');
		}
	}
}