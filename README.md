# SiAkad – Sistem Informasi Akademik

Mini Project **Basis Data Lanjut** (Semester Ganjil 2026/2027) – Kasus 2.
Aplikasi web untuk admin prodi: mengelola mahasiswa, dosen, mata kuliah, KRS, nilai, serta
statistik akademik (Soal **2a–2j**).

- **Backend:** PHP 8.1+ (native, pola MVC sederhana, tanpa framework)
- **Database:** MySQL 8 / MariaDB 10.4+ (`db_akademik`)
- **Frontend:** Bootstrap 5 + Chart.js (via CDN)

## Anggota Kelompok

| No | Nama | NIM | Peran |
|----|------|-----|-------|
| 1 |  |  | Fondasi, Dashboard (2a), Dokumentasi |
| 2 |  |  | Master Data (2b, 2d) |
| 3 |  |  | KRS, Nilai, Profil Akademik (2e, 2i) |
| 4 |  |  | Statistik MK (2c, 2f, 2g, 2h, 2j), Pengujian |

> Isi kolom Nama dan NIM sebelum dikumpulkan.

## Fitur

| Kode | Fitur | Halaman | Status |
|------|-------|---------|--------|
| – | Kartu ringkasan (mahasiswa, dosen, MK, baris KRS) + ringkasan semester aktif | `/` | tersedia |
| 2a | Grafik jumlah mahasiswa per prodi (Chart.js) | `/` | tersedia |
| 2b | Daftar mata kuliah + dosen pengampu | `/matakuliah` | dikerjakan Anggota 2 |
| 2c | Rata-rata nilai per MK | `/statistik` | dikerjakan Anggota 4 |
| 2d | Beban mengajar dosen (≥ 2 MK) | `/dosen` | dikerjakan Anggota 2 |
| 2e | Total SKS per semester | `/akademik/mahasiswa/{nim}` | dikerjakan Anggota 3 |
| 2f | Daftar peserta MK + filter angkatan | `/statistik` | dikerjakan Anggota 4 |
| 2g | Nilai tertinggi/terendah/jumlah peserta | `/statistik` | dikerjakan Anggota 4 |
| 2h | MK tanpa peserta (baris abu-abu) | `/statistik` | dikerjakan Anggota 4 |
| 2i | Rata-rata tertimbang SKS | `/akademik/mahasiswa/{nim}` | dikerjakan Anggota 3 |
| 2j | Jumlah mahasiswa tidak lulus (< 55) | `/statistik` | dikerjakan Anggota 4 |

Menu sidebar untuk halaman yang belum selesai akan menampilkan halaman 404 sampai fiturnya dibuat.

## Prasyarat

| Tools | Versi | Catatan |
|-------|-------|---------|
| PHP | 8.1+ (disarankan 8.2/8.3) | ekstensi `pdo_mysql` dan `mbstring` wajib aktif |
| MySQL / MariaDB | MySQL 8 atau MariaDB 10.4+ | nama database: `db_akademik` |
| Composer | 2.x | hanya untuk alat lint, aplikasi tidak butuh dependensi runtime |
| Git | terbaru | clone repo |

## Instalasi dari Nol

### 1. Clone & pasang dependensi pengembangan

```bash
git clone <url-repo> siakad
cd siakad
composer install
```

### 2. Buat file konfigurasi

```bash
cp config/config.example.php config/config.php     # Windows CMD: copy config\config.example.php config\config.php
```

`config/config.php` berisi kredensial dan **tidak boleh di-commit** (sudah masuk `.gitignore`).
Sesuaikan bagian berikut:

| Kunci | Arti |
|-------|------|
| `app.base_url` | `''` jika memakai `php -S`; `'/siakad/public'` jika lewat Apache/Laragon |
| `app.mode` | `'demo'` → `TANGGAL_ACUAN` tetap `2026-11-13`; `'produksi'` → tanggal hari ini |
| `app.batas_sks` | batas SKS per semester (default 24) |
| `app.batas_lulus` | batas nilai lulus (default 55) |
| `app.debug` | `true` saat pengembangan (detail error tampil), `false` saat demo |
| `db.host` / `db.port` | default `127.0.0.1` / `3306` |
| `db.name` | `db_akademik` |
| `db.user` / `db.pass` | kredensial MySQL lokal (Laragon/XAMPP default: `root` tanpa password) |

### 3. Siapkan database

Pastikan layanan MySQL sudah berjalan lebih dulu (Laragon: **Start All**, XAMPP: **Start** pada MySQL).

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS db_akademik CHARACTER SET utf8mb4"
mysql -u root -p db_akademik < database/01_schema_dan_data.sql
mysql -u root -p db_akademik < database/02_tambahan.sql
```

Tanpa password (default Laragon): hilangkan `-p`.

| Berkas | Isi |
|--------|-----|
| `database/01_schema_dan_data.sql` | skema tabel inti (`mahasiswa`, `dosen`, `matakuliah`, `krs`) + data awal |
| `database/02_tambahan.sql` | objek tambahan di luar tabel inti (tabel `users`) |

Skrip `01` diawali `DROP TABLE IF EXISTS` untuk tabel intinya, jadi aman dijalankan ulang
(seluruh data KRS yang kamu input lewat aplikasi akan terhapus dan kembali ke data awal).

### 4. Jalankan aplikasi

```bash
php -S localhost:8000 -t public
```

Buka <http://localhost:8000>. Jika port 8000 dipakai proses lain, ganti angkanya
(`php -S localhost:8080 -t public`).

**Lewat Laragon/XAMPP (Apache):** letakkan folder proyek di `C:\laragon\www\` atau
`C:\xampp\htdocs\`, lalu buka `http://localhost/siakad/public` dan isi
`app.base_url` = `'/siakad/public'`.

## Data Awal

Setelah import: **4 dosen**, **9 mahasiswa** (Informatika & Sistem Informasi), **6 mata kuliah**,
dan **19 baris KRS** pada semester `2025/2026 Ganjil`, `2025/2026 Genap`, dan `2026/2027 Ganjil`
(semester aktif). Data ini disusun agar sesuai acuan hasil soal 2a–2j, termasuk:

- Bella Safitri (`2401010002`): 10 SKS, rata-rata tertimbang **86.10** (Soal 2e/2i)
- Basis Data: 5 peserta, tertinggi 92.00, terendah 45.00, 2 tidak lulus (Soal 2g/2j)
- Kecerdasan Buatan: belum ada peserta di semester aktif (contoh Soal 2h)
- Ada satu baris KRS dengan `nilai_angka` NULL sebagai contoh nilai belum diisi

Tabel `users` belum berisi akun. Untuk membuat akun admin:

```bash
php -r "echo password_hash('admin123', PASSWORD_DEFAULT), PHP_EOL;"
```

lalu masukkan hash-nya:

```sql
INSERT INTO users (username, password_hash, nama) VALUES ('admin', '<hash>', 'Admin Prodi');
```

## Troubleshooting

| Gejala | Penyebab & solusi |
|--------|-------------------|
| Halaman "Ups, ada yang tidak beres" + `SQLSTATE[HY000]` | Layanan MySQL belum jalan, atau kredensial/port di `config/config.php` salah. Jalankan MySQL lalu muat ulang. |
| `could not find driver` | Ekstensi `pdo_mysql` belum aktif. Buka `php.ini`, hapus `;` di depan `extension=pdo_mysql`, lalu restart server. |
| Karakter nama/teks rusak (Ã©, Â) | Ekstensi `mbstring` belum aktif atau import SQL tanpa `--default-character-set=utf8mb4`. |
| `Failed to listen on ...:8000` | Port dipakai proses lain. Pakai port lain: `php -S localhost:8080 -t public`. |
| Aset CSS/JS tidak termuat saat lewat Laragon | `app.base_url` belum diisi `'/siakad/public'`. |
| Tabel `krs` kosong setelah import | Berkas SQL dijalankan ke database lain. Tambahkan nama database: `mysql -u root db_akademik < database/01_schema_dan_data.sql`. |
| `config/config.php belum ada` | Belum menyalin dari `config/config.example.php`. |
| Perubahan `database/02_tambahan.sql` tidak terlihat | Import ulang berkas itu, lalu beri tahu anggota lain. |

## Struktur Folder

```
siakad/
|-- app/
|   |-- bootstrap.php   <- autoload, config, TANGGAL_ACUAN, session
|   |-- core/           <- Router, Database (PDO), Controller dasar, helpers
|   |-- controllers/    <- logika halaman
|   |-- models/         <- SELURUH query SQL (berkomentar kode soal, mis. -- Soal 2c)
|   |-- routes/         <- daftar route per modul
|   |-- views/          <- templat halaman (layouts, partials, per fitur)
|-- config/             <- config.example.php (config.php TIDAK di-commit)
|-- database/           <- 01_schema_dan_data.sql, 02_tambahan.sql
|-- docs/               <- lembar pengujian, bahan laporan
|-- public/             <- index.php, assets/{css,js,img}
|-- scripts/            <- cek-aturan.php (deteksi subquery & SQL berinterpolasi)
```

## Tanggal Acuan

`TANGGAL_ACUAN` (konstanta PHP) diatur lewat `config/config.php`:
mode `demo` → `2026-11-13`, mode `produksi` → tanggal hari ini.

## Aturan Query

1. Tanpa subquery: seleksi, INNER/OUTER JOIN, self join, agregat, `GROUP BY`/`HAVING`, DML.
2. Semua input pengguna lewat prepared statement (`App\Core\Database`), tidak ada string SQL digabung.
3. Alias kolom mengikuti tabel hasil soal latihan.
4. DML lebih dari satu perintah dibungkus `Database::transaction()`.
5. Setiap query soal diberi komentar kode soal, mis. `-- Soal 2c`.

`scripts/cek-aturan.php` memeriksa aturan 1 dan 2 secara otomatis lewat `composer check`.

## Cek Kualitas Kode

```bash
composer check        # sintaks + gaya kode PSR-12 + cek aturan tugas
composer lint:fix     # perbaiki pelanggaran gaya otomatis
```

## Alur Git

Panduan langkah demi langkah untuk anggota: [`docs/panduan-git.md`](docs/panduan-git.md).

- Branch utama `main` dilindungi: jangan commit langsung. **Hanya Anggota 1 yang merge PR ke `main`.**
- Setiap anggota bekerja di branch sendiri, mis. `fitur/rina-krs`.
- Commit kecil dan sering, lalu push dan buka Pull Request ke `main`.
- Jangan commit: `config/config.php`, `vendor/`, `AGENTS.md`, `.env`.

## Konvensi Commit

Bahasa Indonesia, format `jenis(cakupan): ringkasan`, contoh
`fitur(krs): tambah validasi batas SKS per semester`.
Jenis: `fitur`, `perbaikan`, `refaktor`, `gaya`, `dokumen`, `uji`, `bangun`, `basisdata`.
