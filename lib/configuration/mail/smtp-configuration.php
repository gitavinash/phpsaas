<?php

/* Email Server setup configuration */

if (EMAIL_SMTP_DEFAULT == 0) {

    define('SMTP_HOST', EMAIL_SMTP_HOST);
    define('SMTP_SSL', EMAIL_SMTP_SSL); // is email server is using ssl if yes then change to true
    define('SMTP_PORT', EMAIL_SMTP_PORT); // PORT id of their email server like :- 587, 465
    define('SMTP_USERNAME', EMAIL_SMTP_USERNAME); // email user like :- contact@isw.one
    define('SMTP_PASSWORD', EMAIL_SMTP_PASSWORD); // password of the email
    define('SMTP_EMAIL', EMAIL_SMTP_EMAIL); // Which email is display to receiver end like :- donotreply@isw.one
    define('SMTP_NAME', EMAIL_SMTP_NAME); // Show Name on email :- Blumox Technologies, Avinash Kumar
    define('REPLY_EMAIL', EMAIL_SMTP_EMAIL); // Reply email where user can send a reply mail like :- contact@isw.one
    define('REPLY_EMAIL_NAME', EMAIL_SMTP_REPLY_NAME); // Reply email where user can send a reply mail like :- contact@isw.one

} else {

    define('SMTP_HOST', 'smtp-relay.brevo.com'); // host address of email server like :- mail.isw.one
    define('SMTP_SSL', 'true'); // is email server is using ssl if yes then change to true
    define('SMTP_PORT', '587'); // PORT id of their email server like :- 587, 465
    define('SMTP_USERNAME', 'blumoxtechnologies@gmail.com'); // email user like :- contact@isw.one
    define('SMTP_PASSWORD', 'bgY1MNcrpC6fD9d8'); // password of the email
    define('SMTP_EMAIL', 'donotreply@aleya.in'); // Which email is display to receiver end like :- donotreply@isw.one
    define('SMTP_NAME', SITE_NAME); // Show Name on email :- Blumox Technologies, Avinash Kumar
    define('REPLY_EMAIL', 'donotreply@aleya.in'); // Reply email where user can send a reply mail like :- contact@isw.one
    define('REPLY_EMAIL_NAME', 'Do Not Reply'); // Reply email where user can send a reply mail like :- contact@isw.one

}