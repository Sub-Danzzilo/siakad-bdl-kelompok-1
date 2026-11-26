# SiAkad – Sistem Informasi Akademik

Mini Project **Basis Data Lanjut** (Semester Ganjil 2026/2027) – Kasus 2.
Aplikasi web untuk admin prodi: mengelola mahasiswa, dosen, mata kuliah, KRS, nilai, serta statistik akademik (Soal 2a–2j).

- **Backend:** PHP 8.1+ (native, pola MVC sederhana)
- **Database:** MySQL / MariaDB (`db_akademik`)
- **Frontend:** Bootstrap 5 + Chart.js (via CDN)

## Anggota Kelompok

| No | Nama | NIM | Peran |
|----|------|-----|-------|
| 1 |  |  | Fondasi, Dashboard, Dokumentasi |
| 2 |  |  | Master Data |
| 3 |  |  | KRS, Nilai, Profil Akademik |
| 4 |  |  | Statistik MK, Pengujian |

## Prasyarat

- PHP 8.1 atau lebih baru (ekstensi `pdo_mysql`, `mbstring`)
- MySQL 8 / MariaDB 10.4+
- Composer (untuk alat lint saja, aplikasi tidak butuh dependensi runtime)
- Git

> Cara termudah di Windows: pasang **Laragon** atau **XAMPP** (sudah termasuk PHP + MariaDB).

## Instalasi

```bash
git clone <url-repo> siakad
cd siakad
composer install

# 1. Konfigurasi
cp config/config.example.php config/config.php   # lalu sesuaikan kredensial DB

# 2. Database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS db_akademik CHARACTER SET utf8mb4"
mysql -u root -p db_akademik < database/01_schema_dan_data.sql
mysql -u root -p db_akademik < database/02_tambahan.sql

# 3. Jalankan
php -S localhost:8000 -t public
```

Buka <http://localhost:8000>.
Jika memakai Laragon/XAMPP, isi `base_url` di `config/config.php` (contoh `/siakad/public`).

## Struktur Folder

```
siakad/
|-- app/
|   |-- core/         <- Router, Database (PDO), Controller dasar, helpers
|   |-- controllers/  <- logika halaman
|   |-- models/       <- SELURUH query SQL (berkomentar kode soal, mis. -- Soal 2c)
|   |-- routes/       <- daftar route per modul
|   |-- views/        <- templat halaman (layouts, partials, per fitur)
|-- config/           <- config.example.php (config.php TIDAK di-commit)
|-- database/         <- skrip SQL (skema + data awal, tambahan)
|-- docs/             <- lembar pengujian, bahan laporan
|-- public/           <- index.php, css, js, gambar
|-- scripts/          <- cek-aturan.php
```

## Tanggal Acuan

`TANGGAL_ACUAN` diatur lewat `config/config.php` (`mode = demo` → `2026-11-13`, `mode = produksi` → tanggal hari ini).

## Cek Kualitas Kode

```bash
composer check        # sintaks + gaya kode (PSR-12) + cek aturan tugas
composer lint:fix     # perbaiki gaya kode otomatis
```

## Konvensi Commit

Bahasa Indonesia, format `jenis(cakupan): ringkasan`, contoh:
`fitur(krs): tambah validasi batas SKS per semester`.
Jenis: `fitur`, `perbaikan`, `refaktor`, `gaya`, `dokumen`, `uji`, `bangun`, `basisdata`.
