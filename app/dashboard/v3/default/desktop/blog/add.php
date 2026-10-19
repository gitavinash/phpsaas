<h2 class="heading"><?php echo 'Add New blog' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Blog details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="blogFormV2" method="POST">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter blog details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input name="title" id="title" type="text" />
                            <label class="form-lable" id="Label">Title<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input name="url" id="permalink" type="text" />
                            <label class="form-lable" id="Label">Permalink<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <div class="editor editor-min-500" id="editor0"></div>
                            <textarea name="description" id="editor-0"></textarea>
                            <label class="form-lable" id="Label">Description<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p>Category</p>
                            <select name="category" id="category" class="selectJS2">
                                <option value="0">Select Category</option>
                                <?php
                                if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link WHERE category_site_id='" . APP_SITE_ID . "' AND link_type='category' AND link_relation_id=category_id AND link_status=200 AND category_status=1 AND category_type='post' AND link_site_id=" . SITE_ID)) {
                                    while ($category = mysqli_fetch_assoc($categoryList)) { ?>
                                <option
                                    value="<?php echo $category['category_id'] . '_' . str_replace(SITE_DOMAIN . '/category', '', $category['link_url']); ?>">
                                    <?php echo ucwords($category['category_title']); ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <p><strong>Feature image<sup>*</sup></strong></p>
                            <div id="dropZone">
                                <span class="defaultText">Drag & Drop files here or click to upload</span>
                            </div>
                            <input type="file" id="fileInput" name="featureImage[]" data-limit="1"
                                accept=".jpg, .jpeg, .png" />
                        </div>

                        <div class="form-group-box">
                            <p>Mark as featured</p>
                            <div class="box-2 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="radio1" name="featurePost" value="1" />
                                        <label class="radio-button" for="radio1Lable">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="box-2 left p-left-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="radio2" name="featurePost" value="0" checked />
                                        <label class="radio-button" for="radio2Lable">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="tags" id="tags"></textarea>
                            <label class="form-lable" id="textareaLabel">Blog keywords ( Use , to seprate keywords
                                )</label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="hidden" name="type" value="blog">
                                <input type="hidden" name="action" value="publish">
                                <a href="#" class="btn btn-green" id="addBlogV2">Add new blog</a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>