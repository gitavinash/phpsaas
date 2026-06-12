<div class="product-add-ui">
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
                        <p><span><?php echo SITE_DOMAIN ?>/service/</span></p>
                        <input type="text" id="url" class="form-control" name="url">
                    </div>
                </div>

                <?php

                if ($site_id == 31) { ?>
                    <div class="form-group">
                        <div class="form-group fullscreen-editor">
                            <textarea name="description" class="editor inside-shadow"></textarea>
                        </div>
                    </div>

                    <section style="margin-top:30px">
                        <h3>Quote</h3>
                        <hr>
                        <div class="form-group">
                            <label>Quote message</label>
                            <textarea name="quote" class="form-control inside-shadow" placeholder="Quote message"></textarea>
                        </div>
                    </section>

                    <section style="margin-top: 30px">
                        <div class="form-group">
                            <h3>Treatment Option</h3>
                            <hr>
                            <label>Treatment title</label>
                            <input type="text" class="form-control inside-shadow" name="treatment_header" placeholder="Treatment title">
                        </div>
                        <div class="form-group">
                            <label>Treatment Description</label>
                            <input type="text" class="form-control inside-shadow" name="treatment_description" placeholder="Treatment description">
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Background image</label>
                                    <input type="file" name="treatmentImage_1" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                                </div>
                                <div class="form-group">
                                    <label>Header</label>
                                    <input type="text" class="form-control inside-shadow" name="treatmentImageHeader_1" placeholder="Header">
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" class="form-control inside-shadow" name="treatmentImageDescription_1" placeholder="Description">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Background image</label>
                                    <input type="file" name="treatmentImage_2" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                                </div>
                                <div class="form-group">
                                    <label>Header</label>
                                    <input type="text" class="form-control inside-shadow" name="treatmentImageHeader_2" placeholder="Header">
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" class="form-control inside-shadow" name="treatmentImageDescription_2" placeholder="Description">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Background image</label>
                                    <input type="file" name="treatmentImage_3" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                                </div>
                                <div class="form-group">
                                    <label>Header</label>
                                    <input type="text" class="form-control inside-shadow" name="treatmentImageHeader_3" placeholder="Header">
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" class="form-control inside-shadow" name="treatmentImageDescription_3" placeholder="Description">
                                </div>
                            </div>
                        </div>
                    </section>

                    <section style="margin-top: 30px">
                        <h3>Why Choose Us</h3>
                        <hr>
                        <div class="form-group">
                            <label>Feature image</label>
                            <input type="file" name="whyChooseUs" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                        </div>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow" name="whyChooseTitle" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" class="form-control inside-shadow" name="whyChooseDescription" placeholder="Description">
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <h4>Step 1</h4>
                                <div class="form-group">
                                    <label>Step 1 icon</label>
                                    <input type="file" name="stepIcon_1" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                                </div>
                                <div class="form-group">
                                    <label>Step 1 Description</label>
                                    <input type="text" class="form-control inside-shadow" name="stepHeader_1" placeholder="Step 1 Description">
                                </div>
                            </div>
                            <div class="col-4">
                                <h4>Step 2</h4>
                                <div class="form-group">
                                    <label>Step 2 icon</label>
                                    <input type="file" name="stepIcon_2" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                                </div>
                                <div class="form-group">
                                    <label>Step 2 Description</label>
                                    <input type="text" class="form-control inside-shadow" name="stepHeader_2" placeholder="Step 2 Description">
                                </div>
                            </div>
                            <div class="col-4">
                                <h4>Step 3</h4>
                                <div class="form-group">
                                    <label>Step 3 icon</label>
                                    <input type="file" name="stepIcon_3" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                                </div>
                                <div class="form-group">
                                    <label>Step 3 Description</label>
                                    <input type="text" class="form-control inside-shadow" name="stepHeader_3" placeholder="Step 3 Description">
                                </div>
                            </div>
                        </div>
                    </section>

                    <section style="margin-top:30px">
                        <h3>Before After</h3>
                        <hr>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow" name="berforeAfterHeader" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Sub Header</label>
                            <input type="text" class="form-control inside-shadow" name="berforeAfterSubHeader" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="berforeAfterDescription" class="form-control inside-shadow" placeholder="Description"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Video link</label>
                            <input type="text" class="form-control inside-shadow" name="berforeAfterVideLink" placeholder="Video link">
                        </div>
                        <div class="row">

                            <div class="productGallery generalTab">

                                <div class="input-field">
                                    <div class="input-images" style="padding-top: .5rem;"></div>
                                </div>

                            </div>

                        </div>
                    </section>

                    <section style="margin-top:30px">
                        <h3>Watch video</h3>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow" name="watchVideoHeader" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" class="form-control inside-shadow" name="watchVideoDescription" placeholder="Description">
                        </div>
                        <div class="form-group">
                            <label>Video link</label>
                            <input type="text" class="form-control inside-shadow" name="watchVideoLink" placeholder="Video link">
                        </div>
                        <div class="form-group">
                            <label>Background image</label>
                            <input type="file" name="watchVideoImage" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                        </div>
                    </section>

                    <section style="margin-top:30px">
                        <h3>Our Commitment</h3>
                        <div class="form-group">
                            <label>Header</label>
                            <input type="text" class="form-control inside-shadow" name="commitmentHeader" placeholder="Header">
                        </div>
                        <div class="form-group">
                            <label>Sub Header</label>
                            <input type="text" class="form-control inside-shadow" name="commitmentSubHeader" placeholder="Sub Header">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="commitmentDescription" class="form-control inside-shadow" placeholder="Description"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label>Feature image</label>
                                <input type="file" name="commitmentImage" class="full-width" accept="image/x-png, image/gif, image/jpeg">
                            </div>
                        </div>
                    </section>

                    <section style="margin-top:30px">
                        <h3>FAQ</h3>
                        <div class="form-group">
                            <label>Question 1</label>
                            <input type="text" class="form-control inside-shadow" name="faqQuestion1" placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer1" class="form-control inside-shadow" placeholder="Answer"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Question 2</label>
                            <input type="text" class="form-control inside-shadow" name="faqQuestion2" placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer2" class="form-control inside-shadow" placeholder="Answer"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Question 3</label>
                            <input type="text" class="form-control inside-shadow" name="faqQuestion3" placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer3" class="form-control inside-shadow" placeholder="Answer"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Question 4</label>
                            <input type="text" class="form-control inside-shadow" name="faqQuestion4" placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer4" class="form-control inside-shadow" placeholder="Answer"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Question 5</label>
                            <input type="text" class="form-control inside-shadow" name="faqQuestion5" placeholder="Question">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqAnswer5" class="form-control inside-shadow" placeholder="Answer"></textarea>
                        </div>
                    </section>

                <?php
                } else {
                ?>
                    <div class="form-group fullscreen-editor">
                        <textarea name="description" class="editor inside-shadow"></textarea>
                    </div>

                    <!-- Service description and other things -->

                    <section style="margin-bottom:20px">
                        <h3>Service Details</h3>
                        <ul class='tabs'>
                            <li id="productGeneral" class='active'>General</li>
                            <li id="productInventory">Inventory</li>
                            <li id="productGallery">Gallery</li>
                            <!-- <li id="productAdditionalDetails">Additional Details</li> -->
                        </ul>
                        <div class="productGeneral generalTab">
                            <div class="form-group">
                                <label>Service short description / Highlights</label>
                                <textarea name="highlight" class="form-control inside-shadow" placeholder="Service short description / Highlights"></textarea>
                            </div>
                        </div>
                        <div class="productInventory generalTab" style="display: none;">
                            <div class="box-2">
                                <div class="form-group">
                                    <label>Sale price (₹)</label>
                                    <input type="number" onwheel="this.blur()" class="form-control inside-shadow" name="salePrice" placeholder="Sale price">
                                </div>
                            </div>
                            <div class="box-2">
                                <div class="form-group">
                                    <label>Regular price in ₹ ( MRP )</label>
                                    <input type="number" onwheel="this.blur()" class="form-control inside-shadow" name="mrp" placeholder="Regular price">
                                </div>
                            </div>
                            <div class="box-2">
                                <div class="form-group">
                                    <label>SKU</label>
                                    <input type="text" class="form-control inside-shadow" name="sku" placeholder="SKU">
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
                        </div>
                        <div class="productGallery generalTab" style="display: none;">

                            <!-- Product description and other things -->

                            <div class="input-field">
                                <div class="input-images" style="padding-top: .5rem;"></div>
                            </div>

                        </div>
                        <!-- <div class="productAdditionalDetails generalTab" style="display: none;">

                    </div> -->
                    </section>
                <?php
                }

                ?>

                <!-- SEO & social media marketing -->

                <section style="margin-top: 30px">
                    <h3>Advance Options for digital marketing</h3>
                    <ul class='tabs'>
                        <li id=" seo" class='active'>Search Engine Optimization</li>
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
                        <input type="checkbox" value="1" name='featurePost'> Mark as feature service
                    </div>
                </div>
                <h2>Categories</h2>
                <div class="action-box height-200 inside-shadow">
                    <?php
                    if ($categoryList = mysqli_query($connection, "SELECT category_id, category_description, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_id=link_relation_id AND link_type='category' AND meta_relation_id=link_id AND category_type='{$postType}' AND category_site_id='" . SITE_ID . "' AND link_status=200 ORDER BY category_title ASC")) {
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
</div>