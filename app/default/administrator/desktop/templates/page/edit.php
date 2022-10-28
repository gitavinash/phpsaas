<?php

$id = base64_decode($id);

if ($pageDetails = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "page, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE link_type='page' AND link_relation_id=page_id AND meta_relation_id=link_id AND page_id='{$id}' AND link_status!=301 AND link_type='page' LIMIT 1")) {
    if (mysqli_num_rows($pageDetails) != 0) {
        extract(mysqli_fetch_assoc($pageDetails));

        $page_section = json_decode(base64_decode($page_section));
    } else {
        exit(); ?>
<script type="text/javascript">
alert('Invalid action found');
window.location.href = 'dashboard/page/?type=list&status=all';
</script>
<?php
    }
}

?>
<h2>Update page <span><a href="<?php echo $postType ?>/?type=add&editor=true" class='btn btn-blue btn-sm'>Add
            New</a></span></h2>

<div class="form-box">
    <form method="post" id="formData" enctype="multipart/form-data">
        <div class="left-box">
            <div class="form-group">
                <input type="text" class="form-control inside-shadow" name="title" placeholder="Title"
                    value="<?php echo $page_title ?>" required="">
            </div>
            <div class="form-group">
                <div class="url">
                    <?php
                    $url = explode('/', ltrim(rtrim($link_url, '/'), '/'));
                    ?>
                    <p><strong>Permalink</strong></p>
                    <p><span><?php echo SITE_DOMAIN ?>/</span></p>
                    <input type="text" id="url" class="form-control" value="<?php echo end($url); ?>" name="url">
                    <input type="hidden" name='oldURL' value='<?php echo $link_url ?>'>
                </div>
            </div>
            <?php

            if (SITE_ID == 31 && $page_id == 50) { ?>

            <section style="margin-top:30px">
                <h3>Technology Option 1</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" value="" name="technologyHeader_1"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="technologyDescription_1"
                        placeholder="Description"></textarea>
                </div>
                <div class="row">
                    <h4>Treatment Option 1 Feature Image</h4>
                    <div class="form-group">
                        <label>Feature image</label>
                        <input type="file" name="technologyFeatureImage_1" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>

            <section style="margin-top:30px">
                <h3>Technology Option 2</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" value="" name="technologyHeader_2"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="technologyDescription_2"
                        placeholder="Description"></textarea>
                </div>
                <div class="row">
                    <h4>Treatment Option 2 Feature Image</h4>
                    <div class="form-group">
                        <label>Feature image</label>
                        <input type="file" name="technologyFeatureImage_2" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>
            <section style="margin-top:30px">
                <h3>Technology Option 3</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" value="" name="technologyHeader_3"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="technologyDescription_3"
                        placeholder="Description"></textarea>
                </div>
                <div class="row">
                    <h4>Treatment Option 3 Feature Image</h4>
                    <div class="form-group">
                        <label>Feature image</label>
                        <input type="file" name="technologyFeatureImage_3" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>
            <section style="margin-top:30px">
                <h3>Technology Option 4</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" value="" name="technologyHeader_4"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="technologyDescription_4"
                        placeholder="Description"></textarea>
                </div>
                <div class="row">
                    <h4>Treatment Option 4 Feature Image</h4>
                    <div class="form-group">
                        <label>Feature image</label>
                        <input type="file" name="technologyFeatureImage_4" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>
            <section style="margin-top:30px">
                <h3>Technology Option 5</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" value="" name="technologyHeader_5"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="technologyDescription_5"
                        placeholder="Description"></textarea>
                </div>
                <div class="row">
                    <h4>Treatment Option 5 Feature Image</h4>
                    <div class="form-group">
                        <label>Feature image</label>
                        <input type="file" name="technologyFeatureImage_5" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>
            <section style="margin-top:30px">
                <h3>Technology Option 6</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" value="" name="technologyHeader_6"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="technologyDescription_6"
                        placeholder="Description"></textarea>
                </div>
                <div class="row">
                    <h4>Treatment Option 6 Feature Image</h4>
                    <div class="form-group">
                        <label>Feature image</label>
                        <input type="file" name="technologyFeatureImage_6" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>
            <section style="margin-top:30px">
                <h3>Technology Option 7</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" value="" name="technologyHeader_7"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="technologyDescription_7"
                        placeholder="Description"></textarea>
                </div>
                <div class="row">
                    <h4>Treatment Option 7 Feature Image</h4>
                    <div class="form-group">
                        <label>Feature image</label>
                        <input type="file" name="technologyFeatureImage_7" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>
            <section style="margin-top:30px">
                <h3>Technology Option 8</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" value="" name="technologyHeader_8"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="technologyDescription_8"
                        placeholder="Description"></textarea>
                </div>
                <div class="row">
                    <h4>Treatment Option 8 Feature Image</h4>
                    <div class="form-group">
                        <label>Feature image</label>
                        <input type="file" name="technologyFeatureImage_8" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>
            <section style="margin-top:30px">
                <h3>Technology Option 9</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" value="" name="technologyHeader_9"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="technologyDescription_9"
                        placeholder="Description"></textarea>
                </div>
                <div class="row">
                    <h4>Treatment Option 9 Feature Image</h4>
                    <div class="form-group">
                        <label>Feature image</label>
                        <input type="file" name="technologyFeatureImage_9" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>
            <section style="margin-top:30px">
                <h3>Technology Option 10</h3>
                <hr>
                <div class="form-group">
                    <label>Header</label>
                    <input type="text" class="form-control inside-shadow" value="" name="technologyHeader_10"
                        placeholder="Header">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="technologyDescription_10"
                        placeholder="Description"></textarea>
                </div>
                <div class="row">
                    <h4>Treatment Option 10 Feature Image</h4>
                    <div class="form-group">
                        <label>Feature image</label>
                        <input type="file" name="technologyFeatureImage_10" class="full-width"
                            accept="image/x-png, image/gif, image/jpeg">
                    </div>
                </div>
            </section>

            <?php
            } else { ?>
            <div class="form-group fullscreen-editor">
                <textarea name="description"
                    class="editor inside-shadow"><?php echo html_entity_decode($page_description); ?></textarea>
            </div>
            <?php
            }

            if (SITE_ID == 31 && $page_id == 51) { ?>

            <section style="margin-top:30px">
                <h3>Feature Video</h3>
                <hr>
                <div class="form-group">
                    <label>Youtube video url</label>
                    <input type="text" class="form-control inside-shadow" name="highlight"
                        value="<?php echo $page_highlight ?>" placeholder="Feature Video">
                </div>
            </section>

            <?php
            }

            ?>
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
                            placeholder="Enter keywords of the post ( use , to seprate keywords )"><?php echo $meta_keyword ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Canonical URL</label>
                        <input type="text" name="canonical" class="form-control inside-shadow"
                            value="<?php echo $meta_canonical; ?>" id="canonical"
                            placeholder="Actual URL of the <?php echo $postType ?>">
                    </div>
                    <div class="form-group">
                        <label>Allow search engines to show this post in search results?</label>
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
                <input type="button" id="updatePost" class="btn btn-green btn-block"
                    value=" <?php echo (($page_status == 1) ? 'Update ' : 'Publish '); ?>post">
                <!-- <input type="submit" id="draftPost" class="btn btn-yellow btn-block" value="Save Draft"> -->
                <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name='postStatus' value='<?php echo $page_status ?>'>
                <input type="hidden" name="id" value="<?php echo $id ?>">
                <input type="hidden" name="postType" value="<?php echo $page_type ?>">
                <a href='<?php echo SITE_URL . $link_url; ?>' class='btn btn-block btn-blue' target='_blank'>View live
                    post</a>
            </div>
            <h2>Feature Image</h2>
            <div class="form-group featureImageUploadBox upload-btn"
                style="display: <?php echo (($page_image != '') ? 'none' : 'block') ?>;">
                <span>Set Feature Image</span>
                <input type="hidden" name="oldFeatureImage" value="<?php echo $page_image ?>">
                <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif<strong>Maximum upload
                        limit is 300 MB</strong></p>
            </div>
            <div class="img-preview featureImagePreviewBox action-box"
                style="display: <?php echo (($page_image != '') ? 'block' : 'none') ?>;">
                <img src="//<?php echo SITE_DOMAIN . $page_image ?>" id="featureImagePath" alt="">
                <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
            </div>
        </div>
    </form>
</div>