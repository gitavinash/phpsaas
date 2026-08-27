<script type='text/javascript' src='<?php echo JS_LIBRARY ?>jquery.min.js'></script>
<script type='text/javascript'>
    <?php
    echo "var deviceType='" . SITE_VERSION . "';";
    echo "var domain='" . SITE_URL . "';";
    echo "var vapidKey='" . (defined('NOTIFICATION_PUBLIC_KEY') ? NOTIFICATION_PUBLIC_KEY : 'BCrkm9qYYcj18SvNMa4906f8jm9fcDnE9ld1hHM0OT0TkiyPzEJN_SCwH3P8ioSQGYkhh3e3saO9t9QeC8TdRUA') . "';";
    ?>
</script>
<script type='text/javascript' src="https://www.gstatic.com/firebasejs/10.14.1/firebase-app-compat.js"></script>
<script type='text/javascript' src="https://www.gstatic.com/firebasejs/10.14.1/firebase-messaging-compat.js"></script>
<script type='text/javascript' src='/lib/resource/notification/config.js'></script>
<script type='text/javascript' src='/lib/resource/notification/data.min.js'></script>