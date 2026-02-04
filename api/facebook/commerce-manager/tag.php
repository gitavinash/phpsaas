<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="facebook-commerce-manager-' . strtolower(str_replace(' ', '-', $tagName)) . '-feed-' . strtolower(str_replace('.', '-', SITE_DOMAIN)) . '.csv"');

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
    '" . SITE_NAME . "' AS brand
FROM
    " . TABLE_PREFIX . "link
    INNER JOIN " . TABLE_PREFIX . "product ON product_id = link_relation_id
    INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id = product_id
WHERE
    link_site_id = " . SITE_ID . "
    AND product_status = 1
    AND inventory_site_id = " . SITE_ID . "
    AND link_status = 200
    AND link_type = 'product'
    AND product_id IN ($tagIds)
GROUP BY
    product_id, link, link_status, product_title, image_link, product_gallery,
    description, inventory_mrp, inventory_price, availability
";

    if ($checkProducts = mysqli_query($connection, $mccProductQuery)) {
        $output = fopen('php://output', 'w');

        // Facebook required headers
        fputcsv($output, [
            'id',
            'title',
            'description',
            'availability',
            'condition',
            'price',
            'link',
            'image_link',
            'brand'
        ]);

        while ($product = mysqli_fetch_assoc($checkProducts)) {
            $priceValue = $product['inventory_price'] ?: $product['inventory_mrp'];

            // Clean and limit description (FB max is 5000 characters)
            $description = substr(strip_tags($product['description']), 0, 5000);

            fputcsv($output, [
                $product['product_id'],
                $product['product_title'],
                $description,
                $product['availability'],
                'new', // condition
                number_format($priceValue, 2, '.', '') . ' INR',
                $product['link'],
                $product['image_link'],
                $product['brand']
            ]);
        }

        fclose($output);
    } else {
        echo "Error fetching products.";
    }
} else {
    echo "No tags found matching the specified name.";
}