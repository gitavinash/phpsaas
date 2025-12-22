<?php

function renderStarIcons($hotel_type)
{
    // Extract numeric part (e.g., "3" from "3_star")
    preg_match('/(\d+)/', $hotel_type, $matches);
    $stars = isset($matches[1]) ? (int)$matches[1] : 0;

    // Max stars = 5
    $filledStars = min($stars, 5);
    $emptyStars = 5 - $filledStars;

    // Generate HTML
    $output = '<div class="flex items-center">';
    for ($i = 0; $i < $filledStars; $i++) {
        $output .= '<i class="ri-star-fill text-yellow-400"></i>';
    }
    for ($i = 0; $i <
        $emptyStars; $i++) {
        $output .= '<i class="ri-star-line text-yellow-400"></i>';
    }
    $output .= '</div>';
    return
        $output;
}

function getQuotationDetails($connection, $quotationId)
{
    $query = "
    SELECT 
        q.*, 
        tp.*, 
        e.*, 
        g.*, 
        ep.*,
        GROUP_CONCAT(
            CONCAT_WS(':',
                TRIM(tph.tph_hotel_id),
                TRIM(tph.tph_room_type_id),
                TRIM(tph.tph_room_type),
                TRIM(tph.tph_night)
            )
            ORDER BY tph.tph_night
            SEPARATOR ','
        ) AS hotel_list
    FROM " . TABLE_PREFIX . "quotation q
    INNER JOIN " . TABLE_PREFIX . "tour_package tp 
        ON q.quotation_type_id = tp.tour_package_id
    INNER JOIN " . TABLE_PREFIX . "enquiry e 
        ON e.enquiry_id = q.quotation_ref_id
    LEFT JOIN " . TABLE_PREFIX . "gallery g 
        ON g.gallery_category = tp.tour_package_id 
        AND g.gallery_site_id = " . SITE_ID . "
    LEFT JOIN " . TABLE_PREFIX . "enquiry_profile ep 
        ON ep.enquiry_profile_ref_id = e.enquiry_id 
        AND ep.status = 1
    LEFT JOIN " . TABLE_PREFIX . "tour_package_hotel tph 
        ON tph.tph_package_id = tp.tour_package_id
    WHERE q.quotation_id = '{$quotationId}' 
        AND q.quotation_type = 'tour_package' 
        AND q.quotation_status IN (0,1) 
        AND q.quotation_site_id = " . SITE_ID . "
    GROUP BY q.quotation_id
";

    if ($checkQuote = mysqli_query($connection, $query)) {
        if (mysqli_num_rows($checkQuote) > 0) {
            extract(mysqli_fetch_assoc($checkQuote));
            if (!empty($enquiry_message)) {
                extract(json_decode($enquiry_message, true));
            }

            $hotelListData = explode(',', $hotel_list);
            $hotelIds = [];
            $roomIds = [];

            foreach ($hotelListData as $data) {
                $parts = explode(':', $data);
                if (count($parts) >= 4) {
                    $hotelIds[] = $parts[0];
                    $roomIds[] = $parts[1];
                }
            }

            $hotelIds = array_unique(array_filter($hotelIds));
            $roomIds = array_unique(array_filter($roomIds));

            $hotelIdsIn = implode(',', array_map('intval', $hotelIds));
            $roomIdsIn = implode(',', array_map('intval', $roomIds));

            $hotelRoomData = [];

            if ($hotelIdsIn !== '') {
                $query = "
    SELECT 
        property_id,
        property_title, 
        property_thumbnail, 
        property_city, 
        property_state, 
        property_country, 
        property_type,
        room_id,
        room_feature_image, 
        room_type 
    FROM isw_tour_package_hotel 
    INNER JOIN isw_property ON property_id = tph_hotel_id 
    INNER JOIN isw_room ON room_id = tph_room_type_id
    WHERE tph_hotel_id IN ($hotelIdsIn) 
      AND tph_room_type_id IN ($roomIdsIn)
";
                $result = mysqli_query($connection, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $hotelRoomData[] = $row;
                }
            }

            $quotationDescription = json_decode($quotation_description, true);

            if (empty($quotationDescription)) {
                return [];
            }
            $numberOfDays = $number_of_days ?? 0;
            $numberOfAdults = isset($number_of_adults) ? intval($number_of_adults) : 1;
            $numberOfChilds = isset($number_of_childrens) ? intval($number_of_childrens) : 0;
            $numberOfInfants = isset($number_of_infants) ? intval($number_of_infants) : 0;
            $gst_percent = 5;

            $quotationTotalPrice = isset($quotation_price) ? floatval(preg_replace('/[^\d.]/', '', $quotation_price)) : 0;
            $quotation_per_adult = ($numberOfAdults > 0) ? ($quotationTotalPrice / $numberOfAdults) : 0;

            $base_price_per_person = isset($quotation_per_adult) ? floatval(preg_replace('/[^\d.]/', '', $quotation_per_adult)) : 0;
            $total_base_price = $base_price_per_person * $numberOfAdults;
            $gst_amount = $total_base_price * ($gst_percent / 100);
            $mrp_per_person = $quotation_mrp;
            $total_mrp_price = $mrp_per_person * $numberOfAdults;
            $mrp_gst_total = $total_mrp_price + ($total_mrp_price * $gst_percent / 100);
            $final_price = $total_base_price + $gst_amount;
            $total_discount = $mrp_gst_total - $final_price;

            return [
                'quoteId' => '#' . getInitials(SITE_NAME) . '/' . base64_encode($quotation_ref_id) . '/' . $quotationId,
                'tour_package_title' => $tour_package_title,
                'enquiry_first_name' => $enquiry_first_name,
                'enquiry_last_name' => $enquiry_last_name,
                'tour_package_overview' => $tour_package_overview,
                'tour_package_city' => $tour_package_city,
                'tour_package_hotel' => $tour_package_hotel,
                'tour_package_duration' => $tour_package_duration,
                'quotation_mrp' => $quotation_mrp,
                'quotation_time' => $quotation_time,
                'quotation_price' => $quotation_price,
                'gallery_image' => $gallery_image,
                'itineraries' => $quotationDescription['itinerary'],
                'inclusions' => explode(',', $quotationDescription['inclusions']),
                'exclusions' => explode(',', $quotationDescription['exclusions']),
                'dayCount' => 1,
                'numberOfAdults' => $numberOfAdults,
                'numberOfChilds' => $numberOfChilds,
                'numberOfInfants' => $numberOfInfants,
                'numberOfDays' => $numberOfDays,
                'final_price' => $final_price,
                'mrp_gst_total' => $mrp_gst_total,
                'hotelRoomData' => $hotelRoomData,
                'total_base_price' => $total_base_price,
                'gst_percent' => $gst_percent,
                'gst_amount' => $gst_amount,
                'total_discount' => $total_discount,
                'days' => $numberOfDays + 1,
                'single_person_total' => $base_price_per_person + ($base_price_per_person * $gst_percent / 100),
                'discount_percent' => ($mrp_gst_total > 0)
                    ? round(($total_discount / $mrp_gst_total) * 100)
                    : 0,
                'pdfFileName' => strtolower(str_replace(' ', '_', $enquiry_first_name . ' ' . $enquiry_last_name . ' ' . $tour_package_title) . '_itinerary_' . time() . '.pdf'),
            ];
        } else {
            die("Quotation not found or inactive.");
        }
    } else {
        die("Database query failed: " . mysqli_error($connection));
    }
}