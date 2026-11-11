<?php
require_once FUNCTIONS . 'duplicate.php';

$dupSourceId = iswGetDuplicateSourceId();
$dupData = ($dupSourceId > 0) ? iswGetDuplicateRecord($connection, 'package', $dupSourceId, SITE_ID) : null;
$dupRow = ($dupData !== null) ? $dupData['row'] : array();
$dupTitle = ($dupData !== null) ? (string)$dupData['duplicate_title'] : '';
$dupUrl = ($dupData !== null) ? (string)$dupData['duplicate_slug'] : '';
$dupDesc = ($dupData !== null) ? html_entity_decode((string)($dupRow['post_description'] ?? ''), ENT_QUOTES, 'UTF-8') : '';
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
$selectedCategoryId = ($dupData !== null) ? (int)($dupRow['post_category'] ?? 1) : 1;
$dupTagsCsv = ($dupData !== null) ? (string)$dupData['tags_csv'] : '';
$dupFeatureImg = (string)($dupRow['post_image'] ?? '');
$dupThumbImg = (string)($dupRow['post_thumbnail'] ?? '');
?>
<div class="product-add-ui">
	<h2>Add new <?php echo $postType ?></h2>
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
						<p><span><?php echo SITE_DOMAIN ?>/</span></p>
						<input type="text" id="url" class="form-control" name="url" value="<?php echo iswEscHtml($dupUrl); ?>">
					</div>
				</div>
				<div class="form-group fullscreen-editor">
					<textarea name="description" class="editor inside-shadow"><?php echo iswEscHtml($dupDesc); ?></textarea>
				</div>

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
							<input type="text" name="meta_title" id="meta_title" class="form-control inside-shadow" placeholder="Meta title" value="<?php echo iswEscHtml($dupMetaTitle); ?>">
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
							<input type="text" name="canonical" class="form-control inside-shadow" id="canonical" placeholder="Actual URL of the <?php echo $postType ?>" value="<?php echo iswEscHtml($dupCanonical); ?>">
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
							<input type="text" name="fb_title" id="fb_title" class="form-control inside-shadow" placeholder="Facebook title" value="<?php echo iswEscHtml($dupFbTitle); ?>">
						</div>
						<div class="form-group">
							<label>Facebook Description</label>
							<textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow" placeholder="Facebook Description"><?php echo iswEscHtml($dupFbDesc); ?></textarea>
						</div>
					</div>
					<div class="tw generalTab" style="display: none;">
						<div class="twitterPreviewBox" style="display: <?php echo (($dupTwImg == '') ? 'none' : 'block'); ?>;">
							<input type="hidden" name="oldTwitterImage" value="<?php echo iswEscHtml($dupTwImg); ?>">
							<div class="img"><img src="<?php echo iswPreviewImageUrl($dupTwImg); ?>" id="twitterPath" alt=""></div>
							<p id="twitterChangeImage" class="changeImage">Remove twitter feature image</p>
						</div>
						<div class="twitterUploadBox" style="display: <?php echo (($dupTwImg == '') ? 'block' : 'none'); ?>;">
							<span>Set Twitter feature image</span>
							<input type='file' id="twitterImage" name='twitterImage' />
						</div>
						<div class="form-group">
							<label>Twitter Title</label>
							<input type="text" name="tw_title" id="tw_title" class="form-control inside-shadow" placeholder="Twitter title" value="<?php echo iswEscHtml($dupTwTitle); ?>">
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
					<input type="hidden" name="type" value="<?php echo $postType ?>">
					<input type="hidden" name="action" value="publish">
				</div>
				<div class="form-group">
					<h2>Featured</h2>
					<div class="action-box inside-shadow" style="padding: 10px">
						<input type="checkbox" value="1" name='featurePost' <?php echo ($dupIsFeatured ? 'checked' : ''); ?>> Mark as feature
					</div>
				</div>
				<h2>Categories</h2>
				<div class="action-box height-200 inside-shadow">
					<?php
					if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_site_id='" . SITE_ID . "' AND meta_relation_id=link_id AND link_type='category' AND link_relation_id=category_id AND link_status=200 AND category_status=1 AND category_type='" . rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') . "' AND link_site_id=" . SITE_ID)) {
						while ($category = mysqli_fetch_assoc($categoryList)) { ?>
							<div class="form-group">
								<input type="radio" name="category" value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>" <?php echo (((int)$category['category_id'] === $selectedCategoryId) ? 'checked' : ''); ?>> <?php echo ucwords($category['category_title']); ?>
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
				<div class="form-group featureImageUploadBox upload-btn" style="display: <?php echo (($dupFeatureImg != '') ? 'none' : 'block'); ?>;">
					<span>Set Feature Image</span>
					<input type="hidden" name="oldFeatureImage" value="<?php echo iswEscHtml($dupFeatureImg); ?>">
					<input type="hidden" name="oldThumbnailImage" value="<?php echo iswEscHtml($dupThumbImg); ?>">
					<input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
					<p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif <strong>Recommended file size maximum 500kb</p>
				</div>
				<div class="img-preview featureImagePreviewBox action-box" style="display: <?php echo (($dupFeatureImg != '') ? 'block' : 'none'); ?>;">
					<img src="<?php echo (($dupFeatureImg != '') ? iswPreviewImageUrl($dupFeatureImg) : '#'); ?>" id="featureImagePath" alt="">
					<p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
				</div>
			</div>
		</form>
	</div>
</div>