<?php


if (isset($id)) {

    $id = mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? base64_decode($_GET['id']) : 0));

    if ($categoryData = mysqli_query($connection, "SELECT category_id, category_parent_id, category_title, category_description, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_id=link_relation_id AND meta_relation_id=link_id AND category_id='{$id}' AND link_type='category' AND category_type='post' AND link_status=200")) {

        if (mysqli_num_rows($categoryData) != 0) {

            extract(mysqli_fetch_assoc($categoryData));
        } else {
            $category_id = '';
            $category_title = '';
            $category_description = '';
            $category_parent_id = '';
            $link_url = '';
        }
    }
} else {

    $category_id = '';
    $category_title = '';
    $category_description = '';
    $category_parent_id = '';
    $link_url = '';
}

?>
<h2 class="heading"><?php echo ((isset($id)) ? 'Update category' : 'Add New category') ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Category details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="categoryFormV2" method="POST">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter category details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new category">
                        </div>
                        <div class="form-group">
                            <input name="name" id="title" value="<?php echo $category_title ?>" type="text" />
                            <label class="form-lable" id="Label">Title<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input name="url" id="permalink" value="<?php echo $link_url ?>" type="text" />
                            <label class="form-lable" id="Label">Permalink<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p class="form-lable" id="Label">Description</p>
                        </div>
                        <div class="form-group">
                            <div class="editor" id="editor" style="min-height: 500px"></div>
                            <textarea name="description" id="editor-0"
                                style="display:none"><?php echo $category_description; ?></textarea>
                            <textarea name="description" id="editor-0" style="display:none"></textarea>
                        </div>
                        <div class="form-group">
                            <p>Parent Category</p>
                            <select name="category" id="parentCategory" class="selectJS">
                                <option value="0">Select Parent Category</option>
                                <?php
                                if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link WHERE category_site_id='" . APP_SITE_ID . "' AND link_type='category' AND link_relation_id=category_id AND category_id!='{$category_id}' AND link_status=200 AND category_status=1 AND category_type='post' AND link_site_id=" . SITE_ID)) {
                                    while ($category = mysqli_fetch_assoc($categoryList)) { ?>
                                        <option
                                            value="<?php echo $category['category_id'] . '_' . str_replace(SITE_DOMAIN . '/category', '', $category['link_url']); ?>"
                                            <?php echo (($category['category_id'] == $category_parent_id) ? 'selected' : '') ?>>
                                            <?php echo ucwords($category['category_title']); ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <!-- <div class="form-group">
                            <p><strong>Feature image</strong></p>
                            <div id="dropZone">
                                <span class="defaultText">Drag & Drop files here or click to upload</span>
                            </div>
                            <input type="file" id="fileInput" name="featureImage[]" data-limit="1"
                                accept=".jpg, .jpeg, .png" style="display: none;" />
                        </div> -->
                        <div class="box-1">
                            <input type="hidden" name="type" value="category">
                            <input type="hidden" id="postType" name="postType" value="post">
                            <input type="hidden" id="action" name="action"
                                value="<?php echo (($category_id != '') ? 'update' : 'publish') ?>">
                            <div class="form-group">
                                <?php

                                if ($category_id != '') {
                                    echo "
                                        <input type='hidden' id='oldUrl' name='oldURL' value='{$link_url}'>
                                        <input type='hidden' id='categoryID' value='{$category_id}' name='id'>
                                    ";
                                }

                                ?> <button type="button" class='btn btn-green'
                                    id='submitCategoryV2'><?php echo (($category_id != '') ? 'Update category' : 'Add new category') ?></button>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>