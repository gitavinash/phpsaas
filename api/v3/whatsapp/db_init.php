<?php

/**
 * WhatsApp Database Schema Initializer
 * Path: /api/v3/whatsapp/db_init.php
 */

function initWhatsAppDatabase($connection) {
    static $initialized = false;
    if ($initialized || !$connection) {
        return true;
    }

    $prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    // 1. Conversations table
    $chatTableSql = "CREATE TABLE IF NOT EXISTS " . $prefix . "whatsapp_chat (
        chat_id BIGINT(20) NOT NULL AUTO_INCREMENT,
        chat_site_id BIGINT(20) NOT NULL,
        chat_phone VARCHAR(35) NOT NULL,
        chat_name VARCHAR(150) DEFAULT NULL,
        chat_pushname VARCHAR(150) DEFAULT NULL,
        chat_avatar VARCHAR(500) DEFAULT NULL,
        chat_last_message TEXT DEFAULT NULL,
        chat_last_message_time DATETIME DEFAULT NULL,
        chat_unread_count INT(11) DEFAULT 0,
        chat_lead_id BIGINT(20) DEFAULT NULL,
        chat_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        chat_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (chat_id),
        UNIQUE KEY uq_site_phone (chat_site_id, chat_phone),
        INDEX idx_site_updated (chat_site_id, chat_updated_at),
        INDEX idx_site_msg_time (chat_site_id, chat_last_message_time),
        INDEX idx_lead_id (chat_lead_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    try {
        @mysqli_query($connection, $chatTableSql);
    } catch (Throwable $e) {}

    // Safely add idx_site_msg_time if table was created previously without it
    try {
        $idxRes = @mysqli_query($connection, "SHOW INDEX FROM " . $prefix . "whatsapp_chat WHERE Key_name = 'idx_site_msg_time'");
        if ($idxRes && mysqli_num_rows($idxRes) === 0) {
            @mysqli_query($connection, "ALTER TABLE " . $prefix . "whatsapp_chat ADD INDEX idx_site_msg_time (chat_site_id, chat_last_message_time)");
        }
    } catch (Throwable $e) {}

    // Safely add chat_avatar column if not present
    try {
        $colRes = @mysqli_query($connection, "SHOW COLUMNS FROM " . $prefix . "whatsapp_chat LIKE 'chat_avatar'");
        if ($colRes && mysqli_num_rows($colRes) === 0) {
            @mysqli_query($connection, "ALTER TABLE " . $prefix . "whatsapp_chat ADD COLUMN chat_avatar VARCHAR(500) DEFAULT NULL AFTER chat_pushname");
        }
    } catch (Throwable $e) {}

    // Safely add chat_device_phone column if not present
    try {
        $colRes2 = @mysqli_query($connection, "SHOW COLUMNS FROM " . $prefix . "whatsapp_chat LIKE 'chat_device_phone'");
        if ($colRes2 && mysqli_num_rows($colRes2) === 0) {
            @mysqli_query($connection, "ALTER TABLE " . $prefix . "whatsapp_chat ADD COLUMN chat_device_phone VARCHAR(35) DEFAULT NULL AFTER chat_phone");
            @mysqli_query($connection, "ALTER TABLE " . $prefix . "whatsapp_chat ADD INDEX idx_device_phone (chat_site_id, chat_device_phone)");
        }
    } catch (Throwable $e) {}

    // 2. Messages table
    $msgTableSql = "CREATE TABLE IF NOT EXISTS " . $prefix . "whatsapp_message (
        message_id BIGINT(20) NOT NULL AUTO_INCREMENT,
        message_site_id BIGINT(20) NOT NULL,
        message_chat_id BIGINT(20) NOT NULL,
        message_wa_id VARCHAR(120) DEFAULT NULL,
        message_direction ENUM('incoming', 'outgoing') NOT NULL DEFAULT 'incoming',
        message_sender VARCHAR(40) NOT NULL,
        message_receiver VARCHAR(40) NOT NULL,
        message_type VARCHAR(25) NOT NULL DEFAULT 'text',
        message_text TEXT DEFAULT NULL,
        message_media_url TEXT DEFAULT NULL,
        message_status ENUM('sent', 'delivered', 'read', 'failed') NOT NULL DEFAULT 'sent',
        message_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (message_id),
        INDEX idx_site_chat (message_site_id, message_chat_id),
        INDEX idx_wa_id (message_wa_id),
        INDEX idx_created (message_created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    try {
        @mysqli_query($connection, $msgTableSql);
    } catch (Throwable $e) {}

    // 3. Multi-device table
    $devTableSql = "CREATE TABLE IF NOT EXISTS " . $prefix . "whatsapp_device (
        device_id BIGINT(20) NOT NULL AUTO_INCREMENT,
        device_site_id BIGINT(20) NOT NULL,
        device_session_id VARCHAR(100) NOT NULL,
        device_phone VARCHAR(35) DEFAULT NULL,
        device_name VARCHAR(150) DEFAULT NULL,
        device_assigned_user_id BIGINT(20) DEFAULT NULL,
        device_status ENUM('disconnected', 'qr_ready', 'connected') DEFAULT 'disconnected',
        device_last_sync DATETIME DEFAULT NULL,
        device_created_by BIGINT(20) DEFAULT NULL,
        device_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        device_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (device_id),
        UNIQUE KEY uq_site_session (device_site_id, device_session_id),
        INDEX idx_site_user (device_site_id, device_assigned_user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    try {
        @mysqli_query($connection, $devTableSql);
    } catch (Throwable $e) {}

    // Safely add device_is_default column if not present
    try {
        $colDefRes = @mysqli_query($connection, "SHOW COLUMNS FROM " . $prefix . "whatsapp_device LIKE 'device_is_default'");
        if ($colDefRes && mysqli_num_rows($colDefRes) === 0) {
            @mysqli_query($connection, "ALTER TABLE " . $prefix . "whatsapp_device ADD COLUMN device_is_default TINYINT(1) NOT NULL DEFAULT 0 AFTER device_assigned_user_id");
            @mysqli_query($connection, "ALTER TABLE " . $prefix . "whatsapp_device ADD INDEX idx_device_default (device_site_id, device_is_default)");
        }
    } catch (Throwable $e) {}

    // Safely add device_error_reason and device_disconnected_at columns if not present
    try {
        $colErr = @mysqli_query($connection, "SHOW COLUMNS FROM " . $prefix . "whatsapp_device LIKE 'device_error_reason'");
        if ($colErr && mysqli_num_rows($colErr) === 0) {
            @mysqli_query($connection, "ALTER TABLE " . $prefix . "whatsapp_device ADD COLUMN device_error_reason VARCHAR(255) DEFAULT NULL AFTER device_status");
        }
        $colDisc = @mysqli_query($connection, "SHOW COLUMNS FROM " . $prefix . "whatsapp_device LIKE 'device_disconnected_at'");
        if ($colDisc && mysqli_num_rows($colDisc) === 0) {
            @mysqli_query($connection, "ALTER TABLE " . $prefix . "whatsapp_device ADD COLUMN device_disconnected_at DATETIME DEFAULT NULL AFTER device_error_reason");
        }
    } catch (Throwable $e) {}

    $initialized = true;
    return true;
}
