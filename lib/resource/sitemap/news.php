<?php

    echo "<?xml version='1.0' encoding='UTF-8'?><?xml-stylesheet type='text/xsl' href='".str_replace(ROOT, '', RESOURCE)."sitemap/news-style.xsl' ?>
    <urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9' xmlns:news='http://www.google.com/schemas/sitemap-news/0.9'>";

    $sitemapType=explode('-', str_replace('.xml', '', $urlStructure[0]));

    if(sizeof($sitemapType)==2) {

        $sitemapStartFrom=0;
        $sitemapType=$sitemapType[0];

    } else {

        $sitemapStartFrom=$sitemapType[1]*SITEMAP_LIMIT;
        $sitemapType=$sitemapType[0];

    }

    $sitemapQuery="SELECT post_title, link_url, link_published FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id WHERE link_type='post' AND link_status=200 ORDER BY link_modified DESC LIMIT {$sitemapStartFrom}, ".SITEMAP_LIMIT;

    if($sitemapLinks=mysqli_query($connection, $sitemapQuery)) {

        while($sitemapLinkData=mysqli_fetch_assoc($sitemapLinks)) {

            extract($sitemapLinkData);

            echo "
        <url>
            <loc>".SITE_URL."{$link_url}</loc>
            <news:news>
                <news:publication>
                    <news:name>".SITE_NAME."</news:name>
                    <news:language>hi</news:language>
                </news:publication>
                <news:publication_date>".date('Y-m-d', strtotime($link_published))."</news:publication_date>
                <news:title><![CDATA[{$post_title} ]]></news:title>
            </news:news>
        </url>";

        }

    }


echo "
</urlset>";