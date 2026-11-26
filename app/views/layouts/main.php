<?php
/** @var string $content */
/** @var string $pageTitle */
use App\Core\Config;

$namaApp = (string) Config::get('app.name', 'SiAkad');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle !== '' ? $pageTitle . ' | ' . $namaApp : $namaApp) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= e(asset('css/app.css')) ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="<?= e(url('/')) ?>"><?= e($namaApp) ?></a>
    <span class="navbar-text text-white-50">Admin Prodi</span>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <?php require APP_PATH . '/views/partials/sidebar.php'; ?>
    <main class="col-md-10 p-4">
      <?php foreach (pull_flash() as $notif) : ?>
        <div class="alert alert-<?= e($notif['jenis']) ?> alert-dismissible fade show" role="alert">
          <?= e($notif['pesan']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
      <?php endforeach; ?>

      <?= $content ?>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
