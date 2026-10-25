<?php
$_SERVER['DOCUMENT_ROOT'] = '/opt/homebrew/var/www/php/isw-app';
$_SERVER['HTTP_HOST'] = 'localhost';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/modules/connection.php';
require_once __DIR__ . '/../api/v3/whatsapp/phone_helper.php';

if (!isset($connection)) {
    die("Database connection failed\n");
}

echo "=== WhatsApp Data Isolation Audit ===\n\n";

// 1. Audit chat phone vs message sender/receiver
$sql = "
    SELECT 
        m.message_id,
        m.message_chat_id,
        m.message_direction,
        m.message_sender,
        m.message_receiver,
        c.chat_id,
        c.chat_phone,
        c.chat_name
    FROM isw_whatsapp_message m
    INNER JOIN isw_whatsapp_chat c ON m.message_chat_id = c.chat_id
";
$res = mysqli_query($connection, $sql);
$totalMessages = 0;
$mismatches = [];
$lidMessages = [];

while ($row = mysqli_fetch_assoc($res)) {
    $totalMessages++;
    $chatPhoneDigits = preg_replace('/\D/', '', $row['chat_phone']);
    $senderDigits = preg_replace('/\D/', '', $row['message_sender']);
    $receiverDigits = preg_replace('/\D/', '', $row['message_receiver']);
    
    $isOut = ($row['message_direction'] === 'outgoing');
    $otherPartyDigits = $isOut ? $receiverDigits : $senderDigits;
    $rawOther = $isOut ? $row['message_receiver'] : $row['message_sender'];

    if (isWhatsAppPrivacyLid($rawOther)) {
        $lidMessages[] = $row;
        continue;
    }

    // Check if phone matches canonical E.164
    $normChat = normalizeWhatsAppPhone($row['chat_phone']);
    $normOther = normalizeWhatsAppPhone($rawOther);

    if ($normChat !== $normOther) {
        $mismatches[] = [
            'message_id' => $row['message_id'],
            'chat_id' => $row['chat_id'],
            'chat_phone' => $row['chat_phone'],
            'norm_chat' => $normChat,
            'direction' => $row['message_direction'],
            'other_raw' => $rawOther,
            'norm_other' => $normOther,
        ];
    }
}

echo "Total messages checked: {$totalMessages}\n";
echo "LID (Privacy ID) messages: " . count($lidMessages) . "\n";
echo "Strict phone mismatches found: " . count($mismatches) . "\n\n";

if (!empty($mismatches)) {
    echo "Sample strict phone mismatches:\n";
    foreach (array_slice($mismatches, 0, 10) as $m) {
        echo "Msg #{$m['message_id']} in Chat #{$m['chat_id']} ({$m['chat_phone']} -> {$m['norm_chat']}) vs {$m['direction']} {$m['other_raw']} -> {$m['norm_other']}\n";
    }
} else {
    echo "✓ Zero non-LID phone mismatches in existing messages!\n";
}

// 2. Audit duplicate avatar URLs across distinct contacts
echo "\n=== Duplicate Profile Photos Across Distinct Contacts ===\n";
$avatarSql = "
    SELECT chat_avatar, COUNT(*) as cnt, GROUP_CONCAT(CONCAT(chat_id, ':', chat_phone, ' (', COALESCE(chat_name, 'No Name'), ')') SEPARATOR ' | ') as contacts
    FROM isw_whatsapp_chat
    WHERE chat_avatar IS NOT NULL AND chat_avatar != ''
    GROUP BY chat_avatar
    HAVING cnt > 1
";
$avatarRes = mysqli_query($connection, $avatarSql);
$dupCount = 0;
while ($aRow = mysqli_fetch_assoc($avatarRes)) {
    $dupCount++;
    echo "Duplicate Avatar #{$dupCount} (assigned to {$aRow['cnt']} contacts):\n";
    echo "  Avatar: " . substr($aRow['chat_avatar'], 0, 80) . "...\n";
    echo "  Contacts: {$aRow['contacts']}\n\n";
}
echo "Total duplicate avatar URLs found across multiple distinct contacts: {$dupCount}\n";
