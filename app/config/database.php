<?php

/**
 * Setup MySQL database with PDO
 * Install database connection with flight service
 */
if (!defined('APP_NAME')) {
    die('Forbidden 403');
}

/**
 *  Initialize database connections from configuration file.
 */
$db_host = $configArray['DB_HOST'];
$db_name = $configArray['DB_NAME'];
$db_user = $configArray['DB_USER'];
$db_pass = $configArray['DB_PASSWORD'];
$db_port = $configArray['DB_PORT'];

define('DB_TIMEZONE', $configArray['SERVER_TIMEZONE']);
define('DB_SET_TIMEZONE', (int) $configArray['DB_SET_TIMEZONE']);

/**
 * Register database connection as a function of flight
 */
Flight::register('pdo', 'PDO', [
  "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4;" . (!empty($db_port) ? "port={$db_port};" : ''),
  $db_user,
  $db_pass
    ], function ($pdo) {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (DB_SET_TIMEZONE > 0) {
            $db_timezone = (new DateTime('now', new DateTimeZone(DB_TIMEZONE)))->format('P');
        } else {
            $db_timezone = (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('P');
        }

        $pdo->exec("SET time_zone='{$db_timezone}';");
    });
