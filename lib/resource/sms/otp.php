<?php

    $message=urlencode("Dear user, you verification code is {$otpCode}");

    require SEND_SMS;