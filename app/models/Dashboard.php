<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Query untuk halaman dasbor (Soal 2a dan kartu ringkasan).
 * Semua query memakai COUNT + GROUP BY, tanpa subquery.
 */
final class Dashboard
{
    /**
     * Kartu ringkasan: jumlah mahasiswa, dosen, mata kuliah, dan KRS.
     *
     * @return array{mahasiswa: int, dosen: int, matakuliah: int, krs: int}
     */
    public function ringkasan(): array
    {
        $mahasiswa = Database::fetchOne('SELECT COUNT(*) AS jumlah FROM mahasiswa');
        $dosen = Database::fetchOne('SELECT COUNT(*) AS jumlah FROM dosen');
        $matakuliah = Database::fetchOne('SELECT COUNT(*) AS jumlah FROM matakuliah');
        $krs = Database::fetchOne('SELECT COUNT(*) AS jumlah FROM krs');

        return [
            'mahasiswa' => (int) ($mahasiswa['jumlah'] ?? 0),
            'dosen' => (int) ($dosen['jumlah'] ?? 0),
            'matakuliah' => (int) ($matakuliah['jumlah'] ?? 0),
            'krs' => (int) ($krs['jumlah'] ?? 0),
        ];
    }

    /**
     * Semester terbaru yang ada di tabel krs dianggap sebagai semester aktif.
     */
    public function semesterAktif(): ?string
    {
        $baris = Database::fetchOne(
            'SELECT semester, COUNT(*) AS jumlah_krs
             FROM krs
             GROUP BY semester
             ORDER BY semester DESC
             LIMIT 1'
        );

        return $baris === null ? null : (string) $baris['semester'];
    }

    /**
     * Ringkasan singkat untuk satu semester.
     *
     * @return array{jumlah_krs: int, jumlah_mahasiswa: int, jumlah_mk: int, total_sks: int}
     */
    public function ringkasanSemester(string $semester): array
    {
        $krs = Database::fetchOne(
            'SELECT COUNT(*) AS jumlah_krs,
                    COUNT(DISTINCT nim) AS jumlah_mahasiswa,
                    COUNT(DISTINCT kode_mk) AS jumlah_mk
             FROM krs
             WHERE semester = :semester',
            ['semester' => $semester]
        );

        $sks = Database::fetchOne(
            'SELECT COALESCE(SUM(mk.sks), 0) AS total_sks
             FROM krs k
             INNER JOIN matakuliah mk ON mk.kode_mk = k.kode_mk
             WHERE k.semester = :semester',
            ['semester' => $semester]
        );

        return [
            'jumlah_krs' => (int) ($krs['jumlah_krs'] ?? 0),
            'jumlah_mahasiswa' => (int) ($krs['jumlah_mahasiswa'] ?? 0),
            'jumlah_mk' => (int) ($krs['jumlah_mk'] ?? 0),
            'total_sks' => (int) ($sks['total_sks'] ?? 0),
        ];
    }

    /**
     * Soal 2a: jumlah mahasiswa per prodi (COUNT + GROUP BY).
     *
     * @return array<int, array{prodi: string, jumlah_mahasiswa: int}>
     */
    public function mahasiswaPerProdi(): array
    {
        $baris = Database::fetchAll(
            'SELECT prodi, COUNT(*) AS jumlah_mahasiswa
             FROM mahasiswa
             GROUP BY prodi
             ORDER BY jumlah_mahasiswa DESC, prodi ASC'
        );

        return array_map(
            static fn (array $b): array => [
                'prodi' => (string) $b['prodi'],
                'jumlah_mahasiswa' => (int) $b['jumlah_mahasiswa'],
            ],
            $baris
        );
    }
}
