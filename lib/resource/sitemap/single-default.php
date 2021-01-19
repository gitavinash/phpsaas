<?php

    echo "<?xml version='1.0' encoding='UTF-8' ?><?xml-stylesheet type='text/xsl' href='".str_replace(ROOT, '', RESOURCE)."sitemap/stylesheet/style.xsl' ?>
<urlset xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:image='http://www.google.com/schemas/sitemap-image/1.1' xsi:schemaLocation='http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd' xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>";

    $sitemapType=explode('-', str_replace('.xml', '', $urlStructure[0]));

    if(!is_numeric($sitemapType[1])) {

        $sitemapStartFrom=0;
        $sitemapType=$sitemapType[0];

    } else {

        $sitemapStartFrom=(($sitemapType[1]==1) ? 0 : $sitemapType[1]*SITEMAP_LIMIT );
        $sitemapType=$sitemapType[0];

    }

    $sitemapQuery="SELECT link_url, link_published FROM ".TABLE_PREFIX."link WHERE link_site_id=".SITE_ID." AND link_type='{$sitemapType}' AND link_status=200 ORDER BY link_modified DESC LIMIT {$sitemapStartFrom}, ".SITEMAP_LIMIT;

    if($sitemapLinks=mysqli_query($connection, $sitemapQuery)) {

        while($sitemapLinkData=mysqli_fetch_assoc($sitemapLinks)) {

            extract($sitemapLinkData);

            echo "
        <url>
            <loc>".str_replace('&', '', SITE_URL.$link_url)."</loc>
            <lastmod>".date('c', strtotime($link_published))."</lastmod>
        </url>";

        }

    }


echo "
</urlset>";