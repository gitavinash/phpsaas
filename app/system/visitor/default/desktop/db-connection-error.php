<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo SITE_URL ?>">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Database Error</title>
    <link rel="stylesheet" href="/app/system/visitor/default/desktop/assets/css/style.min.css">
    <link href="https://fonts.googleapis.com/css2?family=KoHo:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <img src="/app/system/visitor/default/desktop/assets/images/database-maintenance.png" alt="">
    <h1>Database Error <span>Error while establishing a database connection | <strong>Please wait!</strong> this page
            will reload
            automatic.</span></h1>
</body>

</html>
<?php

// Execute mysql server restart commands

$sshConnection = ssh2_connect('68.183.246.213', 22);
ssh2_auth_password($sshConnection, 'root', 'z3YurCg^HUW@e_TnBL$dt3MLth#CGMdu');

$restartMySQL = ssh2_exec($sshConnection, "systemctl restart mysql");
$restartApache = shell_exec('systemctl restart apache2');

// Disconnect connection

ssh2_disconnect();

// Reload page

header("Refresh:2");


?>