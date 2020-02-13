<?php

    echo "<rss version='2.0'
            xmlns:content='http://purl.org/rss/1.0/modules/content/'>";
                
                if($newsData=mysqli_query($connection, "SELECT news_id, profile_display_name, link_url, link_published, link_modified, news_image, news_title, news_description, category_title, meta_description FROM isw_news INNER JOIN isw_link ON news_id=link_relation_id INNER JOIN isw_meta ON meta_relation_id=news_id INNER JOIN isw_profile ON profile_id=link_owner_id INNER JOIN isw_category ON news_category=category_id WHERE link_type='news' AND category_type='news' ORDER BY link_modified DESC LIMIT 0, 10")) {
                    
                    /* Article feed section */

                    $i=1;
                    
                    while($newsListData=mysqli_fetch_assoc($newsData)) { extract($newsListData);

                        if($i==1) { ?>

                        <channel>
                            <title><?php echo SITE_NAME ?></title>
                            <link><?php echo SITE_URL ?></link>
                            <description><![CDATA[<?php echo SITE_DESC ?>]]></description>
                            <language>hi_IN</language>
                            <lastBuildDate><?php echo date('Y-m-dTH:i:sZ', strtotime('+5 hour +30 minutes', strtotime($newsListData['link_published']))) ?></lastBuildDate>
                        <?php
                        } ?>

                        <item>
                            <title><![CDATA[<?php echo $news_title ?>]]></title>
                            <link><?php echo SITE_URL.$link_url ?></link>
                            <guid><?php echo $news_id ?></guid>
                            <pubDate><?php echo date('Y-m-dTH:i:sZ', strtotime($link_published)); ?></pubDate>
                            <author><?php echo $profile_display_name ?></author>
                            <description><![CDATA[<?php echo $meta_description ?>]]></description>
                            <content:encoded>
                                <![CDATA[
                                    <!doctype html>
                                    <html lang="en" prefix="op: http://media.facebook.com/op#">
                                    <head>
                                        <meta charset="utf-8">
                                        <link rel="canonical" href="<?php echo SITE_URL.$link_url ?>">
                                        <meta property="op:markup_version" content="v1.0">
                                        <meta property="fb:use_automatic_ad_placement" content="enable=true ad_density=default">
                                    </head>
                                    <body>
                                        <article>

                                            <!-- Header part of Instant Article Page -->

                                            <header>
                                                <figure><img src="<?php echo SITE_URL.$news_image ?>"></figure><h1><?php echo $news_title ?></h1>          
                                                <time class="op-published" datetime="<?php echo date('Y-m-dTH:i:s+00:00', strtotime($link_published)); ?>"><?php echo date('M dS, H:ia', strtotime($link_published)); ?></time>
                                                <time class="op-modified" datetime="<?php echo date('Y-m-dTH:i:s+00:00', strtotime($link_modified)); ?>"><?php echo date('M dS, H:ia', strtotime($link_modified)); ?></time>                     
                                                <h3 class="op-kicker"><?php echo $category_title ?></h3>
                                                <!-- Facebook Direct Ads -->
                                                <figure class="op-ad"><iframe src="https://www.facebook.com/adnw_request?placement=1556823401016138_1556823461016132&amp;adtype=banner300x250" width="300" height="250"></iframe></figure>
                                            </header>

                                            <!-- Body part of Instant Article Page -->

                                            <?php
                                                
                                                $bodyContent=str_replace('<div style="text-align: justify;">', "<p>", str_replace('</div><div style="text-align: justify;">', "</p><p>", html_entity_decode($news_description)));
                                                $bodyContent=str_replace('<img src="', '<img src="'.SITE_URL, str_replace('<p><br></p>', '', str_replace('</div>', '</p>', str_replace('<div>', '<p>', $bodyContent))));

                                                $dom = new DOMDocument();
                                                $dom->encoding = 'utf-8';
                                                $dom->loadHTML(mb_convert_encoding($bodyContent, 'HTML-ENTITIES', 'UTF-8'));
                                                
                                                $span = $dom->createElement('figure');

                                                $images = $dom->getElementsByTagName('img');
                                                
                                                foreach ($images as $image) {

                                                    $span_clone = $span->cloneNode();
                                                    $image->parentNode->replaceChild($span_clone, $image);
                                                    $span_clone->appendChild($image);
                                                }

                                                $bodyContent = $dom->saveHTML($dom->documentElement);
                                                
                                                /** replace blank p tags and inside figure tags in p tags to figure tags */
                                                
                                                $bodyContent=str_replace('<p></p>', '', str_replace('<p><figure>', '<figure>', str_replace('</figure></p>', '</figure>', $bodyContent)));
                                                $bodyContent=str_replace('<p><b><br></b></p>', '', str_replace('<h2 style="text-align: justify;"><figure>', '<figure>', str_replace('</figure></h2>', '</figure>', $bodyContent)));
                                                $bodyContent=str_replace('<p><br><iframe', '<iframe', $bodyContent);

                                                /** Reaplce all H3, H4, H5, H6 Heading to H2 Heading */

                                                $bodyContent=str_replace('<h2 style="text-align: justify;"><br></h2>', '', str_replace('<h3 style="text-align: justify;">', '<h2>', str_replace('<h4 style="text-align: justify;">', '<h2>', str_replace('<h5 style="text-align: justify;">', '<h2>', str_replace('<h6 style="text-align: justify;">', '<h2>', str_replace('<h3>', '<h2>', str_replace('<h4>', '<h2>', str_replace('<h5>', '<h2>', str_replace('<h6>', '<h2>', $bodyContent)))))))));
                                                
                                                echo $bodyContent; 

                                            ?>

                                            <!— Google Analytics Code -->
                                            <figure class="op-tracker">
                                                <iframe>
                                                    <script>
                                                        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                                                        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                                                        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                                                        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
                                                        ga('create', 'UA-10313152-1', 'auto');
                                                        ga('send', 'pageview');
                                                    </script>
                                                </iframe>
                                            </figure>

                                            <!-- Footer part of Instant Article Page -->

                                            <footer>
                                                <small>Copyright © <?php echo date('Y').' '.SITE_NAME ?>. All rights reserved. </small>
                                            </footer>
                                        </article>
                                    </body>
                                    </html>
                                ]]>
                            </content:encoded>
                        </item>
                    <?php
                    $i++;
                    }

                    echo "</channel>
                    </rss>";

                } else {

                    echo mysqli_error($connection);

                }