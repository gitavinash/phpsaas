<?php

$requestId = base64_decode($id);

$accessFilter = '';
if ($site_access_type == 'agent') {
    $accessFilter = " AND request_agent='" . USER_ID . "'";
}

$requestQuery = "SELECT request_id, request_type,
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
    request_id='{$requestId}' AND
    request_site_id = " . SITE_ID . "
    AND request_time BETWEEN '{$startDate}' AND '{$endDate}'
    GROUP BY request_id
ORDER BY
    request_time DESC
LIMIT
    0, 1000";

if ($checkEnquiry = mysqli_query($connection, $requestQuery)) {
    if (mysqli_num_rows($checkEnquiry) != 0) {
        extract(mysqli_fetch_assoc($checkEnquiry));
        extract(json_decode($request_data, true));
    } else {
        echo "Invalid request";
        exit();
    }
} else {
    echo "Something went wrong";
    exit();
}

?>
<h2 class="heading">Request details<span>Complete information of request</span></h2>
<div class="box-1">

    <div class="widget box-7-5 no-padding">
        <div class="main-box">
            <section>
                <div class="widget no-padding box-1">
                    <div class="leadProfileCard">
                        <div class="img">
                            <img src="<?php echo IMG_LIBRARY ?>profile_icon/<?php echo strtolower(substr((trim($profile_display_name) != '' ? $profile_display_name : 'unknown'), 0, 1)); ?>.png"
                                alt="" srcset="">
                        </div>
                        <div class="info">
                            <h2 class="heading"
                                <?php echo (($request_status == 4 || $request_status == 5) ? "style='color:#f44336'" : "") ?>>
                                <?php echo $customerFullName = ucwords($profile_display_name . (($request_status == 4) ? ' ( Closed ) ' : (($request_status == 5) ? '( Duplicate )' : ''))) ?>
                            </h2>
                            <p>
                                <?php

                                if (isset($date_of_birth)) {
                                    echo "<span><strong>Date of Birth:</strong> " . date('d M Y', strtotime($date_of_birth)) . "</span>";
                                }

                                ?>
                                <span><strong>Last Update:
                                    </strong><?php echo date('d-m-Y h:i A', strtotime($request_update)) ?></span>
                            </p>
                        </div>
                        <div class="verifications">

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="main-box">
            <section>
                <div class="widget openBtnBox no-padding box-1" id="leadDetailsBox">
                    <div class="leadInfo">
                        <table>
                            <?php

                            $requestMessage = json_decode($request_data, true);
                            $requestMessageData = $requestMessage['data'];

                            if (is_array($requestMessageData)) {
                                foreach ($requestMessageData as $key => $value) {
                                    echo "<tr>
                                        <td class='box-3'><strong>" . str_replace('_', ' ', ucwords($key)) . "</strong></td>
                                        <td class='box-7'>" . ucfirst(str_replace('_', ' - ', $value)) . "</td>
                                    </tr>";
                                }
                            }

                            ?>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="widget no-padding box-2-5" id="fixedDiv">

        <div class="main-box">
            <section class="leadQuickLinks">
                <a href="" class="btn btn-green" data-id="<?php echo base64_encode($request_id) ?>" id="approveRequestBtn"><i class="fi-rr-check"></i> Approve</a>
                <a href='#' data-type='request' data-id='<?php echo $request_id ?>' title='Delete request' data-title='Delete request' class='btn btn-red deleteButton' data-reload='true' data-redirect='true'><i class='fi fi-rr-trash bgRed'></i> Delete request</a>
            </section>
        </div>
    </div>

</div>