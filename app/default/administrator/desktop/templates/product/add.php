<?php

$allProductList = mysqli_query($connection, "SELECT product_id, product_title, product_thumbnail, link_url FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = product_id WHERE link_status=200 AND link_type='product' AND product_status=1 AND link_site_id=" . SITE_ID);
$allProductOptionsDropDown = '';

if (mysqli_num_rows(($allProductList)) != 0) {

    while ($productData = mysqli_fetch_assoc($allProductList)) {

        extract($productData);
        $allProductOptionsDropDown = $allProductOptionsDropDown . "<option value='{$product_id}&&{$product_title}&&{$product_thumbnail}&&{$link_url}'>{$product_title}</option>";
    }
}

?>

<h2>Add new <?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?></h2>

<div class="form-box">
    <form method="POST" id="formData" enctype="multipart/form-data">
        <div class="left-box">
            <div class="form-group">
                <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title" autofocus required>
            </div>
            <div class="form-group">
                <div class="url">
                    <p><strong>Permalink</strong></p>
                    <p><span><?php echo SITE_DOMAIN ?>/product/</span></p>
                    <input type="text" id="url" class="form-control" name="url">
                </div>
            </div>
            <div class="form-group fullscreen-editor">
                <textarea name="description" class="editor inside-shadow"></textarea>
            </div>

            <!-- Product description and other things -->

            <section style="margin-bottom:20px">
                <h3>Product Details</h3>
                <ul class='tabs'>
                    <li id="productGeneral" class='active'>General</li>
                    <li id="productInventory">Inventory</li>
                    <li id="productShipping">Shipping</li>
                    <li id="productGallery">Gallery</li>
                    <li id="productAttributesDetails">Attributes</li>
                    <li id="productAdditionalDetails">Additional Details</li>
                </ul>
                <div class="productGeneral generalTab">
                    <div class="form-group">
                        <label>Product short description / Highlights</label>
                        <textarea name="highlight" class="form-control inside-shadow" placeholder="Product short description / Highlights"></textarea>
                    </div>
                    <div class="box-1">
                        <input type="checkbox" name="sell_online" value=0> Product is only for display purposes.
                    </div>
                </div>
                <div class="productInventory generalTab" style="display: none;">

                    <div class="box-2">
                        <input type="checkbox" name="in_stock" value=1 checked> In Stock
                    </div>
                    <div class="box-2">
                        <input type="checkbox" name="backorder" value=1 checked> Allowed Backorders (if product is out
                        of stock )
                    </div>
                    <div class="box-2">
                        <div class="form-group">
                            <label>Sale price (₹)</label>
                            <input type="number" class="form-control inside-shadow" name="salePrice" placeholder="Sale price">
                        </div>
                    </div>
                    <div class="box-2">
                        <div class="form-group">
                            <label>Regular price (₹)</label>
                            <input type="number" class="form-control inside-shadow" name="mrp" placeholder="Regular price">
                        </div>
                    </div>
                    <div class="box-2">
                        <div class="form-group">
                            <label>SKU</label>
                            <input type="text" class="form-control inside-shadow" name="sku" placeholder="SKU">
                        </div>
                    </div>
                    <div class="box-2">
                        <div class="form-group">
                            <label>Stock Alert</label>
                            <input type="text" class="form-control inside-shadow" name="stock_warning" placeholder="Stock Alert">
                        </div>
                    </div>
                    <div class="box-2">
                        <div class="form-group">
                            <label>Calculate tax</label>
                            <select name="taxStatus" class="form-control inside-shadow">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="box-2">
                        <div class="form-group">
                            <label>Tax rate ( in % )</label>
                            <input type="number" name="taxRate" placeholder="Tax rate ( in % )" class="form-control inside-shadow">
                        </div>
                    </div>
                </div>
                <div class="productShipping generalTab" style="display: none;">
                    <div class="box-1">
                        <div class="form-group">
                            <label>Weight (kg)</label>
                            <input type="text" class="form-control inside-shadow" name="weight" placeholder="Weight">
                        </div>
                    </div>
                    <label>Dimensions (cm)</label>
                    <div class="form-group">
                        <div class="box-3">
                            <label>Lenght</label>
                            <input type="text" class="form-control inside-shadow" name="lenght" placeholder="Lenght">
                        </div>
                        <div class="box-3">
                            <label>Width</label>
                            <input type="text" class="form-control inside-shadow" name="width" placeholder="Width">
                        </div>
                        <div class="box-3">
                            <label>Height</label>
                            <input type="text" class="form-control inside-shadow" name="height" placeholder="Height">
                        </div>
                    </div>
                    <label>Availability</label>
                    <div class="form-group">
                        <label>Available cities</label>
                        <textarea name="productMeta[availability_cities]" id="availability_cities" class="form-control inside-shadow" placeholder="Availability cities">Delhi</textarea>
                    </div>
                </div>
                <div class="productGallery generalTab" style="display: none;">

                    <div class="input-field">
                        <div class="input-images" style="padding-top: .5rem;"></div>
                    </div>

                </div>
                <div class="productAttributesDetails generalTab" style="display: none;">
                    <div class="productDiv">
                        <div class="form-group">
                            <label>Select product varients ( select multiple )</label>
                            <select name="similarProducts[]" class="form-control inside-shadow selectSimilarProduct" multiple="multiple">
                                <?php echo $allProductOptionsDropDown ?>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="productAdditionalDetails generalTab" style="display: none;">

                    <div class="form-group">
                        <label>Care & Tips</label>
                        <textarea name="productMeta[care_n_tips]" id="care_n_tips" class="form-control inside-shadow" placeholder="Care & tips..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Delivery information</label>
                        <textarea name="productMeta[delivery_information]" id="delivery_information" class="form-control inside-shadow" placeholder="Delivery information..."></textarea>
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
                        <input type="text" name="meta_title" id="meta_title" class="form-control inside-shadow" placeholder="Meta title">
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow" placeholder="Meta Description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow" placeholder="Enter keywords of the post ( use , to seprate keywords )"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Canonical URL</label>
                        <input type="text" name="canonical" class="form-control inside-shadow" id="canonical" placeholder="Actual URL of the <?php echo $postType ?>">
                    </div>
                    <div class="form-group">
                        <label>Allow search engines to show this Post in search results?</label>
                        <select name="robots" class="form-control inside-shadow">
                            <option value="index, follow">Yes</option>
                            <option value="noindex, nofollow">No</option>
                        </select>
                    </div>
                </div>
                <div class="fb generalTab" style="display: none;">
                    <div class="facebookPreviewBox" style="display: none;">
                        <div class="img"><img src="" id="facebookPath" alt=""></div>
                        <p id="facebookChangeImage" class='changeImage'>Remove facebook feature image</p>
                    </div>
                    <div class="facebookUploadBox">
                        <span>Set facebook feature image</span>
                        <input type='file' id="facebookImage" name='facebookImage' />
                    </div>
                    <div class="form-group">
                        <label>Facebook Title</label>
                        <input type="text" name="fb_title" id="fb_title" class="form-control inside-shadow" placeholder="Facebook title">
                    </div>
                    <div class="form-group">
                        <label>Facebook Description</label>
                        <textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow" placeholder="Facebook Description"></textarea>
                    </div>
                </div>
                <div class="tw generalTab" style="display: none;">
                    <div class="twitterPreviewBox" style="display: none;">
                        <div class="img"><img src="" id="twitterPath" alt=""></div>
                        <p id="twitterChangeImage" class="changeImage">Remove twitter feature image</p>
                    </div>
                    <div class="twitterUploadBox">
                        <span>Set Twitter feature image</span>
                        <input type='file' id="twitterImage" name='twitterImage' />
                    </div>
                    <div class="form-group">
                        <label>Twitter Title</label>
                        <input type="text" name="tw_title" id="tw_title" class="form-control inside-shadow" placeholder="Twitter title">
                    </div>
                    <div class="form-group">
                        <label>Twitter Description</label>
                        <textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow" placeholder="Twitter Description"></textarea>
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
                    <input type="checkbox" value="1" name='featurePost'> Mark as feature
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
                                <input type="radio" name="brand" value="<?php echo $brand['category_id']; ?>" <?php echo (($brand['category_id'] == 1) ? 'checked' : '') ?>>
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
                            <input type="radio" name="category" value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>" <?php echo (($category['category_id'] == 1) ? 'checked' : '') ?>>
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
                    <input type="hidden" id='tags' name="tags" value=''>
                </div>
            </div>
            <div class="tagList" style="display: none;"></div>
            <h2>Feature Image</h2>
            <div class="form-group featureImageUploadBox upload-btn">
                <span>Set Feature Image</span>
                <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif <strong>Recommended
                        file size maximum 500kb</p>
            </div>
            <div class="img-preview featureImagePreviewBox action-box" style="display: none;">
                <img src="#" id="featureImagePath" alt="">
                <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
            </div>
        </div>
    </form>
</div>