<?php

$serviceStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($_GET['service']) ? $_GET['service'] : 0);

$serviceStatus = (($serviceStatus == 'all') ? '' : "AND service_status='{$serviceStatus}'");

?>
<h2>All services</h2>
<div class="options-bar">
    <div class="left-box">
        <form method="GET">
            <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
            <select class="form-control" name="status" id="changeStatus">
                <option value="all" <?php echo (($_GET['status'] == 'all') ? 'selected' : '') ?>>All services</option>
                <option value="0" <?php echo (($_GET['status'] == '0') ? 'selected' : '') ?>>Drafts services</option>
                <option value="1" <?php echo (($_GET['status'] == '1') ? 'selected' : '') ?>>Published services</option>
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
            <th style="width: 400px">Title</th>
            <th style="width: 150px">Category</th>
            <th style="width: 150px">Author</th>
            <th style="width: 100px">Publish date</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT service_id, service_title, category_title, link_url, link_status, link_creation, profile_display_name FROM " . TABLE_PREFIX . "service, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "profile WHERE service_owner_id='" . USER_ID . "' AND link_type='service' AND service_type='{$postType}' AND link_relation_id=service_id AND link_site_id='" . SITE_ID . "' AND category_id=service_category AND profile_user_id='" . USER_ID . "' AND link_status!=301 $serviceStatus ORDER BY link_published DESC LIMIT {$startFrom}, 100")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
                    <tr style="height: 80px">
                        <td>
                            <p><a href="service/?type=edit&editor=true&id=<?php echo base64_encode($service_id); ?>"><?php echo $service_title . (($link_status != '200') ? '<strong> — Draft </strong>' : ''); ?></a>
                            </p>
                            <div class="hover">
                                <a href="service/?type=edit&editor=true&id=<?php echo base64_encode($service_id); ?>"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <a href="service/?type=service&id=<?php echo base64_encode($service_id); ?>"><i class="fas fa-trash"></i>
                                    Delete</a>
                                <a href="<?php echo $link_url; ?>" target='_blank'><i class="fas fa-eye"></i> View</a>
                            </div>
                        </td>
                        <td><?php echo $category_title; ?></td>
                        <td><?php echo $profile_display_name; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($link_creation)); ?></td>
                    </tr>
        <?php
                }
            }
        }

        ?>
    </tbody>
</table>