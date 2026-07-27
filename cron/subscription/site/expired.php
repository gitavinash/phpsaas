<?php

require_once __DIR__ . '/../../../lib/functions/log.php';
$dbConnectionFile = __DIR__ . '/../../../dbConfig.php';


if (!file_exists($dbConnectionFile)) {
    writeSchedulerLog('error', 'Aborted: configuration file not found', ['path' => $dbConnectionFile]);
    exit("Configuration file not found\n {$dbConnectionFile}");
} else {
    require_once $dbConnectionFile;
    if ($connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
        writeSchedulerLog('info', 'Database connection established');
    } else {
        writeSchedulerLog('error', 'Aborted: database connection failed', [
            'mysql_error' => mysqli_connect_error(),
        ]);
        exit("Database connection failed\n");
    }
}

if ($checkExpiredSites = mysqli_query($connection, "SELECT site_id, site_domain,  profile_first_name, profile_display_name, profile_email, profile_mobile FROM " . TABLE_PREFIX . "site INNER JOIN " . TABLE_PREFIX . "site_installation ON site_installation_site_id=site_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=site_owner_id WHERE site_status=1 AND site_domain!='' AND site_installation_expiry_date < NOW()")) {

    if (mysqli_num_rows($checkExpiredSites) > 0) {
        while ($expiredSite = mysqli_fetch_assoc($checkExpiredSites)) {
            extract($expiredSite);
        }
    }
}
