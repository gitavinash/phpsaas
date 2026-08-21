<?php

if (!isset($id)) { ?>
<script>
window.location.href = '/dashboard/blog/list/';
</script>
<?php
} else if ($id == '') { ?>
<script>
window.location.href = '/dashboard/blog/list/';
</script>
<?php
}

$supplierId = base64_decode($id);

if ($checkSupplier = mysqli_query($connection, "SELECT *, GROUP_CONCAT(tag_title SEPARATOR ',') AS tag_titles  FROM " . TABLE_PREFIX . "post INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=post_id LEFT JOIN " . TABLE_PREFIX . "tag ON tag_relation_id=post_id AND tag_type='post' WHERE post_id='{$supplierId}' AND link_type='post' AND post_site_id=" . SITE_ID . " AND link_status=200 AND tag_site_id=" . SITE_ID . " AND link_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkSupplier) > 0) {
        extract(mysqli_fetch_assoc($checkSupplier));
    }
}

?>
<h2 class="heading"><?php echo 'Update supplier details' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Blog details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="updateNewPolicyForm" method="POST">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Update blog details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Update Hosting tld">
                        </div>
                        <div class="form-group">
                            <input name="title" id="title" type="text" value="<?php echo $post_title ?>" />
                            <label class="form-lable" id="Label">Title<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <div class="editor" id="editor0" style="min-height: 500px"></div>
                            <textarea name="description" id="editor-0"
                                style="display:none"><?php echo $post_description; ?></textarea>
                            <label class="form-lable" id="Label">Description<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <span style="display: none;" id="oldGallery"><?php echo $post_image ?></span>
                            <p><strong>Feature image</strong></p>
                            <div id="dropZone">
                                <span class="defaultText">Drag & Drop files here or click to upload</span>
                            </div>
                            <input type="file" id="fileInput" data-limit="1" name="featureImage[]"
                                accept=".jpg, .jpeg, .png" />
                        </div>
                        <div class="form-group-box">
                            <p>Mark as featured</p>
                            <div class="box-2 left p-right-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="radio1" name="featurePost" value="1"
                                            <?php echo ($link_is_featured == 1) ? 'checked' : '' ?> />
                                        <label class="radio-button" for="radio1Lable">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="box-2 left p-left-10">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input type="radio" id="radio2" name="featurePost" value="0"
                                            <?php echo ($link_is_featured == 0) ? 'checked' : '' ?> />
                                        <label class="radio-button" for="radio2Lable">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="tags" id="tags"><?php echo $tag_titles ?></textarea>
                            <label class="form-lable" id="textareaLabel">Blog keywords ( Use , to seprate keywords
                                )</label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="hidden" name="id" id="id" value=" <?php echo base64_encode($post_id) ?>">
                                <a href="#" class="btn btn-green" id="updateBlogV2">Update blog</a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>