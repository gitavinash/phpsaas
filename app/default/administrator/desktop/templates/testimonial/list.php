<?php

$postStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($_GET['page']) ? $_GET['page'] : 0);

$postStatus = (($postStatus == 'all') ? '' : "AND page_status='{$postStatus}'");

?>
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
<table id="table">
    <thead>
        <tr>
            <!-- <th style="width: 50px"></th> -->
            <th>Review by</th>
            <th style="width: 150px">Published by</th>
            <th style="width: 100px">Date</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "category INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=category_owner_id WHERE category_type='testimonial' AND category_site_id='" . SITE_ID . "' $postStatus ORDER BY category_time DESC LIMIT {$startFrom}, 10")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr style="height: 60px">
            <td>
                <p><?php echo $category_title ?></p>
                <div class="hover">
                    <a href="testimonial/?type=edit&editor=true&id=<?php echo base64_encode($category_id); ?>"><i
                            class="fas fa-pencil-alt"></i> Edit</a>
                    <a href="#" id="<?php echo base64_encode($page_id) ?>_page" class="deleteBtn"><i
                            class="fas fa-trash"></i> Delete</a>
                </div>
            </td>
            <td><?php echo $profile_display_name; ?></td>
            <td><?php echo date('d/m/Y', strtotime($category_time)); ?></td>
        </tr>
        <?php
                }
            }
        }

        ?>
    </tbody>
</table>