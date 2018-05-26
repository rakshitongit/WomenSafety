<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//Localhost Array
$whitelist = array(
    '127.0.0.1',
    '::1'
);

// for smtp mail server
define('SERVER', 'localhost');                     // Server Name
define('HOSTNAME','smtp.gmail.com');      // Host name comes here
define('USERNAMESMTP','');  // SMTP Username
define('PASSWORDSMTP', ''); // SMTP Password
define('FCMAPIKEY','');  // Google Api Key FCM

if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
    //WHEN RUNNING ON LOCALHOST
    define('DBHOST', 'localhost');
    define('DBUSERNAME', 'root');
    define('DBPASSWORD', 'root');
    define('DBNAME', 'hackethon');
} else {
    //WHEN RUNNING ON SERVER
    define('DBHOST', 'localhost');
    define('DBUSERNAME', 'root');
    define('DBPASSWORD', 'password');
    define('DBNAME', 'databasename');
}

