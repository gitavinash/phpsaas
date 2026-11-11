<?php
require_once FUNCTIONS . 'duplicate.php';

$dupSourceId = iswGetDuplicateSourceId();
$dupData = ($dupSourceId > 0) ? iswGetDuplicateRecord($connection, 'offer', $dupSourceId, SITE_ID) : null;
$dupRow = ($dupData !== null) ? $dupData['row'] : array();
$dupTitle = ($dupData !== null) ? (string)$dupData['duplicate_title'] : '';
$dupUrl = ($dupData !== null) ? (string)$dupData['duplicate_slug'] : '';
$dupDesc = ($dupData !== null) ? html_entity_decode((string)($dupRow['page_description'] ?? ''), ENT_QUOTES, 'UTF-8') : '';
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
$dupFeatureImg = (string)($dupRow['page_image'] ?? '');
$dupThumbImg = (string)($dupRow['page_thumbnail'] ?? '');
?>
<div class="product-add-ui">
    <h2>Add new offer</h2>
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
                        <p><span><?php echo SITE_DOMAIN ?>/</span></p>
                        <input type="text" id="url" class="form-control" name="url" value="<?php echo iswEscHtml($dupUrl); ?>">
                    </div>
                </div>
                <div class="form-group fullscreen-editor">
                    <textarea name="description" class="editor inside-shadow"><?php echo iswEscHtml($dupDesc); ?></textarea>
                </div>
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
                                placeholder="Meta title" value="<?php echo iswEscHtml($dupMetaTitle); ?>">
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
                                placeholder="Actual URL of the <?php echo $postType ?>" value="<?php echo iswEscHtml($dupCanonical); ?>">
                        </div>
                        <div class="form-group">
                            <label>Allow search engines to show this Post in search results?</label>
                            <select name="robots" class="form-control inside-shadow">
                                <option value="index, follow" <?php echo (($dupRobots == 'index, follow') ? 'selected' : ''); ?>>Yes</option>
                                <option value="noindex, nofollow" <?php echo (($dupRobots == 'noindex, nofollow') ? 'selected' : ''); ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="fb generalTab" style="display: none;">
                        <div class="facebookPreviewBox" style="display: <?php echo (($dupFbImg == '') ? 'none' : 'block'); ?>;">
                            <input type="hidden" name="oldFacebookImage" value="<?php echo iswEscHtml($dupFbImg); ?>">
                            <div class="img"><img src="<?php echo iswPreviewImageUrl($dupFbImg); ?>" id="facebookPath" alt=""></div>
                            <p id="facebookChangeImage" class='changeImage'>Remove facebook feature image</p>
                        </div>
                        <div class="facebookUploadBox" style="display: <?php echo (($dupFbImg == '') ? 'block' : 'none'); ?>;">
                            <span>Set facebook feature image</span>
                            <input type='file' id="facebookImage" name='facebookImage' />
                        </div>
                        <div class="form-group">
                            <label>Facebook Title</label>
                            <input type="text" name="fb_title" id="fb_title" class="form-control inside-shadow"
                                placeholder="Facebook title" value="<?php echo iswEscHtml($dupFbTitle); ?>">
                        </div>
                        <div class="form-group">
                            <label>Facebook Description</label>
                            <textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow"
                                placeholder="Facebook Description"><?php echo iswEscHtml($dupFbDesc); ?></textarea>
                        </div>
                    </div>
                    <div class="tw generalTab" style="display: none;">
                        <div class="twitterPreviewBox" style="display: <?php echo (($dupTwImg == '') ? 'none' : 'block'); ?>;">
                            <input type="hidden" name="oldTwitterImage" value="<?php echo iswEscHtml($dupTwImg); ?>">
                            <div class="img"><img src="<?php echo iswPreviewImageUrl($dupTwImg); ?>" id="twitterPath" alt=""></div>
                            <p id="twitterChangeImage" class='changeImage'>Remove twitter feature image</p>
                        </div>
                        <div class="twitterUploadBox" style="display: <?php echo (($dupTwImg == '') ? 'block' : 'none'); ?>;">
                            <span>Set twitter feature image</span>
                            <input type='file' id="twitterImage" name='twitterImage' />
                        </div>
                        <div class="form-group">
                            <label>Twitter Title</label>
                            <input type="text" name="tw_title" id="tw_title" class="form-control inside-shadow"
                                placeholder="Twitter title" value="<?php echo iswEscHtml($dupTwTitle); ?>">
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
                    <!-- <input type="button" id="draftBtn"  class="btn btn-yellow btn-block" value="Save Draft"> -->
                    <input type="hidden" name="type" value="<?php echo ltrim(rtrim(ADMIN_BASE_URL, '/'), '/') ?>">
                </div>

                <h2>Feature Image</h2>
                <div class="form-group featureImageUploadBox upload-btn" style="display: <?php echo (($dupFeatureImg != '') ? 'none' : 'block'); ?>;">
                    <span>Set Feature Image</span>
                    <input type="hidden" name="oldFeatureImage" value="<?php echo iswEscHtml($dupFeatureImg); ?>">
                    <input type="hidden" name="oldThumbnailImage" value="<?php echo iswEscHtml($dupThumbImg); ?>">
                    <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                    <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif, .doc, .docx, .xls,
                        .xlsx, .pdf, .txt, .csv <strong>Maximum upload limit is 300 MB</strong></p>
                </div>
                <div class="img-preview featureImagePreviewBox action-box" style="display: <?php echo (($dupFeatureImg != '') ? 'block' : 'none'); ?>;">
                    <img src="<?php echo (($dupFeatureImg != '') ? iswPreviewImageUrl($dupFeatureImg) : '#'); ?>" id="featureImagePath" alt="">
                    <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
                </div>

            </div>
        </form>
    </div>
</div>