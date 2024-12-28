<?php

require_once 'header.php';

?>
<section>
	<div class="row">
		<div class="container">
			<div class="left-container">
				<div class="categoryHeading">
					<h1><span>Search for: </span><?php echo ucwords($searchKeyword) ?></h1>
				</div>

				<?php

				if (is_array($queryPost)) {


					if (!isset($newsListType)) {

						for ($i = 0; $i < sizeof($queryPost); $i++) {

							$tagId[] = $queryPost[$i]['tag_relation_id'];
						}

						$resultTags = implode(' OR link_relation_id=', $tagId);

						if ($queryResults = mysqli_query($connection, "SELECT link_url, news_title, news_image, news_paid, link_published, meta_title, meta_description, link_published as publishedTime FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "news ON link_relation_id=news_id INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id WHERE link_site_id=" . SITE_ID . " AND link_type='news' AND link_status=200 AND  (link_relation_id={$resultTags}) ORDER BY link_id DESC LIMIT 0, 10")) {

							if (mysqli_num_rows($queryResults) != 0) {

								while ($queryResultsData = mysqli_fetch_assoc($queryResults)) {

									extract($queryResultsData);
									echo "<div class='news_box'><a href='{$link_url}'>
                                        <div class='feature_image'>
                                            <img src='{$news_image}' alt='{$news_title}'>
                                            " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
        </div>
        <div class='news_intro'>
            <h2>{$news_title}</h2>
            <p><span>
                    " . date('d M Y, H:i A', strtotime($publishedTime)) . "
            </span></p>
            <p>{$meta_description}</p>
        </div>
        </a></div>";
								}
							}
						}
					} else {

						for ($i = 0; $i < sizeof($queryPost); $i++) {

							extract($queryPost[$i]);

							echo "<div class='col-md-3'>
										<a href='{$link_url}'>
											<div class='col-card-3'>
												<div class='event-card'>
													<div class='img-cd'>
														<img src='{$news_image}'>
														" . (($link_published > date('Y-m-d', strtotime('-7 days'))) ? "<span style='position: absolute;right: 0px;background: #D4946D;padding: 2px 15px;border-bottom-right-radius: 4px;color: #fff;font-size: 13px;margin-right: 20px;border-bottom-left-radius: 4px;' >New</span>" : '') . "
													</div>
													<h4>{$news_title}</h4>
													<p class='rate'>Rs. " . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . (($inventory_in_stock != 1) ? '<span style="color: red; margin-left: 20px" >( Out of stock )</span>' : '') . "</p>
												</div>
											</div>
										</a>
									</div>";
						}
					}
				} else { ?>

					<!-- No Result found -->

					<section class='categoryHeading'>
						<h1><span>No news found</span></h1>
					</section>

				<?php
				}

				?>

			</div>
			<div class="right-container">
				<div class="hightlightBox">
					<h2>Fintech Hightlights</h2>
					<?php

					if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_image, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=281 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 5")) {
						echo "<ol>";
						if (mysqli_num_rows($checkFinTechArticles) != 0) {
							while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
								extract($finTechArticlesInfo);
								echo "<li>
                                        <a href='{$link_url}' title='{$news_title}'>
                                            <div class='img' ><img src='{$news_image}' ></div>
                                            <div class='text'><h3>{$news_title}</h3></div>
                                        </a>
                                </li>";
							}
							echo
							"<li>
                                        <a href='/category/news/' class='readMore'>
                                            Read more highlights...
                                        </a>
                                </li>";
						} else {
							echo "<li><a title=''>No article found</a></li>";
						}
						echo "</ol>";
					}
					?>
				</div>
				<div class="hightlightBox">
					<h2>Editor's Pick</h2>
					<?php

					if ($checkFinTechArticles = mysqli_query($connection, "SELECT news_title, news_image, link_url FROM " . TABLE_PREFIX . "tag INNER JOIN " . TABLE_PREFIX . "news ON news_id=tag_relation_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE link_site_id=" . SITE_ID . " AND link_type='news' AND link_status=200 AND tag_title LIKE '%editor%' AND tag_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0,3")) {
						echo "<ol>";
						if (mysqli_num_rows($checkFinTechArticles) != 0) {
							while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
								extract($finTechArticlesInfo);
								echo "<li>
                                        <a href='{$link_url}' title='{$news_title}'>
                                            <div class='img' ><img src='{$news_image}' ></div>
                                            <div class='text'><h3>{$news_title}</h3></div>
                                        </a>
                                </li>";
							}
						} else {
							echo "<li><a title=''>No article found</a></li>";
						}
						echo "</ol>";
					}
					?>
				</div>
			</div>
		</div>
	</div>
	</div>
</section>
<?php

require_once 'footer.php';

?>