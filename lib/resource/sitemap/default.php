<?php

    echo "<?xml version='1.0' encoding='UTF-8'?><?xml-stylesheet type='text/xsl' href='".str_replace(ROOT, '', RESOURCE)."sitemap/style.xsl' ?>
<sitemapindex xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>";
    
    if($linkType=mysqli_query($connection, "SELECT COUNT(link_type) as total, link_type, link_published FROM ".TABLE_PREFIX."link GROUP BY link_type ORDER BY link_modified DESC")) {

        if(mysqli_num_rows($linkType)!=0) {

            while($linkData=mysqli_fetch_assoc($linkType)) {

                extract($linkData);

                /** Check total link in particular category */

                if($total!=0){

                    if($total<SITEMAP_LIMIT) {

                        echo "
        <sitemap>
            <loc>".SITE_URL."/{$link_type}-sitemap.xml</loc>
            <lastmod>".date('c', strtotime($link_published))."</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </sitemap>";
    
    
                    } else {
    
                        $splitSitemap=floor($total/SITEMAP_LIMIT);
    
                        for($i=1; $i<=$splitSitemap; $i++) {
    
                            echo "
        <sitemap>
            <loc>".SITE_URL."/{$link_type}-{$i}-sitemap.xml</loc>
            <lastmod>".date('c', strtotime($link_published))."</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </sitemap>";
    
    
                        }

                    }
                }
                
            }

        }

    }

echo "
</sitemapindex>";

