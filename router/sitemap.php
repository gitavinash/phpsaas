<?php

	header('Content-Type: application/xml; charset=utf-8');

	$sitemapType=explode('-', str_replace('.xml', '', $urlStructure[0]));

	$sitemapType=array_shift($sitemapType);

	$sitemapType=(($sitemapType=='sitemap') ? 'default' : $sitemapType );

	if($sitemapType=='sitemap') {

		require_once RESOURCE."sitemap/default.php";

	} else {

		/** Inner page sitemap */

		if(file_exists(RESOURCE."sitemap/{$sitemapType}.php")) {

			require_once RESOURCE."sitemap/{$sitemapType}.php";
	
		} else {
	
			require_once RESOURCE."sitemap/single-default.php";
	
		}

	}