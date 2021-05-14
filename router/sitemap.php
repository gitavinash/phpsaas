<?php

	header('Content-Type: application/xml; charset=utf-8');

	$sitemapType=((sizeof($urlStructure)==1) ? 'default' : $urlStructure[1] );
	$sitemapStartFrom=((isset($urlStructure[2])) ? $urlStructure[2] : 0 );

	if($sitemapType=='default') {

		require_once RESOURCE."sitemap/default.php";

	} else {

		/** Inner page sitemap */
		$sitemapTemplate=RESOURCE."sitemap/{$sitemapType}.php";

		if(file_exists($sitemapTemplate)) {

			require_once $sitemapTemplate;
	
		} else {
	
			require_once RESOURCE."sitemap/single-default.php";
	
		}

	}

	exit();