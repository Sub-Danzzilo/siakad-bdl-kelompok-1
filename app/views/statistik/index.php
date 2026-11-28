<?php
/**
 * Statistik Mata Kuliah - Anggota 4 (Soal 2c, 2f, 2g, 2h, 2j).
 *
 * @var array<int, string> $daftarSemester
 * @var array<int, array{kode_mk: string, nama_mk: string}> $daftarMk
 * @var array<int, int> $daftarAngkatan
 * @var string $semester
 * @var string|null $kodeMk
 * @var int|null $angkatan
 * @var float $batas
 * @var array<int, array<string, mixed>> $rataRata
 * @var array<int, array<string, mixed>> $peserta
 * @var array<int, array<string, mixed>> $nilai
 * @var array<int, array<string, mixed>> $tanpaPeserta
 * @var array<int, array<string, mixed>> $tidakLulus
 */

$batasTampil = rtrim(rtrim(number_format($batas, 2, '.', ''), '0'), '.');
?>
<h1 class="h3 mb-1">Statistik Mata Kuliah</h1>
<p class="text-muted">Rata-rata nilai, peserta, nilai tertinggi/terendah, mata kuliah kosong, dan jumlah tidak lulus.</p>

<?php if ($daftarSemester === []) : ?>
  <div class="alert alert-warning">Belum ada data KRS, sehingga statistik belum dapat ditampilkan.</div>
<?php endif; ?>

<form method="get" action="<?= e(url('/statistik')) ?>" class="card card-body mb-3">
  <div class="row g-2 align-items-end">
    <div class="col-12 col-md-4">
      <label for="semester" class="form-label mb-1">Semester</label>
      <select name="semester" id="semester" class="form-select">
        <?php foreach ($daftarSemester as $s) : ?>
          <option value="<?= e($s) ?>"<?= $s === $semester ? ' selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-12 col-md-4">
      <label for="kode_mk" class="form-label mb-1">Mata kuliah (daftar peserta)</label>
      <select name="kode_mk" id="kode_mk" class="form-select">
        <option value="">Semua mata kuliah</option>
        <?php foreach ($daftarMk as $mk) : ?>
          <option value="<?= e($mk['kode_mk']) ?>"<?= $mk['kode_mk'] === $kodeMk ? ' selected' : '' ?>>
            <?= e($mk['kode_mk'] . ' - ' . $mk['nama_mk']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-6 col-md-2">
      <label for="angkatan" class="form-label mb-1">Angkatan</label>
      <select name="angkatan" id="angkatan" class="form-select">
        <option value="">Semua</option>
        <?php foreach ($daftarAngkatan as $a) : ?>
          <option value="<?= e((string) $a) ?>"<?= $a === $angkatan ? ' selected' : '' ?>><?= e((string) $a) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-6 col-md-2 d-flex gap-2">
      <button type="submit" class="btn btn-primary flex-fill">Terapkan</button>
      <a href="<?= e(url('/statistik')) ?>" class="btn btn-outline-secondary">Reset</a>
    </div>
  </div>
  <div class="form-text mt-2">
    Semester berlaku untuk bagian 2f, 2g, 2h, dan 2j. Mata kuliah dan angkatan hanya memfilter daftar peserta (2f).
    Bagian 2c selalu mencakup seluruh semester.
  </div>
</form>

<ul class="nav nav-pills mb-3 flex-wrap" id="nav-statistik">
  <li class="nav-item"><a class="nav-link" href="#rata-rata">2c Rata-rata</a></li>
  <li class="nav-item"><a class="nav-link" href="#peserta">2f Peserta</a></li>
  <li class="nav-item"><a class="nav-link" href="#nilai">2g Nilai</a></li>
  <li class="nav-item"><a class="nav-link" href="#tanpa-peserta">2h Tanpa Peserta</a></li>
  <li class="nav-item"><a class="nav-link" href="#tidak-lulus">2j Tidak Lulus</a></li>
</ul>

<section id="rata-rata" class="card mb-4">
  <div class="card-header fw-semibold">2c. Rata-rata Nilai per Mata Kuliah (seluruh semester)</div>
  <div class="table-responsive">
    <table class="table table-striped table-hover mb-0 align-middle">
      <thead>
        <tr><th>Kode</th><th>Mata Kuliah</th><th class="text-end">Jumlah Nilai</th><th class="text-end">Rata-rata</th></tr>
      </thead>
      <tbody>
        <?php if ($rataRata === []) : ?>
          <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data mata kuliah.</td></tr>
        <?php endif; ?>
        <?php foreach ($rataRata as $r) : ?>
          <tr>
            <td><?= e((string) $r['kode_mk']) ?></td>
            <td><?= e((string) $r['nama_mk']) ?></td>
            <td class="text-end"><?= e((string) (int) $r['jumlah_nilai']) ?></td>
            <td class="text-end"><?= e(nilai_tampil($r['rata_rata'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section id="peserta" class="card mb-4">
  <div class="card-header fw-semibold">
    2f. Peserta Mata Kuliah
    <span class="fw-normal text-muted">
      &middot; <?= e($semester) ?>
      <?= $kodeMk !== null ? ' &middot; ' . e($kodeMk) : '' ?>
      <?= $angkatan !== null ? ' &middot; angkatan ' . e((string) $angkatan) : '' ?>
      &middot; <?= e((string) count($peserta)) ?> baris
    </span>
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-hover mb-0 align-middle">
      <thead>
        <tr><th>Kode MK</th><th>Mata Kuliah</th><th>NIM</th><th>Nama Mahasiswa</th><th class="text-end">Angkatan</th></tr>
      </thead>
      <tbody>
        <?php if ($peserta === []) : ?>
          <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada peserta yang sesuai dengan filter.</td></tr>
        <?php endif; ?>
        <?php foreach ($peserta as $p) : ?>
          <tr>
            <td><?= e((string) $p['kode_mk']) ?></td>
            <td><?= e((string) $p['nama_mk']) ?></td>
            <td><?= e((string) $p['nim']) ?></td>
            <td><?= e((string) $p['nama']) ?></td>
            <td class="text-end"><?= e((string) $p['angkatan']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section id="nilai" class="card mb-4">
  <div class="card-header fw-semibold">
    2g. Nilai Tertinggi, Terendah, dan Jumlah Peserta
    <span class="fw-normal text-muted">&middot; <?= e($semester) ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-hover mb-0 align-middle">
      <thead>
        <tr><th>Kode</th><th>Mata Kuliah</th><th class="text-end">Peserta</th><th class="text-end">Tertinggi</th><th class="text-end">Terendah</th></tr>
      </thead>
      <tbody>
        <?php if ($nilai === []) : ?>
          <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data KRS.</td></tr>
        <?php endif; ?>
        <?php foreach ($nilai as $n) : ?>
          <tr>
            <td><?= e((string) $n['kode_mk']) ?></td>
            <td><?= e((string) $n['nama_mk']) ?></td>
            <td class="text-end"><?= e((string) (int) $n['jumlah_peserta']) ?></td>
            <td class="text-end"><?= e(nilai_tampil($n['nilai_tertinggi'])) ?></td>
            <td class="text-end"><?= e(nilai_tampil($n['nilai_terendah'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section id="tanpa-peserta" class="card mb-4">
  <div class="card-header fw-semibold">
    2h. Mata Kuliah Tanpa Peserta
    <span class="fw-normal text-muted">&middot; <?= e($semester) ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-hover mb-0 align-middle">
      <thead>
        <tr><th>Kode</th><th>Mata Kuliah</th><th class="text-end">SKS</th><th>Dosen Pengampu</th></tr>
      </thead>
      <tbody>
        <?php if ($semester === '') : ?>
          <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data KRS.</td></tr>
        <?php elseif ($tanpaPeserta === []) : ?>
          <tr><td colspan="4" class="text-center text-muted py-3">Semua mata kuliah memiliki peserta pada semester ini.</td></tr>
        <?php endif; ?>
        <?php foreach ($tanpaPeserta as $t) : ?>
          <tr>
            <td><?= e((string) $t['kode_mk']) ?></td>
            <td><?= e((string) $t['nama_mk']) ?></td>
            <td class="text-end"><?= e((string) (int) $t['sks']) ?></td>
            <td><?= e((string) $t['nama_dosen']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section id="tidak-lulus" class="card mb-4">
  <div class="card-header fw-semibold">
    2j. Mahasiswa Tidak Lulus (nilai &lt; <?= e($batasTampil) ?>)
    <span class="fw-normal text-muted">&middot; <?= e($semester) ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-hover mb-0 align-middle">
      <thead>
        <tr><th>Kode</th><th>Mata Kuliah</th><th class="text-end">Peserta</th><th class="text-end">Tidak Lulus</th></tr>
      </thead>
      <tbody>
        <?php if ($tidakLulus === []) : ?>
          <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data KRS.</td></tr>
        <?php endif; ?>
        <?php foreach ($tidakLulus as $l) : ?>
          <tr>
            <td><?= e((string) $l['kode_mk']) ?></td>
            <td><?= e((string) $l['nama_mk']) ?></td>
            <td class="text-end"><?= e((string) (int) $l['jumlah_peserta']) ?></td>
            <td class="text-end">
              <?php if ((int) $l['jumlah_tidak_lulus'] > 0) : ?>
                <span class="badge text-bg-danger"><?= e((string) (int) $l['jumlah_tidak_lulus']) ?></span>
              <?php else : ?>
                0
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
