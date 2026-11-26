<?php
$path = '/' . trim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = rtrim((string) \App\Core\Config::get('app.base_url', ''), '/');
if ($base !== '' && str_starts_with($path, $base)) {
    $path = '/' . trim(substr($path, strlen($base)), '/');
}

// Daftar menu: [judul grup => [[label, path], ...]]
$menu = [
    '' => [['Dashboard', '/']],
    'Master Data' => [
        ['Mahasiswa', '/mahasiswa'],
        ['Dosen', '/dosen'],
        ['Mata Kuliah', '/matakuliah'],
    ],
    'KRS & Nilai' => [['Kartu Rencana Studi', '/krs']],
    'Statistik MK' => [['Statistik Mata Kuliah', '/statistik']],
    'Akademik Mhs' => [['Profil & Transkrip', '/akademik/mahasiswa']],
];
?>
<aside class="col-md-2 sidebar py-3">
  <ul class="nav flex-column">
    <?php foreach ($menu as $grup => $items) : ?>
      <?php if ($grup !== '') : ?>
        <li class="menu-title"><?= e($grup) ?></li>
      <?php endif; ?>
      <?php foreach ($items as [$label, $href]) : ?>
        <?php $aktif = $href === '/' ? $path === '/' : str_starts_with($path, $href); ?>
        <li class="nav-item">
          <a class="nav-link<?= $aktif ? ' active' : '' ?>" href="<?= e(url($href)) ?>"><?= e($label) ?></a>
        </li>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </ul>
</aside>
