<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Models\StatistikMk;

final class StatistikController extends Controller
{
    public function index(): void
    {
        $model = new StatistikMk();
        $daftarSemester = $model->daftarSemester();
        $daftarMk = $model->daftarMatakuliah();
        $daftarAngkatan = $model->daftarAngkatan();

        // Whitelist: nilai filter hanya diterima bila ada di data; selain itu dianggap tidak dipilih.
        $semester = (string) ($_GET['semester'] ?? '');
        if (!in_array($semester, $daftarSemester, true)) {
            $semester = $daftarSemester[0] ?? '';
        }

        $kodeMk = (string) ($_GET['kode_mk'] ?? '');
        $kodeMk = in_array($kodeMk, array_column($daftarMk, 'kode_mk'), true) ? $kodeMk : null;

        $angkatan = filter_var($_GET['angkatan'] ?? '', FILTER_VALIDATE_INT);
        $angkatan = ($angkatan !== false && in_array($angkatan, $daftarAngkatan, true)) ? $angkatan : null;

        $batas = (float) Config::get('app.batas_lulus', 55);
        $adaSemester = $semester !== '';

        $this->render('statistik/index', [
            'daftarSemester' => $daftarSemester,
            'daftarMk' => $daftarMk,
            'daftarAngkatan' => $daftarAngkatan,
            'semester' => $semester,
            'kodeMk' => $kodeMk,
            'angkatan' => $angkatan,
            'batas' => $batas,
            'rataRata' => $model->rataRataNilai(),
            'peserta' => $adaSemester ? $model->pesertaMatakuliah($semester, $kodeMk, $angkatan) : [],
            'nilai' => $adaSemester ? $model->nilaiPerMatakuliah($semester) : [],
            'tanpaPeserta' => $adaSemester ? $model->matakuliahTanpaPeserta($semester) : [],
            'tidakLulus' => $adaSemester ? $model->tidakLulusPerMatakuliah($semester, $batas) : [],
        ], 'Statistik Mata Kuliah');
    }
}
