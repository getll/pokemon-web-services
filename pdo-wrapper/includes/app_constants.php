<?php

// Constants for MySQL database configuration/credentials.
//TODO: change the following values if you have different settings/options.
define('DB_HOST', 'localhost');
define('DB_NAME', 'chinook_db');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_CHAR', 'utf8mb4');

// MySQL PDO options. This is a global array that is used in our models.
$db_options = [
    //required
    'username' => DB_USERNAME,
    'database' => DB_NAME,
    //optional
    'password' => DB_PASSWORD,
    'type' => 'mysql',
    'charset' => 'utf8mb4',
    'host' => DB_HOST,
    'port' => '3309'
];


// HTTP response status code. 
