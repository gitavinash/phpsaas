<?php

$serviceStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($_GET['service']) ? $_GET['service'] : 0);

$serviceStatus = (($serviceStatus == 'all') ? '' : "AND service_status='{$serviceStatus}'");

?>
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_new.css">
<script src="<?php echo JS_LIBRARY ?>admin_new.js"></script>
<h2>All treatments</h2>
<div class="options-bar">
    <div class="left-box">
        <form method="GET">
            <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
            <select class="form-control" name="status" id="changeStatus">
                <option value="all" <?php echo (($_GET['status'] == 'all') ? 'selected' : '') ?>>All treatments</option>
                <option value="0" <?php echo (($_GET['status'] == '0') ? 'selected' : '') ?>>Drafts treatments</option>
                <option value="1" <?php echo (($_GET['status'] == '1') ? 'selected' : '') ?>>Published treatments
                </option>
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
            <th style="width: 400px">Title</th>
            <th style="width: 150px">Category</th>
            <th style="width: 150px">Author</th>
            <th style="width: 100px">Publish date</th>
            <th style="width: 120px">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT
    s.service_id,
    s.service_title,
    c.category_title,
    l.link_url,
    l.link_status,
    l.link_published,
    p.profile_display_name

FROM " . TABLE_PREFIX . "service s

INNER JOIN " . TABLE_PREFIX . "link l
    ON l.link_relation_id = s.service_id
   AND l.link_type = 'service'
   AND l.link_site_id = " . SITE_ID . "
   AND l.link_status != 301

LEFT JOIN " . TABLE_PREFIX . "category c
    ON c.category_id = s.service_category

LEFT JOIN " . TABLE_PREFIX . "profile p
    ON p.profile_user_id = l.link_owner_id

WHERE
    s.service_type = 'treatment'
    AND s.service_site_id = " . SITE_ID . "
    AND s.service_status = 1

ORDER BY
    l.link_published DESC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
                    <?php
                    $statusLabel = ($link_status == '200') ? 'Published' : 'Draft';
                    $statusClass = ($link_status == '200') ? 'status-live' : 'status-draft';
                    ?>
                    <tr style="height: 80px">
                        <td>
                            <div class="list-cell">
                                <div class="list-meta">
                                    <p><a class="list-title-link"
                                            href="treatment/?type=edit&editor=true&id=<?php echo base64_encode($service_id); ?>"><?php echo $service_title; ?></a>
                                    </p>
                                    <div class="list-tags">
                                        <span class="list-tag <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $category_title; ?></td>
                        <td><?php echo $profile_display_name; ?></td>
                        <td data-order="<?php echo strtotime($link_published); ?>"
                            data-sort="<?php echo strtotime($link_published); ?>">
                            <?php echo date('d/m/Y H:i:s', strtotime($link_published)); ?></td>
                        <td>
                            <div class="action-btns">
                                <a href="treatment/?type=edit&editor=true&id=<?php echo base64_encode($service_id); ?>" class="action-btn btn-edit" title="Edit"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <a href="<?php echo $link_url; ?>" target='_blank' class="action-btn btn-view" title="View"><i class="fas fa-eye"></i> View</a>
                                <a href="treatment/?type=treatment&id=<?php echo base64_encode($service_id); ?>" class="action-btn btn-delete" title="Delete"><i class="fas fa-trash"></i> Delete</a>
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