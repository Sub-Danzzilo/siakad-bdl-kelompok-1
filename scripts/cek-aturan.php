<?php

/**
 * Pengecekan aturan tugas secara otomatis (heuristik):
 *  1. Dilarang subquery pada app/models dan app/controllers.
 *  2. Dilarang menyisipkan variabel langsung ke string SQL (wajib prepared statement).
 *
 * Jalankan: composer cek-aturan   (atau: php scripts/cek-aturan.php)
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$problems = 0;

foreach (['app/models', 'app/controllers'] as $dir) {
    $path = $root . '/' . $dir;
    if (!is_dir($path)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        $content = (string) file_get_contents($file->getPathname());
        $nama = str_replace($root . '/', '', str_replace('\\', '/', $file->getPathname()));

        // 1. Subquery: "(SELECT" (boleh ada spasi/baris baru di antaranya)
        if (preg_match_all('/\(\s*SELECT\b/i', $content, $match, PREG_OFFSET_CAPTURE) > 0) {
            foreach ($match[0] as [, $offset]) {
                $baris = substr_count(substr($content, 0, $offset), "\n") + 1;
                echo "[SUBQUERY]  {$nama}:{$baris} - subquery tidak diperbolehkan\n";
                $problems++;
            }
        }

        // 2. Interpolasi / penyambungan variabel ke string SQL
        foreach (explode("\n", $content) as $no => $line) {
            $sql = '/\b(SELECT|INSERT|UPDATE|DELETE|WHERE|FROM|VALUES)\b/i';
            $interpolasi = '/"[^"]*\$\w+[^"]*"/';
            $sambung = '/[\'"]\s*\.\s*\$|\$\w+(\[[^\]]*\])?\s*\.\s*[\'"]/';
            if (
                preg_match($sql, $line) === 1
                && (preg_match($interpolasi, $line) === 1 || preg_match($sambung, $line) === 1)
            ) {
                echo '[INJEKSI?]  ' . $nama . ':' . ($no + 1) . " - variabel disisipkan ke SQL, pakai prepared statement\n";
                $problems++;
            }
        }
    }
}

if ($problems > 0) {
    echo "\nDitemukan {$problems} masalah. Perbaiki sebelum commit.\n";
    exit(1);
}

echo "cek-aturan: OK (tidak ada subquery / SQL berinterpolasi)\n";
