<?php

$listStatus = (isset($_GET['status']) ? $_GET['status'] : 'all');

$postStatus = mysqli_real_escape_string($connection, $listStatus);
$startFrom = (isset($_GET['page']) ? $_GET['page'] : 0);

$postStatus = (($postStatus == 'all') ? '' : "AND page_status='{$postStatus}'");


?>
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_new.css">
<script src="<?php echo JS_LIBRARY ?>admin_new.js"></script>
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
<table id="table" data-default-order-column="2" data-page-length="10">
    <thead>
        <tr>
            <th style="width: 460px">Title</th>
            <th style="width: 150px">Author</th>
            <th style="width: 100px">Date</th>
            <th style="width: 120px">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT p.page_id, p.page_title, l.link_url, l.link_status, UNIX_TIMESTAMP(COALESCE(l.link_published, l.link_creation, l.link_modified)) AS published_ts, pr.profile_display_name FROM " . TABLE_PREFIX . "page p INNER JOIN (SELECT link_relation_id, MAX(link_id) AS latest_link_id FROM " . TABLE_PREFIX . "link WHERE link_type='{$postType}' AND link_site_id='" . SITE_ID . "' AND link_status!=301 GROUP BY link_relation_id) ll ON ll.link_relation_id=p.page_id INNER JOIN " . TABLE_PREFIX . "link l ON l.link_id=ll.latest_link_id INNER JOIN " . TABLE_PREFIX . "profile pr ON pr.profile_user_id='" . USER_ID . "' WHERE p.page_type='page' $postStatus ORDER BY published_ts DESC, p.page_id DESC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <?php
                    $statusLabel = ($link_status == '200') ? 'Published' : 'Draft';
                    $statusClass = ($link_status == '200') ? 'status-live' : 'status-draft';
        ?>
        <tr style="height: 80px;">
            <td>
                <div class="page-cell">
                    <div class="page-meta">
                        <p><a class="page-title-link"
                                href="page/?type=edit&editor=true&id=<?php echo base64_encode($page_id); ?>"><?php echo $page_title; ?></a>
                        </p>
                        <div class="page-tags">
                            <span class="page-tag <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </div>
                    </div>
                </div>
            </td>
            <td><?php echo $profile_display_name; ?></td>
            <td data-order="<?php echo (int)$published_ts; ?>" data-sort="<?php echo (int)$published_ts; ?>">
                <?php echo date('d/m/Y H:i:s', (int)$published_ts); ?></td>
            <td>
                <div class="action-btns">
                    <a href="page/?type=edit&editor=true&id=<?php echo base64_encode($page_id); ?>" class="action-btn btn-edit" title="Edit"><i class="fas fa-pencil-alt"></i> Edit</a>
                    <a href="<?php echo $link_url; ?>" target="_blank" class="action-btn btn-view" title="View"><i class="fas fa-eye"></i> View</a>
                    <a href="#" id="<?php echo base64_encode($page_id) ?>_page" class="action-btn btn-delete deleteBtn" title="Delete"><i class="fas fa-trash"></i> Delete</a>
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