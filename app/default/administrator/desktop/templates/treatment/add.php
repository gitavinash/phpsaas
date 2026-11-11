<?php
require_once FUNCTIONS . 'duplicate.php';

$dupSourceId = iswGetDuplicateSourceId();
$dupData = ($dupSourceId > 0) ? iswGetDuplicateRecord($connection, 'treatment', $dupSourceId, SITE_ID) : null;
$dupRow = ($dupData !== null) ? $dupData['row'] : array();
$dupSection = ($dupData !== null && isset($dupData['section']) && is_array($dupData['section'])) ? $dupData['section'] : array();

$dupTitle = ($dupData !== null) ? (string)$dupData['duplicate_title'] : '';
$dupUrl = ($dupData !== null) ? (string)$dupData['duplicate_slug'] : '';
$dupDesc = ($dupData !== null) ? html_entity_decode((string)($dupRow['treatment_description'] ?? ''), ENT_QUOTES, 'UTF-8') : '';
$dupHighlight = (string)($dupRow['treatment_highlight'] ?? '');
$dupSalePrice = (string)($dupRow['inventory_price'] ?? '');
$dupMrp = (string)($dupRow['inventory_mrp'] ?? '');
$dupSku = (string)($dupRow['inventory_sku'] ?? '');
$dupWeight = (string)($dupRow['inventory_weight'] ?? '');
$dupLenght = (string)($dupRow['inventory_lenght'] ?? '');
$dupWidth = (string)($dupRow['inventory_width'] ?? '');
$dupHeight = (string)($dupRow['inventory_height'] ?? '');
$dupPreloadedGallery = ($dupData !== null) ? $dupData['preloaded_gallery'] : array();
$dupGallerySource = ($dupData !== null) ? (string)$dupData['encoded_source_id'] : '';

$dupFeatureVideo = (string)($dupSection['feature_video']['url'] ?? '');
$dupQuote = stripslashes((string)($dupSection['quote']['quote'] ?? ''));

$dupTreatmentOptions = array();
for ($ti = 1; $ti <= 10; $ti++) {
    $tIdx = $ti - 1;
    $dupTreatmentOptions[$ti] = array(
        'header' => stripslashes((string)($dupSection['treatment_option'][$tIdx]['header'] ?? '')),
        'indications' => stripslashes((string)($dupSection['treatment_option'][$tIdx]['indications'] ?? '')),
        'process' => stripslashes((string)($dupSection['treatment_option'][$tIdx]['process'] ?? '')),
        'duration' => stripslashes((string)($dupSection['treatment_option'][$tIdx]['duration'] ?? '')),
        'sitting' => stripslashes((string)($dupSection['treatment_option'][$tIdx]['sitting'] ?? '')),
        'bookNow' => stripslashes((string)($dupSection['treatment_option'][$tIdx]['bookNow'] ?? '')),
        'watchVideo' => stripslashes((string)($dupSection['treatment_option'][$tIdx]['watchVideo'] ?? '')),
        'image' => (string)($dupSection['treatment_option'][$tIdx]['image'] ?? ''),
    );
}

$dupBaHeader = stripslashes((string)($dupSection['before_after']['header'] ?? ''));
$dupBaSubHeader = stripslashes((string)($dupSection['before_after']['subHeader'] ?? ''));
$dupBaDesc = stripslashes((string)($dupSection['before_after']['description'] ?? ''));
$dupBaVideo = (string)($dupSection['before_after']['videoLink'] ?? '');
$dupBeforeImage = (string)($dupSection['before_after']['images']['before'] ?? '');
$dupAfterImage = (string)($dupSection['before_after']['images']['after'] ?? '');

$dupWhyImage = (string)($dupSection['why_choose_us']['image'] ?? '');
$dupWhyTitle = stripslashes((string)($dupSection['why_choose_us']['header'] ?? ''));
$dupWhyDesc = stripslashes((string)($dupSection['why_choose_us']['description'] ?? ''));
$dupWhySteps = array();
for ($wi = 1; $wi <= 3; $wi++) {
    $wIdx = $wi - 1;
    $dupWhySteps[$wi] = array(
        'icon' => (string)($dupSection['why_choose_us']['steps'][$wIdx]['icon'] ?? ''),
        'description' => stripslashes((string)($dupSection['why_choose_us']['steps'][$wIdx]['description'] ?? '')),
    );
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
$selectedCategoryId = ($dupData !== null) ? (int)($dupRow['treatment_category'] ?? 0) : 1;
$dupTagsCsv = ($dupData !== null) ? (string)$dupData['tags_csv'] : '';
$dupFeatureImg = (string)($dupRow['treatment_image'] ?? '');
$dupThumbImg = (string)($dupRow['treatment_thumbnail'] ?? '');
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
                    <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title" value="<?php echo iswEscHtml($dupTitle); ?>" autofocus required>
                </div>
                <div class="form-group">
                    <div class="url">
                        <p><strong>Permalink</strong></p>
                        <p><span><?php echo SITE_DOMAIN ?>/treatment/</span></p>
                        <input type="text" id="url" class="form-control" name="url" value="<?php echo iswEscHtml($dupUrl); ?>">
                    </div>
                </div>

                <?php

                if ($site_id == 31) { ?>
                    <div class="form-group">
                        <div class="form-group fullscreen-editor">
                            <textarea name="description" class="editor inside-shadow"><?php echo iswEscHtml($dupDesc); ?></textarea>
                        </div>
                    </div>

                    <section style="margin-top:30px">
                        <h3>Feature Video</h3>
                        <hr>
                        <div class="form-group">
                            <label>Youtube video url</label>
                            <input type="text" class="form-control inside-shadow" name="featureVideo" value="<?php echo iswEscHtml($dupFeatureVideo); ?>" placeholder="Feature Video">
                        </div>
                    </section>

                    <section style="margin-top:30px">
                        <h3>Quote</h3>
                        <hr>
                        <div class="form-group">
                            <label>Quote message</label>
                            <textarea name="quote" class="form-control inside-shadow" placeholder="Quote message"><?php echo iswEscHtml($dupQuote); ?></textarea>
                        </div>
                    </section>

                    <?php for ($ti = 1; $ti <= 10; $ti++) {
                        $tOpt = $dupTreatmentOptions[$ti];
                    ?>
                    <section style="margin-top:30px">
                        <h3>Treatment Option <?php echo $ti; ?></h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow" name="treatmentOptionHeader_<?php echo $ti; ?>" value="<?php echo iswEscHtml($tOpt['header']); ?>" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Indications</label>
                            <input type="text" class="form-control inside-shadow" name="treatmentOptionIndications_<?php echo $ti; ?>" value="<?php echo iswEscHtml($tOpt['indications']); ?>" placeholder="Indications">
                        </div>
                        <div class="form-group">
                            <label>Process</label>
                            <input type="text" class="form-control inside-shadow" name="treatmentOptionProcess_<?php echo $ti; ?>" value="<?php echo iswEscHtml($tOpt['process']); ?>" placeholder="Process">
                        </div>
                        <div class="form-group">
                            <label>Duration</label>
                            <input type="text" class="form-control inside-shadow" name="treatmentOptionDuration_<?php echo $ti; ?>" value="<?php echo iswEscHtml($tOpt['duration']); ?>" placeholder="Duration">
                        </div>
                        <div class="form-group">
                            <label>No. of sittings</label>
                            <input type="text" class="form-control inside-shadow" name="treatmentOptionSitting_<?php echo $ti; ?>" value="<?php echo iswEscHtml($tOpt['sitting']); ?>" placeholder="No. of sittings">
                        </div>
                        <div class="form-group">
                            <label>Book Now</label>
                            <input type="text" class="form-control inside-shadow" name="treatmentOptionBookNow_<?php echo $ti; ?>" value="<?php echo iswEscHtml($tOpt['bookNow']); ?>" placeholder="Book Now Link">
                        </div>
                        <div class="form-group">
                            <label>Watch Video</label>
                            <input type="text" class="form-control inside-shadow" name="treatmentOptionWatchVideo_<?php echo $ti; ?>" value="<?php echo iswEscHtml($tOpt['watchVideo']); ?>" placeholder="Watch Video Link">
                        </div>
                        <div class="row">
                            <h4>Treatment Option <?php echo $ti; ?> Feature Image</h4>
                            <div class="form-group">
                                <label>Before image</label>
                                <input type="hidden" name="oldTreatmentOptionFeatureImage_<?php echo $ti; ?>" value="<?php echo iswEscHtml($tOpt['image']); ?>">
                                <input type="file" name="treatmentOptionFeatureImage_<?php echo $ti; ?>" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                                <?php if ($tOpt['image'] !== '') { ?>
                                    <div class="duplicate-media-preview">
                                        <img src="<?php echo iswPreviewImageUrl($tOpt['image']); ?>" alt="">
                                        <button type="button" class="btn btn-red btn-sm duplicateRemoveMediaBtn" data-target-input="oldTreatmentOptionFeatureImage_<?php echo $ti; ?>">Remove</button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </section>
                    <?php } ?>


                    <section style="margin-top:30px">
                        <h3>Before After</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow" name="berforeAfterHeader" value="<?php echo iswEscHtml($dupBaHeader); ?>" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Sub Header</label>
                            <input type="text" class="form-control inside-shadow" name="berforeAfterSubHeader" value="<?php echo iswEscHtml($dupBaSubHeader); ?>" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="berforeAfterDescription" class="form-control inside-shadow" placeholder="Description"><?php echo iswEscHtml($dupBaDesc); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Video link</label>
                            <input type="text" class="form-control inside-shadow" name="berforeAfterVideLink" value="<?php echo iswEscHtml($dupBaVideo); ?>" placeholder="Video link">
                        </div>
                        <input type="hidden" name="oldBeforeImage" value="<?php echo iswEscHtml($dupBeforeImage); ?>">
                        <input type="hidden" name="oldAfterImage" value="<?php echo iswEscHtml($dupAfterImage); ?>">
                        <div class="row">

                            <div class="productGallery generalTab">

                                <div class="input-field">
                                    <div class="input-images" style="padding-top: .5rem;"></div>
                                </div>
                                <input type="hidden" name="duplicateGallerySource" value="<?php echo iswEscHtml($dupGallerySource); ?>">
                                <?php if (!empty($dupPreloadedGallery)) { ?>
                                    <script>var oldGalleryImages = <?php echo json_encode($dupPreloadedGallery); ?>;</script>
                                <?php } ?>

                            </div>

                        </div>
                    </section>

                    <section style="margin-top: 30px">
                        <h3>Why Choose Us</h3>
                        <hr>
                        <div class="form-group">
                            <label>Feature image</label>
                            <input type="hidden" name="oldWhyChooseUs" value="<?php echo iswEscHtml($dupWhyImage); ?>">
                            <input type="file" name="whyChooseUs" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                            <?php if ($dupWhyImage !== '') { ?>
                                <div class="duplicate-media-preview">
                                    <img src="<?php echo iswPreviewImageUrl($dupWhyImage); ?>" alt="">
                                    <button type="button" class="btn btn-red btn-sm duplicateRemoveMediaBtn" data-target-input="oldWhyChooseUs">Remove</button>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow" name="whyChooseTitle" value="<?php echo iswEscHtml($dupWhyTitle); ?>" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" class="form-control inside-shadow" name="whyChooseDescription" value="<?php echo iswEscHtml($dupWhyDesc); ?>" placeholder="Description">
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <h4>Step 1</h4>
                                <div class="form-group">
                                    <label>Step 1 icon</label>
                                    <input type="hidden" name="oldStepIcon_1" value="<?php echo iswEscHtml($dupWhySteps[1]['icon']); ?>">
                                    <input type="file" name="stepIcon_1" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                                    <?php if ($dupWhySteps[1]['icon'] !== '') { ?>
                                        <div class="duplicate-media-preview">
                                            <img src="<?php echo iswPreviewImageUrl($dupWhySteps[1]['icon']); ?>" alt="">
                                            <button type="button" class="btn btn-red btn-sm duplicateRemoveMediaBtn" data-target-input="oldStepIcon_1">Remove</button>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="form-group">
                                    <label>Step 1 Description</label>
                                    <input type="text" class="form-control inside-shadow" name="stepHeader_1" value="<?php echo iswEscHtml($dupWhySteps[1]['description']); ?>" placeholder="Step 1 Description">
                                </div>
                            </div>
                            <div class="col-4">
                                <h4>Step 2</h4>
                                <div class="form-group">
                                    <label>Step 2 icon</label>
                                    <input type="hidden" name="oldStepIcon_2" value="<?php echo iswEscHtml($dupWhySteps[2]['icon']); ?>">
                                    <input type="file" name="stepIcon_2" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                                    <?php if ($dupWhySteps[2]['icon'] !== '') { ?>
                                        <div class="duplicate-media-preview">
                                            <img src="<?php echo iswPreviewImageUrl($dupWhySteps[2]['icon']); ?>" alt="">
                                            <button type="button" class="btn btn-red btn-sm duplicateRemoveMediaBtn" data-target-input="oldStepIcon_2">Remove</button>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="form-group">
                                    <label>Step 2 Description</label>
                                    <input type="text" class="form-control inside-shadow" name="stepHeader_2" value="<?php echo iswEscHtml($dupWhySteps[2]['description']); ?>" placeholder="Step 2 Description">
                                </div>
                            </div>
                            <div class="col-4">
                                <h4>Step 3</h4>
                                <div class="form-group">
                                    <label>Step 3 icon</label>
                                    <input type="hidden" name="oldStepIcon_3" value="<?php echo iswEscHtml($dupWhySteps[3]['icon']); ?>">
                                    <input type="file" name="stepIcon_3" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                                    <?php if ($dupWhySteps[3]['icon'] !== '') { ?>
                                        <div class="duplicate-media-preview">
                                            <img src="<?php echo iswPreviewImageUrl($dupWhySteps[3]['icon']); ?>" alt="">
                                            <button type="button" class="btn btn-red btn-sm duplicateRemoveMediaBtn" data-target-input="oldStepIcon_3">Remove</button>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="form-group">
                                    <label>Step 3 Description</label>
                                    <input type="text" class="form-control inside-shadow" name="stepHeader_3" value="<?php echo iswEscHtml($dupWhySteps[3]['description']); ?>" placeholder="Step 3 Description">
                                </div>
                            </div>
                        </div>
                    </section>

                <?php
                } else {
                ?>
                    <div class="form-group fullscreen-editor">
                        <textarea name="description" class="editor inside-shadow"><?php echo iswEscHtml($dupDesc); ?></textarea>
                    </div>

                    <!-- Treatment description and other things -->

                    <section style="margin-bottom:20px">
                        <h3>Treatment Details</h3>
                        <ul class='tabs'>
                            <li id="productGeneral" class='active'>General</li>
                            <li id="productInventory">Inventory</li>
                            <li id="productGallery">Gallery</li>
                            <li id="productAdditionalDetails">Additional Details</li>
                        </ul>
                        <div class="productGeneral generalTab">
                            <div class="form-group">
                                <label>Treatment short description / Highlights</label>
                                <textarea name="highlight" class="form-control inside-shadow" placeholder="Treatment short description / Highlights"><?php echo iswEscHtml($dupHighlight); ?></textarea>
                            </div>
                        </div>
                        <div class="productInventory generalTab" style="display: none;">
                            <div class="box-2">
                                <div class="form-group">
                                    <label>Sale price (₹)</label>
                                    <input type="number" onwheel="this.blur()" class="form-control inside-shadow" name="salePrice" value="<?php echo iswEscHtml($dupSalePrice); ?>" placeholder="Sale price">
                                </div>
                            </div>
                            <div class="box-2">
                                <div class="form-group">
                                    <label>Regular price in ₹ ( MRP )</label>
                                    <input type="number" onwheel="this.blur()" class="form-control inside-shadow" name="mrp" value="<?php echo iswEscHtml($dupMrp); ?>" placeholder="Regular price">
                                </div>
                            </div>
                            <div class="box-2">
                                <div class="form-group">
                                    <label>SKU</label>
                                    <input type="text" class="form-control inside-shadow" name="sku" value="<?php echo iswEscHtml($dupSku); ?>" placeholder="SKU">
                                </div>
                            </div>
                        </div>
                        <div class="productShipping generalTab" style="display: none;">
                            <div class="box-1">
                                <div class="form-group">
                                    <label>Weight (kg)</label>
                                    <input type="text" class="form-control inside-shadow" name="weight" value="<?php echo iswEscHtml($dupWeight); ?>" placeholder="Weight">
                                </div>
                            </div>
                            <label>Dimensions (cm)</label>
                            <div class="form-group">
                                <div class="box-3">
                                    <label>Lenght</label>
                                    <input type="text" class="form-control inside-shadow" name="lenght" value="<?php echo iswEscHtml($dupLenght); ?>" placeholder="Lenght">
                                </div>
                                <div class="box-3">
                                    <label>Width</label>
                                    <input type="text" class="form-control inside-shadow" name="width" value="<?php echo iswEscHtml($dupWidth); ?>" placeholder="Width">
                                </div>
                                <div class="box-3">
                                    <label>Height</label>
                                    <input type="text" class="form-control inside-shadow" name="height" value="<?php echo iswEscHtml($dupHeight); ?>" placeholder="Height">
                                </div>
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
                        <div class="productAdditionalDetails generalTab" style="display: none;">

                        </div>
                    </section>
                <?php
                }

                ?>

                <!-- SEO & social media marketing -->

                <section style="margin-top: 30px">
                    <h3>Advance Options for digital marketing</h3>
                    <ul class='tabs'>
                        <li id="seo" class='active'>Search Engine Optimization</li>
                        <li id="fb">Facebook</li>
                        <li id="tw">Twitter</li>
                    </ul>
                    <div class="seo generalTab">
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control inside-shadow" value="<?php echo iswEscHtml($dupMetaTitle); ?>" placeholder="Meta title">
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow" placeholder="Meta Description"><?php echo iswEscHtml($dupMetaDesc); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Meta Keywords</label>
                            <textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow" placeholder="Enter keywords of the post ( use , to seprate keywords )"><?php echo iswEscHtml($dupMetaKey); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Canonical URL</label>
                            <input type="text" name="canonical" class="form-control inside-shadow" id="canonical" value="<?php echo iswEscHtml($dupCanonical); ?>" placeholder="Actual URL of the <?php echo $postType ?>">
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
                            <input type="text" name="fb_title" id="fb_title" class="form-control inside-shadow" value="<?php echo iswEscHtml($dupFbTitle); ?>" placeholder="Facebook title">
                        </div>
                        <div class="form-group">
                            <label>Facebook Description</label>
                            <textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow" placeholder="Facebook Description"><?php echo iswEscHtml($dupFbDesc); ?></textarea>
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
                            <input type="text" name="tw_title" id="tw_title" class="form-control inside-shadow" value="<?php echo iswEscHtml($dupTwTitle); ?>" placeholder="Twitter title">
                        </div>
                        <div class="form-group">
                            <label>Twitter Description</label>
                            <textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow" placeholder="Twitter Description"><?php echo iswEscHtml($dupTwDesc); ?></textarea>
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
                        <input type="checkbox" value="1" name='featurePost' <?php echo $dupIsFeatured ? 'checked' : ''; ?>> Mark as feature treatment
                    </div>
                </div>
                <h2>Categories</h2>
                <div class="action-box height-200 inside-shadow">
                    <?php
                    if ($categoryList = mysqli_query($connection, "SELECT category_id, category_description, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_id=link_relation_id AND link_type='category' AND meta_relation_id=link_id AND category_type='{$postType}' AND category_site_id='" . SITE_ID . "' AND link_status=200 ORDER BY category_title ASC")) {
                        while ($category = mysqli_fetch_assoc($categoryList)) { ?>
                            <div class="form-group">
                                <input type="radio" name="category" value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>" <?php echo (((int)$category['category_id'] === $selectedCategoryId) ? 'checked' : '') ?>>
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
                    <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                    <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif <strong>Recommended
                            file size maximum 500kb</p>
                </div>
                <div class="img-preview featureImagePreviewBox action-box" style="display: <?php echo ($dupFeatureImg !== '') ? 'block' : 'none'; ?>;">
                    <img src="<?php echo ($dupFeatureImg !== '') ? iswPreviewImageUrl($dupFeatureImg) : '#'; ?>" id="featureImagePath" alt="">
                    <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
                </div>
            </div>
        </form>
    </div>
</div>