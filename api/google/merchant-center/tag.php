<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="google-merchant-' . strtolower(str_replace(' ', '-', $tagName)) . '-feed-' . strtolower(str_replace('.', '-', SITE_DOMAIN)) . '.csv"');

$siteName = strtoupper(str_replace(' ', '-', strtolower(SITE_NAME)));

$tagIds = '';

$getAllTagList = "SELECT GROUP_CONCAT(DISTINCT tag_relation_id SEPARATOR ',') AS tag_ids FROM " . TABLE_PREFIX . "tag LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=tag_relation_id AND inventory_site_id=" . SITE_ID . " WHERE tag_title LIKE '%{$tagName}%' AND tag_type IN ('product','service') AND tag_site_id=" . SITE_ID;

if ($checkTags = mysqli_query($connection, $getAllTagList)) {
    if (mysqli_num_rows($checkTags) > 0) {
        $tagData = mysqli_fetch_assoc($checkTags);
        $tagIds = $tagData['tag_ids'];
    }
}

if ($tagIds != '') {

    // Your query to fetch product data
    $mccProductQuery = "
SELECT
    IF(inventory_sku IS NULL OR inventory_sku = '', CONCAT('{$siteName}', product_id), inventory_sku) AS product_id,
    CONCAT('" . SITE_URL . "', link_url) AS link,
    link_status,
    product_title,
    CONCAT('" . SITE_URL . "', product_image) AS image_link,
    product_gallery,
    product_highlight AS description,
    inventory_mrp,
    inventory_price,
    IF(inventory_in_stock IS NULL OR inventory_in_stock = 1, 'in stock', 'out of stock') AS availability,
    '" . SITE_NAME . "' AS brand,
    'no' AS identifier_exists,
    GROUP_CONCAT(DISTINCT tag_title ORDER BY tag_title ASC SEPARATOR ', ') AS tag_title
FROM
    " . TABLE_PREFIX . "link
    INNER JOIN " . TABLE_PREFIX . "product ON product_id = link_relation_id
    INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id = product_id
    LEFT JOIN " . TABLE_PREFIX . "tag ON tag_relation_id = product_id AND tag_site_id = " . SITE_ID . "
WHERE
    link_site_id = " . SITE_ID . "
    AND product_status = 1
    AND inventory_site_id = " . SITE_ID . "
    AND link_status = 200
    AND link_type = 'product'
    AND product_id IN ({$tagIds})
GROUP BY
    product_id, link, link_status, product_title, image_link, product_gallery,
    description, inventory_mrp, inventory_price, availability
";


    // Execute and write to CSV
    if ($checkProducts = mysqli_query($connection, $mccProductQuery)) {
        $output = fopen('php://output', 'w');

        // Required column headers for Google Merchant Center
        fputcsv($output, [
            'id',
            'title',
            'description',
            'link',
            'image_link',
            'additional_image_link',
            'availability',
            'price',
            'brand',
            'identifier_exists',
            'condition',
            'sell_on_google_quantity',
            'custom_label_0',
            'custom_label_1',
            'custom_label_2',
            'custom_label_3',
            'custom_label_4'
        ], ',', '"', '\\');

        while ($product = mysqli_fetch_assoc($checkProducts)) {
            $priceValue = $product['inventory_price'] ?: $product['inventory_mrp'];

            // Handle additional images
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
            $galleryCsvValue = implode(',', $galleryPaths);

            // Split tags and assign to custom_label_0 to custom_label_4
            $customLabels = array_fill(0, 5, ''); // default empty for all 5 labels
            if (!empty($product['tag_title'])) {
                $tagArray = array_map('trim', explode(',', $product['tag_title']));
                $tagArray = array_filter(array_unique($tagArray)); // remove duplicates and empty values
                for ($i = 0; $i < min(5, count($tagArray)); $i++) {
                    $customLabels[$i] = $tagArray[$i];
                }
            }

            // Final row output
            fputcsv($output, [
                $product['product_id'],                // id
                $product['product_title'],             // title
                $product['description'],               // description
                $product['link'],                      // link
                $product['image_link'],                // image_link
                $galleryCsvValue,                      // additional_image_link
                $product['availability'],              // availability
                number_format($priceValue, 2, '.', '') . ' INR', // price
                $product['brand'],                     // brand
                $product['identifier_exists'],         // identifier_exists
                'new',                                 // condition
                100,                                   // sell_on_google_quantity
                $customLabels[0],                      // custom_label_0
                $customLabels[1],                      // custom_label_1 (optional)
                $customLabels[2],                      // custom_label_2 (optional)
                $customLabels[3],                      // custom_label_3 (optional)
                $customLabels[4]                       // custom_label_4 (optional)
            ], ',', '"', '\\');
        }


        fclose($output);
    } else {
        echo "Error fetching products.";
    }
} else {
    echo "No tags found matching the criteria.";
}