<?php
// Check if preview mode is enabled
$isPreview = isset($_GET['preview']) && $_GET['preview'] === 'true';

// if (!$isPreview) {
//     header('Content-Type: text/csv');
//     header('Content-Disposition: attachment; filename="google-merchant-feed-' . strtolower(str_replace('.', '-', SITE_DOMAIN)) . '.csv"');
// } else {
//     header('Content-Type: application/json');
// }

$siteName = strtoupper(str_replace(' ', '-', strtolower(SITE_NAME)));

$applyLimit = $isPreview ? "LIMIT 10" : "";


$getAllTagList = "SELECT GROUP_CONCAT(DISTINCT tag_relation_id SEPARATOR ',') AS tag_ids FROM " . TABLE_PREFIX . "tag LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=tag_relation_id AND inventory_site_id=" . SITE_ID . " WHERE tag_title LIKE '%{$tagName}%' AND tag_type IN ('product','service') AND tag_site_id=" . SITE_ID . " {$applyLimit}";

if ($checkTag = mysqli_query($connection, $getAllTagList)) {

    if (mysqli_num_rows($checkTag) != 0) {

        $tagIds = mysqli_fetch_assoc($checkTag);
        $tagIds = $tagIds['tag_ids'];



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
ORDER BY product_id ASC
{$applyLimit}
";

        // Execute and write to CSV or JSON
        if ($checkProducts = mysqli_query($connection, $mccProductQuery)) {

            if ($isPreview) {
                // Preview mode: collect and output as JSON
                $products = [];

                while ($product = mysqli_fetch_assoc($checkProducts)) {
                    $priceValue = $product['inventory_price'] ?: $product['inventory_mrp'];

                    // Handle additional images
                    $galleryPaths = [];
                    $galleryJson = $product['product_gallery'];
                    if (!empty($galleryJson)) {
                        $decodedGallery = json_decode($galleryJson, true);
                        if (is_array($decodedGallery) && json_last_error() === JSON_ERROR_NONE) {
                            foreach ($decodedGallery as $img) {
                                if (isset($img['path']) && !empty($img['path'])) {
                                    $galleryPaths[] = SITE_URL . $img['path'];
                                }
                            }
                        }
                    }

                    // Split tags and assign to custom_label_0 to custom_label_4
                    $customLabels = array_fill(0, 5, '');
                    if (!empty($product['tag_title'])) {
                        $tagArray = array_map('trim', explode(',', $product['tag_title']));
                        $tagArray = array_filter(array_unique($tagArray));
                        for ($i = 0; $i < min(10, count($tagArray)); $i++) {
                            $customLabels[$i] = $tagArray[$i];
                        }
                    }

                    $products[] = [
                        'id' => $product['product_id'],
                        'title' => $product['product_title'],
                        'description' => $product['description'],
                        'link' => $product['link'],
                        'image_link' => $product['image_link'],
                        'additional_image_link' => $galleryPaths,
                        'availability' => $product['availability'],
                        'price' => number_format($priceValue, 2, '.', '') . ' INR',
                        'brand' => $product['brand'],
                        'identifier_exists' => $product['identifier_exists'],
                        'condition' => 'new',
                        'sell_on_google_quantity' => 100,
                        'custom_label_0' => isset($customLabels[0]) ? $customLabels[0] : '',
                        'custom_label_1' => isset($customLabels[1]) ? $customLabels[1] : '',
                        'custom_label_2' => isset($customLabels[2]) ? $customLabels[2] : '',
                        'custom_label_3' => isset($customLabels[3]) ? $customLabels[3] : '',
                        'custom_label_4' => isset($customLabels[4]) ? $customLabels[4] : '',
                        'custom_label_5' => isset($customLabels[5]) ? $customLabels[5] : '',
                        'custom_label_6' => isset($customLabels[6]) ? $customLabels[6] : '',
                        'custom_label_7' => isset($customLabels[7]) ? $customLabels[7] : '',
                        'custom_label_8' => isset($customLabels[8]) ? $customLabels[8] : '',
                    ];
                }

                echo json_encode([
                    'status' => 'success',
                    'count' => count($products),
                    'data' => $products
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            } else {
                // CSV mode
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
                        if (is_array($decodedGallery) && json_last_error() === JSON_ERROR_NONE) {
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
            }
        } else {
            echo "Error fetching products.";
        }
    } else {
        echo json_encode([
            'status' => 'success',
            'count' => 0,
            'data' => []
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error fetching tags.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}