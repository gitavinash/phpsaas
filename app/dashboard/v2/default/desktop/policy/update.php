<?php

if (!isset($id)) { ?>
    <script>
        window.location.href = '/dashboard/policy/list/';
    </script>
<?php
} else if ($id == '') { ?>
    <script>
        window.location.href = '/dashboard/policy/list/';
    </script>
<?php
}

$supplierId = base64_decode($id);

if ($checkSupplier = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "page INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=page_id WHERE page_id='{$supplierId}' AND page_type='policy' AND link_type='page' AND link_status=200 AND link_site_id=" . SITE_ID)) {
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
            <li class="step-box active" id="step_1"><span>1</span>Policy details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="updateNewPolicyForm" method="POST">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Update policy details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Update Hosting tld">
                        </div>
                        <div class="form-group">
                            <input name="title" id="title" type="text" value="<?php echo $page_title ?>" />
                            <label class="form-lable" id="Label">Policy name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <div class="editor" id="editor1" style="min-height: 500px"></div>
                            <textarea name="description" id="editor-0" style="display:none"><?php echo $page_description; ?></textarea>
                            <label class="form-lable" id="Label">Description<sup>*</sup></label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="hidden" name="id" id="id" value=" <?php echo base64_encode($page_id) ?>">
                                <a href="#" class="btn btn-green" id="updatePolicy">Update policy</a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>