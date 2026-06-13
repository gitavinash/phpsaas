<?php

$serviceStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($_GET['service']) ? $_GET['service'] : 0);

$serviceStatus = (($serviceStatus == 'all') ? '' : "AND service_status='{$serviceStatus}'");

?>
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
    l.link_published DESC LIMIT {$startFrom}, 100")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
                    <tr style="height: 80px">
                        <td>
                            <p><a
                                    href="treatment/?type=edit&editor=true&id=<?php echo base64_encode($service_id); ?>"><?php echo $service_title . (($link_status != '200') ? '<strong> — Draft </strong>' : ''); ?></a>
                            </p>
                            <div class="hover">
                                <a href="treatment/?type=edit&editor=true&id=<?php echo base64_encode($service_id); ?>"><i
                                        class="fas fa-pencil-alt"></i> Edit</a>
                                <a href="treatment/?type=treatment&id=<?php echo base64_encode($service_id); ?>"><i
                                        class="fas fa-trash"></i>
                                    Delete</a>
                                <a href="<?php echo $link_url; ?>" target='_blank'><i class="fas fa-eye"></i> View</a>
                            </div>
                        </td>
                        <td><?php echo $category_title; ?></td>
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
                    [3, "desc"]
                ],
                "order": [
                    [3, "desc"]
                ]
            });
        }

        table.order([3, 'desc']).draw();
    });
</script>