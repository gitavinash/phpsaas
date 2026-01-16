<?php

/**
 * ======================================================
 * Amazon CSV Import → Product Import (FINAL)
 * - Large image extraction
 * - Unicode normalization
 * - Structured gallery JSON
 * - Clean brand name
 * - MYSQL LOGIC UNCHANGED
 * PHP 8.1+ Compatible
 * ======================================================
 */

require_once LOGIN_CHECK;

set_time_limit(0);
ini_set('memory_limit', '512M');

/* --------------------------------------
   Counters
--------------------------------------- */
$i = 0;
$success = 0;
$failed = 0;
$amazonUrlfailed = 0;
$amazonProduct = 0;
$duplicate = 0;
$failedMsg = '';

/* --------------------------------------
   Upload directory
--------------------------------------- */
$uploadPath = ROOT . '/upload/' . date('Y/m/') . '/';
if (!is_dir($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}

/* --------------------------------------
   Unicode Normalizer
--------------------------------------- */
function normalizeText(string $text): string
{
    $map = [
        "\u{2013}" => '-',
        "\u{2014}" => '-',
        "\u{2018}" => "'",
        "\u{2019}" => "'",
        "\u{201C}" => '"',
        "\u{201D}" => '"',
        "\u{00A0}" => ' ',
    ];
    return trim(strtr($text, $map));
}

/* --------------------------------------
   Clean Brand Name
--------------------------------------- */
function normalizeBrand(string $brand): string
{
    $brand = normalizeText($brand);
    $brand = preg_replace('/^brand\s*:\s*/i', '', $brand);
    $brand = preg_replace('/^visit the\s*/i', '', $brand);
    $brand = preg_replace('/\s*store$/i', '', $brand);
    $brand = preg_replace('/[®™©]/u', '', $brand);
    return trim($brand);
}

/* --------------------------------------
   Amazon Image → Original Size
--------------------------------------- */
function getLargeAmazonImage(string $url): string
{
    return preg_replace('/\._[^.]+_\.(jpg|jpeg|png)$/i', '.$1', $url);
}

/* --------------------------------------
   Download Image
--------------------------------------- */
function downloadImage(string $imageUrl, string $uploadPath): ?string
{
    if (!$imageUrl) return null;

    $imageUrl = getLargeAmazonImage($imageUrl);
    $imageName = time() . '_' . basename(parse_url($imageUrl, PHP_URL_PATH));
    $localPath = $uploadPath . $imageName;

    $data = @file_get_contents($imageUrl);
    if ($data === false) return null;

    file_put_contents($localPath, $data);
    return str_replace(ROOT, '', $localPath);
}

/* --------------------------------------
   Build Gallery JSON Object
--------------------------------------- */
function buildGalleryObject(string $relativePath): array
{
    $fullPath = ROOT . $relativePath;
    $info = pathinfo($fullPath);

    return [
        'name' => ucwords(str_replace(['_', '-'], ' ', $info['filename'])),
        'path' => $relativePath,
        'type' => mime_content_type($fullPath) ?: 'image/jpeg'
    ];
}

/* --------------------------------------
   Fetch Amazon HTML
--------------------------------------- */
function fetchHtml(string $url): string
{
    if (!str_starts_with($url, 'http')) {
        $url = 'https://' . ltrim($url, '/');
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
        CURLOPT_HTTPHEADER => ['Accept-Language: en-IN,en;q=0.9'],
    ]);

    $html = curl_exec($ch);
    curl_close($ch);

    return $html ?: '';
}

/* --------------------------------------
   Fetch Amazon Product
--------------------------------------- */
function fetchAmazonProduct(string $url, string $uploadPath): ?array
{
    $html = fetchHtml($url);
    if (!$html || stripos($html, 'captcha') !== false) return null;

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xp = new DOMXPath($dom);

    $titleNode = $xp->query("//span[@id='productTitle']")->item(0);
    if (!$titleNode) return null;

    $productName = normalizeText($titleNode->textContent);
    $productBrand = normalizeBrand($xp->query("//a[@id='bylineInfo']")->item(0)?->textContent ?? '');

    /* Feature Image */
    $imgNode = $xp->query("//img[@id='landingImage']")->item(0);
    $featureImageUrl = $imgNode ? getLargeAmazonImage($imgNode->getAttribute('src')) : '';
    $productFeatureImage = downloadImage($featureImageUrl, $uploadPath);

    /* Gallery */
    $productImages = [];
    foreach ($xp->query("//div[@id='altImages']//img") as $img) {
        $local = downloadImage($img->getAttribute('src'), $uploadPath);
        if ($local) {
            $productImages[] = buildGalleryObject($local);
        }
    }

    /* Price */
    $productSellingPrice = (float)preg_replace('/[^\d]/', '', $xp->query("//span[@class='a-price-whole']")->item(0)?->textContent ?? 0);
    $productMRP = (float)preg_replace('/[^\d]/', '', $xp->query("//span[@class='a-text-price']//span")->item(0)?->textContent ?? $productSellingPrice);

    /* Description */
    $productDescription = '';
    foreach ($xp->query("//div[@id='productDescription']//p") as $p) {
        $productDescription .= normalizeText($p->textContent) . "\n";
    }

    /* ---------- ADDITIONS (SHORT DESC + TAGS) ---------- */

    // Short description from feature bullets
    $shortDescArr = [];
    foreach ($xp->query("//div[@id='feature-bullets']//li/span") as $li) {
        $text = normalizeText($li->textContent);
        if ($text) {
            $shortDescArr[] = $text;
        }
    }
    $productShortDescription = implode(' | ', $shortDescArr);

    // Tags from brand + title + bullets
    $tags = [];

    if ($productBrand) {
        $tags[] = strtolower($productBrand);
    }

    foreach (preg_split('/\s+/', strtolower($productName)) as $word) {
        if (strlen($word) > 3) {
            $tags[] = preg_replace('/[^a-z0-9]/', '', $word);
        }
    }

    foreach ($shortDescArr as $bullet) {
        foreach (preg_split('/\s+/', strtolower($bullet)) as $word) {
            if (strlen($word) > 4) {
                $tags[] = preg_replace('/[^a-z0-9]/', '', $word);
            }
        }
    }

    $productTags = implode(',', array_unique(array_filter($tags)));

    /* ---------- END ADDITIONS ---------- */

    return [
        'productName' => $productName,
        'productBrand' => $productBrand,
        'productShortDescription' => $productShortDescription,
        'productDescription' => trim($productDescription),
        'productFeatureImage' => $productFeatureImage,
        'productImages' => $productImages,
        'productSellingPrice' => $productSellingPrice,
        'productMRP' => $productMRP,
        'productTags' => $productTags
    ];
}

/* --------------------------------------
   CSV Import + MYSQL INSERT
--------------------------------------- */

$file = fopen($_FILES['importFile']['tmp_name'], 'r');

$categoryID = explode('_', $categoryID);
$categoryURL = str_replace('category/', '', $categoryID[1]);
$categoryId = $categoryID[0];

while (($row = fgetcsv($file, 0, ",", '"', "\\")) !== false) {
    if ($i++ < 1) continue;

    $amazonUrl = trim($row[1] ?? '');
    if (!$amazonUrl) {
        if (DEVELOPMENT_MODE == true) {
            echo 'Amazon URL: ' . $amazonUrl . '<br />';
        }
        $amazonUrl++;
        continue;
    }

    $product = fetchAmazonProduct($amazonUrl, $uploadPath);
    if (!$product) {
        if (DEVELOPMENT_MODE == true) {
            echo 'Product: ' . $product . '<br />';
        }
        $amazonProduct++;
        continue;
    }

    /* --------- ADDITIONS (MISSING VARS) --------- */
    $productTitle = $product['productName'];
    $productShortDescription = mysqli_real_escape_string($connection, $product['productShortDescription']);
    $productTags = $product['productTags'];
    $productSKU = uniqid('SKU');
    /* --------- END ADDITIONS --------- */

    /* --------------------------------------
               DATA PREPARATION (MYSQL SAFE)
            --------------------------------------- */
    $productName = mysqli_real_escape_string($connection, $product['productName']);
    $productDescription = mysqli_real_escape_string($connection, $product['productDescription']);
    $productFeatureImage = mysqli_real_escape_string($connection, $product['productFeatureImage']);

    $productImages = mysqli_real_escape_string(
        $connection,
        json_encode($product['productImages'], JSON_UNESCAPED_SLASHES)
    );

    $productSellingPrice = (float)$product['productSellingPrice'];
    $productMRP = (float)$product['productMRP'];

    /* --------------------------------------
               MYSQL LOGIC (UNCHANGED)
            --------------------------------------- */
    if ($checkProduct = mysqli_query($connection, "SELECT product_title FROM " . TABLE_PREFIX . "product WHERE product_title='{$productName}'")) {

        if (mysqli_num_rows($checkProduct) == 0) {

            mysqli_query($connection, "START TRANSACTION");

            $url = '/product' . strtolower($categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $productName)) . '/';

            if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {
                if (mysqli_num_rows($checkURL) != 0) {
                    $url = ltrim($url, '/') . '-' . $productSKU . '/';
                }
            }

            $addNewProduct = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "product(product_site_id, product_title, product_image, product_thumbnail, product_gallery, product_description, product_highlight, product_category, product_owner_id, product_status, product_type) VALUES(" . APP_SITE_ID . ", '{$productName}', '{$productFeatureImage}', '{$productFeatureImage}', '{$productImages}', '{$productDescription}', '{$productShortDescription}', '{$categoryId}', " . USER_ID . ",'1', 'product')");
            $productID = mysqli_insert_id($connection);

            $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$productID}', '200', 'product', '0', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
            $linkID = mysqli_insert_id($connection);

            $addNewInventory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "inventory(inventory_relation_id, inventory_site_id, inventory_sku, inventory_currency, inventory_price, inventory_mrp, inventory_tax_status, inventory_tax_rate, inventory_in_stock, inventory_current_stock, inventory_backorder, inventory_stock_notice, inventory_weight, inventory_lenght, inventory_width, inventory_height, inventory_status, inventory_update) VALUES ('{$productID}', " . APP_SITE_ID . ", '', 'INR', '{$productSellingPrice}', '{$productMRP}', '0', '0', '1', '0', '1', '10', '0', '0', '0', '0', '1', current_timestamp())");

            $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id, meta_type, meta_title, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . APP_SITE_ID . ",  'product', '{$productTitle}', '', '', '', '', '', '{$productTitle}', '', '', '{$productTitle}', '')");

            if ($productTags != '') {
                $tags = explode(',', $productTags);
                for ($t = 0; $t < sizeof($tags); $t++) {
                    $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', trim($tags[$t])));
                    $addNewTags = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$productID}', '" . USER_ID . "', '" . SITE_ID . "', 'product')");
                }
            } else {
                $addNewTags = TRUE;
            }

            if ($addNewProduct && $addNewLink && $addNewInventory && $addNewMeta && $addNewTags) {
                $success++;
                $msg = 'Product import successfully';
                mysqli_query($connection, "COMMIT");
            } else {
                $failed++;
                $msg = 'Something is wrong while adding new product';
                mysqli_query($connection, "ROLLBACK");
            }
        } else {
            $duplicate++;
            $msg = 'Products import successfully but some duplicate products found';
        }
    } else {
        $failed++;
        $msg = 'Something is wrong while checking product';
    }

    sleep(5);
}

fclose($file);


$responseData = array(
    'success' => $success,
    'failed' => $failed,
    'duplicate' => $duplicate
);