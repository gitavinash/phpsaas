<?php
$statusCondition = '';
if (isset($visa_applicationstatus)) {
    $statusCondition = "va.visa_application_status={$visa_applicationstatus} AND ";
}

if (isset($daterange)) {
    $dates = explode(" - ", $daterange);
    $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
    $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
} else {
    $startDate = date("Y-m-d 00:00:00", strtotime("-12 month"));
    $endDate = date("Y-m-d 23:59:59");
}

$user_filter = '';

if ($site_access_type == 'agent') {
    $user_filter = " va.visa_application_agent='" . USER_ID . "' AND ";
}

if (isset($agentId)) {
    $user_filter = " va.visa_application_agent='{$agentId}' AND ";
}

$userList = array();

if ($userInfoList = mysqli_query($connection, "SELECT user_id, user_username, user_mobile, user_email, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE site_access_staus=1 AND site_access_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($userInfoList) != 0) {
        while ($data = mysqli_fetch_assoc($userInfoList)) {
            extract($data);
            $userList[] = array(
                'id' => $user_id,
                'name' => $profile_display_name,
                'email' => $user_email,
            );
        }
    }
}

?>
<h2 class="heading"><?php echo ucwords(str_replace('_', ' ', $urlStructure[1])) ?> List<span>All hostings list will be
        displayed here</span>
</h2>
<div class="main-box">
    <div class="shortcut">
        <a href="/services/" target="_blank"><i class="fi fi-rr-plus"></i></a>
    </div>
    <section>
        <!-- <div class="filters">
            <div class="form-group">
                <label><?php echo ucwords($urlStructure[1]) ?> Status</label>
                <select id="hostingStatus">
                    <option value=""><?php echo ucwords($urlStructure[1]) ?> Status</option>
                    <option value="0">Fresh</option>
                    <option value="1">Confirm</option>
                    <option value="2">Pending</option>
                    <option value="3">Follow up</option>
                    <option value="4">Cancel</option>
                    <option value="5">Duplicate</option>
                </select>
            </div>
            <div class="form-group right">
                <label>Date</label>
                <input type="text" class="dateRangePicker" placeholder="Select date range">
            </div>
        </div> -->
        <div class="list-box">
            <table class="showTableList nowrap">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>
                        <?php echo ((SITE_APP == 'astro_client') ? '<th>Alternate mobile</th>' : '') ?>
                        <th>Country</th>
                        <th>VISA type</th>
                        <th>Time</th>
                        <?php

                        if ($site_access_type != 'agent') {
                            echo "<th>Assign to</th>";
                        }

                        ?>
                        <th>Recent notes</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $visaApplicationListQuery = "
SELECT
    va.visa_application_id,
    va.visa_application_time,
    CASE
        WHEN va.visa_application_agent = 0 THEN NULL
        ELSE p.profile_display_name
    END AS agent_display_name,
    va.visa_application_data,
    va.visa_application_status,
    n.visa_application_notes_meta_type,
    n.visa_application_notes_meta_value,
    vp.visa_package_visa_type,
    vc.visa_country_title
FROM
    " . TABLE_PREFIX . "visa_application va
LEFT JOIN
    " . TABLE_PREFIX . "profile p
    ON va.visa_application_agent = p.profile_user_id
LEFT JOIN
    (
        SELECT
            visa_application_notes_ref_id,
            MAX(visa_application_notes_update) AS latest_notes_time
        FROM
            " . TABLE_PREFIX . "visa_application_notes
        GROUP BY
            visa_application_notes_ref_id
    ) latest_notes
    ON va.visa_application_id = latest_notes.visa_application_notes_ref_id
LEFT JOIN
    " . TABLE_PREFIX . "visa_application_notes n
    ON va.visa_application_id = n.visa_application_notes_ref_id
    AND latest_notes.latest_notes_time = n.visa_application_notes_update
LEFT JOIN
    " . TABLE_PREFIX . "order o
    ON va.visa_application_order_id = o.order_id
    AND o.order_product_type = 'visa_application'
    AND o.order_site_id = " . SITE_ID . "
INNER JOIN
    " . TABLE_PREFIX . "visa_package vp
    ON o.order_product_id = vp.visa_package_id
    AND vp.visa_package_site_id = " . SITE_ID . "
INNER JOIN
    " . TABLE_PREFIX . "visa_country vc
    ON vp.visa_package_country_id = vc.visa_country_id
    AND vc.visa_country_site_id = " . SITE_ID . "
WHERE
    {$user_filter}
    {$statusCondition}
    va.visa_application_site_id = " . SITE_ID . "
    AND va.visa_application_time BETWEEN '{$startDate}' AND '{$endDate}'
GROUP BY
    va.visa_application_id
ORDER BY
    va.visa_application_time DESC
LIMIT 0, 1000";

                    if ($dataList = mysqli_query($connection, $visaApplicationListQuery)) {
                        if (mysqli_num_rows($dataList) != 0) {
                            while ($data = mysqli_fetch_assoc($dataList)) {
                                extract($data);

                                switch ($visa_application_status) {
                                    case 1:
                                        $leadCurrentStatus = 'Success';
                                        break;
                                    case 2:
                                        $leadCurrentStatus = 'Pending';
                                        break;
                                    case 3:
                                        $leadCurrentStatus = 'Follow up';
                                        break;
                                    case 4:
                                        $leadCurrentStatus = 'Failed';
                                        break;
                                    case 5:
                                        $leadCurrentStatus = 'Duplicate';
                                        break;
                                    case 6:
                                        $leadCurrentStatus = 'Payment initiate';
                                        break;
                                    default:
                                        $leadCurrentStatus = 'Initiate';
                                        break;
                                }

                                $passangerData = json_decode($visa_application_data, TRUE);
                                extract($passangerData['personsData'][0]);

                    ?>
                                <tr>
                                    <td><?php echo ucwords($first_name) ?></td>
                                    <td><?php echo $mobile; ?></td>
                                    <td><?php echo ucwords($visa_country_title); ?></td>
                                    <td><?php echo ucwords($visa_package_visa_type); ?> VISA</td>
                                    <td>
                                        <?php echo date('d M Y, H:i A', strtotime($visa_application_time)); ?></td>
                                    <?php

                                    if ($site_access_type != 'agent') { ?>
                                        <td>
                                            <form method="POST">
                                                <?php
                                                if ($site_access_type != 'agent' && $visa_application_status == 0 && $agent_display_name == null) { ?>
                                                    <select class="selectJS form-control" name="userId" required>
                                                        <option value="">Select user</option>
                                                        <?php

                                                        for ($i = 0; $i < sizeof($userList); $i++) {
                                                            echo "<option value='" . base64_encode($userList[$i]['id'] . '_' . $userList[$i]['name']) . "'>{$userList[$i]['name']} {$userList[$i]['email']}</option>";
                                                        }

                                                        ?>
                                                    </select>
                                                    <input type="hidden" name="leadId"
                                                        value="<?php echo base64_encode($visa_application_id) ?>">
                                                    <input type="hidden" name="action" value="assign">
                                                    <input type="hidden" name="type" value="visa_applications">
                                                    <input type="submit" value="Assign lead to user"
                                                        class="btn dataTableSubmitBtn dataTableBtn">
                                                <?php
                                                } else {
                                                    echo $agent_display_name;
                                                }
                                                ?>
                                            </form>
                                        </td>
                                    <?php
                                    }

                                    if (SITE_APP != 'astro_client') { ?>
                                        <td>
                                            <strong><?php echo $visa_application_notes_meta_type ?></strong><br /><?php echo $visa_application_notes_meta_value ?>
                                        </td>
                                    <?php
                                    }

                                    ?>
                                    </td>
                                    <td><?php echo $leadCurrentStatus ?></td>
                                    <td>
                                        <a href='visa_applications/view/?id=<?php echo base64_encode($visa_application_id) ?>'
                                            target='_blank' class='tableActionIconBtn' title='View package'
                                            data-title='View package'><i class="fi-rr-eye bgWhite"></i></a>
                                        <?php

                                        if ($visa_application_status == 0 && $site_access_type != 'agent') { ?>

                                            <a href='#' target='_blank' class='tableActionIconBtn duplicateLead'
                                                title='Mark as Duplicate' id="<?php echo base64_encode($visa_application_id) ?>"
                                                data-title='Mark as Duplicate'><i class="fi-rr-clipoard-wrong bgYellow"></i></a>

                                        <?php
                                        }

                                        if ($site_access_type == 'administrator') { ?>
                                            <a href='#' target='_blank' class='tableActionIconBtn deleteButton' title='Delete lead'
                                                data-type='visa_applications' data-id="<?php echo $visa_application_id ?>"
                                                data-title='Delete lead'><i class="fi-rr-trash bgRed"></i></a>
                                        <?php                                        }

                                        ?>
                                    </td>
                                </tr>
                    <?php
                            }
                        }
                    }

                    ?>
                </tbody>
            </table>
        </div>
    </section>
</div>