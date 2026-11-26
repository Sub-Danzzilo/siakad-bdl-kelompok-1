<?php

declare(strict_types=1);

use App\Core\Config;

function e(?string $nilai): string
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

function url(string $path = '/'): string
{
    return rtrim((string) Config::get('app.base_url', ''), '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Simpan notifikasi untuk ditampilkan di halaman berikutnya.
 * Jenis: success | danger | warning | info
 */
function flash(string $jenis, string $pesan): void
{
    $_SESSION['flash'][] = ['jenis' => $jenis, 'pesan' => $pesan];
}

/**
 * @return array<int, array{jenis: string, pesan: string}>
 */
function pull_flash(): array
{
    $pesan = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $pesan;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    return hash_equals(csrf_token(), (string) ($_POST['_csrf'] ?? ''));
}

/**
 * Format Indonesia: 13 Nov 2026
 */
function format_tanggal(?string $tanggal): string
{
    if ($tanggal === null || $tanggal === '') {
        return '–';
    }

    $ts = strtotime($tanggal);
    if ($ts === false) {
        return $tanggal;
    }

    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    return date('j', $ts) . ' ' . $bulan[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);
}

/**
 * Nilai NULL (belum diisi) ditampilkan sebagai "–".
 */
function nilai_tampil(int|float|string|null $nilai): string
{
    return $nilai === null ? '–' : number_format((float) $nilai, 2, '.', '');
}

/**
 * Skala konversi: A >= 80, B >= 70, C >= 55, D >= 40, E < 40.
 */
function nilai_huruf(int|float|string|null $nilai): string
{
    if ($nilai === null) {
        return '–';
    }

    $n = (float) $nilai;

    return match (true) {
        $n >= 80 => 'A',
        $n >= 70 => 'B',
        $n >= 55 => 'C',
        $n >= 40 => 'D',
        default => 'E',
    };
}
