<?php

$plEsc = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

$plFormatPrice = static function ($amount): string {
    $num = is_numeric($amount) ? (float)$amount : 0.0;
    if (abs($num - round($num)) < 0.005) {
        return number_format($num, 0, '.', ',');
    }
    return number_format($num, 2, '.', ',');
};

$plSafeHref = static function ($url, string $fallback = '#'): string {
    $trimmed = trim((string)$url);
    if ($trimmed === '') {
        return $fallback;
    }
    if (preg_match('/^\s*(?:javascript|data|vbscript):/i', $trimmed)) {
        return $fallback;
    }
    if ($trimmed[0] !== '/' && !preg_match('#^https?://#i', $trimmed)) {
        return '/' . ltrim($trimmed, '/');
    }
    return $trimmed;
};

$plParseDateYmd = static function ($value): string {
    $raw = trim((string)$value);
    if ($raw === '') {
        return '';
    }
    $dt = DateTime::createFromFormat('Y-m-d', $raw);
    if ($dt && $dt->format('Y-m-d') === $raw) {
        return $raw;
    }
    return '';
};

$plRoot = defined('ROOT') ? ROOT : dirname(__FILE__, 6);
$plCssFile = $plRoot . '/lib/resource/ui/css/app/default/desktop/product-list.css';
$plJsFile = $plRoot . '/lib/resource/ui/js/app/default/desktop/product-list.js';
$plCssVer = file_exists($plCssFile) ? (string)filemtime($plCssFile) : '1';
$plJsVer = file_exists($plJsFile) ? (string)filemtime($plJsFile) : '1';
$plCssBase = defined('CSS_LIBRARY') ? CSS_LIBRARY : '/lib/resource/ui/css/';
$plJsBase = defined('JS_LIBRARY') ? JS_LIBRARY : '/lib/resource/ui/js/';

$plTablePrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$plSiteId = defined('SITE_ID') ? (int)SITE_ID : (isset($site_id) ? (int)$site_id : 0);

$plRawType = (isset($_GET['type']) && is_string($_GET['type'])) ? trim($_GET['type']) : 'list';
$plTypeParam = preg_replace('/[^a-zA-Z0-9_-]/', '', $plRawType);
if ($plTypeParam === '') {
    $plTypeParam = 'list';
}

$plRouteSlug = (defined('ADMIN_BASE_URL') && trim((string)ADMIN_BASE_URL, '/') !== '')
    ? trim((string)ADMIN_BASE_URL, '/')
    : 'product';
$plRouteBaseUrl = '/dashboard/' . $plRouteSlug . '/';
$plResetUrl = $plRouteBaseUrl . '?' . http_build_query(array('type' => $plTypeParam, 'status' => 'all'));
$plAddProductUrl = $plRouteBaseUrl . '?' . http_build_query(array('type' => 'add', 'editor' => 'true'));

$plFilterSearch = (isset($_GET['q']) && is_string($_GET['q'])) ? trim($_GET['q']) : '';
if (function_exists('mb_substr')) {
    $plFilterSearch = mb_substr($plFilterSearch, 0, 120, 'UTF-8');
} else {
    $plFilterSearch = substr($plFilterSearch, 0, 120);
}

$plFilterStatus = (isset($_GET['status']) && is_string($_GET['status'])) ? strtolower(trim($_GET['status'])) : 'all';
if (!in_array($plFilterStatus, array('all', '1', '0'), true)) {
    $plFilterStatus = 'all';
}

$plFilterCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
if ($plFilterCategory < 0) {
    $plFilterCategory = 0;
}

$plFilterBrand = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
if ($plFilterBrand < 0) {
    $plFilterBrand = 0;
}

$plFilterStock = (isset($_GET['stock']) && is_string($_GET['stock'])) ? strtolower(trim($_GET['stock'])) : 'all';
if (!in_array($plFilterStock, array('all', '1', '0'), true)) {
    $plFilterStock = 'all';
}

$plFilterFeatured = (isset($_GET['featured']) && is_string($_GET['featured'])) ? strtolower(trim($_GET['featured'])) : 'all';
if (!in_array($plFilterFeatured, array('all', '1', '0'), true)) {
    $plFilterFeatured = 'all';
}

$plFilterDiscount = (isset($_GET['discount']) && is_string($_GET['discount'])) ? strtolower(trim($_GET['discount'])) : 'all';
if (!in_array($plFilterDiscount, array('all', 'yes', 'no'), true)) {
    $plFilterDiscount = 'all';
}

$plFilterTag = (isset($_GET['tag']) && is_string($_GET['tag'])) ? trim($_GET['tag']) : '';
if (function_exists('mb_substr')) {
    $plFilterTag = mb_substr($plFilterTag, 0, 120, 'UTF-8');
} else {
    $plFilterTag = substr($plFilterTag, 0, 120);
}

$plRawMinPrice = (isset($_GET['min_price']) && is_string($_GET['min_price'])) ? trim($_GET['min_price']) : '';
$plRawMaxPrice = (isset($_GET['max_price']) && is_string($_GET['max_price'])) ? trim($_GET['max_price']) : '';
$plFilterMinPrice = (preg_match('/^\d+(?:\.\d{1,2})?$/', $plRawMinPrice) === 1) ? $plRawMinPrice : '';
$plFilterMaxPrice = (preg_match('/^\d+(?:\.\d{1,2})?$/', $plRawMaxPrice) === 1) ? $plRawMaxPrice : '';
if ($plFilterMinPrice !== '' && $plFilterMaxPrice !== '' && (float)$plFilterMinPrice > (float)$plFilterMaxPrice) {
    $plSwapPrice = $plFilterMinPrice;
    $plFilterMinPrice = $plFilterMaxPrice;
    $plFilterMaxPrice = $plSwapPrice;
}

$plFilterDateFrom = $plParseDateYmd(isset($_GET['date_from']) && is_string($_GET['date_from']) ? $_GET['date_from'] : '');
$plFilterDateTo = $plParseDateYmd(isset($_GET['date_to']) && is_string($_GET['date_to']) ? $_GET['date_to'] : '');
if ($plFilterDateFrom !== '' && $plFilterDateTo !== '' && $plFilterDateFrom > $plFilterDateTo) {
    $plSwapDate = $plFilterDateFrom;
    $plFilterDateFrom = $plFilterDateTo;
    $plFilterDateTo = $plSwapDate;
}

$plSortMap = array(
    'newest' => array(
        'label' => 'Newest first',
        'sql' => 'effective_publish_date DESC, p.product_id DESC',
        'dt_col' => 5,
        'dt_dir' => 'desc',
    ),
    'oldest' => array(
        'label' => 'Oldest first',
        'sql' => 'effective_publish_date ASC, p.product_id ASC',
        'dt_col' => 5,
        'dt_dir' => 'asc',
    ),
    'price_asc' => array(
        'label' => 'Selling Price: Low to High',
        'sql' => 'IFNULL(i.inventory_price, 0) ASC, p.product_id DESC',
        'dt_col' => 3,
        'dt_dir' => 'asc',
    ),
    'price_desc' => array(
        'label' => 'Selling Price: High to Low',
        'sql' => 'IFNULL(i.inventory_price, 0) DESC, p.product_id DESC',
        'dt_col' => 3,
        'dt_dir' => 'desc',
    ),
    'title_asc' => array(
        'label' => 'Title: A to Z',
        'sql' => 'LOWER(p.product_title) ASC, p.product_id DESC',
        'dt_col' => 0,
        'dt_dir' => 'asc',
    ),
    'title_desc' => array(
        'label' => 'Title: Z to A',
        'sql' => 'LOWER(p.product_title) DESC, p.product_id DESC',
        'dt_col' => 0,
        'dt_dir' => 'desc',
    ),
);

$plFilterSort = (isset($_GET['sort']) && is_string($_GET['sort'])) ? strtolower(trim($_GET['sort'])) : 'newest';
if (!isset($plSortMap[$plFilterSort])) {
    $plFilterSort = 'newest';
}
$plSortMeta = $plSortMap[$plFilterSort];

// Fetch site's primary currency symbol for price filter hints and chips
$plSiteCurrency = '₹';
$plCurrencySql = "SELECT i.inventory_currency_symbol
    FROM {$plTablePrefix}inventory i
    INNER JOIN {$plTablePrefix}link l
        ON l.link_relation_id = i.inventory_relation_id
        AND l.link_type = 'product'
        AND l.link_site_id = {$plSiteId}
        AND l.link_status != 301
    WHERE i.inventory_status = 1
      AND i.inventory_type = 'product'
      AND TRIM(IFNULL(i.inventory_currency_symbol, '')) != ''
    LIMIT 1";
if ($plCurrencyRes = mysqli_query($connection, $plCurrencySql)) {
    if ($plCurrencyRow = mysqli_fetch_assoc($plCurrencyRes)) {
        $plSiteCurrency = trim((string)$plCurrencyRow['inventory_currency_symbol']) ?: '₹';
    }
    mysqli_free_result($plCurrencyRes);
}

// Fetch active product categories for current site and resolve parent > child hierarchy
$plCategoryNodes = array();
$plChildrenByParent = array();
$plCategorySql = "SELECT c.category_id, c.category_parent_id, c.category_title,
        COUNT(DISTINCT CASE
            WHEN p.product_id IS NOT NULL
                 AND l.link_id IS NOT NULL
                 AND i.inventory_id IS NOT NULL
            THEN p.product_id
            ELSE NULL
        END) AS direct_count
    FROM {$plTablePrefix}category c
    LEFT JOIN {$plTablePrefix}product p
        ON p.product_category = c.category_id
        AND p.product_type = 'product'
    LEFT JOIN {$plTablePrefix}link l
        ON l.link_relation_id = p.product_id
        AND l.link_type = 'product'
        AND l.link_site_id = {$plSiteId}
        AND l.link_status != 301
    LEFT JOIN {$plTablePrefix}inventory i
        ON i.inventory_relation_id = p.product_id
        AND i.inventory_status = 1
        AND i.inventory_type = 'product'
    WHERE c.category_site_id = {$plSiteId}
      AND c.category_type = 'product'
      AND c.category_status = '1'
    GROUP BY c.category_id, c.category_parent_id, c.category_title
    ORDER BY c.category_title ASC";
if ($plCatRes = mysqli_query($connection, $plCategorySql)) {
    while ($plCatRow = mysqli_fetch_assoc($plCatRes)) {
        $plCatId = (int)$plCatRow['category_id'];
        $plParentId = (int)$plCatRow['category_parent_id'];
        $plCategoryNodes[$plCatId] = array(
            'id' => $plCatId,
            'parent_id' => $plParentId,
            'title' => trim((string)$plCatRow['category_title']),
            'direct_count' => (int)$plCatRow['direct_count'],
        );
        if (!isset($plChildrenByParent[$plParentId])) {
            $plChildrenByParent[$plParentId] = array();
        }
        $plChildrenByParent[$plParentId][] = $plCatId;
    }
    mysqli_free_result($plCatRes);
}

$plCollectCategoryTreeIds = static function (int $rootId) use (&$plChildrenByParent): array {
    $ids = array();
    $queue = array($rootId);
    $visited = array();
    while (!empty($queue)) {
        $current = array_shift($queue);
        if (isset($visited[$current])) {
            continue;
        }
        $visited[$current] = true;
        $ids[] = $current;
        if (!empty($plChildrenByParent[$current])) {
            foreach ($plChildrenByParent[$current] as $childId) {
                if (!isset($visited[$childId])) {
                    $queue[] = $childId;
                }
            }
        }
    }
    return $ids;
};

$plResolveCategoryPath = static function (int $catId) use (&$plCategoryNodes): string {
    $parts = array();
    $cursor = $catId;
    $guard = 0;
    $visited = array();
    while ($cursor > 0 && isset($plCategoryNodes[$cursor]) && !isset($visited[$cursor]) && $guard < 12) {
        $visited[$cursor] = true;
        array_unshift($parts, $plCategoryNodes[$cursor]['title']);
        $cursor = (int)$plCategoryNodes[$cursor]['parent_id'];
        $guard++;
    }
    return implode(' > ', $parts);
};

$plCategoryOptions = array();
foreach ($plCategoryNodes as $plCatId => $plNode) {
    $plTreeIds = $plCollectCategoryTreeIds($plCatId);
    $plSubtreeCount = 0;
    foreach ($plTreeIds as $plMemberId) {
        if (isset($plCategoryNodes[$plMemberId])) {
            $plSubtreeCount += (int)$plCategoryNodes[$plMemberId]['direct_count'];
        }
    }
    $plCategoryOptions[$plCatId] = array(
        'id' => $plCatId,
        'title' => $plNode['title'],
        'path' => $plResolveCategoryPath($plCatId),
        'tree_ids' => $plTreeIds,
        'subtree_count' => $plSubtreeCount,
    );
}
uasort($plCategoryOptions, static function ($a, $b) {
    return strcasecmp($a['path'], $b['path']);
});

if ($plFilterCategory > 0 && !isset($plCategoryOptions[$plFilterCategory])) {
    $plFilterCategory = 0;
}

// Fetch active brands for current site (shown only if site defines brands)
$plBrandOptions = array();
$plBrandSql = "SELECT c.category_id, c.category_title,
        COUNT(DISTINCT CASE
            WHEN p.product_id IS NOT NULL
                 AND l.link_id IS NOT NULL
                 AND i.inventory_id IS NOT NULL
            THEN p.product_id
            ELSE NULL
        END) AS product_count
    FROM {$plTablePrefix}category c
    LEFT JOIN {$plTablePrefix}product p
        ON p.product_brand_id = c.category_id
        AND p.product_type = 'product'
    LEFT JOIN {$plTablePrefix}link l
        ON l.link_relation_id = p.product_id
        AND l.link_type = 'product'
        AND l.link_site_id = {$plSiteId}
        AND l.link_status != 301
    LEFT JOIN {$plTablePrefix}inventory i
        ON i.inventory_relation_id = p.product_id
        AND i.inventory_status = 1
        AND i.inventory_type = 'product'
    WHERE c.category_site_id = {$plSiteId}
      AND c.category_type = 'brand'
      AND c.category_status = '1'
    GROUP BY c.category_id, c.category_title
    ORDER BY c.category_title ASC";
if ($plBrandRes = mysqli_query($connection, $plBrandSql)) {
    while ($plBrandRow = mysqli_fetch_assoc($plBrandRes)) {
        $plBrandId = (int)$plBrandRow['category_id'];
        $plBrandOptions[$plBrandId] = array(
            'id' => $plBrandId,
            'title' => trim((string)$plBrandRow['category_title']),
            'product_count' => (int)$plBrandRow['product_count'],
        );
    }
    mysqli_free_result($plBrandRes);
}

if ($plFilterBrand > 0 && !isset($plBrandOptions[$plFilterBrand])) {
    $plFilterBrand = 0;
}

// Fetch popular product tags for datalist suggestions
$plTagSuggestions = array();
$plTagSql = "SELECT MIN(TRIM(t.tag_title)) AS tag_title, COUNT(DISTINCT t.tag_relation_id) AS tag_count
    FROM {$plTablePrefix}tag t
    INNER JOIN {$plTablePrefix}product p
        ON (CASE WHEN p.product_parent_id = 0 THEN p.product_id ELSE p.product_parent_id END) = t.tag_relation_id
    INNER JOIN {$plTablePrefix}link l
        ON l.link_relation_id = p.product_id
        AND l.link_type = 'product'
        AND l.link_site_id = {$plSiteId}
        AND l.link_status != 301
    WHERE t.tag_site_id = {$plSiteId}
      AND t.tag_type = 'product'
      AND p.product_type = 'product'
      AND TRIM(IFNULL(t.tag_title, '')) != ''
    GROUP BY LOWER(TRIM(t.tag_title))
    ORDER BY tag_count DESC, tag_title ASC
    LIMIT 60";
if ($plTagRes = mysqli_query($connection, $plTagSql)) {
    while ($plTagRow = mysqli_fetch_assoc($plTagRes)) {
        $plTagSuggestions[] = (string)$plTagRow['tag_title'];
    }
    mysqli_free_result($plTagRes);
}

// Base FROM + WHERE clause shared by total count and filtered query
$plBaseFromWhere = " FROM {$plTablePrefix}product p
    LEFT JOIN {$plTablePrefix}category c ON c.category_id = p.product_category
    INNER JOIN {$plTablePrefix}link l ON l.link_relation_id = p.product_id
    INNER JOIN {$plTablePrefix}inventory i ON i.inventory_relation_id = p.product_id
    WHERE l.link_type = 'product'
      AND p.product_type = 'product'
      AND l.link_site_id = {$plSiteId}
      AND l.link_status != 301
      AND i.inventory_status = 1
      AND i.inventory_type = 'product'";

$plTotalProducts = 0;
if ($plTotalRes = mysqli_query($connection, "SELECT COUNT(*) AS total_count" . $plBaseFromWhere)) {
    if ($plTotalRow = mysqli_fetch_assoc($plTotalRes)) {
        $plTotalProducts = (int)$plTotalRow['total_count'];
    }
    mysqli_free_result($plTotalRes);
}

$plEffectivePublishExpr = "CASE WHEN l.link_published IS NULL OR l.link_published < '1971-01-01 00:00:00' THEN l.link_creation ELSE l.link_published END";

$plConditions = array();

if ($plFilterSearch !== '') {
    $plLikeTerm = str_replace(array('|', '%', '_'), array('||', '|%', '|_'), strtolower($plFilterSearch));
    $plEscapedLike = mysqli_real_escape_string($connection, $plLikeTerm);
    $plConditions[] = "(LOWER(p.product_title) LIKE '%{$plEscapedLike}%' ESCAPE '|' OR LOWER(IFNULL(i.inventory_sku, '')) LIKE '%{$plEscapedLike}%' ESCAPE '|')";
}

if ($plFilterStatus === '1') {
    $plConditions[] = "p.product_status = '1' AND l.link_status = '200'";
} elseif ($plFilterStatus === '0') {
    $plConditions[] = "(p.product_status = '0' OR l.link_status != '200')";
}

if ($plFilterCategory > 0 && isset($plCategoryOptions[$plFilterCategory])) {
    $plSelectedCatIds = array_map('intval', $plCategoryOptions[$plFilterCategory]['tree_ids']);
    $plConditions[] = "p.product_category IN (" . implode(',', $plSelectedCatIds) . ")";
}

if ($plFilterBrand > 0) {
    $plConditions[] = "p.product_brand_id = " . (int)$plFilterBrand;
}

if ($plFilterStock === '1') {
    $plConditions[] = "IFNULL(i.inventory_in_stock, 0) = 1";
} elseif ($plFilterStock === '0') {
    $plConditions[] = "IFNULL(i.inventory_in_stock, 0) != 1";
}

if ($plFilterFeatured === '1') {
    $plConditions[] = "IFNULL(l.link_is_featured, 0) = 1";
} elseif ($plFilterFeatured === '0') {
    $plConditions[] = "IFNULL(l.link_is_featured, 0) != 1";
}

if ($plFilterDiscount === 'yes') {
    $plConditions[] = "(IFNULL(i.inventory_mrp, 0) > 0 AND IFNULL(i.inventory_price, 0) > 0 AND i.inventory_mrp > i.inventory_price)";
} elseif ($plFilterDiscount === 'no') {
    $plConditions[] = "(IFNULL(i.inventory_mrp, 0) <= 0 OR IFNULL(i.inventory_price, 0) <= 0 OR i.inventory_mrp <= i.inventory_price)";
}

if ($plFilterTag !== '') {
    $plLikeTag = str_replace(array('|', '%', '_'), array('||', '|%', '|_'), strtolower($plFilterTag));
    $plEscapedTag = mysqli_real_escape_string($connection, $plLikeTag);
    $plConditions[] = "EXISTS (
        SELECT 1
        FROM {$plTablePrefix}tag t
        WHERE t.tag_relation_id = CASE WHEN p.product_parent_id = 0 THEN p.product_id ELSE p.product_parent_id END
          AND t.tag_site_id = {$plSiteId}
          AND t.tag_type = 'product'
          AND LOWER(TRIM(IFNULL(t.tag_title, ''))) LIKE '%{$plEscapedTag}%' ESCAPE '|'
    )";
}

if ($plFilterMinPrice !== '') {
    $plConditions[] = "IFNULL(i.inventory_price, 0) >= " . number_format((float)$plFilterMinPrice, 2, '.', '');
}

if ($plFilterMaxPrice !== '') {
    $plConditions[] = "IFNULL(i.inventory_price, 0) <= " . number_format((float)$plFilterMaxPrice, 2, '.', '');
}

if ($plFilterDateFrom !== '') {
    $plEscDateFrom = mysqli_real_escape_string($connection, $plFilterDateFrom . ' 00:00:00');
    $plConditions[] = "{$plEffectivePublishExpr} >= '{$plEscDateFrom}'";
}

if ($plFilterDateTo !== '') {
    $plEscDateTo = mysqli_real_escape_string($connection, $plFilterDateTo . ' 23:59:59');
    $plConditions[] = "{$plEffectivePublishExpr} <= '{$plEscDateTo}'";
}

$plExtraWhere = !empty($plConditions) ? (' AND ' . implode(' AND ', $plConditions)) : '';

$plProductsSql = "SELECT
        p.product_id,
        p.product_title,
        p.product_image,
        p.product_owner_id,
        p.product_status,
        IFNULL(c.category_title, 'Uncategorized') AS category_title,
        l.link_url,
        l.link_status,
        l.link_is_featured,
        l.link_creation,
        l.link_published,
        {$plEffectivePublishExpr} AS effective_publish_date,
        i.inventory_currency_symbol,
        i.inventory_in_stock,
        IFNULL(i.inventory_sku, '') AS inventory_sku,
        IFNULL(i.inventory_mrp, 0) AS inventory_mrp,
        IFNULL(i.inventory_price, 0) AS inventory_price
    {$plBaseFromWhere}
    {$plExtraWhere}
    ORDER BY {$plSortMeta['sql']}";

$plRows = array();
if ($plDataList = mysqli_query($connection, $plProductsSql)) {
    while ($plRow = mysqli_fetch_assoc($plDataList)) {
        $plRows[] = $plRow;
    }
    mysqli_free_result($plDataList);
}
$plMatchedCount = count($plRows);

// Track active advanced filters & build removable chips
$plAdvancedActiveCount = 0;
if ($plFilterFeatured !== 'all') {
    $plAdvancedActiveCount++;
}
if ($plFilterDiscount !== 'all') {
    $plAdvancedActiveCount++;
}
if ($plFilterTag !== '') {
    $plAdvancedActiveCount++;
}
if ($plFilterMinPrice !== '' || $plFilterMaxPrice !== '') {
    $plAdvancedActiveCount++;
}
if ($plFilterDateFrom !== '' || $plFilterDateTo !== '') {
    $plAdvancedActiveCount++;
}
$plShowAdvanced = ($plAdvancedActiveCount > 0);

$plCurrentParams = array('type' => $plTypeParam);
if ($plFilterSearch !== '') {
    $plCurrentParams['q'] = $plFilterSearch;
}
if ($plFilterStatus !== 'all') {
    $plCurrentParams['status'] = $plFilterStatus;
}
if ($plFilterCategory > 0) {
    $plCurrentParams['category'] = (string)$plFilterCategory;
}
if ($plFilterBrand > 0) {
    $plCurrentParams['brand'] = (string)$plFilterBrand;
}
if ($plFilterStock !== 'all') {
    $plCurrentParams['stock'] = $plFilterStock;
}
if ($plFilterFeatured !== 'all') {
    $plCurrentParams['featured'] = $plFilterFeatured;
}
if ($plFilterDiscount !== 'all') {
    $plCurrentParams['discount'] = $plFilterDiscount;
}
if ($plFilterTag !== '') {
    $plCurrentParams['tag'] = $plFilterTag;
}
if ($plFilterMinPrice !== '') {
    $plCurrentParams['min_price'] = $plFilterMinPrice;
}
if ($plFilterMaxPrice !== '') {
    $plCurrentParams['max_price'] = $plFilterMaxPrice;
}
if ($plFilterDateFrom !== '') {
    $plCurrentParams['date_from'] = $plFilterDateFrom;
}
if ($plFilterDateTo !== '') {
    $plCurrentParams['date_to'] = $plFilterDateTo;
}
if ($plFilterSort !== 'newest') {
    $plCurrentParams['sort'] = $plFilterSort;
}

$plBuildRemoveChipUrl = static function (string $keyToReset, string $resetValue = '') use ($plRouteBaseUrl, $plCurrentParams): string {
    $params = $plCurrentParams;
    if ($resetValue === '') {
        unset($params[$keyToReset]);
    } else {
        $params[$keyToReset] = $resetValue;
    }
    if (!isset($params['status'])) {
        $params['status'] = 'all';
    }
    return $plRouteBaseUrl . '?' . http_build_query($params);
};

$plActiveChips = array();
if ($plFilterSearch !== '') {
    $plActiveChips[] = array(
        'label' => 'Search: "' . $plFilterSearch . '"',
        'remove_url' => $plBuildRemoveChipUrl('q'),
        'remove_aria' => 'Remove search filter',
    );
}
if ($plFilterStatus !== 'all') {
    $plActiveChips[] = array(
        'label' => 'Status: ' . ($plFilterStatus === '1' ? 'Published' : 'Draft'),
        'remove_url' => $plBuildRemoveChipUrl('status', 'all'),
        'remove_aria' => 'Remove status filter',
    );
}
if ($plFilterCategory > 0 && isset($plCategoryOptions[$plFilterCategory])) {
    $plActiveChips[] = array(
        'label' => 'Category: ' . $plCategoryOptions[$plFilterCategory]['path'],
        'remove_url' => $plBuildRemoveChipUrl('category'),
        'remove_aria' => 'Remove category filter',
    );
}
if ($plFilterBrand > 0 && isset($plBrandOptions[$plFilterBrand])) {
    $plActiveChips[] = array(
        'label' => 'Brand: ' . $plBrandOptions[$plFilterBrand]['title'],
        'remove_url' => $plBuildRemoveChipUrl('brand'),
        'remove_aria' => 'Remove brand filter',
    );
}
if ($plFilterStock !== 'all') {
    $plActiveChips[] = array(
        'label' => 'Stock: ' . ($plFilterStock === '1' ? 'In Stock' : 'Out of Stock'),
        'remove_url' => $plBuildRemoveChipUrl('stock'),
        'remove_aria' => 'Remove stock filter',
    );
}
if ($plFilterFeatured !== 'all') {
    $plActiveChips[] = array(
        'label' => 'Visibility: ' . ($plFilterFeatured === '1' ? 'Featured only' : 'Standard only'),
        'remove_url' => $plBuildRemoveChipUrl('featured'),
        'remove_aria' => 'Remove featured filter',
    );
}
if ($plFilterDiscount !== 'all') {
    $plActiveChips[] = array(
        'label' => 'Discount: ' . ($plFilterDiscount === 'yes' ? 'On discount' : 'No discount'),
        'remove_url' => $plBuildRemoveChipUrl('discount'),
        'remove_aria' => 'Remove discount filter',
    );
}
if ($plFilterTag !== '') {
    $plActiveChips[] = array(
        'label' => 'Tag: ' . $plFilterTag,
        'remove_url' => $plBuildRemoveChipUrl('tag'),
        'remove_aria' => 'Remove tag filter',
    );
}
if ($plFilterMinPrice !== '') {
    $plActiveChips[] = array(
        'label' => 'Min price: ' . $plSiteCurrency . $plFormatPrice($plFilterMinPrice),
        'remove_url' => $plBuildRemoveChipUrl('min_price'),
        'remove_aria' => 'Remove minimum price filter',
    );
}
if ($plFilterMaxPrice !== '') {
    $plActiveChips[] = array(
        'label' => 'Max price: ' . $plSiteCurrency . $plFormatPrice($plFilterMaxPrice),
        'remove_url' => $plBuildRemoveChipUrl('max_price'),
        'remove_aria' => 'Remove maximum price filter',
    );
}
if ($plFilterDateFrom !== '') {
    $plActiveChips[] = array(
        'label' => 'Published from: ' . $plFilterDateFrom,
        'remove_url' => $plBuildRemoveChipUrl('date_from'),
        'remove_aria' => 'Remove start date filter',
    );
}
if ($plFilterDateTo !== '') {
    $plActiveChips[] = array(
        'label' => 'Published to: ' . $plFilterDateTo,
        'remove_url' => $plBuildRemoveChipUrl('date_to'),
        'remove_aria' => 'Remove end date filter',
    );
}
if ($plFilterSort !== 'newest') {
    $plActiveChips[] = array(
        'label' => 'Sort: ' . $plSortMeta['label'],
        'remove_url' => $plBuildRemoveChipUrl('sort'),
        'remove_aria' => 'Reset sort order to newest first',
    );
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo $plEsc($plCssBase . 'app/default/desktop/product-list.css?v=' . $plCssVer); ?>">
<script defer src="<?php echo $plEsc($plJsBase . 'app/default/desktop/product-list.js?v=' . $plJsVer); ?>"></script>

<div class="modern-product-list">
    <div class="product-list-header">
        <h2 id="productListHeading">All Products</h2>
        <a href="<?php echo $plEsc($plAddProductUrl); ?>" class="product-add-link">
            <i class="fas fa-plus" aria-hidden="true"></i>
            <span>Add New Product</span>
        </a>
    </div>

    <div class="product-list-panel">
        <search>
            <section class="product-filter-card" aria-labelledby="productFiltersHeading">
                <h3 id="productFiltersHeading" class="visually-hidden">Filter products</h3>
                <form id="productFilterForm" class="product-filter-form" method="GET" action="<?php echo $plEsc($plRouteBaseUrl); ?>" aria-labelledby="productFiltersHeading">
                    <input type="hidden" name="type" value="<?php echo $plEsc($plTypeParam); ?>">

                    <div class="filter-primary-grid">
                        <div class="filter-field filter-field-search">
                            <label for="filterSearch">Search products</label>
                            <input
                                type="search"
                                id="filterSearch"
                                name="q"
                                class="filter-control"
                                value="<?php echo $plEsc($plFilterSearch); ?>"
                                placeholder="Search by product title or SKU"
                                autocomplete="off"
                                autocorrect="off"
                                autocapitalize="none"
                                spellcheck="false"
                                enterkeyhint="search"
                                maxlength="120"
                                aria-describedby="filterSearchHint">
                            <span id="filterSearchHint" class="filter-hint">Matches product title or SKU code.</span>
                        </div>

                        <div class="filter-field">
                            <label for="filterCategory">Category</label>
                            <select id="filterCategory" name="category" class="filter-control">
                                <option value="0" <?php echo ($plFilterCategory === 0) ? 'selected' : ''; ?>>All categories</option>
                                <?php foreach ($plCategoryOptions as $plCatOpt) { ?>
                                    <option value="<?php echo (int)$plCatOpt['id']; ?>" <?php echo ($plFilterCategory === (int)$plCatOpt['id']) ? 'selected' : ''; ?>>
                                        <?php echo $plEsc($plCatOpt['path'] . ' (' . (int)$plCatOpt['subtree_count'] . ')'); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <?php if (!empty($plBrandOptions)) { ?>
                            <div class="filter-field">
                                <label for="filterBrand">Brand</label>
                                <select id="filterBrand" name="brand" class="filter-control">
                                    <option value="0" <?php echo ($plFilterBrand === 0) ? 'selected' : ''; ?>>All brands</option>
                                    <?php foreach ($plBrandOptions as $plBrandOpt) { ?>
                                        <option value="<?php echo (int)$plBrandOpt['id']; ?>" <?php echo ($plFilterBrand === (int)$plBrandOpt['id']) ? 'selected' : ''; ?>>
                                            <?php echo $plEsc($plBrandOpt['title'] . ' (' . (int)$plBrandOpt['product_count'] . ')'); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>

                        <div class="filter-field">
                            <label for="filterSort">Sort by</label>
                            <select id="filterSort" name="sort" class="filter-control">
                                <?php foreach ($plSortMap as $plSortKey => $plSortInfo) { ?>
                                    <option value="<?php echo $plEsc($plSortKey); ?>" <?php echo ($plFilterSort === $plSortKey) ? 'selected' : ''; ?>>
                                        <?php echo $plEsc($plSortInfo['label']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <fieldset class="filter-fieldset filter-fieldset-wide">
                            <legend class="filter-legend">Status</legend>
                            <div class="filter-radio-group">
                                <div class="filter-radio-option">
                                    <input type="radio" id="filterStatusAll" name="status" value="all" <?php echo ($plFilterStatus === 'all') ? 'checked' : ''; ?>>
                                    <label for="filterStatusAll">All</label>
                                </div>
                                <div class="filter-radio-option">
                                    <input type="radio" id="filterStatusPublished" name="status" value="1" <?php echo ($plFilterStatus === '1') ? 'checked' : ''; ?>>
                                    <label for="filterStatusPublished">Published</label>
                                </div>
                                <div class="filter-radio-option">
                                    <input type="radio" id="filterStatusDraft" name="status" value="0" <?php echo ($plFilterStatus === '0') ? 'checked' : ''; ?>>
                                    <label for="filterStatusDraft">Draft</label>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="filter-fieldset filter-fieldset-wide">
                            <legend class="filter-legend">Stock availability</legend>
                            <div class="filter-radio-group">
                                <div class="filter-radio-option">
                                    <input type="radio" id="filterStockAll" name="stock" value="all" <?php echo ($plFilterStock === 'all') ? 'checked' : ''; ?>>
                                    <label for="filterStockAll">All</label>
                                </div>
                                <div class="filter-radio-option">
                                    <input type="radio" id="filterStockIn" name="stock" value="1" <?php echo ($plFilterStock === '1') ? 'checked' : ''; ?>>
                                    <label for="filterStockIn">In Stock</label>
                                </div>
                                <div class="filter-radio-option">
                                    <input type="radio" id="filterStockOut" name="stock" value="0" <?php echo ($plFilterStock === '0') ? 'checked' : ''; ?>>
                                    <label for="filterStockOut">Out of Stock</label>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div id="advancedProductFilters" class="advanced-filters-panel" <?php echo $plShowAdvanced ? '' : 'hidden'; ?>>
                        <fieldset class="advanced-filters-fieldset">
                            <legend>Advanced filters</legend>
                            <div class="filter-advanced-grid">
                                <div class="filter-field">
                                    <label for="filterTag">Product tag</label>
                                    <input
                                        type="text"
                                        id="filterTag"
                                        name="tag"
                                        list="productTagOptions"
                                        class="filter-control"
                                        value="<?php echo $plEsc($plFilterTag); ?>"
                                        placeholder="Choose or type a tag"
                                        autocomplete="off"
                                        autocorrect="off"
                                        autocapitalize="none"
                                        spellcheck="false"
                                        enterkeyhint="search"
                                        maxlength="120"
                                        aria-describedby="filterTagHint">
                                    <span id="filterTagHint" class="filter-hint">Partial or exact match on assigned product tags.</span>
                                    <datalist id="productTagOptions">
                                        <?php foreach ($plTagSuggestions as $plTagVal) { ?>
                                            <option value="<?php echo $plEsc($plTagVal); ?>"></option>
                                        <?php } ?>
                                    </datalist>
                                </div>

                                <fieldset class="filter-fieldset filter-fieldset-wide">
                                    <legend class="filter-legend">Visibility</legend>
                                    <div class="filter-radio-group">
                                        <div class="filter-radio-option">
                                            <input type="radio" id="filterFeaturedAll" name="featured" value="all" <?php echo ($plFilterFeatured === 'all') ? 'checked' : ''; ?>>
                                            <label for="filterFeaturedAll">All</label>
                                        </div>
                                        <div class="filter-radio-option">
                                            <input type="radio" id="filterFeaturedOnly" name="featured" value="1" <?php echo ($plFilterFeatured === '1') ? 'checked' : ''; ?>>
                                            <label for="filterFeaturedOnly">Featured only</label>
                                        </div>
                                        <div class="filter-radio-option">
                                            <input type="radio" id="filterFeaturedStandard" name="featured" value="0" <?php echo ($plFilterFeatured === '0') ? 'checked' : ''; ?>>
                                            <label for="filterFeaturedStandard">Standard only</label>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="filter-fieldset filter-fieldset-wide">
                                    <legend class="filter-legend">Pricing &amp; discount</legend>
                                    <div class="filter-radio-group">
                                        <div class="filter-radio-option">
                                            <input type="radio" id="filterDiscountAll" name="discount" value="all" <?php echo ($plFilterDiscount === 'all') ? 'checked' : ''; ?>>
                                            <label for="filterDiscountAll">All</label>
                                        </div>
                                        <div class="filter-radio-option">
                                            <input type="radio" id="filterDiscountYes" name="discount" value="yes" <?php echo ($plFilterDiscount === 'yes') ? 'checked' : ''; ?>>
                                            <label for="filterDiscountYes">On discount</label>
                                        </div>
                                        <div class="filter-radio-option">
                                            <input type="radio" id="filterDiscountNo" name="discount" value="no" <?php echo ($plFilterDiscount === 'no') ? 'checked' : ''; ?>>
                                            <label for="filterDiscountNo">No discount</label>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="filter-fieldset filter-fieldset-wide">
                                    <legend class="filter-legend">Selling price range (<?php echo $plEsc($plSiteCurrency); ?>)</legend>
                                    <div class="filter-range-group">
                                        <div class="filter-field">
                                            <label for="filterMinPrice">Min price</label>
                                            <input
                                                type="text"
                                                id="filterMinPrice"
                                                name="min_price"
                                                class="filter-control"
                                                value="<?php echo $plEsc($plFilterMinPrice); ?>"
                                                inputmode="decimal"
                                                placeholder="e.g. 500"
                                                autocorrect="off"
                                                autocapitalize="none"
                                                spellcheck="false"
                                                enterkeyhint="next"
                                                aria-describedby="filterPriceHint">
                                        </div>
                                        <div class="filter-field">
                                            <label for="filterMaxPrice">Max price</label>
                                            <input
                                                type="text"
                                                id="filterMaxPrice"
                                                name="max_price"
                                                class="filter-control"
                                                value="<?php echo $plEsc($plFilterMaxPrice); ?>"
                                                inputmode="decimal"
                                                placeholder="e.g. 5000"
                                                autocorrect="off"
                                                autocapitalize="none"
                                                spellcheck="false"
                                                enterkeyhint="done"
                                                aria-describedby="filterPriceHint">
                                        </div>
                                    </div>
                                    <span id="filterPriceHint" class="filter-hint">Numeric values only (up to 2 decimals).</span>
                                </fieldset>

                                <fieldset class="filter-fieldset filter-fieldset-wide">
                                    <legend class="filter-legend">Publish date range</legend>
                                    <div class="filter-range-group">
                                        <div class="filter-field">
                                            <label for="filterDateFrom">From date</label>
                                            <input
                                                type="date"
                                                id="filterDateFrom"
                                                name="date_from"
                                                class="filter-control"
                                                value="<?php echo $plEsc($plFilterDateFrom); ?>">
                                        </div>
                                        <div class="filter-field">
                                            <label for="filterDateTo">To date</label>
                                            <input
                                                type="date"
                                                id="filterDateTo"
                                                name="date_to"
                                                class="filter-control"
                                                value="<?php echo $plEsc($plFilterDateTo); ?>">
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </fieldset>
                    </div>

                    <div id="productFilterFeedback" class="filter-form-feedback" role="status" aria-live="polite"></div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-filter" aria-hidden="true"></i>
                            <span>Apply Filters</span>
                        </button>
                        <button
                            type="button"
                            id="toggleAdvancedFilters"
                            class="btn-filter-more"
                            aria-expanded="<?php echo $plShowAdvanced ? 'true' : 'false'; ?>"
                            aria-controls="advancedProductFilters">
                            <i class="fas fa-sliders-h" aria-hidden="true"></i>
                            <span
                                data-toggle-label
                                data-label-closed="More Filters"
                                data-label-open="Less Filters"><?php echo $plShowAdvanced ? 'Less Filters' : 'More Filters'; ?></span>
                            <?php if ($plAdvancedActiveCount > 0) { ?>
                                <span class="advanced-filter-badge" aria-label="<?php echo (int)$plAdvancedActiveCount; ?> active advanced filters"><?php echo (int)$plAdvancedActiveCount; ?></span>
                            <?php } ?>
                        </button>
                        <a href="<?php echo $plEsc($plResetUrl); ?>" class="btn-filter-reset">
                            <i class="fas fa-undo-alt" aria-hidden="true"></i>
                            <span>Reset</span>
                        </a>
                    </div>
                </form>
            </section>
        </search>

        <div class="filter-status-bar">
            <p class="filter-result-summary">
                Showing <strong><?php echo (int)$plMatchedCount; ?></strong> of <strong><?php echo (int)$plTotalProducts; ?></strong> product(s)
            </p>
            <?php if (!empty($plActiveChips)) { ?>
                <div class="active-filter-chips" aria-label="Active product filters">
                    <?php foreach ($plActiveChips as $plChip) { ?>
                        <a
                            href="<?php echo $plEsc($plChip['remove_url']); ?>"
                            class="active-filter-chip"
                            aria-label="<?php echo $plEsc($plChip['remove_aria'] . ' (' . $plChip['label'] . ')'); ?>">
                            <span><?php echo $plEsc($plChip['label']); ?></span>
                            <span aria-hidden="true">&times;</span>
                        </a>
                    <?php } ?>
                    <a href="<?php echo $plEsc($plResetUrl); ?>" class="active-filter-clear-all">Clear all</a>
                </div>
            <?php } ?>
        </div>

        <?php if ($plMatchedCount === 0) { ?>
            <div class="empty-filter-banner" role="status">
                <span>No products matched the selected filter criteria.</span>
                <?php if (!empty($plActiveChips)) { ?>
                    <a href="<?php echo $plEsc($plResetUrl); ?>" class="btn-filter-reset">Reset Filters</a>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="dt-controls-top" id="productTableControlsTop"></div>

        <div class="table-container-shell">
        <div class="table-wrapper" tabindex="0" role="region" aria-labelledby="productListHeading">
            <table
                id="table"
                data-default-order-column="<?php echo (int)$plSortMeta['dt_col']; ?>"
                data-default-order-dir="<?php echo $plEsc($plSortMeta['dt_dir']); ?>"
                data-page-length="10"
                data-empty-message="No products matched the selected filters.">
                <thead>
                    <tr>
                        <th class="col-product" scope="col">Product</th>
                        <th class="col-category" scope="col">Category</th>
                        <th class="col-stock" scope="col">Stock</th>
                        <th class="col-price" scope="col">Selling Price</th>
                        <th class="col-price" scope="col">MRP</th>
                        <th class="col-date" scope="col">Publish date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($plRows as $plRow) {
                        $plProductId = (int)$plRow['product_id'];
                        $plEncodedId = base64_encode((string)$plProductId);
                        $plProductTitle = trim((string)$plRow['product_title']) !== '' ? (string)$plRow['product_title'] : 'Untitled product';
                        $plSku = trim((string)($plRow['inventory_sku'] ?? ''));
                        $plCategoryTitle = trim((string)($plRow['category_title'] ?? ''));
                        if ($plCategoryTitle === '') {
                            $plCategoryTitle = 'Uncategorized';
                        }

                        $plInStock = ((int)($plRow['inventory_in_stock'] ?? 0) === 1);
                        $plStockLabel = $plInStock ? 'In Stock' : 'Out of Stock';
                        $plStockClass = $plInStock ? 'status-live' : 'status-out';

                        $plIsPublished = ((string)$plRow['link_status'] === '200' && (string)$plRow['product_status'] === '1');
                        $plStatusLabel = $plIsPublished ? 'Published' : 'Draft';
                        $plStatusClass = $plIsPublished ? 'status-live' : 'status-draft';

                        $plIsFeatured = ((int)($plRow['link_is_featured'] ?? 0) === 1);

                        $plPriceNum = (float)($plRow['inventory_price'] ?? 0);
                        $plMrpNum = (float)($plRow['inventory_mrp'] ?? 0);
                        $plCurrency = trim((string)($plRow['inventory_currency_symbol'] ?? ''));
                        if ($plCurrency === '') {
                            $plCurrency = $plSiteCurrency;
                        }

                        $plDiscountLabel = '';
                        if ($plMrpNum > 0 && $plPriceNum > 0 && $plMrpNum > $plPriceNum) {
                            $plDiscountPercent = (int)round((($plMrpNum - $plPriceNum) / $plMrpNum) * 100);
                            $plDiscountLabel = $plDiscountPercent . '% off';
                        }

                        $plRawImage = trim((string)($plRow['product_image'] ?? ''));
                        $plImageSrc = ($plRawImage !== '') ? $plSafeHref($plRawImage, '') : '';
                        $plHasImage = ($plImageSrc !== '');

                        $plRawLinkUrl = trim((string)($plRow['link_url'] ?? ''));
                        $plViewHref = $plSafeHref($plRawLinkUrl, '#');
                        $plEditHref = $plRouteBaseUrl . '?type=edit&editor=true&id=' . rawurlencode($plEncodedId);
                        $plDuplicateHref = $plRouteBaseUrl . '?type=add&editor=true&duplicate_id=' . rawurlencode($plEncodedId);

                        $plEffDateStr = (string)($plRow['effective_publish_date'] ?? $plRow['link_creation']);
                        $plTimestamp = strtotime($plEffDateStr);
                        if ($plTimestamp === false) {
                            $plTimestamp = 0;
                        }
                        $plFormattedDate = $plTimestamp > 0 ? date('d/m/Y', $plTimestamp) : '-';
                    ?>
                        <tr>
                            <th scope="row" data-order="<?php echo $plEsc(strtolower($plProductTitle)); ?>">
                                <div class="product-cell">
                                    <div class="product-thumb<?php echo $plHasImage ? '' : ' is-fallback'; ?>">
                                        <?php if ($plHasImage) { ?>
                                            <img
                                                src="<?php echo $plEsc($plImageSrc); ?>"
                                                alt=""
                                                loading="lazy"
                                                decoding="async">
                                        <?php } ?>
                                        <span class="product-thumb-fallback<?php echo $plHasImage ? '' : ' is-visible'; ?>" aria-hidden="true">
                                            <i class="fas fa-box-open"></i>
                                        </span>
                                    </div>
                                    <div class="product-meta">
                                        <p>
                                            <a class="product-title-link" href="<?php echo $plEsc($plEditHref); ?>">
                                                <?php echo $plEsc($plProductTitle); ?>
                                                <?php if ($plSku !== '') { ?>
                                                    <span class="product-ids"> | SKU: <?php echo $plEsc($plSku); ?></span>
                                                <?php } ?>
                                            </a>
                                        </p>
                                        <div class="product-tags">
                                            <span class="product-tag <?php echo $plEsc($plStatusClass); ?>"><?php echo $plEsc($plStatusLabel); ?></span>
                                            <span class="product-tag <?php echo $plEsc($plStockClass); ?>"><?php echo $plEsc($plStockLabel); ?></span>
                                            <?php if ($plIsFeatured) { ?>
                                                <span class="product-tag status-featured">Featured</span>
                                            <?php } ?>
                                            <?php if ($plDiscountLabel !== '') { ?>
                                                <span class="product-tag status-discount"><?php echo $plEsc($plDiscountLabel); ?></span>
                                            <?php } ?>
                                        </div>
                                        <div class="hover">
                                            <a href="<?php echo $plEsc($plEditHref); ?>" aria-label="<?php echo $plEsc('Edit ' . $plProductTitle); ?>">
                                                <i class="fas fa-pencil-alt" aria-hidden="true"></i> Edit
                                            </a>
                                            <a href="<?php echo $plEsc($plDuplicateHref); ?>" aria-label="<?php echo $plEsc('Duplicate ' . $plProductTitle); ?>">
                                                <i class="fas fa-copy" aria-hidden="true"></i> Duplicate
                                            </a>
                                            <a
                                                href="#"
                                                id="<?php echo $plEsc($plEncodedId); ?>_product"
                                                class="deleteBtn"
                                                aria-label="<?php echo $plEsc('Delete ' . $plProductTitle); ?>">
                                                <i class="fas fa-trash" aria-hidden="true"></i> Delete
                                            </a>
                                            <?php if ($plViewHref !== '#') { ?>
                                                <a
                                                    href="<?php echo $plEsc($plViewHref); ?>"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    aria-label="<?php echo $plEsc('View ' . $plProductTitle . ' in new tab'); ?>">
                                                    <i class="fas fa-eye" aria-hidden="true"></i> View
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <td data-order="<?php echo $plEsc(strtolower($plCategoryTitle)); ?>">
                                <?php echo $plEsc($plCategoryTitle); ?>
                            </td>
                            <td data-order="<?php echo $plInStock ? '1' : '0'; ?>">
                                <span class="product-tag <?php echo $plEsc($plStockClass); ?>"><?php echo $plEsc($plStockLabel); ?></span>
                            </td>
                            <td data-order="<?php echo $plEsc(number_format($plPriceNum, 2, '.', '')); ?>">
                                <div class="price-main"><?php echo $plEsc($plCurrency . ' ' . $plFormatPrice($plPriceNum)); ?></div>
                                <?php if ($plDiscountLabel !== '') { ?>
                                    <div class="price-sub"><?php echo $plEsc($plDiscountLabel); ?></div>
                                <?php } ?>
                            </td>
                            <td data-order="<?php echo $plEsc(number_format($plMrpNum, 2, '.', '')); ?>">
                                <div class="price-main"><?php echo $plEsc($plCurrency . ' ' . $plFormatPrice($plMrpNum)); ?></div>
                            </td>
                            <td data-order="<?php echo (int)$plTimestamp; ?>">
                                <?php echo $plEsc($plFormattedDate); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        </div>

        <div class="dt-controls-bottom" id="productTableControlsBottom"></div>
    </div>
</div>
