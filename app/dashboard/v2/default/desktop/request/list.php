<?php
$statusCondition = '';
if (isset($leadStatus)) {
    $statusCondition = "request_status={$leadStatus} AND ";
}

if (isset($daterange)) {
    $dates = explode(" - ", $daterange);
    $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
    $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
} else {
    $startDate = date("Y-m-d 00:00:00", strtotime("-6 month"));
    $endDate = date("Y-m-d 23:59:59");
}

$user_filter = '';

if ($site_access_type == 'agent') {
    $user_filter = " request_action_user='" . USER_ID . "' AND ";
}

if (isset($agentId)) {
    $user_filter = " request_action_user='{$agentId}' AND ";
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
<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> Requests<span>All request list will be displayed here</span>
</h2>
<div class="main-box">
    <div class="shortcut">
        <a href="/dashboard/request/add/"><i class="fi fi-rr-plus"></i></a>
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
                        <th>Reuqest for</th>
                        <th>Request by</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    if ($dataList = mysqli_query($connection, "SELECT request_id, request_type,
       request_relation_id,
       request_title,
       request_data,
       request_status,
       request_action_user,
       profile_display_name,
       request_time,
       request_update
FROM   " . TABLE_PREFIX . "request
       INNER JOIN " . TABLE_PREFIX . "profile
               ON request_user_id = profile_user_id
WHERE
    {$user_filter}
    {$statusCondition}
    request_site_id = " . SITE_ID . "
    AND request_time BETWEEN '{$startDate}' AND '{$endDate}'
    GROUP BY request_id
ORDER BY
    request_time DESC
LIMIT
    0, 1000")) {
                        if (mysqli_num_rows($dataList) != 0) {
                            while ($data = mysqli_fetch_assoc($dataList)) {
                                extract($data);

                                switch ($request_status) {
                                    case 1:
                                        $requestCurrentStatus = 'Success';
                                        break;
                                    case 2:
                                        $requestCurrentStatus = 'Reject';
                                        break;
                                    default:
                                        $requestCurrentStatus = 'Pending';
                                        break;
                                }

                    ?>
                                <tr>
                                    <td><?php echo ucwords($request_title) ?></td>
                                    <td><?php echo $profile_display_name; ?></td>
                                    <td>
                                        <?php echo date('d M Y, H:i A', strtotime($request_time)); ?></td>
                                    <td><?php echo $requestCurrentStatus; ?></td>
                                    <td>
                                        <a href='/dashboard/request/view/?id=<?php echo base64_encode($request_id) ?>' class='tableActionIconBtn' title='View request' data-title='View request'><i class='fi fi-rr-eye bgWhite'></i></a>
                                        <a href='#' data-type='request' data-id='<?php echo $request_id ?>' title='Delete request' data-title='Delete request' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash bgRed'></i></a>
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