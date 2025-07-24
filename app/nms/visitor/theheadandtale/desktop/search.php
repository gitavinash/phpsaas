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

						if ($queryResults = mysqli_query($connection, "SELECT link_url, news_title, news_image, news_paid, link_published, meta_title, meta_description, link_published as publishedTime FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "news ON link_relation_id=news_id INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id WHERE link_site_id=" . SITE_ID . " AND link_type='news' AND link_status=200 AND  (link_relation_id={$resultTags}) ORDER BY link_published DESC LIMIT 0, 100")) {

							if (mysqli_num_rows($queryResults) != 0) {

								define('TOTAL_PAGE', round(mysqli_num_rows($queryResults) / PER_PAGE_LIMIT));
								define('TOTAL_RESULT', mysqli_num_rows($queryResults));

								while ($queryResultsData = mysqli_fetch_assoc($queryResults)) {

									extract($queryResultsData);
									echo "<div class='news_box'><a href='{$link_url}' target='_blank'>
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
										<a href='{$link_url}' target='_blank'>
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

				<?php

				require_once 'pagination.php';

				?>
			</div>
			<?php

			require_once 'sidebar.php';

			?>
		</div>
	</div>
	</div>
</section>
<?php

require_once 'footer.php';

?>