<?php

/**
 * Salin file ini menjadi config/config.php lalu sesuaikan.
 * JANGAN commit config/config.php karena berisi kredensial database.
 */

return [
    'app' => [
        'name' => 'SiAkad',
        // '' jika memakai: php -S localhost:8000 -t public
        // '/siakad/public' jika lewat Laragon/XAMPP (sesuaikan nama folder)
        'base_url' => '',
        // 'demo' => TANGGAL_ACUAN = tanggal_acuan_demo | 'produksi' => tanggal hari ini
        'mode' => 'demo',
        'tanggal_acuan_demo' => '2026-11-13',
        'batas_sks' => 24,
        'batas_lulus' => 55,
        'per_page' => 10,
        'debug' => true,
    ],
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'db_akademik',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
];
