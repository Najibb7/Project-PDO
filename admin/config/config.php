<?php 

# APP TAG
define('APP_TAG', 'valid_nains');

# DATABASE
define('DB_ENGINE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'gurdil');
define('DB_CHARSET', 'utf8mb4');
define('DB_USER', 'root');
$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (strpos($user_agent, 'Win') !== false) {
    define('DB_PWD', '');
} elseif (strpos($user_agent, 'Mac') !== false) {
    define('DB_PWD', 'root');
}

define('DSN', DB_ENGINE .':host='. DB_HOST .';dbname='. DB_NAME .';charset='. DB_CHARSET );