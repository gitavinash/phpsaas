<?php
/**
 * Migration: CRM Lead Status, Customer Interest, Response Tracking & Tasks
 * File: api/v3/leads/migrate-crm-schema.php
 * Safe & idempotent: applies ALTER TABLE and CREATE TABLE only if not already present.
 */

require_once __DIR__ . '/../../../dbConfig.php';

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

echo "=== Running CRM Enhancements Schema Migration ===\n";

// Helper to check if column exists
function columnExists($conn, $table, $column) {
    $res = mysqli_query($conn, "SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    return ($res && mysqli_num_rows($res) > 0);
}

// 1. Add fields to enquiry table
$enquiryTable = "{$tblPrefix}enquiry";

$fieldsToAdd = [
    'enquiry_interest_level' => "ALTER TABLE `{$enquiryTable}` ADD COLUMN `enquiry_interest_level` VARCHAR(20) NOT NULL DEFAULT 'Unknown' AFTER `enquiry_status`",
    'enquiry_last_contacted_at' => "ALTER TABLE `{$enquiryTable}` ADD COLUMN `enquiry_last_contacted_at` DATETIME NULL DEFAULT NULL AFTER `enquiry_interest_level`",
    'enquiry_last_customer_response_at' => "ALTER TABLE `{$enquiryTable}` ADD COLUMN `enquiry_last_customer_response_at` DATETIME NULL DEFAULT NULL AFTER `enquiry_last_contacted_at`",
    'enquiry_response_status' => "ALTER TABLE `{$enquiryTable}` ADD COLUMN `enquiry_response_status` VARCHAR(40) NOT NULL DEFAULT 'No Activity' AFTER `enquiry_last_customer_response_at`",
    'enquiry_expected_value' => "ALTER TABLE `{$enquiryTable}` ADD COLUMN `enquiry_expected_value` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `enquiry_response_status`",
    'enquiry_booking_id' => "ALTER TABLE `{$enquiryTable}` ADD COLUMN `enquiry_booking_id` BIGINT NULL DEFAULT NULL AFTER `enquiry_expected_value`",
];

foreach ($fieldsToAdd as $col => $sql) {
    if (!columnExists($connection, $enquiryTable, $col)) {
        if (mysqli_query($connection, $sql)) {
            echo "Added column `{$col}` to `{$enquiryTable}`.\n";
        } else {
            echo "Error adding `{$col}`: " . mysqli_error($connection) . "\n";
        }
    } else {
        echo "Column `{$col}` already exists in `{$enquiryTable}`.\n";
    }
}

// Add indexes if missing
$indexes = [
    'idx_enquiry_interest' => "ALTER TABLE `{$enquiryTable}` ADD INDEX `idx_enquiry_interest` (`enquiry_interest_level`)",
    'idx_enquiry_response_st' => "ALTER TABLE `{$enquiryTable}` ADD INDEX `idx_enquiry_response_st` (`enquiry_response_status`)",
    'idx_enquiry_last_contact' => "ALTER TABLE `{$enquiryTable}` ADD INDEX `idx_enquiry_last_contact` (`enquiry_last_contacted_at`)",
    'idx_enquiry_last_response' => "ALTER TABLE `{$enquiryTable}` ADD INDEX `idx_enquiry_last_response` (`enquiry_last_customer_response_at`)",
    'idx_enquiry_booking_id' => "ALTER TABLE `{$enquiryTable}` ADD INDEX `idx_enquiry_booking_id` (`enquiry_booking_id`)",
];

foreach ($indexes as $idxName => $idxSql) {
    $idxCheck = mysqli_query($connection, "SHOW INDEX FROM `{$enquiryTable}` WHERE Key_name = '{$idxName}'");
    if (!$idxCheck || mysqli_num_rows($idxCheck) === 0) {
        if (mysqli_query($connection, $idxSql)) {
            echo "Added index `{$idxName}` to `{$enquiryTable}`.\n";
        } else {
            echo "Note on `{$idxName}`: " . mysqli_error($connection) . "\n";
        }
    } else {
        echo "Index `{$idxName}` already exists.\n";
    }
}

// 2. Create isw_enquiry_task table
$taskTable = "{$tblPrefix}enquiry_task";
$createTaskTableSql = "CREATE TABLE IF NOT EXISTS `{$taskTable}` (
    `task_id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `task_site_id` BIGINT NOT NULL DEFAULT 0,
    `task_enquiry_id` BIGINT NOT NULL,
    `task_agent_id` BIGINT NOT NULL DEFAULT 0,
    `task_type` ENUM('Call', 'WhatsApp', 'Email', 'Meeting', 'Other') NOT NULL DEFAULT 'Call',
    `task_due_at` DATETIME NOT NULL,
    `task_notes` TEXT NULL,
    `task_next_action` VARCHAR(255) NULL,
    `task_status` ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `task_outcome` VARCHAR(255) NULL,
    `task_completed_at` DATETIME NULL,
    `task_completed_by` BIGINT NULL,
    `task_created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `task_updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_task_enquiry` (`task_enquiry_id`),
    INDEX `idx_task_site` (`task_site_id`),
    INDEX `idx_task_agent` (`task_agent_id`),
    INDEX `idx_task_due` (`task_due_at`),
    INDEX `idx_task_status` (`task_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($connection, $createTaskTableSql)) {
    echo "Table `{$taskTable}` checked / created successfully.\n";
} else {
    echo "Error creating table `{$taskTable}`: " . mysqli_error($connection) . "\n";
}

// 3. Populate existing booking linkages if enquiry_booking_id is empty
$updateBookingLinkSql = "UPDATE `{$enquiryTable}` e
INNER JOIN (
    SELECT booking_enquiry_id, MAX(booking_id) as latest_booking_id
    FROM `{$tblPrefix}booking`
    WHERE booking_enquiry_id > 0
    GROUP BY booking_enquiry_id
) b ON e.enquiry_id = b.booking_enquiry_id
SET e.enquiry_booking_id = b.latest_booking_id
WHERE e.enquiry_booking_id IS NULL OR e.enquiry_booking_id = 0";
$updRes = mysqli_query($connection, $updateBookingLinkSql);
$affectedBookings = mysqli_affected_rows($connection);
echo "Linked {$affectedBookings} bookings to leads in `enquiry_booking_id`.\n";

// 4. Initial seed for last_contacted_at from notes where meta_type is call/followup
$updateContactedSql = "UPDATE `{$enquiryTable}` e
INNER JOIN (
    SELECT enquiry_notes_ref_id, MAX(enquiry_notes_update) as last_note_time
    FROM `{$tblPrefix}enquiry_notes`
    WHERE enquiry_notes_meta_type IN ('followup', 'call', 'itinerary', 'note', 'status')
    GROUP BY enquiry_notes_ref_id
) n ON e.enquiry_id = n.enquiry_notes_ref_id
SET e.enquiry_last_contacted_at = n.last_note_time
WHERE e.enquiry_last_contacted_at IS NULL";
mysqli_query($connection, $updateContactedSql);
echo "Seeded initial last_contacted_at timestamps.\n";

echo "=== Schema Migration Completed Successfully ===\n";
