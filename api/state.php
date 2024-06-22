<?php

if (isset($country_id)) {

    if ($state = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "state WHERE state_country_id='{$country_id}' AND state_status=1 ORDER BY state_name ASC")) {

        if (mysqli_num_rows($state) != 0) {

            while ($stateInfo = mysqli_fetch_assoc($state)) {

                extract($stateInfo);

                $stateData[] = array(
                    'id' => $state_id,
                    'name' => $state_name
                );

                $status = 'success';
                $msg = 'State data fetch successfully';
            }
        } else {

            $msg = 'Not Found';
        }
    }
} else {

    $msg = 'Invalid country id found';
}

$result = array(
    'status' => ((isset($status)) ? $status : 206),
    'msg' => ((isset($msg)) ? $msg : ''),
    'data' => ((isset($stateData)) ? $stateData : ''),
);

echo json_encode($result, TRUE);
exit();
