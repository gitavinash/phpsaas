<?php


// Pagination logic inspired by category.php
$currentPage = 1;
if (isset($_GET['pg']) && is_numeric($_GET['pg']) && $_GET['pg'] > 0) {
	$currentPage = (int)$_GET['pg'];
}
$authorId = (int)$urlStructure[1];


// Count total result for pagination
$countQuery = "SELECT COUNT(DISTINCT n.news_id) AS total_result
FROM " . TABLE_PREFIX . "news n
INNER JOIN " . TABLE_PREFIX . "link l 
    ON l.link_relation_id = n.news_id 
    AND l.link_status = 200 
    AND l.link_site_id = '" . SITE_ID . "' 
    AND l.link_type = 'news'
LEFT JOIN " . TABLE_PREFIX . "authors a
    ON n.news_id = a.rel_id 
    AND a.rel_type = 'news'
WHERE n.news_owner_id = {$authorId}
   OR a.profile_user_id = {$authorId}";


$total_result = 0;
$totalPages = 0;
if ($totalAuthorPost = mysqli_query($connection, $countQuery)) {
	if (mysqli_num_rows($totalAuthorPost) != 0) {
		$totalRow = mysqli_fetch_assoc($totalAuthorPost);
		$total_result = (int)$totalRow['total_result'];
		$totalPages = ($total_result > 0) ? (int)ceil($total_result / PER_PAGE_LIMIT) : 0;
		define('TOTAL_PAGE', $totalPages);
		define('TOTAL_RESULT', $total_result);
	}
}

$offset = ($currentPage - 1) * PER_PAGE_LIMIT;
if ($offset < 0) $offset = 0;

$authorQuery = "SELECT DISTINCT n.*, l.link_url
FROM " . TABLE_PREFIX . "news n
INNER JOIN " . TABLE_PREFIX . "link l 
	ON l.link_relation_id = n.news_id 
	AND l.link_status = 200 
	AND l.link_site_id = '" . SITE_ID . "' 
	AND l.link_type = 'news'
LEFT JOIN " . TABLE_PREFIX . "authors a
	ON n.news_id = a.rel_id 
	AND a.rel_type = 'news'
WHERE n.news_owner_id = {$authorId}
   OR a.profile_user_id = {$authorId}
ORDER BY l.link_published DESC
LIMIT " . PER_PAGE_LIMIT . " OFFSET $offset";

$authorArticles = [];

if ($fetchAuthorArticles = mysqli_query($connection, $authorQuery)) {
	if (mysqli_num_rows($fetchAuthorArticles) > 0) {
		while ($authorArticle = mysqli_fetch_assoc($fetchAuthorArticles)) {
			$authorArticles[] = $authorArticle;
		}
	}
}

// Set pagination constants for template
if (!defined('PAGE_NO')) define('PAGE_NO', $currentPage);
if (!defined('NEXT_PAGE')) define('NEXT_PAGE', min($currentPage + 1, $totalPages));
if (!defined('PREVIOUS_PAGE')) define('PREVIOUS_PAGE', max(1, $currentPage - 1));

$dedicatedAuthorTemplate = USER_PATH . $authorId . '-author.php';
$defaultAuthorTemplate = USER_PATH . 'author.php';

if (file_exists($dedicatedAuthorTemplate)) {
	include_once $dedicatedAuthorTemplate;
} else {
	if (file_exists($defaultAuthorTemplate)) {
		include_once $defaultAuthorTemplate;
	} else {
		http_response_code(404);
		echo '<h1>Unable to find the author template path</h1>';
	}
}