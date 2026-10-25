<?php
$_SERVER['DOCUMENT_ROOT'] = '/opt/homebrew/var/www/php/isw-app';
$_SERVER['HTTP_HOST'] = 'localhost';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/modules/connection.php';
require_once __DIR__ . '/../api/v3/whatsapp/phone_helper.php';

$res = mysqli_query($connection, "SELECT chat_id, chat_phone, chat_name FROM isw_whatsapp_chat WHERE chat_phone LIKE '%8178539254%'");
while ($row = mysqli_fetch_assoc($res)) {
    echo "Chat ID: {$row['chat_id']} | Phone: {$row['chat_phone']} | Name: {$row['chat_name']}\n";
}
