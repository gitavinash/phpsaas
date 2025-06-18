<?php

if (DEVELOPMENT_MODE == true) {
    $sendMessageTo = '9990026008';
} else {
    $sendMessageTo = ((isset($mobile)) ? $mobile : '');
}