# Statistik Mata Kuliah (Anggota 4)

Halaman: `GET /statistik` — `StatistikController::index` → model `App\Models\StatistikMk` → view `app/views/statistik/index.php`.
Semua query memakai prepared statement (named parameter) dan tanpa subquery (`composer cek-aturan` lolos).

## Cara memakai filter

- **Semester**: berlaku untuk 2f, 2g, 2h, 2j. Default = semester terbaru di tabel `krs` (2026/2027 Ganjil).
- **Mata kuliah** dan **Angkatan**: hanya memfilter daftar peserta (2f). Kosong = semua.
- **Reset** mengembalikan ke default. Nilai filter yang tidak ada di data diabaikan (whitelist).
- 2c selalu menghitung seluruh semester.

## Query

**2c — rata-rata nilai (seluruh semester)**
```sql
SELECT m.kode_mk, m.nama_mk, COUNT(k.nilai_angka) AS jumlah_nilai, ROUND(AVG(k.nilai_angka), 2) AS rata_rata
FROM matakuliah m
LEFT JOIN krs k ON k.kode_mk = m.kode_mk
GROUP BY m.kode_mk, m.nama_mk
ORDER BY m.kode_mk;
```

**2f — peserta MK + filter** (`:kode_a/:kode_b` dan `:angkatan_a/:angkatan_b` bernilai NULL bila filter kosong)
```sql
SELECT m.kode_mk, m.nama_mk, s.nim, s.nama, s.angkatan, k.nilai_angka
FROM krs k
INNER JOIN matakuliah m ON m.kode_mk = k.kode_mk
INNER JOIN mahasiswa s ON s.nim = k.nim
WHERE k.semester = :semester
  AND (:kode_a IS NULL OR m.kode_mk = :kode_b)
  AND (:angkatan_a IS NULL OR s.angkatan = :angkatan_b)
ORDER BY m.kode_mk, s.angkatan, s.nim;
```

**2g — jumlah peserta, nilai tertinggi, terendah**
```sql
SELECT m.kode_mk, m.nama_mk, COUNT(k.nim) AS jumlah_peserta,
       MAX(k.nilai_angka) AS nilai_tertinggi, MIN(k.nilai_angka) AS nilai_terendah
FROM matakuliah m
LEFT JOIN krs k ON k.kode_mk = m.kode_mk AND k.semester = :semester
GROUP BY m.kode_mk, m.nama_mk
ORDER BY m.kode_mk;
```

**2h — MK tanpa peserta**
```sql
SELECT m.kode_mk, m.nama_mk, m.sks, d.nama_dosen
FROM matakuliah m
INNER JOIN dosen d ON d.nidn = m.nidn
LEFT JOIN krs k ON k.kode_mk = m.kode_mk AND k.semester = :semester
WHERE k.nim IS NULL
ORDER BY m.kode_mk;
```

**2j — jumlah tidak lulus (nilai < `app.batas_lulus` = 55)**
```sql
SELECT m.kode_mk, m.nama_mk, COUNT(k.nim) AS jumlah_peserta,
       COUNT(CASE WHEN k.nilai_angka < :batas THEN 1 END) AS jumlah_tidak_lulus
FROM matakuliah m
LEFT JOIN krs k ON k.kode_mk = m.kode_mk AND k.semester = :semester
GROUP BY m.kode_mk, m.nama_mk
ORDER BY m.kode_mk;
```

Catatan: syarat semester ada di `ON` agar MK tanpa peserta tetap muncul. NULL tidak dihitung sebagai 0 (diabaikan `AVG/MAX/MIN` dan `< 55`), tetapi barisnya tetap dihitung sebagai peserta.

## Hasil pengujian

Lingkungan: MariaDB 10.11 + `php -S` (PHP CLI), data awal dari commit `21e0417`. Diuji dengan permintaan HTTP ke `/statistik`. Tangkapan layar browser **belum** diambil.

| Pengujian | Hasil | Status |
|---|---|---|
| `php -l` (model, controller, route, view) | tanpa error sintaks | Berhasil |
| `php scripts/cek-aturan.php` | OK | Berhasil |
| 2g/2j semester 2026/2027 Ganjil: IF201 5/92/45/2; IF202 3/81/50/1; IF203 3/87/68/0; IF204 1/88/88/0; IF205 0/NULL/NULL/0; SI201 2/83/70/0 | sama dengan acuan | Berhasil |
| 2c: IF201 72.67, IF202 68.75, IF203 78.00, IF204 88.00, IF205 90.00 (dari riwayat), SI201 76.50 | sesuai hitungan manual | Berhasil |
| 2h 2026/2027 Ganjil | hanya IF205 | Berhasil |
| 2f filter: IF201 → 5 baris; angkatan 2025 → 3; IF201 + 2024 → 3; IF205 → kosong dengan pesan | sesuai data | Berhasil |
| Ganti semester 2025/2026 Ganjil | IF201 1, IF202 2 (Dian NULL tetap peserta, min/max 69), IF205 1; 2h = IF203, IF204, SI201 | Berhasil |
| Ganti semester 2025/2026 Genap | hanya IF205 (92); 2h = lima MK lain | Berhasil |
| Nilai NULL | tidak masuk AVG/MAX/MIN/tidak lulus | Berhasil |
| Batas nilai (salinan DB `db_uji`): 55.00 tidak dihitung, 54.99 dihitung, NULL tidak dihitung | IF203 tidak lulus = 1 | Berhasil |
| KRS kosong / MK kosong (salinan DB `db_uji`) | halaman HTTP 200, pesan data kosong | Berhasil |
| Input filter tidak valid (`semester=xx`, kode_mk berisi `' OR 1=1`, `angkatan=abc`, `angkatan[]`) | diabaikan, tanpa error | Berhasil |

Data awal `db_akademik` tidak diubah; uji batas dan data kosong dilakukan pada salinan `db_uji`.
