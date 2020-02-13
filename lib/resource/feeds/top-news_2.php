<?php

	$tag_title=str_replace('-', ' ', $urlStructure[2]);
	
    if($tagList=mysqli_query($connection, "SELECT link_id, link_url, link_published, link_modified, news_id, news_image, news_title, news_description, news_category, category_title, news_owner_id, news_type, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc,  profile_display_name, profile_image FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."news ON link_relation_id=news_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=news_id INNER JOIN ".TABLE_PREFIX."category ON category_id=news_category INNER JOIN ".TABLE_PREFIX."profile ON profile_id=news_owner_id INNER JOIN ".TABLE_PREFIX."user ON profile_owner_id=user_id INNER JOIN ".TABLE_PREFIX."tag ON tag_relation_id=news_id WHERE link_type='news' AND link_status=200 AND tag_type='news' AND tag_title LIKE '%{$tag_title}%' ORDER BY link_published DESC LIMIT 0, 10")) {

        if(mysqli_num_rows($tagList)!=0) { ?>

            <rss version="2.0"
				xmlns:content="http://purl.org/rss/1.0/modules/content/"
				xmlns:wfw="http://wellformedweb.org/CommentAPI/"
				xmlns:dc="http://purl.org/dc/elements/1.1/"
				xmlns:atom="http://www.w3.org/2005/Atom"
				xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
				xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
				xmlns:media="http://search.yahoo.com/mrss/"
				>

				<image>
					<url><?php echo USER_IMAGES.'logo.png' ?></url>
					<title><?php echo SITE_NAME; ?></title>
					<link><?php echo SITE_URL ?></link>
					<width>32</width>
					<height>32</height>
				</image>

            <?php
            
            $i=1;

            while($tagnewsData=mysqli_fetch_assoc($tagList)) {

                extract($tagnewsData);

                if($i==1) { ?>

                    <channel>
						<title><?php echo $tag_title ?></title>
						<atom:link href="<?php echo SITE_URL.'/feed/tag/'.$urlStructure[2] ?>/" rel="self" type="application/rss+xml" />
						<link><?php echo SITE_URL ?></link>
						<description><?php echo SITE_DESC ?></description>
						<lastBuildDate><?php echo date('r', strtotime('+5 hour +30 minutes', strtotime($link_published))) ?></lastBuildDate>
						<language>hi_IN</language>
						<sy:updatePeriod>hourly</sy:updatePeriod>
						<sy:updateFrequency>1</sy:updateFrequency>

                <?php
                }

                ?>

                <item>
					<Articleid><?php echo $link_id; ?></Articleid>
					<title><![CDATA[<?php echo $news_title; ?>]]></title>
					<link><?php echo SITE_URL.$link_url; ?></link>
					<thumbimage><![CDATA[<?php echo SITE_URL.$news_image; ?>]]></thumbimage>
					<fullimage><![CDATA[<?php echo SITE_URL.$news_image; ?>]]></fullimage>
					<pubDate><?php echo date('r', strtotime('+5 hour +30 minutes', strtotime($link_published))) ?></pubDate>
					<modifiedDate><?php echo date('r', strtotime('+5 hour +30 minutes',strtotime($link_modified))) ?></modifiedDate>
					<description><![CDATA[<?php echo str_replace('src="', 'src="'.SITE_URL.'', html_entity_decode($news_description)); ?>]]></description>
					<authorname><?php echo $profile_display_name ?></authorname>
					<excerpt><![CDATA[<?php echo $meta_description; ?>]]></excerpt>
					<tags>
						<?php

							$tagsList=mysqli_query($connection, "SELECT GROUP_CONCAT(tag_title) as tags FROM isw_tag WHERE tag_relation_id={$news_id}");
							extract(mysqli_fetch_assoc($tagsList));
							echo $tags;

						?>
					</tags>
				</item>

                <?php

                $i++;
                
            }

            echo '</channel></rss>';

        } else {

        }

	} else {

		echo mysqli_error($connection);

	}