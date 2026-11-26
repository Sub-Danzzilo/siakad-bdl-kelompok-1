<?php

declare(strict_types=1);

use App\Core\Config;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', __DIR__);

// Autoloader sederhana: App\Core\Router => app/core/Router.php
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $parts = explode('\\', substr($class, strlen($prefix)));
    $file = array_pop($parts);
    $dir = strtolower(implode('/', $parts));
    $path = APP_PATH . '/' . ($dir !== '' ? $dir . '/' : '') . $file . '.php';

    if (is_file($path)) {
        require $path;
    }
});

require_once APP_PATH . '/core/helpers.php';

$configFile = BASE_PATH . '/config/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('config/config.php belum ada. Salin dari config/config.example.php lalu sesuaikan.');
}

Config::load(require $configFile);

date_default_timezone_set('Asia/Jakarta');
ini_set('display_errors', Config::get('app.debug', false) ? '1' : '0');
error_reporting(E_ALL);

// Tanggal acuan: demo = tetap (agar hasil sama dengan dokumen latihan), produksi = hari ini.
define(
    'TANGGAL_ACUAN',
    Config::get('app.mode') === 'demo'
        ? (string) Config::get('app.tanggal_acuan_demo', '2026-11-13')
        : date('Y-m-d')
);

session_start();
