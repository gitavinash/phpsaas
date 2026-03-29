<?php

$postStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($_GET['news']) ? $_GET['news'] : 0);

$statusFilter = (($postStatus == 'all') ? '' : "AND news_status='{$postStatus}'");

?>
<h2>All <?php echo ucwords($postType) ?></h2>
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
                <option value="2" <?php echo (($_GET['status'] == '2') ? 'selected' : '') ?>>Scheduled
                    <?php echo $postType ?></option>
            </select>
            <input type="submit" class="btn" value="Filter">
        </form>
    </div>
    <div class="right-box">

    </div>
</div>
<table id="table" data-default-order-column="4" data-default-order-dir="desc">
    <thead>
        <tr>
            <!-- <th style="width: 50px"></th> -->
            <th>Title</th>
            <th style="width: 150px">Category</th>
            <th style="width: 150px">Author</th>
            <th style="width: 150px">Paid</th>
            <th style="width: 100px">Date</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT news_id, news_title, news_paid, news_gallery, news_status, category_title, link_url, link_status, link_published, profile_display_name FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id INNER JOIN " . TABLE_PREFIX . "profile ON news_owner_id=profile_user_id INNER JOIN " . TABLE_PREFIX . "category ON news_category=category_id WHERE link_site_id=" . SITE_ID . " AND link_type='news' AND link_status!=301 {$statusFilter} ORDER BY UNIX_TIMESTAMP(link_published) DESC, news_id DESC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr>
            <td>
                <?php
                            $statusBadge = '';
                            if ((int) $news_status === 0) {
                                $statusBadge = '<strong> — Draft </strong>';
                            } elseif ((int) $news_status === 2) {
                                $statusBadge = '<strong> — Scheduled </strong>';
                            }
                            ?>
                <p><a
                        href="news/?type=edit&editor=true&id=<?php echo base64_encode($news_id);
                                                                            echo (($news_gallery != '') ? '&theme=gallery' : '') ?>"><?php echo $news_title . $statusBadge; ?></a>
                </p>
                <div class="hover">
                    <a
                        href="news/?type=edit&editor=true&id=<?php echo base64_encode($news_id);
                                                                            echo (($news_gallery != '') ? '&theme=gallery' : '') ?>"><i
                            class="fas fa-pencil-alt"></i> Edit</a>
                    <a href="#" id="<?php echo base64_encode($news_id); ?>_news" class="deleteBtn"><i
                            class="fas fa-trash"></i> Delete</a>
                    <?php if ((int) $news_status === 1 && $link_status == 200) { ?>
                    <a href="<?php echo $link_url; ?>" target='_blank'><i class="fas fa-eye"></i> View</a>
                    <?php } ?>
                </div>
            </td>
            <td><?php echo $category_title; ?></td>
            <td><?php echo $profile_display_name; ?></td>
            <td><?php echo (($news_paid == 1) ? 'Yes' : 'No') ?></td>
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
    var table = $('#table').DataTable({
        "bDestroy": true,
        "bStateSave": false,
        "ordering": true,
        "aaSorting": [
            [-1, "desc"]
        ],
        "order": [
            [-1, "desc"]
        ]
    });

    // Re-apply to avoid global defaults/plugins overriding initial sort.
    table.order([-1, "desc"]).draw();
});
</script>