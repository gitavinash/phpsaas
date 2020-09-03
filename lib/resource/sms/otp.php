<?php

    $message=urlencode("Dear user, you verification code is {$otpCode}");

    require_once SEND_SMS;