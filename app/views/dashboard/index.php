<?php
/**
 * Dashboard SiAkad - Anggota 1.
 *
 * @var array{mahasiswa: int, dosen: int, matakuliah: int, krs: int} $ringkasan
 * @var string|null $semesterAktif
 * @var array{jumlah_krs: int, jumlah_mahasiswa: int, jumlah_mk: int, total_sks: int}|null $ringkasanSemester
 * @var array<int, array{prodi: string, jumlah_mahasiswa: int}> $perProdi
 */

$totalMahasiswa = array_sum(array_column($perProdi, 'jumlah_mahasiswa'));
$labelProdi = array_column($perProdi, 'prodi');
$jumlahProdi = array_column($perProdi, 'jumlah_mahasiswa');

// Data grafik di-encode aman agar tidak bisa keluar dari tag <script>.
$jsonLabel = json_encode(
    $labelProdi,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
$jsonJumlah = json_encode(
    $jumlahProdi,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
?>
<h1 class="h3 mb-1">Dashboard</h1>
<p class="text-muted">Data per tanggal acuan: <?= e(format_tanggal(TANGGAL_ACUAN)) ?></p>

<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="card card-ringkasan h-100">
      <div class="card-body">
        <div class="text-muted small">Total Mahasiswa</div>
        <div class="angka"><?= e((string) $ringkasan['mahasiswa']) ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card card-ringkasan h-100">
      <div class="card-body">
        <div class="text-muted small">Total Dosen</div>
        <div class="angka"><?= e((string) $ringkasan['dosen']) ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card card-ringkasan h-100">
      <div class="card-body">
        <div class="text-muted small">Total Mata Kuliah</div>
        <div class="angka"><?= e((string) $ringkasan['matakuliah']) ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card card-ringkasan h-100">
      <div class="card-body">
        <div class="text-muted small">Total Baris KRS</div>
        <div class="angka"><?= e((string) $ringkasan['krs']) ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Jumlah Mahasiswa per Prodi</span>
        <span class="badge text-bg-secondary">Soal 2a</span>
      </div>
      <div class="card-body">
        <?php if ($perProdi === []) : ?>
          <p class="text-muted mb-0">Belum ada data mahasiswa.</p>
        <?php else : ?>
          <canvas id="grafik-prodi" height="220"></canvas>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header fw-semibold">Semester Aktif</div>
      <div class="card-body">
        <?php if ($semesterAktif === null || $ringkasanSemester === null) : ?>
          <p class="text-muted mb-0">Belum ada data KRS pada semester mana pun.</p>
        <?php else : ?>
          <p class="fs-5 fw-semibold mb-3"><?= e($semesterAktif) ?></p>
          <div class="row text-center g-3">
            <div class="col-6">
              <div class="border rounded py-2">
                <div class="text-muted small">Baris KRS</div>
                <div class="fw-bold fs-4"><?= e((string) $ringkasanSemester['jumlah_krs']) ?></div>
              </div>
            </div>
            <div class="col-6">
              <div class="border rounded py-2">
                <div class="text-muted small">Mahasiswa</div>
                <div class="fw-bold fs-4"><?= e((string) $ringkasanSemester['jumlah_mahasiswa']) ?></div>
              </div>
            </div>
            <div class="col-6">
              <div class="border rounded py-2">
                <div class="text-muted small">Mata Kuliah</div>
                <div class="fw-bold fs-4"><?= e((string) $ringkasanSemester['jumlah_mk']) ?></div>
              </div>
            </div>
            <div class="col-6">
              <div class="border rounded py-2">
                <div class="text-muted small">Total SKS</div>
                <div class="fw-bold fs-4"><?= e((string) $ringkasanSemester['total_sks']) ?></div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php if ($perProdi !== []) : ?>
  <div class="card mt-3">
    <div class="card-header fw-semibold">Rincian Jumlah Mahasiswa per Prodi</div>
    <div class="table-responsive">
      <table class="table table-striped mb-0 align-middle">
        <thead>
          <tr>
            <th scope="col">Prodi</th>
            <th scope="col" class="text-end">Jumlah Mahasiswa</th>
            <th scope="col" class="text-end">Persentase</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($perProdi as $baris) : ?>
            <?php
            $persen = $totalMahasiswa > 0
                ? round($baris['jumlah_mahasiswa'] * 100 / $totalMahasiswa, 1)
                : 0.0;
            ?>
            <tr>
              <td><?= e($baris['prodi']) ?></td>
              <td class="text-end"><?= e((string) $baris['jumlah_mahasiswa']) ?></td>
              <td class="text-end"><?= e(number_format($persen, 1, ',', '.')) ?>%</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="fw-semibold">
            <td>Total</td>
            <td class="text-end"><?= e((string) $totalMahasiswa) ?></td>
            <td class="text-end">100%</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <script>
    // Soal 2a: grafik jumlah mahasiswa per prodi (data dari COUNT + GROUP BY di model).
    (function () {
      const canvas = document.getElementById('grafik-prodi');
      if (!canvas || typeof Chart === 'undefined') {
        return;
      }

      new Chart(canvas, {
        type: 'bar',
        data: {
          labels: <?= $jsonLabel ?>,
          datasets: [{
            label: 'Jumlah mahasiswa',
            data: <?= $jsonJumlah ?>,
            backgroundColor: 'rgba(13, 110, 253, 0.6)',
            borderColor: 'rgba(13, 110, 253, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { precision: 0 }
            }
          }
        }
      });
    })();
  </script>
<?php endif; ?>
