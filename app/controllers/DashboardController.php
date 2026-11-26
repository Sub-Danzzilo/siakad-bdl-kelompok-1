<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Dashboard;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $dashboard = new Dashboard();
        $semesterAktif = $dashboard->semesterAktif();

        $this->render('dashboard/index', [
            'ringkasan' => $dashboard->ringkasan(),
            'semesterAktif' => $semesterAktif,
            'ringkasanSemester' => $semesterAktif === null
                ? null
                : $dashboard->ringkasanSemester($semesterAktif),
            'perProdi' => $dashboard->mahasiswaPerProdi(),
        ], 'Dashboard');
    }
}
