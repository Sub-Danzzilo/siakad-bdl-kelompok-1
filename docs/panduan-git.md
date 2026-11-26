# Panduan Git untuk Anggota (Pemula)

Berkas ini untuk **Anggota 2–4**. Tujuannya satu: kamu tidak merusak pekerjaan orang lain
dan tidak kehilangan pekerjaanmu sendiri. Tidak perlu paham konsep git — ikuti saja 6 langkah
di bawah.

> **Anggota 1 pemegang branch `main`.** Hanya Anggota 1 yang merge Pull Request.
> Kamu **tidak pernah** mengubah `main` sendiri.

## Alur kerja harian (6 langkah)

```bash
# 1. Ambil pembaruan terbaru
git switch main
git pull

# 2. Masuk ke branch kerjamu (sekali saja, kalau belum ada: pakai -c)
git switch fitur/<namamu>-<fitur>          # sudah ada
git switch -c fitur/<namamu>-<fitur>       # baru pertama kali

# 3. Kerja: edit file, jalankan composer check, uji halamannya

# 4. Simpan perubahan (path spesifik, jangan "git add .")
git status
git add app/controllers/MahasiswaController.php app/views/mahasiswa/
git commit -m "fitur(mahasiswa): tambah daftar mahasiswa dengan pencarian"

# 5. Kirim branch ke GitHub (tanpa --force)
git push

# 6. Buka Pull Request ke main di GitHub, lalu tunggu Anggota 1 me-review dan merge
```

**Sebelum mulai kerja berikutnya, ulangi langkah 1–2.** Kalau kamu lupa `git pull`,
pekerjaanmu akan berbasis kode lama dan PR-mu berkonflik.

## Branch siapa apa

| Branch | Pemilik | Boleh diubah siapa |
|--------|---------|--------------------|
| `main` | Anggota 1 | hanya Anggota 1 (lewat merge PR) |
| `fitur/...` | masing-masing anggota | pemilik branch itu |

Kalau kamu tidak sengaja bekerja di branch orang lain, **jangan** lanjut: `git status`,
lalu lapor ke grup.

## Commit: kecil, sering, jelas

```bash
git commit -m "fitur(krs): tambah validasi batas SKS per semester"
```

Jenis yang dipakai: `fitur`, `perbaikan`, `refaktor`, `gaya`, `dokumen`, `uji`, `bangun`, `basisdata`.
Satu fitur/perbaikan = satu commit. Jangan menumpuk seminggu pekerjaan dalam satu commit.

## Jangan pernah lakukan ini

| Larangan | Akibatnya |
|----------|-----------|
| `git push` ke `main` | ditolak / merusak branch utama |
| `git push --force` | pekerjaan orang lain bisa hilang |
| `git reset --hard`, `git clean -fd` | perubahan yang belum di-commit hilang permanen |
| `git rebase`, `git branch -D`, `git stash drop` | riwayat/branch hilang, sulit dipulihkan |
| `git add .` tanpa `git status` | `config/config.php`, `vendor/` ikut ter-commit |
| Commit `config/config.php` | kredensial bocor ke repo |
| Menyentuh file anggota lain | konflik merge, pekerjaan orang tertimpa |
| Import ulang `database/01_schema_dan_data.sql` | tabel di-`DROP`, data uji (KRS) hilang |
| `git clone` ulang saat bingung | perubahan lokal hilang; biasanya cukup `git pull` |

## Kalau muncul masalah

| Gejala | Arti & solusi |
|--------|----------------|
| `error: Your local changes would be overwritten` | ada perubahan belum di-commit. `git status` → commit dulu, baru `git switch`/`git pull`. |
| `Updates were rejected because the remote contains work...` | branch di GitHub lebih baru: `git pull` lalu `git push`. |
| `Please tell me who you are` | sekali saja: `git config user.name "Nama Kamu"` dan `git config user.email "email@kamu"`. |
| Muncul `<<<<<<< HEAD` di file | **konflik**. Jangan commit. Screenshot lalu kirim ke grup, minta bantuan Anggota 1. |
| `Permission denied (publickey)` | remote masih SSH; pakai HTTPS atau pasang SSH key. Lapor ke grup. |
| Lupa sedang di branch mana | `git branch --show-current` |

## Sebelum bilang "selesai"

1. `composer check` lulus (sintaks, gaya PSR-12, cek aturan tugas).
2. Halamannya dibuka di browser dan datanya tampil benar.
3. Hasil query soal dibandingkan dengan acuan di dokumen latihan.
4. Pull Request sudah dibuka, dengan judul jelas dan daftar file yang berubah.
