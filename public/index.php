<?php

declare(strict_types=1);

use App\Controllers\ErrorController;
use App\Core\Router;

require dirname(__DIR__) . '/app/bootstrap.php';

try {
    $router = new Router();

    // Urutan penting: route yang lebih spesifik harus didaftarkan lebih dulu.
    foreach (['dashboard', 'master', 'krs', 'statistik', 'akademik'] as $modul) {
        (require APP_PATH . '/routes/' . $modul . '.php')($router);
    }

    $router->dispatch(
        $_SERVER['REQUEST_METHOD'],
        (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
    );
} catch (Throwable $galat) {
    // Termasuk database mati / query gagal: tampilkan halaman galat, bukan layar putih.
    (new ErrorController())->serverError($galat);
}
