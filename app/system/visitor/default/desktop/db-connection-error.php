<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo SITE_URL ?>">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Error</title>
    <link rel="stylesheet" href="/app/system/visitor/default/desktop/assets/css/style.min.css">
    <link href="https://fonts.googleapis.com/css2?family=KoHo:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <img src="/app/system/visitor/default/desktop/assets/images/database-maintenance.png" alt="">
    <h1>Database Error <span>Error while establishing a database connection</span></h1>
</body>

</html>
<?php

// Execute mysql server restart commands

$restartMySQL = exec("sudo systemctl restart mysql");

print_r($restartMySQL);

// $restartApache = shell_exec('systemctl restart apache2');


?>