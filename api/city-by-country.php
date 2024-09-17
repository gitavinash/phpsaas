<?php

if (isset($country_id)) {
    if (isset($query)) {
        if ($city = mysqli_query($connection, "SELECT CONCAT(city_name, ', ', state_name, ', ', 'India') as city_name FROM isw_state INNER JOIN isw_city ON city_state_id=state_id WHERE state_country_id={$country_id} AND city_name LIKE '{$query}%' ORDER BY city_name ASC LIMIT 0, 10")) {

            if (mysqli_num_rows($city) != 0) {
                $status = 'success';
                $cityData = array();
                while ($cityInfo = mysqli_fetch_assoc($city)) {
                    $cityData[] = $cityInfo['city_name'];
                }

            } else {

                $msg = 'Not Found';
            }
        }
    } else {
        $msg = 'Search query is missing';
    }
} else {

    $msg = 'Invalid state id found';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : ''),
    'cityData' => ((isset($cityData)) ? $cityData : ''),
);

echo json_encode($result, true);
exit();
