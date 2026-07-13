<?php

$postStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($_GET['page']) ? $_GET['page'] : 0);

$postStatus = (($postStatus == 'all') ? '' : "AND category_status='{$postStatus}'");

?>
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_new.css">
<script src="<?php echo JS_LIBRARY ?>admin_new.js"></script>
<h2>All testimonial</h2>
<div class="options-bar">
    <div class="left-box">
        <form method="GET">
            <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
            <select class="form-control" name="status" id="changeStatus">
                <option value="all" <?php echo (($_GET['status'] == 'all') ? 'selected' : '') ?>>All
                    <?php echo $postType ?></option>
                <option value="0" <?php echo (($_GET['status'] == '0') ? 'selected' : '') ?>>Drafts
                    <?php echo $postType ?></option>
                <option value="1" <?php echo (($_GET['status'] == '1') ? 'selected' : '') ?>>Published
                    <?php echo $postType ?></option>
            </select>
            <input type="submit" class="btn" value="Filter">
        </form>
    </div>
    <div class="right-box">

    </div>
</div>
<table id="table" data-default-order-column="2" data-page-length="10">
    <thead>
        <tr>
            <th>Review by</th>
            <th style="width: 150px">Published by</th>
            <th style="width: 100px">Date</th>
            <th style="width: 120px">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "category INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=category_owner_id WHERE category_type='testimonial' AND category_site_id='" . SITE_ID . "' $postStatus ORDER BY category_time DESC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
                    <?php
                    $statusLabel = ($category_status == '1') ? 'Published' : 'Draft';
                    $statusClass = ($category_status == '1') ? 'status-live' : 'status-draft';
                    ?>
                    <tr style="height: 80px">
                        <td>
                            <div class="list-cell">
                                <div class="list-meta">
                                    <p><a class="list-title-link" href="testimonial/?type=edit&editor=true&id=<?php echo base64_encode($category_id); ?>"><?php echo $category_title; ?></a></p>
                                    <div class="list-tags">
                                        <span class="list-tag <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $profile_display_name; ?></td>
                        <td data-order="<?php echo strtotime($category_time); ?>"
                            data-sort="<?php echo strtotime($category_time); ?>">
                            <?php echo date('d/m/Y H:i:s', strtotime($category_time)); ?></td>
                        <td>
                            <div class="action-btns">
                                <a href="testimonial/?type=edit&editor=true&id=<?php echo base64_encode($category_id); ?>" class="action-btn btn-edit" title="Edit"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <a href="#" id="<?php echo base64_encode($category_id) ?>_testimonial" class="action-btn btn-delete deleteBtn" title="Delete"><i class="fas fa-trash"></i> Delete</a>
                            </div>
                        </td>
                    </tr>
        <?php
                }
            }
        }

        ?>
    </tbody>
</table>