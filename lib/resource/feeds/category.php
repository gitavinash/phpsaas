<?php

	echo '<?xml version="1.0" encoding="UTF-8"?>';

	$feedURL=str_replace('feed/', '', BASE_URL);

	if($categoryData=mysqli_query($connection, "SELECT category_id, category_title, link_url as category_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link WHERE link_url='{$feedURL}' AND link_relation_id=category_id AND link_status=200 AND link_type='category'")) {

		if(mysqli_num_rows($categoryData)!=0) {

			extract(mysqli_fetch_assoc($categoryData));

			if($findnews=mysqli_query($connection, "SELECT link_id, link_url, link_published, link_modified, news_title, news_description, news_image, profile_image, profile_display_name, meta_description FROM ".TABLE_PREFIX."news INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=news_id INNER JOIN ".TABLE_PREFIX."profile ON profile_id=news_owner_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=news_id WHERE news_category={$category_id} AND link_type='news' AND link_status=200 ORDER BY link_modified DESC LIMIT 0, 10")) {

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
						
						extract($newsData);

						if($i==1) { ?>

							<channel>
								<title><?php echo $category_title ?></title>
								<atom:link href="<?php echo SITE_URL.'/feed'.$category_url ?>" rel="self" type="application/rss+xml" />
								<link><?php echo SITE_URL ?></link>
								<description><?php echo SITE_DESC ?></description>
								<lastBuildDate><?php echo date('r', strtotime($link_published)) ?></lastBuildDate>
								<language>hi</language>
								<sy:updatePeriod>hourly</sy:updatePeriod>
								<sy:updateFrequency>1</sy:updateFrequency>

						<?php
						} ?>

						<item>
							<Articleid><?php echo $link_id; ?></Articleid>
							<title><![CDATA[<?php echo $news_title; ?>]]></title>
							<link><![CDATA[<?php echo SITE_URL.$link_url; ?>]]></link>
							<authorname><?php echo $profile_display_name ?></authorname>
							<thumbimage><![CDATA[<?php echo SITE_URL.$news_image ?>]]></thumbimage>
							<fullimage><![CDATA[<?php echo SITE_URL.$news_image ?>]]></fullimage>
							<pubDate><?php echo date('r', strtotime($link_published)) ?></pubDate>
							<modifiedDate><?php echo date('r', strtotime($link_modified)) ?></modifiedDate>
							<description><![CDATA[<?php echo str_replace('<img src="', '<img src="'.SITE_URL.'', html_entity_decode($news_description)); ?>]]></description>
							<excerpt><![CDATA[<?php echo $meta_description; ?>]]></excerpt>
							<tags>
							<?php

								$tagsList=mysqli_query($connection, "SELECT GROUP_CONCAT(tag_title) as tags FROM isw_tag WHERE tag_relation_id={$category_id}");
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

		}

	} else {
		echo mysqli_error($connection);
	}

?>