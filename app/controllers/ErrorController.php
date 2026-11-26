<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use Throwable;

final class ErrorController extends Controller
{
    /**
     * Peta halaman yang belum dibuat: path => [judul, pemilik, soal].
     * Dipakai halaman 404 agar pengguna tahu fitur itu sedang dikerjakan siapa.
     *
     * @var array<string, array{judul: string, pemilik: string, soal: string}>
     */
    private const HALAMAN_BELUM_SIAP = [
        '/mahasiswa' => [
            'judul' => 'Master Data Mahasiswa',
            'pemilik' => 'Anggota 2',
            'soal' => 'CRUD mahasiswa',
        ],
        '/dosen' => [
            'judul' => 'Master Data Dosen',
            'pemilik' => 'Anggota 2',
            'soal' => 'Soal 2b & 2d',
        ],
        '/matakuliah' => [
            'judul' => 'Master Data Mata Kuliah',
            'pemilik' => 'Anggota 2',
            'soal' => 'Soal 2b',
        ],
        '/krs' => [
            'judul' => 'Kartu Rencana Studi',
            'pemilik' => 'Anggota 3',
            'soal' => 'Soal 2e',
        ],
        '/statistik' => [
            'judul' => 'Statistik Mata Kuliah',
            'pemilik' => 'Anggota 4',
            'soal' => 'Soal 2c, 2f, 2g, 2h, 2j',
        ],
        '/akademik' => [
            'judul' => 'Profil & Transkrip Akademik',
            'pemilik' => 'Anggota 3',
            'soal' => 'Soal 2e & 2i',
        ],
    ];

    public function notFound(): void
    {
        http_response_code(404);

        $this->render('errors/404', [
            'halaman' => $this->cariHalamanBelumSiap(),
        ], 'Halaman tidak ditemukan');
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

    /**
     * Cocokkan URI yang diminta dengan daftar halaman yang belum dibuat.
     *
     * @return array{judul: string, pemilik: string, soal: string}|null
     */
    private function cariHalamanBelumSiap(): ?array
    {
        $path = '/' . trim((string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH), '/');
        $base = rtrim((string) Config::get('app.base_url', ''), '/');
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = '/' . trim(substr($path, strlen($base)), '/');
        }

        foreach (self::HALAMAN_BELUM_SIAP as $awalan => $info) {
            if ($path === $awalan || str_starts_with($path, $awalan . '/')) {
                return $info;
            }
        }

        return null;
    }
}
