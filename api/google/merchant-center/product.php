<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="google-merchant-feed-' . strtolower(str_replace('.', '-', SITE_DOMAIN)) . '.csv"');

$siteName = strtoupper(str_replace(' ', '-', strtolower(SITE_NAME)));

// Your query to fetch product data
$mccProductQuery = "
SELECT
	IF(inventory_sku IS NULL OR inventory_sku = '', CONCAT('{$siteName}', product_id), inventory_sku) AS product_id,
	CONCAT('" . SITE_URL . "', link_url) as link,
	link_status,
	product_title,
	CONCAT('" . SITE_URL . "', product_image) as image_link,
	product_gallery,
	product_highlight AS description,
	inventory_mrp,
	inventory_price,
	IF(inventory_in_stock IS NULL OR inventory_in_stock = 1, 'in stock', 'out of stock') AS availability,
	'" . SITE_NAME . "' AS brand,
	'no' AS identifier_exists
FROM
	" . TABLE_PREFIX . "link
	INNER JOIN
		" . TABLE_PREFIX . "product ON product_id=link_relation_id
	INNER JOIN
		" . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id
WHERE
	link_site_id = " . SITE_ID . "
	AND product_status = 1
	AND inventory_site_id = " . SITE_ID . "
	AND link_status = 200
	AND link_type = 'product'
";

// Execute and write to CSV
if ($checkProducts = mysqli_query($connection, $mccProductQuery)) {
	$output = fopen('php://output', 'w');

	// Required column headers for Google Merchant Center
	fputcsv($output, array_merge([
		'id',
		'title',
		'description',
		'link',
		'image link'
	], [
		'additional_image_link_1',
		'additional_image_link_2',
		'additional_image_link_3',
		'additional_image_link_4',
		'additional_image_link_5',
		'additional_image_link_6',
		'additional_image_link_7',
		'additional_image_link_8',
		'additional_image_link_9',
		'additional_image_link_10'
	], [
		'availability',
		'price',
		'brand',
		'identifier exists',
		'condition',
		'sell on google quantity'
	]), ',', '"', '\\');


	while ($product = mysqli_fetch_assoc($checkProducts)) {
		$priceValue = $product['inventory_price'] ?: $product['inventory_mrp'];

		$galleryPaths = [];

		$galleryJson = $product['product_gallery'];

		if (!empty($galleryJson)) {
			$decodedGallery = json_decode($galleryJson, true);
			if (is_array($decodedGallery)) {
				foreach ($decodedGallery as $img) {
					if (isset($img['path']) && !empty($img['path'])) {
						$galleryPaths[] = SITE_URL . $img['path'];
					}
				}
			}
		}

		$maxGalleryImages = 10;
		$galleryCsvArray = array_slice($galleryPaths, 0, $maxGalleryImages);

		// Fill remaining slots with empty strings to match column count
		$galleryCsvArray = array_pad($galleryCsvArray, $maxGalleryImages, '');

		fputcsv($output, array_merge([
			$product['product_id'],
			$product['product_title'],
			$product['description'],
			$product['link'],
			$product['image_link'],
		], $galleryCsvArray, [
			$product['availability'],
			number_format($priceValue, 2, '.', '') . ' INR',
			$product['brand'],
			$product['identifier_exists'],
			'new',
			100
		]), ',', '"', '\\');
	}

	fclose($output);
} else {
	echo "Error fetching products.";
}