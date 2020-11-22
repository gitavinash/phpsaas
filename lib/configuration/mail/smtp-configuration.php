<?php

	/* Email Server setup configuration */

    if(isset($email_active)) {

        define('SMTP_HOST', $email_smtp_host);
        define('SMTP_SSL', $email_ssl); // is email server is using ssl if yes then change to true
        define('SMTP_PORT', $email_smtp_port); // PORT id of their email server like :- 587, 465
        define('SMTP_USERNAME', $email_smtp_username); // email user like :- contact@isw.one
        define('SMTP_PASSWORD', $email_smtp_password); // password of the email
        define('SMTP_EMAIL', 'donotreply@'.SITE_DOMAIN); // Which email is display to receiver end like :- donotreply@isw.one
        define('SMTP_NAME', SITE_NAME); // Show Name on email :- ITS Soft World, Avinash Kumar
        define('REPLY_EMAIL', SITE_NAME); // Reply email where user can send a reply mail like :- contact@isw.one

    } else {

        define('SMTP_HOST', 'smtp-relay.sendinblue.com'); // host address of email server like :- mail.isw.one
        define('SMTP_SSL', 'false'); // is email server is using ssl if yes then change to true
        define('SMTP_PORT', '587'); // PORT id of their email server like :- 587, 465
        define('SMTP_USERNAME', 'avinash@itssoftworld.com'); // email user like :- contact@isw.one
        define('SMTP_PASSWORD', 'yh5w3pUL7faAqjvE'); // password of the email
        define('SMTP_EMAIL', 'donotreply@isw.one'); // Which email is display to receiver end like :- donotreply@isw.one
        define('SMTP_NAME', SITE_NAME); // Show Name on email :- ITS Soft World, Avinash Kumar
        define('REPLY_EMAIL', SITE_NAME); // Reply email where user can send a reply mail like :- contact@isw.one

    }