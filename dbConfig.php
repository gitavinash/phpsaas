<?php

define('DEVELOPMENT_MODE', true); // Error reporting true to show error or false to hide error

/* database connection setting */

if (DEVELOPMENT_MODE == true) {

    define('DB_HOST', 'localhost');        // database hostname ex :- hostname
    define('DB_USER', 'root');        // database username ex :- root
    define('DB_PASS', '9289786810');        // datapase user password :- 12345
    define('DB_NAME', 'isw_app');        // database name ex :- cms_db
    define('MONGO_DB_CONNECTION_URL', 'mongodb://localhost:27017/');        // MongoDB Connection URL

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {

    define('DB_HOST', 'localhost');        // database hostname ex :- hostname
    define('DB_USER', 'root');        // database username ex :- root
    define('DB_PASS', 'A=2ADu5P7WqCPm!Unc6*a?N^8Ka2?Htp');        // datapase user password :- 12345
    define('DB_NAME', 'isw_saas');        // database name ex :- cms_db
    define('MONGO_DB_CONNECTION_URL', 'mongodb+srv://iswone:!KL0KdVY65K8@cluster0.vldbb2n.mongodb.net/test');        // MongoDB Connection URL
    error_reporting(0);
    ini_set('display_errors', 0);
}

/* database table prefix */

define('TABLE_PREFIX', 'isw_');