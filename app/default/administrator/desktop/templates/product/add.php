<?php
require_once FUNCTIONS . 'duplicate.php';

$dupSourceId = iswGetDuplicateSourceId();
$dupData = ($dupSourceId > 0) ? iswGetDuplicateRecord($connection, 'product', $dupSourceId, SITE_ID) : null;
$dupRow = ($dupData !== null) ? $dupData['row'] : array();
$dupProductMeta = ($dupData !== null && isset($dupData['product_meta']) && is_array($dupData['product_meta'])) ? $dupData['product_meta'] : array();

$dupTitle = ($dupData !== null) ? (string)$dupData['duplicate_title'] : '';
$dupUrl = ($dupData !== null) ? (string)$dupData['duplicate_slug'] : '';
$dupDesc = ($dupData !== null) ? html_entity_decode((string)($dupRow['product_description'] ?? ''), ENT_QUOTES, 'UTF-8') : '';
$dupHighlight = (string)($dupRow['product_highlight'] ?? '');
$dupSubHeading = (string)($dupProductMeta['sub_heading'] ?? '');
$dupAvailabilityCities = (string)($dupProductMeta['availability_cities'] ?? '');
$dupCareNTips = (string)($dupProductMeta['care_n_tips'] ?? '');
$dupDeliveryInfo = (string)($dupProductMeta['delivery_information'] ?? '');

$dupSellDisplayOnly = ($dupData !== null && (int)($dupRow['inventory_sell_online'] ?? 1) === 0);
$dupInStock = ($dupData === null) ? true : ((int)($dupRow['inventory_in_stock'] ?? 0) === 1);
$dupBackorder = ($dupData === null) ? true : ((int)($dupRow['inventory_backorder'] ?? 0) === 1);
$dupSalePrice = (string)($dupRow['inventory_price'] ?? '');
$dupMrp = (string)($dupRow['inventory_mrp'] ?? '');
$dupSku = (string)($dupRow['inventory_sku'] ?? '');
$dupStockWarning = (string)($dupRow['inventory_stock_notice'] ?? '');
$dupTaxStatus = (string)($dupRow['inventory_tax_status'] ?? '0');
$dupTaxRate = (string)($dupRow['inventory_tax_rate'] ?? '');
$dupWeight = (string)($dupRow['inventory_weight'] ?? '');
$dupLenght = (string)($dupRow['inventory_lenght'] ?? '');
$dupWidth = (string)($dupRow['inventory_width'] ?? '');
$dupHeight = (string)($dupRow['inventory_height'] ?? '');

$dupPreloadedGallery = ($dupData !== null) ? $dupData['preloaded_gallery'] : array();
$dupGallerySource = ($dupData !== null) ? (string)$dupData['encoded_source_id'] : '';

$selectedSimilarIds = array();
if (!empty($dupRow['inventory_variation'])) {
    $decodedVar = json_decode((string)(base64_decode((string)$dupRow['inventory_variation'], true) ?: ''), true);
    if (is_array($decodedVar)) {
        foreach ($decodedVar as $varItem) {
            if (is_array($varItem) && isset($varItem[0])) {
                $selectedSimilarIds[] = (string)$varItem[0];
            }
        }
    }
}

$selectedAddonIds = array();
if (!empty($dupRow['inventory_addons'])) {
    $decodedAddon = json_decode((string)(base64_decode((string)$dupRow['inventory_addons'], true) ?: ''), true);
    if (is_array($decodedAddon)) {
        foreach ($decodedAddon as $addonItem) {
            if (is_array($addonItem) && isset($addonItem[0])) {
                $selectedAddonIds[] = (string)$addonItem[0];
            }
        }
    }
}

$allProductList = mysqli_query($connection, "SELECT product_id, product_title, product_thumbnail, link_url FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = product_id WHERE link_status=200 AND link_type='product' AND product_status=1 AND link_site_id=" . SITE_ID . " ORDER BY product_title ASC");
$similarProductOptions = '';
$addOnProductOptions = '';

if ($allProductList && mysqli_num_rows($allProductList) != 0) {
    while ($productData = mysqli_fetch_assoc($allProductList)) {
        $optId = (string)($productData['product_id'] ?? '');
        $optTitle = (string)($productData['product_title'] ?? '');
        $optThumb = (string)($productData['product_thumbnail'] ?? '');
        $optUrl = (string)($productData['link_url'] ?? '');
        $optValEsc = iswEscHtml("{$optId}&&{$optTitle}&&{$optThumb}&&{$optUrl}");
        $optTitleEsc = iswEscHtml($optTitle);

        $simSel = in_array($optId, $selectedSimilarIds, true) ? ' selected' : '';
        $addonSel = in_array($optId, $selectedAddonIds, true) ? ' selected' : '';
        $similarProductOptions .= "<option value='{$optValEsc}'{$simSel}>{$optTitleEsc}</option>";
        $addOnProductOptions .= "<option value='{$optValEsc}'{$addonSel}>{$optTitleEsc}</option>";
    }
}

$dupMetaTitle = (string)($dupRow['meta_title'] ?? '');
$dupMetaDesc = (string)($dupRow['meta_description'] ?? '');
$dupMetaKey = (string)($dupRow['meta_keyword'] ?? '');
$dupCanonical = (string)($dupRow['meta_canonical'] ?? '');
$dupRobots = (string)($dupRow['meta_robots'] ?? '');
$dupFbImg = (string)($dupRow['fb_img'] ?? '');
$dupFbTitle = (string)($dupRow['fb_title'] ?? '');
$dupFbDesc = (string)($dupRow['fb_desc'] ?? '');
$dupTwImg = (string)($dupRow['tw_img'] ?? '');
$dupTwTitle = (string)($dupRow['tw_title'] ?? '');
$dupTwDesc = (string)($dupRow['tw_desc'] ?? '');
$dupIsFeatured = ((string)($dupRow['link_is_featured'] ?? '') === '1');
$selectedBrandId = ($dupData !== null) ? (int)($dupRow['product_brand_id'] ?? 0) : 1;
$selectedCategoryId = ($dupData !== null) ? (int)($dupRow['product_category'] ?? 0) : 1;
$dupTagsCsv = ($dupData !== null) ? (string)$dupData['tags_csv'] : '';
$dupFeatureImg = (string)($dupRow['product_image'] ?? '');
$dupThumbImg = (string)($dupRow['product_thumbnail'] ?? '');
?>

<div class="product-add-ui">
    <h2>Add new <?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?></h2>
    <?php if ($dupData !== null) { ?>
        <div class="duplicate-mode-banner">Duplicating: <strong><?php echo iswEscHtml($dupData['source_title']); ?></strong> &mdash; Review your changes and click Publish to save a new copy.</div>
    <?php } ?>

    <div class="form-box">
        <form method="POST" id="formData" enctype="multipart/form-data">
            <div class="left-box">
                <div class="form-group">
                    <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title"
                        value="<?php echo iswEscHtml($dupTitle); ?>" autofocus required>
                </div>
                <div class="form-group">
                    <div class="url">
                        <p><strong>Permalink</strong></p>
                        <p><span><?php echo SITE_DOMAIN ?>/product/</span></p>
                        <input type="text" id="url" class="form-control" name="url" value="<?php echo iswEscHtml($dupUrl); ?>">
                    </div>
                </div>
                <div class="form-group fullscreen-editor">
                    <textarea name="description" class="editor inside-shadow"><?php echo iswEscHtml($dupDesc); ?></textarea>
                </div>

                <!-- Product description and other things -->

                <section style="margin-bottom:20px">
                    <h3>Product Details</h3>
                    <ul class='tabs'>
                        <li id="productGeneral" class='active'>General</li>
                        <li id="productSpecifications">Specifications</li>
                        <li id="productInventory">Inventory</li>
                        <li id="productShipping">Shipping</li>
                        <li id="productGallery">Gallery</li>
                        <li id="productAttributesDetails">Attributes</li>
                        <li id="productAddOnsDetails">Add Ons</li>
                        <li id="productAdditionalDetails">Additional Details</li>
                    </ul>
                    <div class="productGeneral generalTab">
                        <div class="form-group">
                            <label>Product short description / Highlights</label>
                            <textarea name="highlight" class="form-control inside-shadow"
                                placeholder="Product short description / Highlights"><?php echo iswEscHtml($dupHighlight); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Product sub heading</label>
                            <input type="text" name="productMeta[sub_heading]" class="form-control inside-shadow"
                                value="<?php echo iswEscHtml($dupSubHeading); ?>" placeholder="Product sub heading">
                        </div>
                        <div class="box-1">
                            <input type="checkbox" name="sell_online" value=0 <?php echo $dupSellDisplayOnly ? 'checked' : ''; ?>> Product is only for display purposes.
                        </div>
                    </div>
                    <div class="productSpecifications generalTab">

                    </div>
                    <div class="productInventory generalTab" style="display: none;">

                        <div class="box-2">
                            <input type="checkbox" name="in_stock" value=1 <?php echo $dupInStock ? 'checked' : ''; ?>> In Stock
                        </div>
                        <div class="box-2">
                            <input type="checkbox" name="backorder" value=1 <?php echo $dupBackorder ? 'checked' : ''; ?>> Allowed Backorders (if product is
                            out
                            of stock )
                        </div>
                        <div class="box-2">
                            <div class="form-group">
                                <label>Sale price (₹)</label>
                                <input type="number" onwheel="this.blur()" class="form-control inside-shadow"
                                    value="<?php echo iswEscHtml($dupSalePrice); ?>" name="salePrice" placeholder="Sale price">
                            </div>
                        </div>
                        <div class="box-2">
                            <div class="form-group">
                                <label>Regular price in ₹ ( MRP )</label>
                                <input type="number" onwheel="this.blur()" class="form-control inside-shadow" name="mrp"
                                    value="<?php echo iswEscHtml($dupMrp); ?>" placeholder="Regular price">
                            </div>
                        </div>
                        <div class="box-2">
                            <div class="form-group">
                                <label>SKU</label>
                                <input type="text" class="form-control inside-shadow" name="sku" value="<?php echo iswEscHtml($dupSku); ?>" placeholder="SKU">
                            </div>
                        </div>
                        <div class="box-2">
                            <div class="form-group">
                                <label>Stock Alert</label>
                                <input type="text" class="form-control inside-shadow" name="stock_warning"
                                    value="<?php echo iswEscHtml($dupStockWarning); ?>" placeholder="Stock Alert">
                            </div>
                        </div>
                        <div class="box-2">
                            <div class="form-group">
                                <label>Calculate tax</label>
                                <select name="taxStatus" class="form-control inside-shadow">
                                    <option value="0" <?php echo ($dupTaxStatus === '0') ? 'selected' : ''; ?>>No</option>
                                    <option value="1" <?php echo ($dupTaxStatus === '1') ? 'selected' : ''; ?>>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="box-2">
                            <div class="form-group">
                                <label>Tax rate ( in % )</label>
                                <input type="number" onwheel="this.blur()" name="taxRate"
                                    value="<?php echo iswEscHtml($dupTaxRate); ?>" placeholder="Tax rate ( in % )" class="form-control inside-shadow">
                            </div>
                        </div>
                    </div>
                    <div class="productShipping generalTab" style="display: none;">
                        <div class="box-1">
                            <div class="form-group">
                                <label>Weight (kg)</label>
                                <input type="text" class="form-control inside-shadow" name="weight"
                                    value="<?php echo iswEscHtml($dupWeight); ?>" placeholder="Weight">
                            </div>
                        </div>
                        <label>Dimensions (cm)</label>
                        <div class="form-group">
                            <div class="box-3">
                                <label>Lenght</label>
                                <input type="text" class="form-control inside-shadow" name="lenght"
                                    value="<?php echo iswEscHtml($dupLenght); ?>" placeholder="Lenght">
                            </div>
                            <div class="box-3">
                                <label>Width</label>
                                <input type="text" class="form-control inside-shadow" name="width" value="<?php echo iswEscHtml($dupWidth); ?>" placeholder="Width">
                            </div>
                            <div class="box-3">
                                <label>Height</label>
                                <input type="text" class="form-control inside-shadow" name="height"
                                    value="<?php echo iswEscHtml($dupHeight); ?>" placeholder="Height">
                            </div>
                        </div>
                        <label>Availability</label>
                        <div class="form-group">
                            <label>Available cities</label>
                            <textarea name="productMeta[availability_cities]" id="availability_cities"
                                class="form-control inside-shadow" placeholder="Availability cities"><?php echo iswEscHtml($dupAvailabilityCities); ?></textarea>
                        </div>
                    </div>
                    <div class="productGallery generalTab" style="display: none;">

                        <div class="input-field">
                            <div class="input-images" style="padding-top: .5rem;"></div>
                        </div>
                        <input type="hidden" name="duplicateGallerySource" value="<?php echo iswEscHtml($dupGallerySource); ?>">
                        <?php if (!empty($dupPreloadedGallery)) { ?>
                            <script>var oldGalleryImages = <?php echo json_encode($dupPreloadedGallery); ?>;</script>
                        <?php } ?>

                    </div>
                    <div class="productAttributesDetails generalTab" style="display: none;">
                        <div class="productDiv">
                            <div class="form-group">
                                <label>Select product varients ( select multiple )</label>
                                <select name="similarProducts[]" class="form-control inside-shadow selectSimilarProduct"
                                    multiple="multiple">
                                    <?php echo $similarProductOptions; ?>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="productAddOnsDetails generalTab" style="display: none;">
                        <div class="productDiv">
                            <div class="form-group">
                                <label>Select add-on product ( select multiple )</label>
                                <select name="addOnProducts[]" class="form-control inside-shadow selectSimilarProduct"
                                    multiple="multiple">
                                    <?php echo $addOnProductOptions; ?>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="productAdditionalDetails generalTab" style="display: none;">

                        <div class="form-group">
                            <label>Care & Tips</label>
                            <textarea name="productMeta[care_n_tips]" id="care_n_tips"
                                class="form-control inside-shadow" placeholder="Care & tips..."><?php echo iswEscHtml($dupCareNTips); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Delivery information</label>
                            <textarea name="productMeta[delivery_information]" id="delivery_information"
                                class="form-control inside-shadow" placeholder="Delivery information..."><?php echo iswEscHtml($dupDeliveryInfo); ?></textarea>
                        </div>

                    </div>
                </section>

                <!-- SEO & social media marketing -->

                <section>
                    <h3>Advance Options for digital marketing</h3>
                    <ul class='tabs'>
                        <li id="seo" class='active'>Search Engine Optimization</li>
                        <li id="fb">Facebook</li>
                        <li id="tw">Twitter</li>
                    </ul>
                    <div class="seo generalTab">
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control inside-shadow"
                                value="<?php echo iswEscHtml($dupMetaTitle); ?>" placeholder="Meta title">
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow"
                                placeholder="Meta Description"><?php echo iswEscHtml($dupMetaDesc); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Meta Keywords</label>
                            <textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow"
                                placeholder="Enter keywords of the post ( use , to seprate keywords )"><?php echo iswEscHtml($dupMetaKey); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Canonical URL</label>
                            <input type="text" name="canonical" class="form-control inside-shadow" id="canonical"
                                value="<?php echo iswEscHtml($dupCanonical); ?>" placeholder="Actual URL of the <?php echo $postType ?>">
                        </div>
                        <div class="form-group">
                            <label>Allow search engines to show this Post in search results?</label>
                            <select name="robots" class="form-control inside-shadow">
                                <option value="index, follow" <?php echo (($dupRobots === 'index, follow') ? 'selected' : ''); ?>>Yes</option>
                                <option value="noindex, nofollow" <?php echo (($dupRobots === 'noindex, nofollow') ? 'selected' : ''); ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="fb generalTab" style="display: none;">
                        <div class="facebookPreviewBox" style="display: <?php echo ($dupFbImg !== '') ? 'block' : 'none'; ?>;">
                            <input type="hidden" name="oldFacebookImage" value="<?php echo iswEscHtml($dupFbImg); ?>">
                            <div class="img"><img src="<?php echo ($dupFbImg !== '') ? iswPreviewImageUrl($dupFbImg) : ''; ?>" id="facebookPath" alt=""></div>
                            <p id="facebookChangeImage" class='changeImage'>Remove facebook feature image</p>
                        </div>
                        <div class="facebookUploadBox" style="display: <?php echo ($dupFbImg !== '') ? 'none' : 'block'; ?>;">
                            <span>Set facebook feature image</span>
                            <input type='file' id="facebookImage" name='facebookImage' />
                        </div>
                        <div class="form-group">
                            <label>Facebook Title</label>
                            <input type="text" name="fb_title" id="fb_title" class="form-control inside-shadow"
                                value="<?php echo iswEscHtml($dupFbTitle); ?>" placeholder="Facebook title">
                        </div>
                        <div class="form-group">
                            <label>Facebook Description</label>
                            <textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow"
                                placeholder="Facebook Description"><?php echo iswEscHtml($dupFbDesc); ?></textarea>
                        </div>
                    </div>
                    <div class="tw generalTab" style="display: none;">
                        <div class="twitterPreviewBox" style="display: <?php echo ($dupTwImg !== '') ? 'block' : 'none'; ?>;">
                            <input type="hidden" name="oldTwitterImage" value="<?php echo iswEscHtml($dupTwImg); ?>">
                            <div class="img"><img src="<?php echo ($dupTwImg !== '') ? iswPreviewImageUrl($dupTwImg) : ''; ?>" id="twitterPath" alt=""></div>
                            <p id="twitterChangeImage" class="changeImage">Remove twitter feature image</p>
                        </div>
                        <div class="twitterUploadBox" style="display: <?php echo ($dupTwImg !== '') ? 'none' : 'block'; ?>;">
                            <span>Set Twitter feature image</span>
                            <input type='file' id="twitterImage" name='twitterImage' />
                        </div>
                        <div class="form-group">
                            <label>Twitter Title</label>
                            <input type="text" name="tw_title" id="tw_title" class="form-control inside-shadow"
                                value="<?php echo iswEscHtml($dupTwTitle); ?>" placeholder="Twitter title">
                        </div>
                        <div class="form-group">
                            <label>Twitter Description</label>
                            <textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow"
                                placeholder="Twitter Description"><?php echo iswEscHtml($dupTwDesc); ?></textarea>
                        </div>
                    </div>
                </section>

            </div>
            <div class="right-box">
                <div class="action-box inside-shadow">
                    <input type="button" id="publishPost" class="btn btn-green btn-block" value="Publish">
                    <!-- <input type="submit" id="draftPost" class="btn btn-yellow btn-block" value="Save Draft"> -->
                    <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                    <input type="hidden" name="action" value="publish">
                </div>
                <div class="form-group">
                    <h2>Featured</h2>
                    <div class="action-box inside-shadow" style="padding: 10px">
                        <input type="checkbox" value="1" name='featurePost' <?php echo $dupIsFeatured ? 'checked' : ''; ?>> Mark as feature
                    </div>
                </div>
                <?php

                if (SITE_ID == 32) { ?>
                <h2>Brand</h2>
                <div class="action-box height-200 inside-shadow">
                    <?php
                        if ($brandList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link WHERE category_site_id='" . SITE_ID . "' AND link_type='category' AND link_relation_id=category_id AND category_type='brand' AND link_status=200 ORDER BY category_title ASC")) {
                            while ($brand = mysqli_fetch_assoc($brandList)) { ?>
                    <div class="form-group">
                        <input type="radio" name="brand" value="<?php echo $brand['category_id']; ?>"
                            <?php echo (((int)$brand['category_id'] === $selectedBrandId) ? 'checked' : '') ?>>
                        <?php echo ucwords($brand['category_title']); ?>
                    </div>
                    <?php
                            }
                        }
                        ?>
                </div>
                <?php
                }

                ?>
                <h2>Categories</h2>
                <div class="action-box height-200 inside-shadow">
                    <?php
                    if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link WHERE category_site_id='" . SITE_ID . "' AND link_type='category' AND link_relation_id=category_id AND category_type='product' AND  link_status=200 ORDER BY category_title ASC")) {
                        while ($category = mysqli_fetch_assoc($categoryList)) { ?>
                    <div class="form-group">
                        <input type="radio" name="category"
                            value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>"
                            <?php echo (((int)$category['category_id'] === $selectedCategoryId) ? 'checked' : '') ?>>
                        <?php echo ucwords($category['category_title']); ?>
                    </div>
                    <?php
                        }
                    }
                    ?>
                </div>
                <h2>Tags</h2>
                <div class="action-box inside-shadow">
                    <div class="form-group">
                        <input type="text" class='tags' />
                        <input type="hidden" id="tagValue" value="<?php echo iswEscHtml($dupTagsCsv); ?>">
                        <input type="hidden" id='tags' name="tags" value=''>
                    </div>
                </div>
                <div class="tagList" style="display: none;"></div>
                <h2>Feature Image</h2>
                <div class="form-group featureImageUploadBox upload-btn" style="display: <?php echo ($dupFeatureImg !== '') ? 'none' : 'block'; ?>;">
                    <span>Set Feature Image</span>
                    <input type="hidden" name="oldFeatureImage" value="<?php echo iswEscHtml($dupFeatureImg); ?>">
                    <input type="hidden" name="oldThumbnailImage" value="<?php echo iswEscHtml($dupThumbImg); ?>">
                    <input type="file" id="featureImage" name="featureImage"
                        accept="image/x-png, image/gif, image/jpeg">
                    <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif
                        <strong>Recommended
                            file size maximum 500kb
                    </p>
                </div>
                <div class="img-preview featureImagePreviewBox action-box" style="display: <?php echo ($dupFeatureImg !== '') ? 'block' : 'none'; ?>;">
                    <img src="<?php echo ($dupFeatureImg !== '') ? iswPreviewImageUrl($dupFeatureImg) : '#'; ?>" id="featureImagePath" alt="">
                    <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
                </div>
            </div>
        </form>
    </div>
</div>