<?php

$id = base64_decode($id);

if ($pageDetails = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "page INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=page_id AND link_site_id=" . SITE_ID . " AND link_type='page' AND link_status=200 LEFT JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id WHERE page_id='{$id}' AND page_site_id='" . SITE_ID . "'")) {
    if (mysqli_num_rows($pageDetails) != 0) {
        extract(mysqli_fetch_assoc($pageDetails));

        $pageSection = ((isset($page_section)) ? json_decode(base64_decode($page_section)) : '');
    } else {
?>
        <script type="text/javascript">
            alert('Invalid action found');
            window.location.href = 'dashboard/page/?type=list&status=all';
        </script>
<?php
    }
}

?>
<div class="product-add-ui">
    <h2>Update page <span><a href="<?php echo $postType ?>/?type=add&editor=true" class='btn btn-blue btn-sm'>Add
                New</a></span></h2>

    <div class="form-box">
        <form id="formData" enctype="multipart/form-data">
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

                <div class="form-group fullscreen-editor">
                    <textarea name="description"
                        class="editor inside-shadow"><?php echo htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <?php


                if (SITE_ID == 31 && $page_id == 50) { ?>

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

                if (SITE_ID == 31 && $page_id == 50) { ?>


                    <section style="margin-top:30px">
                        <h3>Technology Option 1</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->technology_option[0]->header) ?>"
                                name="technologyOptionHeader_1" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control inside-shadow"
                                name="technologyOptionDescription_1"><?php echo stripslashes($pageSection->technology_option[0]->description) ?></textarea>
                        </div>
                        <div class="row">
                            <h4>Technology Option 1 Feature Image</h4>
                            <div class="form-group">
                                <label>Before image</label>
                                <input type="file" name="technologyOptionFeatureImage_1" class="full-width"
                                    accept="image/x-png, image/gif, image/jpeg">
                            </div>
                        </div>
                    </section>
                    <section style="margin-top:30px">
                        <h3>Technology Option 2</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->technology_option[1]->header) ?>"
                                name="technologyOptionHeader_2" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control inside-shadow"
                                name="technologyOptionDescription_2"><?php echo stripslashes($pageSection->technology_option[1]->description) ?></textarea>
                        </div>
                        <div class="row">
                            <h4>Technology Option 2 Feature Image</h4>
                            <div class="form-group">
                                <label>Before image</label>
                                <input type="file" name="technologyOptionFeatureImage_2" class="full-width"
                                    accept="image/x-png, image/gif, image/jpeg">
                            </div>
                        </div>
                    </section>
                    <section style="margin-top:30px">
                        <h3>Technology Option 3</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->technology_option[2]->header) ?>"
                                name="technologyOptionHeader_3" placeholder="Header">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control inside-shadow"
                                name="technologyOptionDescription_3"><?php echo stripslashes($pageSection->technology_option[2]->description) ?></textarea>
                        </div>
                        <div class="row">
                            <h4>Technology Option 3 Feature Image</h4>
                            <div class="form-group">
                                <label>Before image</label>
                                <input type="file" name="technologyOptionFeatureImage_3" class="full-width"
                                    accept="image/x-png, image/gif, image/jpeg">
                            </div>
                        </div>
                    </section>
                    <section style="margin-top:30px">
                        <h3>Technology Option 4</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->technology_option[3]->header) ?>"
                                name="technologyOptionHeader_4" placeholder="Header">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control inside-shadow"
                                name="technologyOptionDescription_4"><?php echo stripslashes($pageSection->technology_option[3]->description) ?></textarea>
                        </div>
                        <div class="row">
                            <h4>Technology Option 4 Feature Image</h4>
                            <div class="form-group">
                                <label>Before image</label>
                                <input type="file" name="technologyOptionFeatureImage_4" class="full-width"
                                    accept="image/x-png, image/gif, image/jpeg">
                            </div>
                        </div>
                    </section>
                    <section style="margin-top:30px">
                        <h3>Technology Option 5</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->technology_option[4]->header) ?>"
                                name="technologyOptionHeader_5" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control inside-shadow"
                                name="technologyOptionDescription_5"><?php echo stripslashes($pageSection->technology_option[4]->description) ?></textarea>
                        </div>
                        <div class="row">
                            <h4>Technology Option 5 Feature Image</h4>
                            <div class="form-group">
                                <label>Before image</label>
                                <input type="file" name="technologyOptionFeatureImage_5" class="full-width"
                                    accept="image/x-png, image/gif, image/jpeg">
                            </div>
                        </div>
                    </section>
                    <section style="margin-top:30px">
                        <h3>Technology Option 6</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->technology_option[5]->header) ?>"
                                name="technologyOptionHeader_6" placeholder="Header">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control inside-shadow"
                                name="technologyOptionDescription_6"><?php echo stripslashes($pageSection->technology_option[5]->description) ?></textarea>
                        </div>
                        <div class="row">
                            <h4>Technology Option 6 Feature Image</h4>
                            <div class="form-group">
                                <label>Before image</label>
                                <input type="file" name="technologyOptionFeatureImage_6" class="full-width"
                                    accept="image/x-png, image/gif, image/jpeg">
                            </div>
                        </div>
                    </section>
                    <section style="margin-top:30px">
                        <h3>Technology Option 7</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->technology_option[6]->header) ?>"
                                name="technologyOptionHeader_7" placeholder="Header">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control inside-shadow"
                                name="technologyOptionDescription_7"><?php echo stripslashes($pageSection->technology_option[6]->description) ?></textarea>
                        </div>
                        <div class="row">
                            <h4>Technology Option 7 Feature Image</h4>
                            <div class="form-group">
                                <label>Before image</label>
                                <input type="file" name="technologyOptionFeatureImage_7" class="full-width"
                                    accept="image/x-png, image/gif, image/jpeg">
                            </div>
                        </div>
                    </section>
                    <section style="margin-top:30px">
                        <h3>Technology Option 8</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->technology_option[7]->header) ?>"
                                name="technologyOptionHeader_8" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control inside-shadow"
                                name="technologyOptionDescription_8"><?php echo stripslashes($pageSection->technology_option[7]->description) ?></textarea>
                        </div>
                        <div class="row">
                            <h4>Technology Option 8 Feature Image</h4>
                            <div class="form-group">
                                <label>Before image</label>
                                <input type="file" name="technologyOptionFeatureImage_8" class="full-width"
                                    accept="image/x-png, image/gif, image/jpeg">
                            </div>
                        </div>
                    </section>
                    <section style="margin-top:30px">
                        <h3>Technology Option 9</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->technology_option[8]->header) ?>"
                                name="technologyOptionHeader_9" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control inside-shadow"
                                name="technologyOptionDescription_9"><?php echo stripslashes($pageSection->technology_option[8]->description) ?></textarea>
                        </div>
                        <div class="row">
                            <h4>Technology Option 9 Feature Image</h4>
                            <div class="form-group">
                                <label>Before image</label>
                                <input type="file" name="technologyOptionFeatureImage_9" class="full-width"
                                    accept="image/x-png, image/gif, image/jpeg">
                            </div>
                        </div>
                    </section>
                    <section style="margin-top:30px">
                        <h3>Technology Option 10</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->technology_option[9]->header) ?>"
                                name="technologyOptionHeader_10" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control inside-shadow"
                                name="technologyOptionDescription_10"><?php echo stripslashes($pageSection->technology_option[9]->description) ?></textarea>
                        </div>
                        <div class="row">
                            <h4>Technology Option 10 Feature Image</h4>
                            <div class="form-group">
                                <label>Before image</label>
                                <input type="file" name="technologyOptionFeatureImage_10" class="full-width"
                                    accept="image/x-png, image/gif, image/jpeg">
                            </div>
                        </div>
                    </section>

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

                if (SITE_ID == 31 && $page_id == 48) { ?>

                    <section style="margin-top:30px">
                        <h3>FAQ</h3>
                        <div class="form-group">
                            <label>Question 1</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->faq[0]->question) ?>" name="faqQuestion1"
                                placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer1" class="form-control inside-shadow"
                                placeholder="Answer"><?php echo stripslashes($pageSection->faq[0]->answer) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Question 2</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->faq[1]->question) ?>" name="faqQuestion2"
                                placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer2" class="form-control inside-shadow"
                                placeholder="Answer"><?php echo stripslashes($pageSection->faq[1]->answer) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Question 3</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->faq[2]->question) ?>" name="faqQuestion3"
                                placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer3" class="form-control inside-shadow"
                                placeholder="Answer"><?php echo stripslashes($pageSection->faq[2]->answer) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Question 4</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->faq[3]->question) ?>" name="faqQuestion4"
                                placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer4" class="form-control inside-shadow"
                                placeholder="Answer"><?php echo stripslashes($pageSection->faq[3]->answer) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Question 5</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->faq[4]->question) ?>" name="faqQuestion5"
                                placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer5" class="form-control inside-shadow"
                                placeholder="Answer"><?php echo stripslashes($pageSection->faq[4]->answer) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Question 6</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->faq[5]->question) ?>" name="faqQuestion6"
                                placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer6" class="form-control inside-shadow"
                                placeholder="Answer"><?php echo stripslashes($pageSection->faq[5]->answer) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Question 7</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->faq[6]->question) ?>" name="faqQuestion7"
                                placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer7" class="form-control inside-shadow"
                                placeholder="Answer"><?php echo stripslashes($pageSection->faq[6]->answer) ?></textarea>
                        </div>
                    </section>

                <?php
                }

                if (SITE_ID == 31 && $page_id == 46) { ?>


                    <section style="margin-top: 30px">
                        <h3>Why Choose Us</h3>
                        <hr>
                        <div class="form-group">
                            <label>Feature image</label>
                            <input type="file" name="whyChooseUs" class="full-width"
                                accept="image/x-png, image/gif, image/jpeg">
                        </div>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->why_choose_us->header) ?>" name="whyChooseTitle"
                                placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" class="form-control inside-shadow"
                                value="<?php echo stripslashes($pageSection->why_choose_us->description) ?>"
                                name="whyChooseDescription" placeholder="Description">
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <h4>Step 1</h4>
                                <div class="form-group">
                                    <label>Step 1 icon</label>
                                    <input type="file" name="stepIcon_1" class="full-width"
                                        accept="image/x-png, image/gif, image/jpeg">
                                </div>
                                <div class="form-group">
                                    <label>Step 1 Description</label>
                                    <input type="text" class="form-control inside-shadow"
                                        value="<?php echo stripslashes($pageSection->why_choose_us->steps[0]->description) ?>"
                                        name="stepHeader_1" placeholder="Step 1 Description">
                                </div>
                            </div>
                            <div class="col-4">
                                <h4>Step 2</h4>
                                <div class="form-group">
                                    <label>Step 2 icon</label>
                                    <input type="file" name="stepIcon_2" class="full-width"
                                        accept="image/x-png, image/gif, image/jpeg">
                                </div>
                                <div class="form-group">
                                    <label>Step 2 Description</label>
                                    <input type="text" class="form-control inside-shadow"
                                        value="<?php echo stripslashes($pageSection->why_choose_us->steps[1]->description) ?>"
                                        name="stepHeader_2" placeholder="Step 2 Description">
                                </div>
                            </div>
                            <div class="col-4">
                                <h4>Step 3</h4>
                                <div class="form-group">
                                    <label>Step 3 icon</label>
                                    <input type="file" name="stepIcon_3" class="full-width"
                                        accept="image/x-png, image/gif, image/jpeg">
                                </div>
                                <div class="form-group">
                                    <label>Step 3 Description</label>
                                    <input type="text" class="form-control inside-shadow"
                                        value="<?php echo stripslashes($pageSection->why_choose_us->steps[2]->description) ?>"
                                        name="stepHeader_3" placeholder="Step 3 Description">
                                </div>
                            </div>
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
                        value=" <?php echo (($page_status == 1) ? 'Update ' : 'Publish '); ?>page">
                    <!-- <input type="submit" id="draftPost" class="btn btn-yellow btn-block" value="Save Draft"> -->
                    <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name='postStatus' value='<?php echo $page_status ?>'>
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                    <input type="hidden" name="postType" value="<?php echo $page_type ?>">
                    <a href='<?php echo SITE_URL . $link_url; ?>' class='btn btn-block btn-blue' target='_blank'>View live
                        page</a>
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
</div>