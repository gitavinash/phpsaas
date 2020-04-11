<?php

    /** Convert HTML to AMP format */

    $bodyContent=str_replace('<p><br></p>', '', str_replace('<script async="" src="//www.instagram.com/embed.js"></script>', '', str_replace('<script async="" src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>', '', str_replace('<div>', '', str_replace('<p style="text-align: justify;"><br></p>', '', str_replace('&nbsp;', '', str_replace('</div>', '</p>', str_replace('<div style="text-align: justify;"', '<p', ${$link_type.'_description'}))))))));

    /** Find all image tags */

    $imageTags=array();
    $imageTagsValue=array();
    $imageTagList = preg_match_all('/<img[^>]*>(.*?)/is', $bodyContent, $matches);

    for ($i = 0; $i < $imageTagList; ++$i) {
        
        $imageTags[] = $matches[0][$i];

        /** Get src value from image tags */

        preg_match_all('/src="([^"]*)"/', $matches[0][$i], $imageTagsValue[$i]);

        /** Convert img tag to amp-img tag */

        $imageTitle=explode('/', $imageTagsValue[$i][1][0]);
        $imageTitle=substr(end($imageTitle), 0, strpos(end($imageTitle), "."));
        $imageTitle=rtrim(ltrim(preg_replace('/[0-9]+/', '', ucwords(str_replace('_', ' ', $imageTitle))), ' '), ' ');

        $bodyContent=str_replace($matches[0][$i], '<amp-img layout="responsive" alt="'.$imageTitle.'" src="'.$imageTagsValue[$i][1][0].'" width="'.IMAGE_THUMBNAIL_WIDTH.'" height="'.IMAGE_THUMBNAIL_HEIGHT.'" ></amp-img>', $bodyContent);
        
    }

    /** Find all blockquote tags */

    $embedTags=array();
    $socialMediaLinks=array();
    $embedTagsList=preg_match_all('/<blockquote[^>]*>(.*?)<\/blockquote>|<iframe[^>]*>(.*?)<\/iframe>/is', $bodyContent, $matches);

    for ($i = 0; $i < $embedTagsList; ++$i) {

        $embedTags[] = $matches[0][$i];

        /** Get href value from a tags */

        preg_match_all('/href="([^"]*)"/', $matches[0][$i], $socialMediaLinks[$i]);

        if(sizeof($socialMediaLinks[$i][0])==0) {

            /** Get src value from a tag */

            preg_match_all('/src="([^"]*)"/', $matches[0][$i], $socialMediaLinks[$i]);

        }
        

    }

    for($i=0; $i<sizeof($embedTags); $i++) {

        for($j=0; $j<sizeof($socialMediaLinks[$i][1]); $j++) {

            $socialLink=parse_url($socialMediaLinks[$i][1][$j]);

            switch($socialLink['host']) {

                case 'twitter.com' :

                    if(preg_match('/status/', $socialMediaLinks[$i][1][$j])) {

                        $twitterID=explode('/', $socialMediaLinks[$i][1][$j]);
                        $twitterID=substr(end($twitterID), 0, strpos(end($twitterID), "?"));
                        $bodyContent=str_replace($embedTags[$i], "<amp-twitter width='375' height='472' layout='responsive' data-tweetid='{$twitterID}' ></amp-twitter>", $bodyContent);

                    }
                    break;

                case 'www.instagram.com' :

                    if(preg_match('/embed/', $socialMediaLinks[$i][1][$j])) {

                        $instagramID=explode('/', $socialMediaLinks[$i][1][$j]);
                        $instagramID=$instagramID[4];
                        $bodyContent=str_replace($embedTags[$i], "<amp-instagram data-shortcode='{$instagramID}' data-captioned width='400' height='400' layout='responsive' ></amp-instagram>", $bodyContent);

                    }
                    break;

                case 'www.youtube.com' :
                    if(preg_match('/embed/', $socialMediaLinks[$i][1][$j])) {

                        $youtubeID=explode('/', $socialMediaLinks[$i][1][$j]);
                        $youtubeID=end($youtubeID);
                        $bodyContent=str_replace($embedTags[$i], "<amp-youtube data-videoid='{$youtubeID}' layout='responsive' width='480' height='270' ></amp-youtube>", $bodyContent);

                    }
                    break;

            }

        }

    }

    $bodyContent=str_replace(SITE_URL, SITE_URL.'/amp', str_replace('</amp-img></p>', '</amp-img>', str_replace('<p><amp-img', '<amp-img', $bodyContent)));

    /** Impliment Ads into the Article */

    $htmlContent=explode('<p>', $bodyContent);

    $newBodyContent='';
    $newBodyContentLenght=0;
    $j=0;

    for($i=0; $i<sizeof($htmlContent); $i++) {

        $newBodyContent=$newBodyContent.'<p>'.$htmlContent[$i];
        $newBodyContentLenght=$newBodyContentLenght+strlen($htmlContent[$i]);

        if($j==0) {

            $newBodyContent=$newBodyContent.'<div class="in-article-ad-box"></div>';
            $j=0;
            $newBodyContentLenght=0;

        }

        if($j==2) {

            $newBodyContent=$newBodyContent.'<div class="in-article-ad-box"></div>';
            $j=0;
            $newBodyContentLenght=0;

        }

        $j++;

    }

    $bodyContent=$newBodyContent;