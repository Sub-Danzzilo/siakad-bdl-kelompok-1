<h1 class="h3 mb-2">404 – Halaman tidak ditemukan</h1>
<p class="text-muted">Alamat yang kamu buka tidak tersedia atau salah ketik.</p>

<?php if ($halaman !== null) : ?>
  <div class="alert alert-warning">
    <div class="fw-semibold mb-1">Halaman <?= e($halaman['judul']) ?> belum dibuat</div>
    Fitur ini sedang dikerjakan <strong><?= e($halaman['pemilik']) ?></strong>
    (<?= e($halaman['soal']) ?>).
  </div>
<?php endif; ?>

<div class="card mt-3">
  <div class="card-header fw-semibold">Halaman yang sudah tersedia</div>
  <div class="card-body">
    <ul class="mb-0">
      <li><a href="<?= e(url('/')) ?>">Dashboard</a> – ringkasan data dan grafik mahasiswa per prodi (Soal 2a)</li>
    </ul>
  </div>
</div>

<a class="btn btn-primary mt-3" href="<?= e(url('/')) ?>">Kembali ke Dashboard</a>
