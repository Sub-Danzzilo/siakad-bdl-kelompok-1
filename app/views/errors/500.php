<h1 class="h3 mb-2">Ups, ada yang tidak beres</h1>
<p class="text-muted">
  Aplikasi tidak dapat menyelesaikan permintaan ini. Biasanya penyebabnya database belum siap.
</p>

<div class="card">
  <div class="card-header fw-semibold">Coba langkah berikut</div>
  <div class="card-body">
    <ol class="mb-0">
      <li>Pastikan layanan <strong>MySQL/MariaDB sudah berjalan</strong>
        (Laragon: <em>Start All</em>; XAMPP: <em>Start</em> pada modul MySQL).</li>
      <li>Periksa <code>host</code>, <code>port</code>, <code>user</code>, dan <code>pass</code>
        pada <code>config/config.php</code>.</li>
      <li>Pastikan database <code>db_akademik</code> sudah dibuat dan skrip
        <code>database/01_schema_dan_data.sql</code> sudah di-import.</li>
      <li>Muat ulang halaman ini setelah perbaikan.</li>
    </ol>
  </div>
</div>

<?php if ($detail !== null) : ?>
  <div class="alert alert-warning mt-3 mb-0">
    <div class="fw-semibold mb-1">Detail teknis (mode debug aktif)</div>
    <pre class="mb-0 small text-wrap"><?= e($detail) ?></pre>
  </div>
<?php endif; ?>

<a class="btn btn-primary mt-3" href="<?= e(url('/')) ?>">Kembali ke Dashboard</a>
