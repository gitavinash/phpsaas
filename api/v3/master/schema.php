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

        // Fast path: if tables already exist and reseed is not requested, return immediately without any DDL
        if (!$forceReseed) {
            $chk = @mysqli_query($connection, "SHOW TABLES LIKE '{$masterTourPkgTable}'");
            if ($chk && mysqli_num_rows($chk) > 0) {
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

        // Helper to safely clear tables without throwing 'DROP command denied' when TRUNCATE lacks DROP privilege
        $clearTable = function(string $table) use ($connection) {
            try {
                @mysqli_query($connection, "TRUNCATE TABLE `{$table}`");
            } catch (\Throwable $e) {
                @mysqli_query($connection, "DELETE FROM `{$table}`");
            }
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

                    $copySql = "INSERT INTO `{$mPkgTable}` SELECT * FROM `{$tblPrefix}tour_package` WHERE tour_package_id = {$origId}";
                    if (mysqli_query($connection, $copySql)) {
                        $counts['tour_package']++;
                    }
                }
            }

            // Copy prices and hotels for the selected master packages
            if (!empty($copiedPkgIds)) {
                $idsChunk = implode(',', $copiedPkgIds);
                mysqli_query($connection, "
                    INSERT INTO `{$tblPrefix}master_tour_package_price`
                    SELECT * FROM `{$tblPrefix}tour_package_price`
                    WHERE tour_package_price_parent_id IN ({$idsChunk})
                ");
                $counts['tour_package_price'] = (int)mysqli_affected_rows($connection);

                mysqli_query($connection, "
                    INSERT INTO `{$tblPrefix}master_tour_package_hotel`
                    SELECT * FROM `{$tblPrefix}tour_package_hotel`
                    WHERE tph_package_id IN ({$idsChunk})
                ");
                $counts['tour_package_hotel'] = (int)mysqli_affected_rows($connection);
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
            mysqli_query($connection, "INSERT INTO `{$mDestTable}` SELECT * FROM `{$tblPrefix}locations`");
            $counts['travel_destinations'] = (int)mysqli_affected_rows($connection);
        } else {
            $counts['travel_destinations'] = (int)$cntDest;
        }

        // 3. Seed Master Vehicles
        $mVehTable = "{$tblPrefix}master_vehicles";
        $cntVeh = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM `{$mVehTable}`"))['c'] ?? 0;
        if ($force || (int)$cntVeh === 0) {
            if ($force) $clearTable($mVehTable);
            mysqli_query($connection, "INSERT INTO `{$mVehTable}` SELECT * FROM `{$tblPrefix}vehicles`");
            $counts['vehicles'] = (int)mysqli_affected_rows($connection);
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

                    $copySql = "INSERT INTO `{$mHotelsTable}` SELECT * FROM `{$tblPrefix}property` WHERE property_id = {$origId}";
                    if (mysqli_query($connection, $copySql)) {
                        $counts['hotels']++;
                    }
                }
            }

            // Copy rooms
            if (!empty($copiedHotelIds)) {
                $hotelChunk = implode(',', $copiedHotelIds);
                mysqli_query($connection, "
                    INSERT INTO `{$tblPrefix}master_rooms`
                    SELECT * FROM `{$tblPrefix}room`
                    WHERE room_property_id IN ({$hotelChunk})
                ");
                $counts['rooms'] = (int)mysqli_affected_rows($connection);
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
            mysqli_query($connection, "INSERT INTO `{$mTransTable}` SELECT * FROM `{$tblPrefix}transporter`");
            $counts['transporters'] = (int)mysqli_affected_rows($connection);
        } else {
            $counts['transporters'] = (int)$cntTrans;
        }

        return $counts;
    }
}
