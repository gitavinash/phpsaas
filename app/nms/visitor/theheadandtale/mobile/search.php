<?php
require_once 'header.php';

/**
 * Map normalized $searchData payload
 */
$searchKeyword = $searchData['q']        ?? ($searchKeyword ?? '');
$queryPost     = $searchData['results']  ?? [];
$source        = $searchData['source']   ?? 'post';

// Helper for escaping
function h($s)
{
	// Remove any HTML or script tags, then safely escape characters
	$clean = strip_tags((string)$s);
	return htmlspecialchars($clean, ENT_QUOTES, 'UTF-8');
}

?>

<section>
    <div class="row">
        <div class="container">
            <div class="left-container">
                <div class="categoryHeading">
                    <h1><span>Search for: </span><?php echo h(ucwords($searchKeyword)); ?></h1>
                </div>

                <?php
				if (is_array($queryPost) && count($queryPost) > 0) {

					if (!isset($newsListType)) {

						foreach ($queryPost as $row) {

							$link_url        = $row['url'] ?? '#';
							if ($link_url !== '' && $link_url[0] !== '/') $link_url = '/' . ltrim($link_url, '/');

							$news_title      = $row['title']         ?? '';
							$news_image      = $row['image']         ?? '';
							$news_paid       = $row['news_paid']     ?? 0;
							$news_published  = $row['published_at']  ?? '';
							$meta_description = $row['meta_description'] ?? '';
							$news_description = $row['description']   ?? '';

							// ✅ prefer meta_description; fallback to news/post/product description
							$shortDescription = trim($meta_description) ?: trim($news_description);

							// ✅ limit to 160 characters
							if (strlen($shortDescription) > 160) {
								$shortDescription = substr($shortDescription, 0, 157) . '...';
							}

							echo "<div class='news_box'><a href='" . h($link_url) . "' target='_blank'>
                                <div class='feature_image'>
                                    <img src='" . h($news_image) . "' alt='" . h($news_title) . "'>
                                    " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
                                </div>
                                <div class='news_intro'>
                                    <h2>" . h($news_title) . "</h2>
                                    <p><span>"
								. ($news_published ? date('d M Y, H:i A', strtotime($news_published)) : '') .
								"</span></p>
                                    <p>" . h($shortDescription) . "</p>
                                </div>
                            </a></div>";
						}
					} else {
						foreach ($queryPost as $row) {

							$link_url          = $row['url'] ?? '#';
							if ($link_url !== '' && $link_url[0] !== '/') $link_url = '/' . ltrim($link_url, '/');

							$news_title        = $row['title'] ?? '';
							$news_image        = $row['image'] ?? '';
							$link_published    = $row['published_at'] ?? '';
							$inventory_price   = $row['inventory_price'] ?? 0;
							$inventory_mrp     = $row['inventory_mrp'] ?? 0;
							$inventory_in_stock = $row['inventory_in_stock'] ?? 1;

							// "New" badge if published within 7 days
							$isNew = $link_published && (strtotime($link_published) > strtotime('-7 days'));

							echo "<div class='col-md-3'>
                                    <a href='" . h($link_url) . "' target='_blank'>
                                        <div class='col-card-3'>
                                            <div class='event-card'>
                                                <div class='img-cd'>
                                                    <img src='" . h($news_image) . "'>
                                                    " . ($isNew ? "<span style='position: absolute;right: 0px;background: #D4946D;padding: 2px 15px;border-bottom-right-radius: 4px;color: #fff;font-size: 13px;margin-right: 20px;border-bottom-left-radius: 4px;' >New</span>" : '') . "
                                                </div>
                                                <h4>" . h($news_title) . "</h4>
                                                <p class='rate'>Rs. " . (($inventory_price != 0) ? h($inventory_price) : h($inventory_mrp)) . (($inventory_in_stock != 1) ? '<span style="color: red; margin-left: 20px" >( Out of stock )</span>' : '') . "</p>
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

                <?php } ?>

                <?php
				// Pagination include (unchanged)
				require_once 'pagination.php';
				?>
            </div>

            <?php require_once 'sidebar.php'; ?>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>