<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Query Statistik Mata Kuliah (Soal 2c, 2f, 2g, 2h, 2j - Anggota 4).
 * Tanpa subquery; semua input filter lewat prepared statement.
 */
final class StatistikMk
{
    /**
     * Daftar semester yang ada di krs, terbaru lebih dulu.
     *
     * @return array<int, string>
     */
    public function daftarSemester(): array
    {
        $baris = Database::fetchAll(
            'SELECT semester FROM krs GROUP BY semester ORDER BY semester DESC'
        );

        return array_map(static fn (array $r): string => (string) $r['semester'], $baris);
    }

    /**
     * @return array<int, array{kode_mk: string, nama_mk: string}>
     */
    public function daftarMatakuliah(): array
    {
        return Database::fetchAll('SELECT kode_mk, nama_mk FROM matakuliah ORDER BY kode_mk');
    }

    /**
     * @return array<int, int>
     */
    public function daftarAngkatan(): array
    {
        $baris = Database::fetchAll('SELECT angkatan FROM mahasiswa GROUP BY angkatan ORDER BY angkatan');

        return array_map(static fn (array $r): int => (int) $r['angkatan'], $baris);
    }

    /**
     * Soal 2c: rata-rata nilai per mata kuliah, seluruh semester.
     * AVG mengabaikan NULL; MK tanpa nilai tetap tampil dengan rata_rata NULL.
     *
     * @return array<int, array<string, mixed>>
     */
    public function rataRataNilai(): array
    {
        return Database::fetchAll(
            'SELECT m.kode_mk, m.nama_mk,
                    COUNT(k.nilai_angka) AS jumlah_nilai,
                    ROUND(AVG(k.nilai_angka), 2) AS rata_rata
             FROM matakuliah m
             LEFT JOIN krs k ON k.kode_mk = m.kode_mk
             GROUP BY m.kode_mk, m.nama_mk
             ORDER BY m.kode_mk'
        );
    }

    /**
     * Soal 2f: peserta mata kuliah pada semester terpilih, filter MK dan angkatan opsional.
     *
     * @return array<int, array<string, mixed>>
     */
    public function pesertaMatakuliah(string $semester, ?string $kodeMk, ?int $angkatan): array
    {
        return Database::fetchAll(
            'SELECT m.kode_mk, m.nama_mk, s.nim, s.nama, s.angkatan, k.nilai_angka
             FROM krs k
             INNER JOIN matakuliah m ON m.kode_mk = k.kode_mk
             INNER JOIN mahasiswa s ON s.nim = k.nim
             WHERE k.semester = :semester
               AND (:kode_a IS NULL OR m.kode_mk = :kode_b)
               AND (:angkatan_a IS NULL OR s.angkatan = :angkatan_b)
             ORDER BY m.kode_mk, s.angkatan, s.nim',
            [
                'semester' => $semester,
                'kode_a' => $kodeMk,
                'kode_b' => $kodeMk,
                'angkatan_a' => $angkatan,
                'angkatan_b' => $angkatan,
            ]
        );
    }

    /**
     * Soal 2g: jumlah peserta, nilai tertinggi dan terendah per MK pada satu semester.
     * Syarat semester di klausa ON agar MK tanpa peserta tetap muncul (peserta 0).
     *
     * @return array<int, array<string, mixed>>
     */
    public function nilaiPerMatakuliah(string $semester): array
    {
        return Database::fetchAll(
            'SELECT m.kode_mk, m.nama_mk,
                    COUNT(k.nim) AS jumlah_peserta,
                    MAX(k.nilai_angka) AS nilai_tertinggi,
                    MIN(k.nilai_angka) AS nilai_terendah
             FROM matakuliah m
             LEFT JOIN krs k ON k.kode_mk = m.kode_mk AND k.semester = :semester
             GROUP BY m.kode_mk, m.nama_mk
             ORDER BY m.kode_mk',
            ['semester' => $semester]
        );
    }

    /**
     * Soal 2h: MK yang belum punya KRS pada semester terpilih (LEFT JOIN ... IS NULL).
     *
     * @return array<int, array<string, mixed>>
     */
    public function matakuliahTanpaPeserta(string $semester): array
    {
        return Database::fetchAll(
            'SELECT m.kode_mk, m.nama_mk, m.sks, d.nama_dosen
             FROM matakuliah m
             INNER JOIN dosen d ON d.nidn = m.nidn
             LEFT JOIN krs k ON k.kode_mk = m.kode_mk AND k.semester = :semester
             WHERE k.nim IS NULL
             ORDER BY m.kode_mk',
            ['semester' => $semester]
        );
    }

    /**
     * Soal 2j: jumlah mahasiswa tidak lulus (nilai < batas) per MK pada satu semester.
     * Nilai tepat di batas tidak dihitung; NULL tidak memenuhi pembanding sehingga tidak dihitung.
     *
     * @return array<int, array<string, mixed>>
     */
    public function tidakLulusPerMatakuliah(string $semester, float $batas): array
    {
        return Database::fetchAll(
            'SELECT m.kode_mk, m.nama_mk,
                    COUNT(k.nim) AS jumlah_peserta,
                    COUNT(CASE WHEN k.nilai_angka < :batas THEN 1 END) AS jumlah_tidak_lulus
             FROM matakuliah m
             LEFT JOIN krs k ON k.kode_mk = m.kode_mk AND k.semester = :semester
             GROUP BY m.kode_mk, m.nama_mk
             ORDER BY m.kode_mk',
            ['batas' => $batas, 'semester' => $semester]
        );
    }
}
