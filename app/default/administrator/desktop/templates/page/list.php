<?php

$listStatus = (isset($_GET['status']) ? $_GET['status'] : 'all');

$postStatus = mysqli_real_escape_string($connection, $listStatus);
$startFrom = (isset($_GET['page']) ? $_GET['page'] : 0);

$postStatus = (($postStatus == 'all') ? '' : "AND page_status='{$postStatus}'");

?>
<h2>All pages</h2>
<div class="options-bar">
    <div class="left-box">
        <form method="GET">
            <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
            <select class="form-control" name="status" id="changeStatus">
                <option value="all" <?php echo (($listStatus == 'all') ? 'selected' : '') ?>>All
                    <?php echo $postType ?></option>
                <option value="0" <?php echo (($listStatus == '0') ? 'selected' : '') ?>>Drafts
                    <?php echo $postType ?></option>
                <option value="1" <?php echo (($listStatus == '1') ? 'selected' : '') ?>>Published
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
            <th>Title</th>
            <th style="width: 150px">Author</th>
            <th style="width: 100px">Date</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT page_id, page_title ,link_url, link_status, link_published, profile_display_name FROM " . TABLE_PREFIX . "page, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "profile WHERE link_type='{$postType}' AND page_type='page' AND link_relation_id=page_id AND link_site_id='" . SITE_ID . "' AND profile_user_id='" . USER_ID . "' AND link_status!=301 $postStatus ORDER BY link_published DESC LIMIT {$startFrom}, 10")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr style="height: 60px">
            <td>
                <p><a
                        href="page/?type=edit&editor=true&id=<?php echo base64_encode($page_id); ?>"><?php echo $page_title . (($link_status != '200') ? '<strong> — Draft </strong>' : ''); ?></a>
                </p>
                <div class="hover">
                    <a href="page/?type=edit&editor=true&id=<?php echo base64_encode($page_id); ?>"><i
                            class="fas fa-pencil-alt"></i> Edit</a>
                    <a href="#" id="<?php echo base64_encode($page_id) ?>_page" class="deleteBtn"><i
                            class="fas fa-trash"></i> Delete</a>
                    <a href="<?php echo $link_url; ?>" target='_blank'><i class="fas fa-eye"></i> View</a>
                </div>
            </td>
            <td><?php echo $profile_display_name; ?></td>
            <td data-order="<?php echo strtotime($link_published); ?>"
                data-sort="<?php echo strtotime($link_published); ?>">
                <?php echo date('d/m/Y H:i:s', strtotime($link_published)); ?></td>
        </tr>
        <?php
                }
            }
        }

        ?>
    </tbody>
</table>
<script>
$(document).ready(function() {
    var table;

    if ($.fn.dataTable.isDataTable('#table')) {
        table = $('#table').DataTable();
    } else {
        table = $('#table').DataTable({
            "aaSorting": [
                [2, "desc"]
            ],
            "order": [
                [2, "desc"]
            ]
        });
    }

    table.order([2, 'desc']).draw();
});
</script>