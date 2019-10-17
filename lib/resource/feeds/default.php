<?php

	echo '<?xml version="1.0" encoding="UTF-8"?>';

	$feedURL=str_replace('feed/', '', BASE_URL);
	
	if($findPost=mysqli_query($connection, "SELECT post_id, post_image, post_title, post_description, post_category, category_title, post_owner_id, profile_display_name, profile_image, post_type, meta_title, meta_description, meta_canonical, meta_keyword, meta_robots, meta_geo_position, meta_geo_place_name, meta_geo_region, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc, link_id, link_status, link_type, link_published, link_modified, link_url FROM ".TABLE_PREFIX."link, ".TABLE_PREFIX."site, ".TABLE_PREFIX."post, ".TABLE_PREFIX."meta, ".TABLE_PREFIX."category, ".TABLE_PREFIX."profile WHERE link_status=200 AND link_site_id=site_id AND link_relation_id=post_id AND meta_relation_id=post_id AND post_category=category_id AND link_type='post' AND post_owner_id=profile_owner_id ORDER BY link_modified DESC LIMIT 0, 10")) {

		if(mysqli_num_rows(($findPost))!=0) { ?>
					
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
				while($postData=mysqli_fetch_assoc($findPost)) {

					extract($postData);
					
					if($i==1) { ?>

						<channel>
							<title><?php echo SITE_NAME ?></title>
							<atom:link href="<?php echo SITE_URL ?>" rel="self" type="application/rss+xml" />
							<link><?php echo SITE_URL ?></link>
							<description><?php echo SITE_DESC ?></description>
							<lastBuildDate><?php echo date('D, M d, Y H:i A', strtotime('+5 hour +30 minutes', strtotime($link_published))) ?></lastBuildDate>
							<language>hi</language>
							<sy:updatePeriod>hourly</sy:updatePeriod>
							<sy:updateFrequency>1</sy:updateFrequency>

					<?php
					} ?>

					<item>
						<Articleid><?php echo $link_id; ?></Articleid>
						<title><![CDATA[<?php echo $post_title; ?>]]></title>
						<link><![CDATA[<?php echo SITE_URL.$link_url; ?>]]></link>
						<authorname><?php echo $profile_display_name ?></authorname>
						<authorimage><![CDATA[<?php echo SITE_URL.$profile_image ?>]]></authorimage>
						<thumbimage><![CDATA[<?php echo SITE_URL.$post_image ?>]]></thumbimage>
						<fullimage><![CDATA[<?php echo SITE_URL.$post_image ?>]]></fullimage>
						<pubDate><?php echo date('r', strtotime('+5 hour +30 minutes', strtotime($link_published))) ?></pubDate>
						<modifiedDate><?php echo date('r', strtotime('+5 hour +30 minutes',strtotime($link_modified))) ?></modifiedDate>
						<description><![CDATA[<?php echo str_replace('<img src="', '<img src="'.SITE_URL.'', html_entity_decode($post_description)); ?>]]></description>
						<excerpt><![CDATA[<?php echo $meta_description; ?>]]></excerpt>
						<tags>
						<?php
						
							$tagsList=mysqli_query($connection, "SELECT GROUP_CONCAT(tag_name) as tags FROM isw_tag WHERE tag_relation_id={$post_id}");
							extract(mysqli_fetch_assoc($tagsList));
							echo $tags;

						?>
						</tags>
					</item>

				<?php
					$i++;
				}

			echo '</channel></rss>';
				
		}
	
	}

?>