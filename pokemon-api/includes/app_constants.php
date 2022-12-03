<?php

// Constants for MySQL database configuration/credentials.
//TODO: change the following values if you have different settings/options.
define('DB_HOST', 'localhost');
define('DB_NAME', 'pokemon_database');
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
define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_NO_CONTENT', 204);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_METHOD_NOT_ALLOWED', 405);
define('HTTP_UNSUPPORTED_MEDIA_TYPE',415);
define('HTTP_NOT_FOUND',404);

// Supported Media Types.
define('APP_MEDIA_TYPE_JSON', "application/json");
define('APP_MEDIA_TYPE_XML', "application/xml");
define('APP_MEDIA_TYPE_YAML', "application/yaml");

// arrays for validation: types, natures, move categories, gender
define('POKEMON_TYPES', array('Normal','Fire','Fighting','Water','Flying','Grass','Poison','Electric','Ground','Psychic','Rock','Ice','Bug','Dragon','Ghost','Dark','Steel','Fairy'));
define('POKEDEX_NATURES', array('Hardy','Lonely','Brave','Adamant','Naughty','Bold','Docile','Relaxed','Impish','Lax','Timid','Hasty','Serious','Jolly','Naive','Modest','Mild','Quiet','Bashful','Rash','Calm','Gentle','Sassy','Careful','Quirky'));
define('MOVE_CATEGORIES', array("Physical", "Special", "Status"));
define('POKEMON_GENDERS', array("M", "F", "O"));
define('TRAINER_GENDERS', array("M", "F", "O"));