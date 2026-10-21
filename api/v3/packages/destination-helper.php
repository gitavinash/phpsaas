<?php

/**
 * V3 Tour Package - Master Destination & Catalog Migration Helper
 * Path: /api/v3/packages/destination-helper.php
 */

if (!function_exists('classifyPackageDestination')) {
    /**
     * Classifies a tour package into a standardized domestic state or international country
     *
     * @param array $pkg
     * @return array
     */
    function classifyPackageDestination(array $pkg): array {
        $country = strtolower(trim($pkg['tour_package_country'] ?? ''));
        $state   = strtolower(trim($pkg['tour_package_state'] ?? ''));
        $city    = strtolower(trim($pkg['tour_package_city'] ?? ''));
        $title   = strtolower(trim($pkg['tour_package_title'] ?? ''));
        $text    = "{$country} {$state} {$city} {$title}";

        // International Destinations
        if (strpos($text, 'vietnam') !== false || strpos($text, 'viet nam') !== false) {
            return ['type' => 'international', 'key' => 'vietnam', 'name' => 'Vietnam', 'icon' => 'fa-solid fa-plane-departure', 'highlights' => 'Hanoi, Da Nang, Ha Long Bay', 'default' => true];
        }
        if (strpos($text, 'thailand') !== false || strpos($text, 'phuket') !== false || strpos($text, 'bangkok') !== false || strpos($text, 'pattaya') !== false) {
            return ['type' => 'international', 'key' => 'thailand', 'name' => 'Thailand', 'icon' => 'fa-solid fa-umbrella-beach', 'highlights' => 'Phuket, Bangkok, Pattaya, Krabi', 'default' => true];
        }
        if (strpos($text, 'dubai') !== false || strpos($text, 'united arab emirates') !== false || strpos($text, 'uae') !== false) {
            return ['type' => 'international', 'key' => 'dubai', 'name' => 'Dubai (UAE)', 'icon' => 'fa-solid fa-city', 'highlights' => 'Burj Khalifa, Desert Safari, Marina', 'default' => true];
        }
        if (strpos($text, 'bali') !== false || strpos($text, 'indonesia') !== false) {
            return ['type' => 'international', 'key' => 'bali', 'name' => 'Bali (Indonesia)', 'icon' => 'fa-solid fa-sun', 'highlights' => 'Ubud, Kuta, Seminyak, Nusa Penida', 'default' => true];
        }
        if (strpos($text, 'maldives') !== false || strpos($text, 'male') !== false) {
            return ['type' => 'international', 'key' => 'maldives', 'name' => 'Maldives', 'icon' => 'fa-solid fa-water', 'highlights' => 'Overwater Villas, Male, Water Sports', 'default' => true];
        }
        if (strpos($text, 'singapore') !== false) {
            return ['type' => 'international', 'key' => 'singapore', 'name' => 'Singapore', 'icon' => 'fa-solid fa-building-columns', 'highlights' => 'Marina Bay, Sentosa, Universal Studios', 'default' => false];
        }
        if (strpos($text, 'malaysia') !== false || strpos($text, 'langkawi') !== false || strpos($text, 'kedah') !== false) {
            return ['type' => 'international', 'key' => 'malaysia', 'name' => 'Malaysia', 'icon' => 'fa-solid fa-tower-observation', 'highlights' => 'Kuala Lumpur, Langkawi, Genting', 'default' => false];
        }
        if (strpos($text, 'japan') !== false || strpos($text, 'tokyo') !== false) {
            return ['type' => 'international', 'key' => 'japan', 'name' => 'Japan', 'icon' => 'fa-solid fa-torii-gate', 'highlights' => 'Tokyo, Kyoto, Mount Fuji, Osaka', 'default' => false];
        }
        if (strpos($text, 'nepal') !== false || strpos($text, 'kathmandu') !== false) {
            return ['type' => 'international', 'key' => 'nepal', 'name' => 'Nepal', 'icon' => 'fa-solid fa-mountain', 'highlights' => 'Kathmandu, Pokhara, Nagarkot', 'default' => false];
        }
        if (strpos($text, 'mauritius') !== false) {
            return ['type' => 'international', 'key' => 'mauritius', 'name' => 'Mauritius', 'icon' => 'fa-solid fa-compass', 'highlights' => 'Port Louis, Belle Mare, Chamarel', 'default' => false];
        }

        // Domestic Destinations / States
        if (strpos($text, 'tamil nadu') !== false || strpos($text, 'tamilnadu') !== false || strpos($text, 'mahabalipuram') !== false) {
            return ['type' => 'domestic', 'key' => 'tamil-nadu', 'name' => 'Tamil Nadu', 'icon' => 'fa-solid fa-landmark', 'highlights' => 'Mahabalipuram, Madurai, Rameshwaram', 'default' => false];
        }
        if (strpos($text, 'punjab') !== false) {
            return ['type' => 'domestic', 'key' => 'punjab', 'name' => 'Punjab', 'icon' => 'fa-solid fa-monument', 'highlights' => 'Amritsar, Golden Temple, Wagah Border', 'default' => false];
        }
        if (strpos($text, 'himachal') !== false || strpos($text, 'manali') !== false || strpos($text, 'shimla') !== false || strpos($text, 'spiti') !== false || strpos($text, 'kasol') !== false || strpos($text, 'jibhi') !== false || strpos($text, 'kullu') !== false || strpos($text, 'dharamshala') !== false) {
            return ['type' => 'domestic', 'key' => 'himachal-pradesh', 'name' => 'Himachal Pradesh', 'icon' => 'fa-solid fa-mountain', 'highlights' => 'Manali, Shimla, Spiti Valley, Kasol', 'default' => true];
        }
        if (strpos($text, 'rajasthan') !== false || strpos($text, 'jaipur') !== false || strpos($text, 'udaipur') !== false || strpos($text, 'jodhpur') !== false || strpos($text, 'jaisalmer') !== false || strpos($text, 'mount abu') !== false || strpos($text, 'chittorgarh') !== false || strpos($text, 'kumbhalgarh') !== false || strpos($text, 'bikaner') !== false) {
            return ['type' => 'domestic', 'key' => 'rajasthan', 'name' => 'Rajasthan', 'icon' => 'fa-solid fa-fort-awesome', 'highlights' => 'Jaipur, Udaipur, Jodhpur, Jaisalmer', 'default' => true];
        }
        if (strpos($text, 'kerala') !== false || strpos($text, 'munnar') !== false || strpos($text, 'alleppey') !== false || strpos($text, 'thekkady') !== false || strpos($text, 'cochin') !== false || strpos($text, 'kochi') !== false || strpos($text, 'kovalam') !== false) {
            return ['type' => 'domestic', 'key' => 'kerala', 'name' => 'Kerala', 'icon' => 'fa-solid fa-water', 'highlights' => 'Munnar, Alleppey, Thekkady, Kovalam', 'default' => true];
        }
        if (strpos($text, 'goa') !== false) {
            return ['type' => 'domestic', 'key' => 'goa', 'name' => 'Goa', 'icon' => 'fa-solid fa-umbrella-beach', 'highlights' => 'North & South Goa Beaches, Cruises', 'default' => true];
        }
        if (strpos($text, 'uttarakhand') !== false || strpos($text, 'nainital') !== false || strpos($text, 'mussoorie') !== false || strpos($text, 'rishikesh') !== false || strpos($text, 'haridwar') !== false || strpos($text, 'jimcorbett') !== false || strpos($text, 'corbett') !== false) {
            return ['type' => 'domestic', 'key' => 'uttarakhand', 'name' => 'Uttarakhand', 'icon' => 'fa-solid fa-person-hiking', 'highlights' => 'Nainital, Mussoorie, Rishikesh, Corbett', 'default' => true];
        }
        if (strpos($text, 'gujarat') !== false || strpos($text, 'kutch') !== false || strpos($text, 'dwarka') !== false || strpos($text, 'somnath') !== false || strpos($text, 'rann') !== false) {
            return ['type' => 'domestic', 'key' => 'gujarat', 'name' => 'Gujarat', 'icon' => 'fa-solid fa-shield-halved', 'highlights' => 'Rann of Kutch, Dwarka, Somnath', 'default' => false];
        }
        if (strpos($text, 'ladakh') !== false || strpos($text, 'leh') !== false || strpos($text, 'kashmir') !== false || strpos($text, 'gulmarg') !== false || strpos($text, 'srinagar') !== false || strpos($text, 'pahalgam') !== false) {
            return ['type' => 'domestic', 'key' => 'ladakh-kashmir', 'name' => 'Ladakh & Kashmir', 'icon' => 'fa-solid fa-snowflake', 'highlights' => 'Leh, Pangong, Gulmarg, Srinagar', 'default' => true];
        }
        if (strpos($text, 'sikkim') !== false || strpos($text, 'darjeeling') !== false || strpos($text, 'gangtok') !== false) {
            return ['type' => 'domestic', 'key' => 'sikkim-darjeeling', 'name' => 'Sikkim & Darjeeling', 'icon' => 'fa-solid fa-mountain-sun', 'highlights' => 'Gangtok, Pelling, Tsomgo Lake', 'default' => false];
        }
        if (strpos($text, 'andaman') !== false || strpos($text, 'port blair') !== false || strpos($text, 'havelock') !== false) {
            return ['type' => 'domestic', 'key' => 'andaman-nicobar', 'name' => 'Andaman & Nicobar', 'icon' => 'fa-solid fa-anchor', 'highlights' => 'Havelock, Neil Island, Port Blair', 'default' => false];
        }
        if (strpos($text, 'meghalaya') !== false || strpos($text, 'shillong') !== false || strpos($text, 'cherrapunji') !== false || strpos($text, 'northeast') !== false) {
            return ['type' => 'domestic', 'key' => 'meghalaya-northeast', 'name' => 'Meghalaya & Northeast', 'icon' => 'fa-solid fa-leaf', 'highlights' => 'Shillong, Cherrapunji, Dawki', 'default' => false];
        }
        if (strpos($text, 'delhi') !== false || strpos($text, 'golden triangle') !== false || strpos($text, 'agra') !== false) {
            return ['type' => 'domestic', 'key' => 'golden-triangle-delhi', 'name' => 'Golden Triangle / Delhi', 'icon' => 'fa-solid fa-monument', 'highlights' => 'Delhi, Agra, Fatehpur Sikri', 'default' => false];
        }

        return ['type' => 'domestic', 'key' => 'other-domestic', 'name' => 'Other Destinations', 'icon' => 'fa-solid fa-map-location-dot', 'highlights' => 'Custom Packages', 'default' => false];
    }
}

if (!function_exists('getMasterPackagesCatalog')) {
    /**
     * Returns deduplicated master packages prioritized by completeness
     *
     * @param mysqli $connection
     * @param string $tblPrefix
     * @return array
     */
    function getMasterPackagesCatalog($connection, string $tblPrefix): array {
        if (!$connection) return [];

        // Check if master_tour_package table exists
        $hasMaster = false;
        $chkMaster = @mysqli_query($connection, "SHOW TABLES LIKE '{$tblPrefix}master_tour_package'");
        if ($chkMaster && mysqli_num_rows($chkMaster) > 0) {
            $hasMaster = true;
        }

        $sourcePkgTable = $hasMaster ? "{$tblPrefix}master_tour_package" : "{$tblPrefix}tour_package";
        $sourcePriceTable = $hasMaster ? "{$tblPrefix}master_tour_package_price" : "{$tblPrefix}tour_package_price";
        $sourceHotelTable = $hasMaster ? "{$tblPrefix}master_tour_package_hotel" : "{$tblPrefix}tour_package_hotel";

        $q = mysqli_query($connection, "
            SELECT p.*,
                   (SELECT COUNT(*) FROM {$sourcePriceTable} WHERE tour_package_price_parent_id = p.tour_package_id) as price_count,
                   (SELECT COUNT(*) FROM {$sourceHotelTable} WHERE tph_package_id = p.tour_package_id) as hotel_count,
                   (SELECT COUNT(*) FROM {$tblPrefix}link WHERE link_relation_id = p.tour_package_id AND link_type = 'tour_package') as link_count,
                   LENGTH(COALESCE(p.tour_package_itinerary, '')) as itin_length
            FROM {$sourcePkgTable} p
            WHERE (p.tour_package_status = 1 OR p.tour_package_status IS NULL)
            ORDER BY price_count DESC, hotel_count DESC, itin_length DESC, p.tour_package_id DESC
        ");

        $catalog = [];
        if ($q) {
            while ($row = mysqli_fetch_assoc($q)) {
                $cleanTitle = strtolower(trim($row['tour_package_title'] ?? ''));
                if (empty($cleanTitle) || isset($catalog[$cleanTitle])) {
                    continue;
                }
                $destInfo = classifyPackageDestination($row);
                $row['_destination'] = $destInfo;
                $catalog[$cleanTitle] = $row;
            }
        }

        return $catalog;
    }
}

if (!function_exists('migrateMasterPackagesToSite')) {
    /**
     * Clones master packages matching selected destination keys into the target client site
     *
     * @param mysqli $connection
     * @param string $tblPrefix
     * @param int $siteId
     * @param int $userId
     * @param array $selectedDestKeys
     * @return array
     */
    function migrateMasterPackagesToSite($connection, string $tblPrefix, int $siteId, int $userId, array $selectedDestKeys): array {
        if (!$connection || $siteId <= 0) {
            return [
                'status'    => 'error',
                'msg'       => 'Invalid database connection or site ID.',
                'migrated'  => 0
            ];
        }

        // Clean & normalize destination keys
        $cleanKeys = [];
        foreach ($selectedDestKeys as $k) {
            $normalized = strtolower(trim((string)$k));
            if (!empty($normalized)) {
                $cleanKeys[$normalized] = true;
            }
        }

        // If no keys specified, use default high-demand keys
        if (empty($cleanKeys)) {
            $cleanKeys = [
                'himachal-pradesh' => true,
                'rajasthan'        => true,
                'kerala'           => true,
                'uttarakhand'      => true,
                'goa'              => true,
                'vietnam'          => true,
                'dubai'            => true,
                'bali'             => true,
                'thailand'         => true,
                'maldives'         => true
            ];
        }

        $chkMaster = @mysqli_query($connection, "SHOW TABLES LIKE '{$tblPrefix}master_tour_package'");
        $hasMaster = ($chkMaster && mysqli_num_rows($chkMaster) > 0);
        $sourcePriceTable = $hasMaster ? "{$tblPrefix}master_tour_package_price" : "{$tblPrefix}tour_package_price";
        $sourceHotelTable = $hasMaster ? "{$tblPrefix}master_tour_package_hotel" : "{$tblPrefix}tour_package_hotel";

        $allMaster = getMasterPackagesCatalog($connection, $tblPrefix);
        $migratedCount = 0;
        $skippedCount  = 0;
        $destinationsHit = [];

        // Check already existing package titles in the client site
        $existingTitles = [];
        $existQ = mysqli_query($connection, "SELECT LOWER(TRIM(tour_package_title)) as t FROM {$tblPrefix}tour_package WHERE tour_package_site_id = {$siteId}");
        if ($existQ) {
            while ($ex = mysqli_fetch_assoc($existQ)) {
                $existingTitles[$ex['t']] = true;
            }
        }

        $insertPkgStmt = mysqli_prepare($connection, "
            INSERT INTO {$tblPrefix}tour_package (
                tour_package_site_id, tour_package_created_by, tour_package_image, tour_package_title,
                tour_package_theme, tour_package_template, tour_package_start_from, tour_package_end_to,
                tour_package_city, tour_package_duration, tour_package_overview, tour_package_itinerary,
                tour_package_inclusions, tour_package_exclusions, tour_package_price, tour_package_hotel,
                tour_package_state, tour_package_country, tour_package_type, tour_package_outstation_vehicle,
                tour_package_localsightseen_vehicle, tour_package_status, tour_package_created_at, tour_package_updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
        ");

        foreach ($allMaster as $cleanTitle => $master) {
            $destKey = $master['_destination']['key'] ?? '';
            if (!isset($cleanKeys[$destKey]) && !isset($cleanKeys['all'])) {
                continue;
            }

            if (isset($existingTitles[$cleanTitle])) {
                $skippedCount++;
                continue;
            }

            $masterPkgId = (int)$master['tour_package_id'];
            $destinationsHit[$destKey] = true;

            $duration = (int)($master['tour_package_duration'] ?? 0);
            $pkgTitle = $master['tour_package_title'] ?? '';

            if ($insertPkgStmt) {
                mysqli_stmt_bind_param(
                    $insertPkgStmt,
                    'iisssssssisssssssssss',
                    $siteId,
                    $userId,
                    $master['tour_package_image'],
                    $master['tour_package_title'],
                    $master['tour_package_theme'],
                    $master['tour_package_template'],
                    $master['tour_package_start_from'],
                    $master['tour_package_end_to'],
                    $master['tour_package_city'],
                    $duration,
                    $master['tour_package_overview'],
                    $master['tour_package_itinerary'],
                    $master['tour_package_inclusions'],
                    $master['tour_package_exclusions'],
                    $master['tour_package_price'],
                    $master['tour_package_hotel'],
                    $master['tour_package_state'],
                    $master['tour_package_country'],
                    $master['tour_package_type'],
                    $master['tour_package_outstation_vehicle'],
                    $master['tour_package_localsightseen_vehicle']
                );
                
                if (mysqli_stmt_execute($insertPkgStmt)) {
                    $newPkgId = mysqli_insert_id($connection);
                    $existingTitles[$cleanTitle] = true;
                    $migratedCount++;

                    // Clone pricing rows
                    mysqli_query($connection, "
                        INSERT INTO {$tblPrefix}tour_package_price (
                            tour_package_price_parent_id, tour_package_price_site_id, tour_package_price_user_id,
                            tour_package_price_date_start, tour_package_price_date_end, tour_package_price_net_adult,
                            tour_package_price_net_child, tour_package_price_mrp_adult, tour_package_price_mrp_child,
                            tour_package_price_selling_adult, tour_package_price_selling_child, tour_package_price_agent_adult,
                            tour_package_price_agent_child, tour_package_price_type, tour_package_price_status, tour_package_price_created_at
                        )
                        SELECT 
                            {$newPkgId}, {$siteId}, {$userId},
                            tour_package_price_date_start, tour_package_price_date_end, tour_package_price_net_adult,
                            tour_package_price_net_child, tour_package_price_mrp_adult, tour_package_price_mrp_child,
                            tour_package_price_selling_adult, tour_package_price_selling_child, tour_package_price_agent_adult,
                            tour_package_price_agent_child, tour_package_price_type, 1, NOW()
                        FROM {$sourcePriceTable}
                        WHERE tour_package_price_parent_id = {$masterPkgId}
                    ");

                    // Clone hotels rows
                    mysqli_query($connection, "
                        INSERT INTO {$tblPrefix}tour_package_hotel (
                            tph_hotel_id, tph_package_id, tph_room_type_id, tph_room_type, tph_night, tph_site_id, tph_status
                        )
                        SELECT 
                            tph_hotel_id, {$newPkgId}, tph_room_type_id, tph_room_type, tph_night, {$siteId}, 1
                        FROM {$sourceHotelTable}
                        WHERE tph_package_id = {$masterPkgId}
                    ");

                    // Clone or generate SEO link
                    $linkQ = mysqli_query($connection, "
                        SELECT link_url, link_is_featured, link_index 
                        FROM {$tblPrefix}link 
                        WHERE link_relation_id = {$masterPkgId} AND link_type = 'tour_package' 
                        LIMIT 1
                    ");
                    if ($linkQ && mysqli_num_rows($linkQ) > 0) {
                        $linkRow = mysqli_fetch_assoc($linkQ);
                        $linkUrl = mysqli_real_escape_string($connection, $linkRow['link_url'] ?? '');
                        $linkFeatured = (int)($linkRow['link_is_featured'] ?? 0);
                        $linkIndex = mysqli_real_escape_string($connection, $linkRow['link_index'] ?? 'index, follow');
                        mysqli_query($connection, "
                            INSERT INTO {$tblPrefix}link (
                                link_url, link_relation_id, link_status, link_type, link_is_featured, link_index, link_owner_id, link_site_id, link_creation, link_published
                            ) VALUES (
                                '{$linkUrl}', {$newPkgId}, 1, 'tour_package', {$linkFeatured}, '{$linkIndex}', {$userId}, {$siteId}, NOW(), NOW()
                            )
                        ");
                    } else {
                        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $pkgTitle), '-'));
                        if (empty($slug)) $slug = 'tour-package-' . $newPkgId;
                        mysqli_query($connection, "
                            INSERT INTO {$tblPrefix}link (
                                link_url, link_relation_id, link_status, link_type, link_is_featured, link_index, link_owner_id, link_site_id, link_creation, link_published
                            ) VALUES (
                                '{$slug}', {$newPkgId}, 1, 'tour_package', 0, 'index, follow', {$userId}, {$siteId}, NOW(), NOW()
                            )
                        ");
                    }
                }
            }
        }

        if ($insertPkgStmt) {
            mysqli_stmt_close($insertPkgStmt);
        }

        return [
            'status'            => 'success',
            'migrated_count'    => $migratedCount,
            'skipped_count'     => $skippedCount,
            'destinations_count'=> count($destinationsHit)
        ];
    }
}
