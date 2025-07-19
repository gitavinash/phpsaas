<?php

$tourPackageId = base64_decode(mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0)));

$query = "
SELECT 
    p.property_id,
    p.property_title, 
    p.property_thumbnail, 
    p.property_address, 
    p.property_city, 
    p.property_state, 
    p.property_country, 
    p.property_type,
    tp.tour_package_id,
    tp.tour_package_title, 
    tp.tour_package_duration,
    GROUP_CONCAT(DISTINCT CONCAT(r.room_id, ':', r.room_type) ORDER BY r.room_id SEPARATOR ',') AS rooms
FROM " . TABLE_PREFIX . "tour_package tp
LEFT JOIN " . TABLE_PREFIX . "property p 
    ON tp.tour_package_state = p.property_state 
    AND tp.tour_package_hotel = p.property_type 
    AND p.property_site_id = " . SITE_ID . " 
    AND p.property_status = 1
LEFT JOIN " . TABLE_PREFIX . "room r
    ON r.room_property_id = p.property_id
    AND r.room_site_id = " . SITE_ID . "
WHERE tp.tour_package_id = {$tourPackageId}
  AND tp.tour_package_site_id = " . SITE_ID . "
GROUP BY p.property_id
";


$hotelList = array();

if ($hotelData = mysqli_query($connection, $query)) {
    if (mysqli_num_rows($hotelData) != 0) {
        while ($row = mysqli_fetch_assoc($hotelData)) {
            $hotelList[] = $row;
        }
    }
}

// Optional: extract tour_package_title from first row if available
if (!empty($hotelList)) {
    extract($hotelList[0]);
}

?>

<h2 class="heading"><?php echo ucwords($tour_package_title ?? '') ?> | Hotels list<span>Hotels included in
        tour package</span>
</h2>
<div class="box-1">

    <div class="widget box-1 no-padding">
        <div class="main-box">
            <section>

                <?php
                if (!empty($hotelList)) {
                    foreach ($hotelList as $hotel) {
                        if ($hotel['property_title'] != '') {
                            $path = !empty($hotel['property_thumbnail']) ? $hotel['property_thumbnail'] : ADMIN_IMAGES . 'default-picture.png';
                            $hotel_id = $hotel['property_id'] ?? '';  // adjust if you have property_id or similar field
                            $id = $hotel['tour_package_id'] ?? '';    // adjust if you want a category/id from somewhere else

                            echo "
        <div class='editGallery galleryImageBox setAsFeaturedBox'>
            <img src='{$path}' alt='" . htmlspecialchars($hotel['property_title']) . "'>
            <h2>{$hotel['property_title']}</h2>
            <div class='showOnHoverBox' style='margin-top: -120px'>
                <select class='night' style='width:100%;padding: 5px 0px;margin-bottom: 10px;border-radius: 5px;border: none;' name='day'>
                    <option value='#'>Select night to stay</option>
                    ";

                            for ($i = 1; $i <= $tour_package_duration; $i++) {
                                echo "<option value='{$i}'>Night {$i}</option>";
                            }

                            echo "
                </select>
                <select class='roomType' style='width:100%;padding: 5px 0px;margin-bottom: 10px;border-radius: 5px;border: none;' name='day'>
                    <option value='#'>Select room type</option>";

                            $roomList = explode(',', $rooms);

                            for ($j = 0; $j < sizeof($roomList); $j++) {
                                $roomParts = explode(':', $roomList[$j]);
                                $roomId = $roomParts[0];
                                $roomType = $roomParts[1];
                                echo "<option value='{$roomId}:{$roomType}'>" . ucwords(str_replace('_', ' ', $roomType)) . "</option>";
                            }

                            echo "
                </select>
                <a href='#' class='btn addHotelToPackage' data-hotel-id='{$hotel['property_id']}' data-tour-id='{$tourPackageId}'>Add to package</a>
            </div>
        </div>
        ";
                        }
                    }
                } else {
                    echo "<p>No hotels found.</p>";
                }
                ?>
                <div class="shortcut">
                    <a href="/dashboard/vendors/property/add/"><i class="fi fi-rr-plus"></i></a>
                </div>
            </section>
        </div>
    </div>
</div>