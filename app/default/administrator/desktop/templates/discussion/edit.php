<?php

$discussion_id = base64_decode(mysqli_real_escape_string($connection, $_GET['id']));

if ($thoughtDetails = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "discussion, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE link_type='discussion' AND link_relation_id=discussion_id AND meta_relation_id=link_id AND discussion_id='{$discussion_id}' AND link_status!=301 AND link_site_id=" . SITE_ID . " LIMIT 1")) {
	if (mysqli_num_rows($thoughtDetails) != 0) {
		extract(mysqli_fetch_assoc($thoughtDetails));
	} else { ?>
<script type="text/javascript">
alert('Invalid action found');
window.location.href = 'dashboard/discussion/?type=list&status=all';
</script>
<?php
	}
}

?>
<h2>Update <?php echo (($link_status == '206') ? 'Drafted ' : '') . ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?>
    <span><a href="<?php echo $postType ?>/?type=add&editor=true" class='btn btn-blue btn-sm'>Add New</a></span>
</h2>

<div class="form-box">
    <form id="formData" enctype="multipart/form-data" autocomplete="off">
        <div class="left-box">
            <div class="form-group">
                <input type="text" class="form-control inside-shadow" name="title" placeholder="Title"
                    value="<?php echo $discussion_title ?>" required="">
            </div>
            <div class="form-group">
                <div class="url">
                    <?php
					$url = explode('/', ltrim(rtrim($link_url, '/'), '/'));
					?>
                    <p><strong>Permalink</strong></p>
                    <p><span><?php echo SITE_DOMAIN . '/...'; ?>/</span></p>
                    <input type="text" id="url" class="form-control" value="<?php echo end($url); ?>" name="url">
                    <input type="hidden" name='oldURL' value='<?php echo $link_url ?>'>
                </div>
            </div>
            <div class="form-group fullscreen-editor">
                <textarea name="description"
                    class="editor inside-shadow"><?php echo html_entity_decode($discussion_description); ?></textarea>
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
                        <input type="text" name="meta_title" value="<?php echo $meta_title; ?>" id="meta_title"
                            class="form-control inside-shadow" placeholder="Meta title">
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow"
                            placeholder="Meta Description"><?php echo $meta_description ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow"
                            placeholder="Enter keywords of the discussion ( use , to seprate keywords )"><?php echo $meta_keyword ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Canonical URL</label>
                        <input type="text" name="canonical" class="form-control inside-shadow"
                            value="<?php echo $meta_canonical; ?>" id="canonical"
                            placeholder="Actual URL of the <?php echo $postType ?>">
                    </div>
                    <div class="form-group">
                        <label>Allow search engines to show this discussion in search results?</label>
                        <select name="robots" class="form-control inside-shadow">
                            <option value="index, follow"
                                <?php echo (($meta_robots == 'index, follow') ? 'selected' : '') ?>>Yes</option>
                            <option value="noindex, nofollow"
                                <?php echo (($meta_robots == 'noindex, nofollow') ? 'selected' : '') ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="fb generalTab" style="display: none;">
                    <div class="facebookPreviewBox"
                        style="display: <?php echo (($fb_img == '') ? 'none' : 'block') ?>;">
                        <input type="hidden" name="oldFacebookImage" value="<?php echo $fb_img ?>">
                        <div class="img"><img src="<?php echo $fb_img; ?>" id="facebookPath" alt=""></div>
                        <p id="facebookChangeImage" class='changeImage'>Remove facebook feature image</p>
                    </div>
                    <div class="facebookUploadBox" style="display: <?php echo (($fb_img == '') ? 'block' : 'none') ?>;">
                        <span>Set facebook feature image</span>
                        <input type='file' id="facebookImage" name='facebookImage' />
                    </div>
                    <div class="form-group">
                        <label>Facebook Title</label>
                        <input type="text" name="fb_title" class="form-control inside-shadow"
                            value="<?php echo $fb_title ?>" placeholder="Facebook title">
                    </div>
                    <div class="form-group">
                        <label>Facebook Description</label>
                        <textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow"
                            placeholder="Facebook Description"><?php echo $fb_desc ?></textarea>
                    </div>
                </div>
                <div class="tw generalTab" style="display: none;">
                    <div class="twitterPreviewBox" style="display: <?php echo (($tw_img == '') ? 'none' : 'block') ?>;">
                        <input type="hidden" name="oldTwitterImage" value="<?php echo $tw_img ?>">
                        <div class="img"><img src="<?php echo $tw_img ?>" id="twitterPath" alt=""></div>
                        <p id="twitterChangeImage" class="changeImage">Remove twitter feature image</p>
                    </div>
                    <div class="twitterUploadBox" style="display: <?php echo (($fb_img == '') ? 'block' : 'none') ?>;">
                        <span>Set Twitter feature image</span>
                        <input type='file' id="twitterImage" name='twitterImage' />
                    </div>
                    <div class="form-group">
                        <label>Twitter Title</label>
                        <input type="text" name="tw_title" class="form-control inside-shadow"
                            value="<?php echo $tw_title; ?>" placeholder="Twitter title">
                    </div>
                    <div class="form-group">
                        <label>Twitter Description</label>
                        <textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow"
                            placeholder="Twitter Description"><?php echo $tw_desc ?></textarea>
                    </div>
                </div>
            </section>
        </div>
        <div class="right-box">
            <div class="action-box inside-shadow">
                <?php

				if ($discussion_status == 0) { ?>
                <input type="button" id="<?php echo $discussion_id ?>_discussion"
                    class="btn btn-green btn-block makeItLive" value="Make it live">
                <?php
				} else { ?>
                <input type="button" id="updatePost" class="btn btn-green btn-block"
                    value=" <?php echo (($discussion_status == 1) ? 'Update ' : 'Publish '); ?>discussion">
                <a href='<?php echo SITE_URL . $link_url; ?>' class='btn btn-block btn-blue' target='_blank'>View live
                    discussion</a>
                <?php
				}

				?>
                <!-- <input type="submit" id="draftPost" class="btn btn-yellow btn-block" value="Save Draft"> -->
                <input type="hidden" name="type" value="<?php echo $postType ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name='postStatus' value='<?php echo $discussion_status ?>'>
                <input type="hidden" name="id" value="<?php echo $discussion_id ?>">
                <input type="hidden" name="postType" value="<?php echo $discussion_type ?>">
            </div>
            <div class="form-group">
                <h2>Featured</h2>
                <div class="action-box inside-shadow" style="padding: 10px">
                    <input type="checkbox" value="1" name='featurePost'
                        <?php echo (($link_is_featured == '1') ? 'checked' : '') ?>> Mark as feature
                </div>
            </div>
            <h2>Categories</h2>
            <div class="action-box height-200 inside-shadow">
                <?php
				if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_site_id='" . SITE_ID . "' AND meta_relation_id=link_id AND link_type='category' AND link_relation_id=category_id AND link_status=200 AND category_status=1 AND category_type='" . rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') . "' AND link_site_id=" . SITE_ID)) {
					while ($category = mysqli_fetch_assoc($categoryList)) {
						if ($category['category_id'] == ${'discussion_category'}) { ?>
                <div class="form-group">
                    <input type="radio" name="category"
                        value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>"
                        <?php echo (($category['category_id'] == $discussion_category) ? 'checked' : '') ?>>
                    <?php echo ucwords($category['category_title']); ?>
                </div>
                <?php
						} else { ?>
                <div class="form-group">
                    <input type="radio" name="category"
                        value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>">
                    <?php echo ucwords($category['category_title']); ?>
                </div>
                <?php
						}
					}
				}
				?>
            </div>
            <h2>Discussion Type</h2>
            <div class="action-box height-200 inside-shadow">
                <div class="form-group">
                    <input type="radio" name="category" value="discussion"
                        <?php echo (($discussion_type == 'discussion') ? 'checked' : '') ?>> Discussion
                </div>
                <div class="form-group">
                    <input type="radio" name="category" value="questions"
                        <?php echo (($discussion_type == 'questions') ? 'checked' : '') ?>> Questions
                </div>
                <div class="form-group">
                    <input type="radio" name="category" value="success_story"
                        <?php echo (($discussion_type == 'success_story') ? 'checked' : '') ?>> Success Story
                </div>
            </div>
            <h2>Tags</h2>
            <?php
			$tagpostID = (($discussion_parent_id == 0) ? $discussion_id : $discussion_parent_id);

			if ($tagList = mysqli_query($connection, "SELECT tag_title FROM " . TABLE_PREFIX . "tag WHERE tag_relation_id='{$tagpostID}' AND tag_site_id='" . SITE_ID . "' AND tag_type='discussion'")) {
				if (mysqli_num_rows($tagList) != 0) {
					while ($tagsName = mysqli_fetch_assoc($tagList)) {
						$tags[] = $tagsName['tag_title'];
					}
					$tags = implode(',', $tags);
				} else {
					$tags = '';
				}
			}
			?>
            <div class="action-box inside-shadow">
                <div class="form-group">
                    <input type="text" class='tags' />
                    <input type="hidden" id="tagValue" value="<?php echo $tags; ?>">
                    <input type="hidden" id='tags' name="tags" value=''>
                </div>
            </div>
            <div class="tagList" style="display: none;"></div>
            <h2>discussion date</h2>
            <div class="form-group">
                <input type="text" name="thoughtDate" class="form-control inside-shadow thoughtDatepicker"
                    value="<?php echo date('m/d/Y', strtotime($link_published)); ?>">
                <?php echo "<script>var thoughtDate=" . date('d-m-Y', strtotime($link_published)) . "</script>" ?>
            </div>
            <h2>Feature Image</h2>
            <div class="form-group featureImageUploadBox upload-btn"
                style="display: <?php echo (($discussion_image != '') ? 'none' : 'block') ?>;">
                <span>Set Feature Image</span>
                <input type="hidden" name="oldFeatureImage" value="<?php echo $discussion_image ?>">
                <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif<strong>Maximum upload
                        limit is 300 MB</strong></p>
            </div>
            <div class="img-preview featureImagePreviewBox action-box"
                style="display: <?php echo (($discussion_image != '') ? 'block' : 'none') ?>;">
                <img src="//<?php echo SITE_DOMAIN . $discussion_image ?>" id="featureImagePath" alt="">
                <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
            </div>
        </div>
    </form>
</div>