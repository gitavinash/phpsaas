<?php

    echo "<?xml version='1.0' encoding='UTF-8'?><?xml-stylesheet type='text/xsl' href='".str_replace(ROOT, '', RESOURCE)."sitemap/stylesheet/inner-style.xsl' ?>
    <urlset xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd' xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>";

    $sitemapQuery="SELECT {$sitemapType}_title, link_url, link_published FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."{$sitemapType} ON link_relation_id={$sitemapType}_id WHERE link_type='{$sitemapType}' AND link_site_id=".SITE_ID." AND link_status=200 ORDER BY link_modified DESC LIMIT {$sitemapStartFrom}, ".SITEMAP_LIMIT;

    if($sitemapLinks=mysqli_query($connection, $sitemapQuery)) {

        while($sitemapLinkData=mysqli_fetch_assoc($sitemapLinks)) {

            extract($sitemapLinkData);

            echo "
        <url>
            <loc>".SITE_URL."{$link_url}</loc>
            <lastmod>".date('Y-m-d', strtotime($link_published))."</lastmod>
            <changefreq>Always</changefreq>
            <priority>1.0</priority>
        </url>";

        }

    }


echo "
</urlset>";