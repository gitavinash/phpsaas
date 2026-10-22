<?php

/**
 * Master Data Schema & Seeder
 * Path: /api/v3/master/schema.php
 * Handles creation of master data tables with prefix TABLE_PREFIX . 'master_{data_type_name}'
 * and populating them with existing tour packages, travel destinations, vehicles, hotels, and transporters.
 */

if (!function_exists('ensureMasterDataTables')) {
    /**
     * Ensures all master data tables and views exist, and seeds them with existing data if empty.
     *
     * @param mysqli $connection
     * @param string $tblPrefix
     * @param bool $forceReseed
     * @return array
     */
    function ensureMasterDataTables($connection, string $tblPrefix = 'isw_', bool $forceReseed = false): array {
        if (!$connection) {
            return ['status' => 'error', 'msg' => 'Invalid database connection.'];
        }

        $masterTourPkgTable = "{$tblPrefix}master_tour_package";

        // Fast path: only skip if master catalog table already exists AND has records
        if (!$forceReseed) {
            $chk = @mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$masterTourPkgTable}`");
            if ($chk && ($r = mysqli_fetch_assoc($chk)) && (int)$r['c'] > 0) {
                return [
                    'status' => 'success',
                    'data'   => ['already_initialized' => true]
                ];
            }
        }

        $results = [
            'tables_created' => [],
            'views_created'  => [],
            'seeded_counts'  => []
        ];

        // Safe DDL executor to avoid uncaught mysqli_sql_exception on restricted MySQL users
        $safeDdl = function(string $sql) use ($connection) {
            try {
                return @mysqli_query($connection, $sql);
            } catch (\Throwable $e) {
                error_log("Master Schema DDL Warning: " . $e->getMessage());
                return false;
            }
        };

        // 1. Master Tour Package
        $srcTourPkgTable = "{$tblPrefix}tour_package";
        $chk = @mysqli_query($connection, "SHOW TABLES LIKE '{$masterTourPkgTable}'");
        if (!$chk || mysqli_num_rows($chk) === 0) {
            if ($safeDdl("CREATE TABLE IF NOT EXISTS `{$masterTourPkgTable}` LIKE `{$srcTourPkgTable}`")) {
                $results['tables_created'][] = $masterTourPkgTable;
            }
        }

        // Master Tour Package Price
        $masterTourPriceTable = "{$tblPrefix}master_tour_package_price";
        $srcTourPriceTable    = "{$tblPrefix}tour_package_price";
        $chk = @mysqli_query($connection, "SHOW TABLES LIKE '{$masterTourPriceTable}'");
        if (!$chk || mysqli_num_rows($chk) === 0) {
            if ($safeDdl("CREATE TABLE IF NOT EXISTS `{$masterTourPriceTable}` LIKE `{$srcTourPriceTable}`")) {
                $results['tables_created'][] = $masterTourPriceTable;
            }
        }

        // Master Tour Package Hotel
        $masterTourHotelTable = "{$tblPrefix}master_tour_package_hotel";
        $srcTourHotelTable    = "{$tblPrefix}tour_package_hotel";
        $chk = @mysqli_query($connection, "SHOW TABLES LIKE '{$masterTourHotelTable}'");
        if (!$chk || mysqli_num_rows($chk) === 0) {
            if ($safeDdl("CREATE TABLE IF NOT EXISTS `{$masterTourHotelTable}` LIKE `{$srcTourHotelTable}`")) {
                $results['tables_created'][] = $masterTourHotelTable;
            }
        }

        // 2. Master Travel Destinations
        $masterDestTable = "{$tblPrefix}master_travel_destinations";
        $srcDestTable    = "{$tblPrefix}locations";
        $chk = @mysqli_query($connection, "SHOW TABLES LIKE '{$masterDestTable}'");
        if (!$chk || mysqli_num_rows($chk) === 0) {
            if ($safeDdl("CREATE TABLE IF NOT EXISTS `{$masterDestTable}` LIKE `{$srcDestTable}`")) {
                $results['tables_created'][] = $masterDestTable;
            }
        }

        // 3. Master Vehicles
        $masterVehiclesTable = "{$tblPrefix}master_vehicles";
        $srcVehiclesTable    = "{$tblPrefix}vehicles";
        $chk = @mysqli_query($connection, "SHOW TABLES LIKE '{$masterVehiclesTable}'");
        if (!$chk || mysqli_num_rows($chk) === 0) {
            if ($safeDdl("CREATE TABLE IF NOT EXISTS `{$masterVehiclesTable}` LIKE `{$srcVehiclesTable}`")) {
                $results['tables_created'][] = $masterVehiclesTable;
            }
        }
        $chkCol = @mysqli_query($connection, "SHOW COLUMNS FROM `{$masterVehiclesTable}` LIKE 'vehicle_site_id'");
        if (!$chkCol || mysqli_num_rows($chkCol) === 0) {
            $safeDdl("ALTER TABLE `{$masterVehiclesTable}` ADD COLUMN `vehicle_site_id` BIGINT NOT NULL DEFAULT 0");
        }

        // 4. Master Hotels (Properties & Rooms)
        $masterHotelsTable = "{$tblPrefix}master_hotels";
        $srcHotelsTable    = "{$tblPrefix}property";
        $chk = @mysqli_query($connection, "SHOW TABLES LIKE '{$masterHotelsTable}'");
        if (!$chk || mysqli_num_rows($chk) === 0) {
            if ($safeDdl("CREATE TABLE IF NOT EXISTS `{$masterHotelsTable}` LIKE `{$srcHotelsTable}`")) {
                $safeDdl("ALTER TABLE `{$masterHotelsTable}` DROP INDEX `property_site_id_2`");
                $results['tables_created'][] = $masterHotelsTable;
            }
        }

        $masterRoomsTable = "{$tblPrefix}master_rooms";
        $srcRoomsTable    = "{$tblPrefix}room";
        $chk = @mysqli_query($connection, "SHOW TABLES LIKE '{$masterRoomsTable}'");
        if (!$chk || mysqli_num_rows($chk) === 0) {
            if ($safeDdl("CREATE TABLE IF NOT EXISTS `{$masterRoomsTable}` LIKE `{$srcRoomsTable}`")) {
                $results['tables_created'][] = $masterRoomsTable;
            }
        }

        // 5. Master Transporters
        $masterTransportersTable = "{$tblPrefix}master_transporters";
        $srcTransportersTable    = "{$tblPrefix}transporter";
        $chk = @mysqli_query($connection, "SHOW TABLES LIKE '{$masterTransportersTable}'");
        if (!$chk || mysqli_num_rows($chk) === 0) {
            if ($safeDdl("CREATE TABLE IF NOT EXISTS `{$masterTransportersTable}` LIKE `{$srcTransportersTable}`")) {
                $results['tables_created'][] = $masterTransportersTable;
            }
        }

        // Seed data if empty or forced
        $results['seeded_counts'] = seedMasterData($connection, $tblPrefix, $forceReseed);

        return [
            'status' => 'success',
            'data'   => $results
        ];
    }
}

if (!function_exists('copyTableDataSafe')) {
    /**
     * Safely copies data between two tables using only their common columns,
     * preventing column count mismatches across different environments.
     */
    function copyTableDataSafe($connection, string $targetTable, string $srcTable, string $whereClause = ''): int {
        $tCols = [];
        $sCols = [];
        $rT = @mysqli_query($connection, "SHOW COLUMNS FROM `{$targetTable}`");
        if ($rT) {
            while ($c = mysqli_fetch_assoc($rT)) $tCols[] = $c['Field'];
        }
        $rS = @mysqli_query($connection, "SHOW COLUMNS FROM `{$srcTable}`");
        if ($rS) {
            while ($c = mysqli_fetch_assoc($rS)) $sCols[] = $c['Field'];
        }
        $common = array_intersect($tCols, $sCols);
        if (empty($common)) {
            return 0;
        }
        $colList = '`' . implode('`, `', $common) . '`';
        $sql = "INSERT IGNORE INTO `{$targetTable}` ({$colList}) SELECT {$colList} FROM `{$srcTable}`" . ($whereClause ? " WHERE {$whereClause}" : '');
        @mysqli_query($connection, $sql);
        return (int)mysqli_affected_rows($connection);
    }
}

if (!function_exists('seedMasterData')) {
    /**
     * Seeds master data tables from existing production data
     *
     * @param mysqli $connection
     * @param string $tblPrefix
     * @param bool $force
     * @return array
     */
    function seedMasterData($connection, string $tblPrefix = 'isw_', bool $force = false): array {
        $counts = [
            'tour_package'        => 0,
            'tour_package_price'  => 0,
            'tour_package_hotel'  => 0,
            'travel_destinations' => 0,
            'vehicles'            => 0,
            'hotels'              => 0,
            'rooms'               => 0,
            'transporters'        => 0
        ];

        // Helper to safely clear tables without throwing 'DROP command denied'
        $clearTable = function(string $table) use ($connection) {
            @mysqli_query($connection, "DELETE FROM `{$table}`");
        };

        // 1. Seed Master Tour Packages (Deduplicated, most complete version of each title)
        $mPkgTable = "{$tblPrefix}master_tour_package";
        $cntPkg = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mPkgTable}`"))['c'] ?? 0;
        if ($force || (int)$cntPkg === 0) {
            if ($force) {
                $clearTable($mPkgTable);
                $clearTable("{$tblPrefix}master_tour_package_price");
                $clearTable("{$tblPrefix}master_tour_package_hotel");
            }

            // Retrieve best packages ordered by completeness
            $q = mysqli_query($connection, "
                SELECT p.*,
                       (SELECT COUNT(*) FROM `{$tblPrefix}tour_package_price` WHERE tour_package_price_parent_id = p.tour_package_id) as price_count,
                       (SELECT COUNT(*) FROM `{$tblPrefix}tour_package_hotel` WHERE tph_package_id = p.tour_package_id) as hotel_count,
                       LENGTH(COALESCE(p.tour_package_itinerary, '')) as itin_length
                FROM `{$tblPrefix}tour_package` p
                WHERE (p.tour_package_status = 1 OR p.tour_package_status IS NULL)
                ORDER BY price_count DESC, hotel_count DESC, itin_length DESC, p.tour_package_id DESC
            ");

            if (!$q || mysqli_num_rows($q) === 0) {
                $q = mysqli_query($connection, "
                    SELECT p.*,
                           (SELECT COUNT(*) FROM `{$tblPrefix}tour_package_price` WHERE tour_package_price_parent_id = p.tour_package_id) as price_count,
                           (SELECT COUNT(*) FROM `{$tblPrefix}tour_package_hotel` WHERE tph_package_id = p.tour_package_id) as hotel_count,
                           LENGTH(COALESCE(p.tour_package_itinerary, '')) as itin_length
                    FROM `{$tblPrefix}tour_package` p
                    ORDER BY price_count DESC, hotel_count DESC, itin_length DESC, p.tour_package_id DESC
                ");
            }

            $seenTitles = [];
            $copiedPkgIds = [];
            if ($q) {
                while ($row = mysqli_fetch_assoc($q)) {
                    $cleanTitle = strtolower(trim($row['tour_package_title'] ?? ''));
                    if (empty($cleanTitle) || isset($seenTitles[$cleanTitle])) {
                        continue;
                    }
                    $seenTitles[$cleanTitle] = true;
                    $origId = (int)$row['tour_package_id'];
                    $copiedPkgIds[] = $origId;

                    if (copyTableDataSafe($connection, $mPkgTable, "{$tblPrefix}tour_package", "tour_package_id = {$origId}") > 0) {
                        $counts['tour_package']++;
                    }
                }
            }

            // Copy prices and hotels for the selected master packages
            if (!empty($copiedPkgIds)) {
                $idsChunk = implode(',', $copiedPkgIds);
                copyTableDataSafe($connection, "{$tblPrefix}master_tour_package_price", "{$tblPrefix}tour_package_price", "tour_package_price_parent_id IN ({$idsChunk})");
                $counts['tour_package_price'] = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}master_tour_package_price`"))['c'] ?? 0);

                copyTableDataSafe($connection, "{$tblPrefix}master_tour_package_hotel", "{$tblPrefix}tour_package_hotel", "tph_package_id IN ({$idsChunk})");
                $counts['tour_package_hotel'] = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}master_tour_package_hotel`"))['c'] ?? 0);
            }
        } else {
            $counts['tour_package'] = (int)$cntPkg;
            $counts['tour_package_price'] = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}master_tour_package_price`"))['c'] ?? 0);
            $counts['tour_package_hotel'] = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}master_tour_package_hotel`"))['c'] ?? 0);
        }

        // 2. Seed Master Travel Destinations
        $mDestTable = "{$tblPrefix}master_travel_destinations";
        $cntDest = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mDestTable}`"))['c'] ?? 0;
        if ($force || (int)$cntDest === 0) {
            if ($force) $clearTable($mDestTable);
            copyTableDataSafe($connection, $mDestTable, "{$tblPrefix}locations");
            $curCnt = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mDestTable}`"))['c'] ?? 0);

            // Fallback 1: Extract distinct locations from existing tour packages
            if ($curCnt === 0) {
                $pkgLocQ = @mysqli_query($connection, "
                    SELECT DISTINCT tour_package_city, tour_package_state, tour_package_country
                    FROM `{$tblPrefix}tour_package`
                    WHERE tour_package_city IS NOT NULL AND TRIM(tour_package_city) != ''
                ");
                if ($pkgLocQ && mysqli_num_rows($pkgLocQ) > 0) {
                    while ($loc = mysqli_fetch_assoc($pkgLocQ)) {
                        $locCity = mysqli_real_escape_string($connection, trim($loc['tour_package_city']));
                        $locState = mysqli_real_escape_string($connection, trim($loc['tour_package_state'] ?? ''));
                        $locCountry = mysqli_real_escape_string($connection, trim($loc['tour_package_country'] ?? ''));
                        @mysqli_query($connection, "
                            INSERT IGNORE INTO `{$mDestTable}` (location_title, location_state, location_country, location_best_from, location_best_to, location_description, location_is_deletable, location_time)
                            VALUES ('{$locCity}', '{$locState}', '{$locCountry}', '', '', '', 1, NOW())
                        ");
                    }
                }
                $curCnt = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mDestTable}`"))['c'] ?? 0);
            }

            // Fallback 2: Seed curated standard destinations
            $curatedDests = [
                ['Manali', '14', '101', 'july', 'january', 'Shimla Manali'],
                ['Shimla', '14', '101', '', '', ''],
                ['Goa', '11', '101', '', '', ''],
                ['Kashmir', '15', '101', 'june', 'june', ''],
                ['Kerala', '19', '101', 'december', 'july', 'Beautiful valley of south'],
                ['Maldives', '0', '133', '', '', ''],
                ['Dubai', '0', '229', '', '', ''],
                ['Thailand', '0', '217', '', '', ''],
                ['Japan', '0', '109', '', '', ''],
                ['Singapore', '0', '196', '', '', ''],
                ['Malaysia', '0', '132', '', '', ''],
                ['Vietnam', '0', '238', '', '', ''],
                ['Nepal', '0', '153', '', '', ''],
                ['Bali', '0', '102', '', '', ''],
                ['Mauritius', '0', '140', '', '', ''],
                ['Andaman Nicobar', '1', '101', '', '', ''],
                ['Uttarakhand', '39', '101', 'january', 'january', '']
            ];
            foreach ($curatedDests as $cd) {
                @mysqli_query($connection, "
                    INSERT IGNORE INTO `{$mDestTable}` (location_title, location_state, location_country, location_best_from, location_best_to, location_description, location_is_deletable, location_time)
                    VALUES ('{$cd[0]}', '{$cd[1]}', '{$cd[2]}', '{$cd[3]}', '{$cd[4]}', '{$cd[5]}', 1, NOW())
                ");
            }
            $counts['travel_destinations'] = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mDestTable}`"))['c'] ?? 0);
        } else {
            $counts['travel_destinations'] = (int)$cntDest;
        }

        // 3. Seed Master Vehicles
        $mVehTable = "{$tblPrefix}master_vehicles";
        $cntVeh = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mVehTable}`"))['c'] ?? 0;
        if ($force || (int)$cntVeh === 0) {
            if ($force) $clearTable($mVehTable);
            copyTableDataSafe($connection, $mVehTable, "{$tblPrefix}vehicles");
            $curVehCnt = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mVehTable}`"))['c'] ?? 0);

            // Fallback: If source vehicles was empty, seed curated standard vehicles
            if ($curVehCnt === 0) {
                $curatedVehicles = [
                    ['Urbania - 9 Seater', 'traveller', 9, 0, '40.00', '35.00', 'Air Conditioned (AC), Pushback Reclining Seats'],
                    ['Urbania - 16 Seater', 'traveller', 12, 0, '40.00', '35.00', ''],
                    ['Urbania - 12 Seater', 'traveller', 12, 0, '40.00', '35.00', ''],
                    ['Innova Crysta', 'mpv', 6, 0, '18.00', '0.00', ''],
                    ['Tempo Traveller - 16 Seater', 'traveller', 16, 0, '32.00', '25.00', ''],
                    ['Tempo Traveller - 12 Seater', 'traveller', 12, 0, '32.00', '25.00', ''],
                    ['Tempo Traveller - 20 Seater', 'traveller', 20, 0, '32.00', '25.00', ''],
                    ['Tempo Traveller - 26 Seater', 'traveller', 26, 0, '40.00', '32.00', 'Air Conditioned (AC), Pushback Reclining Seats'],
                    ['Alto', 'hatchback', 3, 0, '0.00', '0.00', ''],
                    ['Dezire', 'sedan', 4, 0, '0.00', '0.00', ''],
                    ['Volvo', 'bus', 0, 0, '0.00', '0.00', '']
                ];
                foreach ($curatedVehicles as $cv) {
                    @mysqli_query($connection, "
                        INSERT IGNORE INTO `{$mVehTable}` (vehicle_title, vehicle_type, vehicle_seating, vehicle_luggage, vehicle_per_km_rate_with_tax, vehicle_per_km_rate_without_tax, vehicle_description, vehicle_is_deletable, vehicle_time)
                        VALUES ('{$cv[0]}', '{$cv[1]}', {$cv[2]}, {$cv[3]}, {$cv[4]}, {$cv[5]}, '{$cv[6]}', 1, NOW())
                    ");
                }
            }
            $counts['vehicles'] = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mVehTable}`"))['c'] ?? 0);
        } else {
            $counts['vehicles'] = (int)$cntVeh;
        }

        // 4. Seed Master Hotels & Rooms
        $mHotelsTable = "{$tblPrefix}master_hotels";
        $cntHotels = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mHotelsTable}`"))['c'] ?? 0;
        if ($force || (int)$cntHotels === 0) {
            if ($force) {
                $clearTable($mHotelsTable);
                $clearTable("{$tblPrefix}master_rooms");
            }

            // Retrieve best hotels ordered by room count and details
            $q = mysqli_query($connection, "
                SELECT p.*,
                       (SELECT COUNT(*) FROM `{$tblPrefix}room` WHERE room_property_id = p.property_id) as room_count,
                       LENGTH(COALESCE(p.property_description, '')) as desc_len
                FROM `{$tblPrefix}property` p
                WHERE (p.property_status = 1 OR p.property_status IS NULL)
                ORDER BY room_count DESC, desc_len DESC, p.property_id DESC
            ");

            if (!$q || mysqli_num_rows($q) === 0) {
                $q = mysqli_query($connection, "
                    SELECT p.*,
                           (SELECT COUNT(*) FROM `{$tblPrefix}room` WHERE room_property_id = p.property_id) as room_count,
                           LENGTH(COALESCE(p.property_description, '')) as desc_len
                    FROM `{$tblPrefix}property` p
                    ORDER BY room_count DESC, desc_len DESC, p.property_id DESC
                ");
            }

            $seenHotels = [];
            $copiedHotelIds = [];
            if ($q) {
                while ($row = mysqli_fetch_assoc($q)) {
                    $normKey = strtolower(trim($row['property_title'] ?? '')) . '|' . strtolower(trim($row['property_city'] ?? ''));
                    if (empty($row['property_title']) || isset($seenHotels[$normKey])) {
                        continue;
                    }
                    $seenHotels[$normKey] = true;
                    $origId = (int)$row['property_id'];
                    $copiedHotelIds[] = $origId;

                    if (copyTableDataSafe($connection, $mHotelsTable, "{$tblPrefix}property", "property_id = {$origId}") > 0) {
                        $counts['hotels']++;
                    }
                }
            }

            // Copy rooms
            if (!empty($copiedHotelIds)) {
                $hotelChunk = implode(',', $copiedHotelIds);
                copyTableDataSafe($connection, "{$tblPrefix}master_rooms", "{$tblPrefix}room", "room_property_id IN ({$hotelChunk})");
                $counts['rooms'] = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}master_rooms`"))['c'] ?? 0);
            }

            // Fallback: If source property table was empty, seed curated standard hotels
            $curHotelCnt = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mHotelsTable}`"))['c'] ?? 0);
            if ($curHotelCnt === 0) {
                $curatedHotels = [
                    ['The White Stone Resorts', 'Manali', 'Himachal Pradesh', 7200, 1, '5_star_hotel'],
                    ['Himalayan Stories', 'Manali', 'Himachal Pradesh', 1500, 2, '3_star_hotel'],
                    ['Hayer Regency', 'Manali', 'Himachal Pradesh', 1300, 2, '4_star_hotel'],
                    ['Hotel Willow Banks', 'Shimla', 'Himachal Pradesh', 4500, 1, '4_star_hotel'],
                    ['Radisson Hotel', 'Shimla', 'Himachal Pradesh', 6500, 1, '5_star_hotel'],
                    ['Umaid Bhawan Palace Hotel', 'Jaipur', 'Rajasthan', 5000, 1, '4_star_hotel'],
                    ['Hotel Diggi Palace', 'Jaipur', 'Rajasthan', 3500, 1, '3_star_hotel'],
                    ['Grand Hyatt', 'Goa', 'Goa', 8500, 1, '5_star_hotel'],
                    ['Santana Beach Resort', 'Goa', 'Goa', 3200, 1, '3_star_hotel'],
                    ['The Lalit Grand Palace', 'Srinagar', 'Jammu and Kashmir', 9500, 1, '5_star_hotel'],
                    ['Vivanta Dal View', 'Srinagar', 'Jammu and Kashmir', 8000, 1, '5_star_hotel'],
                    ['Blanket Hotel and Spa', 'Munnar', 'Kerala', 5500, 1, '5_star_hotel'],
                    ['Tea County Resort', 'Munnar', 'Kerala', 4200, 1, '4_star_hotel']
                ];
                foreach ($curatedHotels as $ch) {
                    $t = mysqli_real_escape_string($connection, $ch[0]);
                    $ci = mysqli_real_escape_string($connection, $ch[1]);
                    $st = mysqli_real_escape_string($connection, $ch[2]);
                    $ty = mysqli_real_escape_string($connection, $ch[5]);
                    @mysqli_query($connection, "
                        INSERT IGNORE INTO `{$mHotelsTable}` (property_title, property_city, property_state, property_starting_price, property_total_rooms, property_type, property_status, property_time)
                        VALUES ('{$t}', '{$ci}', '{$st}', {$ch[3]}, {$ch[4]}, '{$ty}', 1, NOW())
                    ");
                }
                $counts['hotels'] = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mHotelsTable}`"))['c'] ?? 0);
            }
        } else {
            $counts['hotels'] = (int)$cntHotels;
            $counts['rooms'] = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$tblPrefix}master_rooms`"))['c'] ?? 0);
        }

        // 5. Seed Master Transporters
        $mTransTable = "{$tblPrefix}master_transporters";
        $cntTrans = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mTransTable}`"))['c'] ?? 0;
        if ($force || (int)$cntTrans === 0) {
            if ($force) $clearTable($mTransTable);
            copyTableDataSafe($connection, $mTransTable, "{$tblPrefix}transporter");
            $curTrpCnt = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mTransTable}`"))['c'] ?? 0);

            // Fallback: If source transporters was empty, seed curated standard transporter
            if ($curTrpCnt === 0) {
                @mysqli_query($connection, "
                    INSERT IGNORE INTO `{$mTransTable}` (transporter_title, transporter_email, transporter_mobile, transporter_address, transporter_city, transporter_pincode, transporter_service_state, transporter_time)
                    VALUES ('RK Travels', 'rktravel650@gmail.com', '8860916172', 'Karol Bagh', 'New Delhi', '110005', 'Manali, Shimla, Delhi, Agra, Jaipur, Dharamshala, Dalhousie, Amritsar, Rishikesh Haridwar', NOW())
                ");
            }
            $counts['transporters'] = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mTransTable}`"))['c'] ?? 0);
        } else {
            $counts['transporters'] = (int)$cntTrans;
        }

        return $counts;
    }
}

// Endpoint direct execution handler
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'schema.php' || strpos($_SERVER['REQUEST_URI'] ?? '', '/api/v3/master/schema') !== false) {
    if (!isset($connection)) {
        $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
        if (file_exists($docRoot . '/config.php')) require_once $docRoot . '/config.php';
        if (file_exists($docRoot . '/lib/modules/connection.php')) require_once $docRoot . '/lib/modules/connection.php';
    }
    if (isset($connection) && $connection instanceof mysqli) {
        $force = isset($_GET['force']) && ($_GET['force'] === '1' || $_GET['force'] === 'true');
        $res = ensureMasterDataTables($connection, defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_', $force);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($res);
        exit;
    }
}
