<?php
$_SERVER['DOCUMENT_ROOT'] = '/opt/homebrew/var/www/php/isw-app';
$_SERVER['HTTP_HOST'] = 'localhost';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/modules/connection.php';
require_once __DIR__ . '/../api/v3/whatsapp/phone_helper.php';

echo "=== WhatsApp Database Remediation & Backup ===\n\n";

// 1. Create backup table for messages if not exists
mysqli_query($connection, "
    CREATE TABLE IF NOT EXISTS isw_whatsapp_message_isolation_backup (
        backup_id INT AUTO_INCREMENT PRIMARY KEY,
        backup_reason VARCHAR(255),
        original_message_id INT,
        original_chat_id INT,
        original_sender VARCHAR(64),
        original_receiver VARCHAR(64),
        backed_up_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

// 2. Backup and reassign +918178539254 messages from Chat 620 to Chat 3487
$mIds = [3994, 5517, 5531, 5728];
foreach ($mIds as $mid) {
    $q = mysqli_query($connection, "SELECT * FROM isw_whatsapp_message WHERE message_id = {$mid}");
    if ($row = mysqli_fetch_assoc($q)) {
        mysqli_query($connection, "
            INSERT INTO isw_whatsapp_message_isolation_backup 
            (backup_reason, original_message_id, original_chat_id, original_sender, original_receiver)
            VALUES ('Reassigned +918178539254 message from +18178539254 Chat 620 to Chat 3487', {$row['message_id']}, {$row['message_chat_id']}, '{$row['message_sender']}', '{$row['message_receiver']}')
        ");
        mysqli_query($connection, "UPDATE isw_whatsapp_message SET message_chat_id = 3487 WHERE message_id = {$mid}");
        echo "✓ Message #{$mid} reassigned to Chat #3487 (+918178539254)\n";
    }
}

// 3. Backup message 84 (Privacy LID message in Chat 3)
$q84 = mysqli_query($connection, "SELECT * FROM isw_whatsapp_message WHERE message_id = 84");
if ($r84 = mysqli_fetch_assoc($q84)) {
    mysqli_query($connection, "
        INSERT INTO isw_whatsapp_message_isolation_backup 
        (backup_reason, original_message_id, original_chat_id, original_sender, original_receiver)
        VALUES ('LID message attached to Chat 3 (+919136383880)', {$r84['message_id']}, {$r84['message_chat_id']}, '{$r84['message_sender']}', '{$r84['message_receiver']}')
    ");
    echo "✓ Message #84 backed up in isolation backup table.\n";
}

// 4. Create backup table for duplicate avatars
mysqli_query($connection, "
    CREATE TABLE IF NOT EXISTS isw_whatsapp_chat_avatar_backup (
        backup_id INT AUTO_INCREMENT PRIMARY KEY,
        chat_id INT,
        chat_phone VARCHAR(64),
        chat_name VARCHAR(255),
        chat_avatar TEXT,
        backed_up_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

// 5. Find duplicate avatars across distinct contacts and clean cross-contact contamination
$avatarSql = "
    SELECT chat_avatar, COUNT(*) as cnt
    FROM isw_whatsapp_chat
    WHERE chat_avatar IS NOT NULL AND chat_avatar != ''
    GROUP BY chat_avatar
    HAVING cnt > 1
";
$avatarRes = mysqli_query($connection, $avatarSql);
$cleanedChats = 0;

while ($aRow = mysqli_fetch_assoc($avatarRes)) {
    $avatar = mysqli_real_escape_string($connection, $aRow['chat_avatar']);
    // Backup all chats sharing this avatar
    $chatsQ = mysqli_query($connection, "SELECT chat_id, chat_phone, chat_name, chat_avatar FROM isw_whatsapp_chat WHERE chat_avatar = '{$avatar}'");
    $first = true;
    while ($c = mysqli_fetch_assoc($chatsQ)) {
        mysqli_query($connection, "
            INSERT INTO isw_whatsapp_chat_avatar_backup (chat_id, chat_phone, chat_name, chat_avatar)
            VALUES ({$c['chat_id']}, '{$c['chat_phone']}', '" . mysqli_real_escape_string($connection, $c['chat_name']) . "', '{$avatar}')
        ");
        // Keep avatar on the first (oldest) contact, clear from duplicated contacts to prevent leakage
        if ($first) {
            $first = false;
        } else {
            mysqli_query($connection, "UPDATE isw_whatsapp_chat SET chat_avatar = NULL WHERE chat_id = {$c['chat_id']}");
            $cleanedChats++;
            echo "✓ Cleared leaked duplicate avatar from Chat #{$c['chat_id']} ({$c['chat_phone']} - {$c['chat_name']})\n";
        }
    }
}

echo "\nRemediation complete. Cleaned {$cleanedChats} leaked duplicate avatars.\n";
