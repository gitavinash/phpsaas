<?php

/**
 * Control Panel Database Schema Helper
 * Ensures required custom control panel tables exist across all environments and databases.
 * Path: /api/v3/control_panel/schema_helper.php
 */

if (!function_exists('ensureControlPanelTables')) {
    function ensureControlPanelTables($connection, $tblPrefix = 'isw_') {
        static $ensured = false;
        if ($ensured || !$connection) {
            return;
        }
        $ensured = true;

        try {
            // 1. Ensure isw_control_panel_invoice
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

            // 2. Ensure isw_control_panel_commission
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
        } catch (\Throwable $e) {
            // Silently ignore if DB user lacks CREATE TABLE privileges
        }
    }
}
