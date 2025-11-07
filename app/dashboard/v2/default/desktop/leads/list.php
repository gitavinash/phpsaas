<?php
$statusCondition = '';
$nameCondition = '';
$emailCondition = '';
$mobileCondition = '';
$durationCondition = '';

if (isset($leadStatus)) {
    $statusCondition = "e.enquiry_status={$leadStatus} AND ";
}

if (isset($daterange)) {
    $dates = explode(" - ", $daterange);
    $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
    $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
    $durationCondition = " e.enquiry_time BETWEEN '{$startDate}' AND '{$endDate}' AND ";
}

if (isset($leadEmail)) {
    $emailCondition = " e.enquiry_email='{$leadEmail}' AND ";
}

if (isset($leadMobile)) {
    $leadMobile = normalizeIndianMobile($leadMobile);
    $mobileCondition = " e.enquiry_mobile='{$leadMobile}' AND ";
}

if (isset($leadName)) {
    $nameCondition = " e.enquiry_first_name LIKE '%{$leadName}%' AND ";
}
// if (isset($leadName)) {
//     $nameCondition = " CONCAT_WS(' ', e.enquiry_first_name, e.enquiry_middle_name, e.enquiry_last_name) LIKE CONCAT('%{$leadName}%') AND ";
// }

$user_filter = '';

if ($site_access_type == 'agent') {
    $user_filter = " e.enquiry_agent='" . USER_ID . "' AND ";
}

if (isset($agentId)) {
    $user_filter = " e.enquiry_agent='{$agentId}' AND ";
}

$userList = array();

if ($userInfoList = mysqli_query($connection, "SELECT user_id, user_username, user_mobile, user_email, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE site_access_site_id=" . SITE_ID)) {
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
<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List ( Between
    <?php echo date('jS F y', strtotime($startDate)) . ' to ' . date('jS F y', strtotime($endDate)) ?>)<span>All
        hostings list will be displayed here</span>
</h2>
<div class="box-1">
    <div class="shortcut">
        <a href="#" id="showFilterPopUp" data-title="Filter leads" style="background: #167a92;"><i
                class="fi fi-rr-filter"></i></a>
        <a href="/dashboard/leads/add/" data-title="Add new leads"><i class="fi fi-rr-plus"></i></a>
    </div>
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
                    <?php

                    if ($site_access_type == 'digital_marketing') { ?>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>Time</th>
                    <th>Source</th>
                    <th>Status</th>
                    <?php
                    } else { ?>
                    <th>Name</th>
                    <!-- <th>Email</th> -->
                    <th>Mobile</th>
                    <?php echo ((SITE_APP == 'astro_client') ? '<th>Alternate mobile</th>' : '') ?>
                    <th>Subject</th>
                    <th>Time</th>
                    <?php

                        if ($site_access_type != 'agent') {
                            echo (($site_access_type != 'team_lead') ? '<th>Source</th>' : '');
                            echo "<th>Assign to</th>";
                        }
                        if (SITE_APP != 'astro_client') {
                            echo "<th>Recent note</th>";
                        }

                        ?>
                    <th>Status</th>
                    <th></th>
                    <?php

                    }

                    ?>
                </tr>
            </thead>
            <tbody>
                <?php

                $enquiryQuery = "SELECT
    e.enquiry_id,
    e.enquiry_time,
    CASE
        WHEN e.enquiry_agent = 0 THEN NULL
        ELSE p.profile_display_name
    END AS agent_display_name,
    e.enquiry_first_name,
    e.enquiry_email,
    e.enquiry_mobile,
    e.enquiry_alt_mobile,
    e.enquiry_subject,
    e.enquiry_source,
    e.enquiry_status,
    n.enquiry_notes_meta_type,
    n.enquiry_notes_meta_value
FROM
    " . TABLE_PREFIX . "enquiry e
LEFT JOIN
    " . TABLE_PREFIX . "profile p ON e.enquiry_agent = p.profile_user_id
LEFT JOIN
    (SELECT
         enquiry_notes_ref_id,
         MAX(enquiry_notes_update) AS latest_notes_time
     FROM
         " . TABLE_PREFIX . "enquiry_notes
     GROUP BY
         enquiry_notes_ref_id) latest_notes ON enquiry_id = latest_notes.enquiry_notes_ref_id
LEFT JOIN
    " . TABLE_PREFIX . "enquiry_notes n ON enquiry_id = n.enquiry_notes_ref_id AND latest_notes.latest_notes_time = n.enquiry_notes_update
WHERE
    {$user_filter}
    {$statusCondition}
    {$nameCondition}
    {$mobileCondition}
    {$emailCondition}
    {$durationCondition}
    e.enquiry_site_id = " . SITE_ID . "
    GROUP BY enquiry_id
ORDER BY
    e.enquiry_time DESC LIMIT 0, 1000";

                if ($dataList = mysqli_query($connection, $enquiryQuery)) {
                    if (mysqli_num_rows($dataList) != 0) {
                        while ($data = mysqli_fetch_assoc($dataList)) {
                            extract($data);

                            switch ($enquiry_status) {
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
                                case 8:
                                    $leadCurrentStatus = 'Closed';
                                    break;
                                default:
                                    $leadCurrentStatus = 'Initiate';
                                    break;
                            }

                ?>
                <tr>
                    <?php

                                if ($site_access_type == 'digital_marketing') { ?>
                    <td><?php echo ucwords($enquiry_first_name) ?></td>
                    <td><?php echo $enquiry_subject; ?></td>
                    <td>
                        <?php echo date('d M Y, H:i A', strtotime($enquiry_time)); ?></td>
                    <td><?php echo ucwords($enquiry_source) ?></td>
                    <td><?php echo ucwords($leadCurrentStatus) ?></td>
                    <?php
                                } else { ?>
                    <td><?php echo ucwords($enquiry_first_name) ?></td>
                    <!-- <td><?php echo $enquiry_email; ?></td> -->
                    <td><?php echo $enquiry_mobile; ?></td>
                    <?php

                                    if (SITE_APP == 'astro_client') { ?>
                    <td><?php echo $enquiry_alt_mobile; ?></td>
                    <?php
                                    }

                                    ?>
                    <td><?php echo $enquiry_subject; ?></td>
                    <td>
                        <?php echo date('d M Y, H:i A', strtotime($enquiry_time)); ?></td>
                    <?php

                                    if ($site_access_type != 'agent') {
                                        if ($site_access_type != 'team_lead') {
                                            echo "<td>" . ucwords($enquiry_source) . "</td>";
                                        } ?>

                    <td>
                        <form method="POST">
                            <?php
                                                if ($site_access_type != 'agent' && $enquiry_status == 0 && $agent_display_name == null) { ?>
                            <select class="selectJS form-control" name="userId" required>
                                <option value="">Select user</option>
                                <?php

                                                        for ($i = 0; $i < sizeof($userList); $i++) {
                                                            echo "<option value='" . base64_encode($userList[$i]['id'] . '_' . $userList[$i]['name']) . "'>{$userList[$i]['name']} {$userList[$i]['email']}</option>";
                                                        }

                                                        ?>
                            </select>
                            <input type="hidden" name="leadId" value="<?php echo base64_encode($enquiry_id) ?>">
                            <input type="hidden" name="action" value="assign">
                            <input type="hidden" name="type" value="leads">
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
                        <strong><?php echo $enquiry_notes_meta_type ?></strong><br /><?php echo $enquiry_notes_meta_value ?>
                    </td>
                    <?php
                                    }

                                    ?>
                    </td>
                    <td><?php echo $leadCurrentStatus ?></td>
                    <td>
                        <a href='leads/view/?id=<?php echo base64_encode($enquiry_id) ?>' target='_blank'
                            class='tableActionIconBtn' title='View package' data-title='View package'><i
                                class="fi-rr-eye bgWhite"></i></a>
                        <?php

                                        if ($enquiry_status == 0 && $site_access_type != 'agent') { ?>

                        <a href='#' target='_blank' class='tableActionIconBtn duplicateLead' title='Mark as Duplicate'
                            id="<?php echo base64_encode($enquiry_id) ?>" data-title='Mark as Duplicate'><i
                                class="fi-rr-clipoard-wrong bgYellow"></i></a>

                        <?php
                                        }

                                        if ($site_access_type == 'administrator') { ?>
                        <a href='#' target='_blank' class='tableActionIconBtn deleteButton' title='Delete lead'
                            data-type='leads' data-id="<?php echo $enquiry_id ?>" data-title='Delete lead'><i
                                class="fi-rr-trash bgRed"></i></a>
                        <?php                                        }

                                        ?>
                    </td>
                    <?php
                                }

                                ?>
                </tr>
                <?php
                        }
                    }
                }

                ?>
            </tbody>
        </table>
    </div>
    <div class="popUp filterBox">
        <div class="fullScreenPopUpBox">
            <div class="widget box-7">
                <div class="contentArea">
                    <form method="POST" action="#" autocomplete="off">
                        <section class="form">
                            <div class="step-open-box step_1_box">
                                <div class="form-header">
                                    <h4 class="heading">Set Filter conditions</h4>
                                </div>
                                <div class="form-group">
                                    <select class="selectJS form-control" id="filterStatus" name="filterStatus">
                                        <option value="">All statuses</option>
                                        <option value="0">Initiated</option>
                                        <option value="1">Successful</option>
                                        <option value="2">Pending</option>
                                        <option value="3">Follow-up</option>
                                        <option value="4">Failed</option>
                                        <option value="5">Duplicate</option>
                                        <option value="8">Closed</option>
                                    </select>
                                    <label>Status</label>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="name" id="filterName">
                                    <label class="form-lable">Name</label>
                                </div>
                                <div class="form-group">
                                    <input type="email" name="filterEmail" id="filterEmail">
                                    <label class="form-lable">Email</label>
                                </div>
                                <div class="form-group">
                                    <input type="number" name="filterMobile" id="filterMobile">
                                    <label class="form-lable">Mobile</label>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="dateRangePicker" name="filterDuration"
                                        id="filterDuration">
                                    <label class="form-lable">Duration</label>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
            <div class="widget box-3">
                <div class="actionArea">
                    <span class="btn popUpBtn" id="applyFilterOnLeads"><i class="fi-rr-check"></i> Apply filter</span>
                    <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                        Cancel</span>
                </div>
            </div>
        </div>
    </div>

</div>