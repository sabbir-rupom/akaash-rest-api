<?php

if (!defined('APP_NAME'))
    die('Forbidden');

/*
 * Register database connection with the flight server
 */
$configArray = Flight::get('app_config');

$db_host = $configArray['DB_HOST'];
$db_name = $configArray['DB_NAME'];
$db_user = $configArray['DB_USER'];
$db_pass = $configArray['DB_PASSWORD'];
$db_port = $configArray['DB_PORT'];

define('DB_TIMEZONE', $configArray['DB_TIMEZONE']);
define('DB_SET_TIMEZONE', (int) $configArray['DB_SET_TIMEZONE']);

Flight::register('pdo', 'PDO', array("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4;" . (!empty($db_port) ? "port={$db_port};" : ''), $db_user, $db_pass), function ($pdo) {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (DB_SET_TIMEZONE == 1) {
        $db_timezone = DB_TIMEZONE;
    } else {
        $db_timezone = (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('P'); // 
    }
    $pdo->exec("SET time_zone='{$db_timezone}';");
});



