<?php

	echo '<?xml version="1.0" encoding="UTF-8"?>';

	$feedURL=str_replace('feed/', '', BASE_URL);
	
	if($findnews=mysqli_query($connection, "SELECT news_id, news_image, news_title, news_description, news_category, category_title, news_owner_id, profile_display_name, news_type, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc, link_status, link_type, link_published, link_modified, link_url FROM ".TABLE_PREFIX."link, ".TABLE_PREFIX."site, ".TABLE_PREFIX."news, ".TABLE_PREFIX."meta, ".TABLE_PREFIX."category, ".TABLE_PREFIX."profile WHERE link_status=200 AND link_site_id=site_id AND link_relation_id=news_id AND meta_relation_id=news_id AND news_category=category_id AND link_type='news' AND news_owner_id=profile_user_id AND link_is_featured=1 ORDER BY link_modified DESC LIMIT 0, 30")) {

		if(mysqli_num_rows(($findnews))!=0) { ?>
					
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
				while($newsData=mysqli_fetch_assoc($findnews)) {
					
					if($i==1) { ?>

						<channel>
							<title><?php echo SITE_NAME ?></title>
							<atom:link href="<?php echo SITE_URL ?>" rel="self" type="application/rss+xml" />
							<link><?php echo SITE_URL ?></link>
							<description><?php echo SITE_DESC ?></description>
							<lastBuildDate><?php echo date('r', strtotime($newsData['link_published'])) ?></lastBuildDate>
							<language>hi</language>
							<sy:updatePeriod>hourly</sy:updatePeriod>
							<sy:updateFrequency>1</sy:updateFrequency>

					<?php
					} ?>

					<item>
						<title><![CDATA[<?php echo $newsData['news_title']; ?>]]></title>
					</item>

				<?php
					$i++;
				}

			echo '</channel></rss>';
				
		}
	
	}

?>