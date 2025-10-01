<?php

if (DEVELOPMENT_MODE == true) {
    $sendMessageTo = '9990026008';
} else {
    if (defined('ADMIN_WHASTAPP_NUMBER')) {
        $sendMessageTo = ADMIN_WHASTAPP_NUMBER;
    }
}
