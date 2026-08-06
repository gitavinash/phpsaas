<?php $canonicalURL = str_replace('amp/', '', ((isset($meta_canonical)) ?  (($meta_canonical != '') ? $meta_canonical : HTTP_PROTOCOL . SITE_DOMAIN . BASE_URL) : HTTP_PROTOCOL . SITE_DOMAIN . BASE_URL)); ?>
<!-- Site Default Configuration -->
<link rel="icon" href="<?php echo SITE_ICON ?>">
<title>
    <?php echo ucfirst(((isset($meta_title)) ? (($meta_title != '') ? $meta_title : ${$link_type . '_title'}) : SITE_DESC . ' | ' . SITE_NAME)) ?>
</title>

<?php

echo "<link rel='canonical' href='{$canonicalURL}' />";

$isSearch = strpos(BASE_URL, '/search/') !== false;
$isTag = strpos(BASE_URL, '/tag/') !== false;
$isPaginated = (int)($_GET['page'] ?? $_GET['page'] ?? 1) > 1;

if ($isSearch || $isTag) {
    if ($isPaginated) {
        echo '<meta name="robots" content="noindex,follow">';
    }
}

if (defined('ENABLE_AMP')) {

    // AMP

    if (SITE_VERSION == 'amp') {

        // AMP site tag

        echo "<link rel='alternate' href='{$canonicalURL}' />";
    } else {

        // Non AMP site tag

        echo "<link rel='amphtml' href='" . str_replace(SITE_URL, SITE_URL . '/amp', $canonicalURL) . "' >";
    }
}

?>