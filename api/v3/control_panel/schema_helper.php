<?php

/**
 * Control Panel Database Schema Helper
 * Ensures required custom control panel tables and columns exist across all environments and databases.
 * Path: /api/v3/control_panel/schema_helper.php
 * Verifies table structure and auto-creates missing columns on load.
 */

if (!function_exists('ensureControlPanelTables')) {
    function ensureControlPanelTables($connection, $tblPrefix = 'isw_') {
        static $ensured = false;
        if ($ensured || !$connection) {
            return;
        }
        $ensured = true;

        try {
            // Helper function to check and add missing column
            $ensureColumn = function ($table, $column, $definition) use ($connection) {
                try {
                    $chk = @mysqli_query($connection, "SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
                    if ($chk && mysqli_num_rows($chk) === 0) {
                        @mysqli_query($connection, "ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
                    }
                } catch (\Throwable $e) {
                    // Silently ignore if lacking ALTER TABLE privileges
                }
            };

            // 1. Ensure isw_control_panel_invoice table
            $createInvoiceSql = "CREATE TABLE IF NOT EXISTS `{$tblPrefix}control_panel_invoice` (
              `invoice_id` bigint NOT NULL AUTO_INCREMENT,
              `invoice_number` varchar(100) NOT NULL,
              `invoice_site_id` bigint NOT NULL,
              `invoice_user_id` bigint NOT NULL DEFAULT '0',
              `invoice_partner_id` bigint NOT NULL DEFAULT '0',
              `invoice_plan` varchar(100) NOT NULL DEFAULT 'Standard',
              `invoice_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
              `invoice_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
              `invoice_total` decimal(10,2) NOT NULL DEFAULT '0.00',
              `invoice_currency` varchar(4) NOT NULL DEFAULT 'INR',
              `invoice_status` varchar(20) NOT NULL DEFAULT 'unpaid',
              `invoice_payment_date` datetime DEFAULT NULL,
              `invoice_due_date` datetime DEFAULT NULL,
              `invoice_notes` longtext,
              `invoice_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`invoice_id`),
              UNIQUE KEY `invoice_number` (`invoice_number`),
              KEY `idx_site` (`invoice_site_id`),
              KEY `idx_partner` (`invoice_partner_id`),
              KEY `idx_status` (`invoice_status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            @mysqli_query($connection, $createInvoiceSql);

            // 2. Ensure isw_control_panel_commission table
            $createCommissionSql = "CREATE TABLE IF NOT EXISTS `{$tblPrefix}control_panel_commission` (
              `commission_id` bigint NOT NULL AUTO_INCREMENT,
              `commission_partner_id` bigint NOT NULL,
              `commission_site_id` bigint NOT NULL,
              `commission_invoice_id` bigint DEFAULT NULL,
              `commission_rate_percent` decimal(5,2) NOT NULL DEFAULT '10.00',
              `commission_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
              `commission_status` varchar(20) NOT NULL DEFAULT 'pending',
              `commission_payout_ref` varchar(100) DEFAULT NULL,
              `commission_payout_date` datetime DEFAULT NULL,
              `commission_notes` longtext,
              `commission_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`commission_id`),
              KEY `idx_partner` (`commission_partner_id`),
              KEY `idx_site` (`commission_site_id`),
              KEY `idx_status` (`commission_status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            @mysqli_query($connection, $createCommissionSql);

            // 3. Verify and auto-create missing columns in isw_control_panel_commission
            $ensureColumn("{$tblPrefix}control_panel_commission", "commission_partner_id", "bigint NOT NULL DEFAULT 0 AFTER `commission_id`");
            $ensureColumn("{$tblPrefix}control_panel_commission", "commission_site_id", "bigint NOT NULL DEFAULT 0 AFTER `commission_partner_id`");
            $ensureColumn("{$tblPrefix}control_panel_commission", "commission_invoice_id", "bigint DEFAULT NULL AFTER `commission_site_id`");
            $ensureColumn("{$tblPrefix}control_panel_commission", "commission_rate_percent", "decimal(5,2) NOT NULL DEFAULT '10.00' AFTER `commission_invoice_id`");
            $ensureColumn("{$tblPrefix}control_panel_commission", "commission_amount", "decimal(10,2) NOT NULL DEFAULT '0.00' AFTER `commission_rate_percent`");
            $ensureColumn("{$tblPrefix}control_panel_commission", "commission_status", "varchar(20) NOT NULL DEFAULT 'pending' AFTER `commission_amount`");
            $ensureColumn("{$tblPrefix}control_panel_commission", "commission_payout_ref", "varchar(100) DEFAULT NULL AFTER `commission_status`");
            $ensureColumn("{$tblPrefix}control_panel_commission", "commission_payout_date", "datetime DEFAULT NULL AFTER `commission_payout_ref`");
            $ensureColumn("{$tblPrefix}control_panel_commission", "commission_notes", "longtext DEFAULT NULL AFTER `commission_payout_date`");
            $ensureColumn("{$tblPrefix}control_panel_commission", "commission_created_at", "timestamp NULL DEFAULT CURRENT_TIMESTAMP AFTER `commission_notes`");

            // 4. Verify and auto-create missing columns in isw_control_panel_invoice
            $ensureColumn("{$tblPrefix}control_panel_invoice", "invoice_partner_id", "bigint NOT NULL DEFAULT '0' AFTER `invoice_user_id`");
            $ensureColumn("{$tblPrefix}control_panel_invoice", "invoice_plan", "varchar(100) NOT NULL DEFAULT 'Standard' AFTER `invoice_partner_id`");
            $ensureColumn("{$tblPrefix}control_panel_invoice", "invoice_tax", "decimal(10,2) NOT NULL DEFAULT '0.00' AFTER `invoice_amount`");
            $ensureColumn("{$tblPrefix}control_panel_invoice", "invoice_currency", "varchar(4) NOT NULL DEFAULT 'INR' AFTER `invoice_total`");
            $ensureColumn("{$tblPrefix}control_panel_invoice", "invoice_payment_date", "datetime DEFAULT NULL AFTER `invoice_status`");
            $ensureColumn("{$tblPrefix}control_panel_invoice", "invoice_due_date", "datetime DEFAULT NULL AFTER `invoice_payment_date`");
            $ensureColumn("{$tblPrefix}control_panel_invoice", "invoice_notes", "longtext DEFAULT NULL AFTER `invoice_due_date`");

            // 5. Verify and auto-create missing columns in isw_site_access (crucial for partner metadata & profile storage)
            $ensureColumn("{$tblPrefix}site_access", "site_access_meta", "longtext DEFAULT NULL AFTER `site_access_status`");
            $ensureColumn("{$tblPrefix}site_access", "site_access_type", "varchar(50) NOT NULL DEFAULT 'member' AFTER `site_access_user_id`");
            $ensureColumn("{$tblPrefix}site_access", "site_access_status", "int NOT NULL DEFAULT 1 AFTER `site_access_type`");

            // 6. Verify and auto-create missing columns in isw_site
            $ensureColumn("{$tblPrefix}site", "site_dashboard_version", "int NOT NULL DEFAULT 3 AFTER `site_theme_id`");
            $ensureColumn("{$tblPrefix}site", "site_status", "int NOT NULL DEFAULT 1 AFTER `site_index`");

            // 7. Verify and auto-create missing columns in isw_site_configuration
            $ensureColumn("{$tblPrefix}site_configuration", "site_configuration_global", "int NOT NULL DEFAULT 0 AFTER `site_configuration_status`");

            // 8. Verify and auto-create missing columns in isw_app
            $ensureColumn("{$tblPrefix}app", "app_deletable", "tinyint(1) NOT NULL DEFAULT 0 AFTER `app_visiblity`");

        } catch (\Throwable $e) {
            // Silently ignore if DB user lacks CREATE or ALTER TABLE privileges
        }
    }
}
