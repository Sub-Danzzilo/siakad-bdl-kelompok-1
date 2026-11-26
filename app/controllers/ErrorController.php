<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use Throwable;

final class ErrorController extends Controller
{
    public function notFound(): void
    {
        http_response_code(404);
        $this->render('errors/404', [], 'Halaman tidak ditemukan');
    }

    /**
     * Halaman galat ramah: dipakai saat terjadi exception (mis. database mati).
     * Pesan teknis hanya ditampilkan bila app.debug = true.
     */
    public function serverError(Throwable $galat): void
    {
        // Buang output yang mungkin sudah setengah jadi agar halaman galat bersih.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            http_response_code(500);
        }

        error_log('[SiAkad] ' . $galat::class . ': ' . $galat->getMessage());

        $this->render('errors/500', [
            'detail' => Config::get('app.debug', false) ? $galat->getMessage() : null,
        ], 'Terjadi kesalahan');
    }
}
