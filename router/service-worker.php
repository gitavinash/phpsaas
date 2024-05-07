<?php
header('Content-Type: application/javascript');

echo "const siteName = '" . strtolower(str_replace(' ', '_', SITE_NAME)) . "';";

require_once DASHBOARD_SERVICE_WORKER;
// if ($urlStructure[0] == 'dashboard') {
// } else {
//     require_once VISITOR_SERVICE_WORKER;
// }
exit();