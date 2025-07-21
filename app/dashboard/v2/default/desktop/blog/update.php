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

if ($checkSupplier = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "post INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=post_id WHERE post_id='{$supplierId}' AND link_type='post' AND post_site_id=" . SITE_ID . " AND link_status=200 AND link_site_id=" . SITE_ID)) {
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
                        <div class="box-1">
                            <div class="form-group">
                                <input type="hidden" name="id" id="id" value=" <?php echo base64_encode($post_id) ?>">
                                <a href="#" class="btn btn-green" id="updatePolicy">Update blog</a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>