<?php

$id = base64_decode($id);

$pageType = 'widget';

if ($pageDetails = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "category WHERE category_type='{$pageType}' AND category_id='{$id}' AND category_status=1 AND category_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($pageDetails) != 0) {
        extract(mysqli_fetch_assoc($pageDetails));
    } else {
        exit(); ?>
        <script type="text/javascript">
            alert('Invalid action found');
            window.location.href = "dashboard/<?php echo $pageType; ?>/?type=list&status=all";
        </script>
<?php
    }
}

?>
<div class="product-add-ui">
    <h2>Update widget <span><a href="<?php echo $postType ?>/?type=add&editor=true" class='btn btn-blue btn-sm'>Add
                New</a></span></h2>

    <div class="form-box">
        <form id="formData" enctype="multipart/form-data">
            <div class="left-box">
                <div class="form-group">
                    <input type="text" class="form-control inside-shadow" name="title" placeholder="Title"
                        value="<?php echo $category_title ?>" required="">
                </div>
                <div class="form-group fullscreen-editor">
                    <textarea name="description"
                        class="editor inside-shadow"><?php echo html_entity_decode($category_description); ?></textarea>
                </div>
            </div>
            <div class="right-box">
                <div class="action-box inside-shadow">
                    <input type="button" id="updatePost" class="btn btn-green btn-block"
                        value=" <?php echo (($category_status == 1) ? 'Update ' : 'Publish '); ?>post">
                    <!-- <input type="submit" id="draftPost" class="btn btn-yellow btn-block" value="Save Draft"> -->
                    <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name='postStatus' value='<?php echo $category_status ?>'>
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                    <input type="hidden" name="postType" value="<?php echo $category_type ?>">
                </div>
                <h2>Feature Image</h2>
                <div class="form-group featureImageUploadBox upload-btn"
                    style="display: <?php echo (($category_image != '') ? 'none' : 'block') ?>;">
                    <span>Set Feature Image</span>
                    <input type="hidden" name="oldFeatureImage" value="<?php echo $category_image ?>">
                    <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                    <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif<strong>Maximum upload
                            limit is 300 MB</strong></p>
                </div>
                <div class="img-preview featureImagePreviewBox action-box"
                    style="display: <?php echo (($category_image != '') ? 'block' : 'none') ?>;">
                    <img src="//<?php echo SITE_DOMAIN . $category_image ?>" id="featureImagePath" alt="">
                    <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
                </div>
            </div>
        </form>
    </div>
</div>