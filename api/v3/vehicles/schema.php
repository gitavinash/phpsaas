<?php
/**
 * Safe Idempotent Schema Migration for Vehicles & Transporter Features
 * Path: /api/v3/vehicles/schema.php
 * Automatically creates/adds columns to production database if missing.
 */

if (!function_exists('ensureTransporterVehiclesSchema')) {
    function ensureTransporterVehiclesSchema($connection, $tblPrefix = 'isw_', $siteId = 1) {
        static $checked = false;
        if ($checked || !$connection) {
            return;
        }
        $checked = true;

        $vehiclesTable = "{$tblPrefix}vehicles";
        $vCols = [
            'vehicle_thumbnail'               => "VARCHAR(255) NULL DEFAULT ''",
            'vehicle_gallery'                 => "LONGTEXT NULL",
            'vehicle_seating'                 => "INT(11) NULL DEFAULT 0",
            'vehicle_luggage'                 => "INT(11) NULL DEFAULT 0",
            'vehicle_fuel_type'               => "VARCHAR(50) NULL DEFAULT ''",
            'vehicle_type'                    => "VARCHAR(100) NULL DEFAULT 'other'",
            'vehicle_per_km_rate_with_tax'    => "DECIMAL(10,2) NULL DEFAULT 0.00",
            'vehicle_per_km_rate_without_tax' => "DECIMAL(10,2) NULL DEFAULT 0.00",
            'vehicle_is_deletable'            => "TINYINT(1) NULL DEFAULT 1"
        ];

        // 1. Verify and auto-add missing columns to vehicles table
        foreach ($vCols as $colName => $colDef) {
            $chk = @mysqli_query($connection, "SHOW COLUMNS FROM `{$vehiclesTable}` LIKE '{$colName}'");
            if ($chk && mysqli_num_rows($chk) === 0) {
                @mysqli_query($connection, "ALTER TABLE `{$vehiclesTable}` ADD COLUMN `{$colName}` {$colDef}");
            }
        }

        // 2. Verify and auto-add missing columns to enquiry_notes table
        $notesTable = "{$tblPrefix}enquiry_notes";
        $nCols = [
            'enquiry_notes_meta_type'  => "VARCHAR(255) NULL DEFAULT ''",
            'enquiry_notes_meta_value' => "LONGTEXT NULL"
        ];
        foreach ($nCols as $colName => $colDef) {
            $chk = @mysqli_query($connection, "SHOW COLUMNS FROM `{$notesTable}` LIKE '{$colName}'");
            if ($chk && mysqli_num_rows($chk) === 0) {
                @mysqli_query($connection, "ALTER TABLE `{$notesTable}` ADD COLUMN `{$colName}` {$colDef}");
            }
        }

        // 3. Ensure default site_configuration entries for transporter features
        if ($siteId > 0) {
            $cfgTable = "{$tblPrefix}site_configuration";
            $defConfigs = [
                'lead_view_show_send_photos' => '1'
            ];
            foreach ($defConfigs as $k => $v) {
                $chk = @mysqli_query($connection, "SELECT site_configuration_id FROM `{$cfgTable}` WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = '{$k}' LIMIT 1");
                if ($chk && mysqli_num_rows($chk) === 0) {
                    @mysqli_query($connection, "INSERT INTO `{$cfgTable}` (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status) VALUES ({$siteId}, '{$k}', '{$v}', 1)");
                }
            }
        }
    }
}
