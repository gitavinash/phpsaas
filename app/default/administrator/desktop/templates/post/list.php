<?php

$postStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($_GET['post']) ? $_GET['post'] : 0);

$postStatus = (($postStatus == 'all') ? '' : "AND post_status='{$postStatus}'");

?>
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_new.css">
<script src="<?php echo JS_LIBRARY ?>admin_new.js"></script>
<h2>All <?php echo ucwords($postType . 's') ?></h2>
<a href="post/?type=add&editor=true" class="floating-add-btn" title="Add New Post">
    <i class="fas fa-plus"></i>
    <span>Add Post</span>
</a>
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
<table id="table" data-default-order-column="3" data-page-length="10">
    <thead>
        <tr>
            <th style="width: 460px">Title</th>
            <th style="width: 150px">Category</th>
            <th style="width: 150px">Author</th>
            <th style="width: 100px">Date</th>
            <th style="width: 120px">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($dataList = mysqli_query($connection, "SELECT post_id, post_title, category_title, link_url, link_status, link_creation, profile_display_name FROM " . TABLE_PREFIX . "post, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "profile WHERE post_owner_id='" . USER_ID . "' AND link_type='post' AND post_type='{$postType}' AND link_relation_id=post_id AND link_site_id='" . SITE_ID . "' AND category_id=post_category AND profile_user_id=link_owner_id AND link_status!=301 $postStatus ORDER BY link_published DESC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <?php
                    $statusLabel = ($link_status == '200') ? 'Published' : 'Draft';
                    $statusClass = ($link_status == '200') ? 'status-live' : 'status-draft';
        ?>
        <tr style="height: 80px;">
            <td>
                <div class="post-cell">
                    <div class="post-meta">
                        <p><a class="post-title-link"
                                href="post/?type=edit&editor=true&id=<?php echo base64_encode($post_id); ?>"><?php echo $post_title; ?></a>
                        </p>
                        <div class="post-tags">
                            <span class="post-tag <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </div>
                    </div>
                </div>
            </td>
            <td><?php echo $category_title; ?></td>
            <td><?php echo $profile_display_name; ?></td>
            <td data-order="<?php echo strtotime($link_creation); ?>"><?php echo date('d/m/Y', strtotime($link_creation)); ?></td>
            <td>
                <div class="action-btns">
                    <a href="post/?type=edit&editor=true&id=<?php echo base64_encode($post_id); ?>" class="action-btn btn-edit" title="Edit"><i class="fas fa-pencil-alt"></i> Edit</a>
                    <a href="<?php echo $link_url; ?>" target="_blank" class="action-btn btn-view" title="View"><i class="fas fa-eye"></i> View</a>
                    <a href="#" id="<?php echo base64_encode($post_id) ?>_post" class="action-btn btn-delete deleteBtn" title="Delete"><i class="fas fa-trash"></i> Delete</a>
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