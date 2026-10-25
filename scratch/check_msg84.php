<?php
$_SERVER['DOCUMENT_ROOT'] = '/opt/homebrew/var/www/php/isw-app';
$_SERVER['HTTP_HOST'] = 'localhost';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/modules/connection.php';

$res = mysqli_query($connection, "SELECT * FROM isw_whatsapp_message WHERE message_id = 84");
$m = mysqli_fetch_assoc($res);
print_r($m);
