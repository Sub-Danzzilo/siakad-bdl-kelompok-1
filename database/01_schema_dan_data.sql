-- =====================================================================
-- 01_schema_dan_data.sql
-- Skema tabel inti + data awal db_akademik (Kasus 2 - SiAkad).
--
-- Struktur mengikuti ERD pada dokumen tugas (Gambar 11):
--   mahasiswa(nim, nama, prodi, angkatan)
--   dosen(nidn, nama_dosen, email)
--   matakuliah(kode_mk, nama_mk, sks, nidn)
--   krs(nim, kode_mk, semester, nilai_angka)  PK gabungan 3 kolom
--
-- ATURAN: struktur tabel inti (mahasiswa, dosen, matakuliah, krs)
-- TIDAK BOLEH diubah. Tabel tambahan taruh di 02_tambahan.sql.
--
-- CATATAN DATA AWAL:
-- Nilai pada bagian INSERT disusun agar cocok dengan acuan hasil di
-- AGENTS.md Bagian 11 (Bella 86.10, IF201 72.67, IF205 90.00, dst).
-- Cocokkan kembali dengan dokumen latihan sebelum dikumpulkan.
--
-- Jalankan:  mysql -u root db_akademik < database/01_schema_dan_data.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS db_akademik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_akademik;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS krs;
DROP TABLE IF EXISTS matakuliah;
DROP TABLE IF EXISTS mahasiswa;
DROP TABLE IF EXISTS dosen;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- Tabel: dosen
-- ---------------------------------------------------------------------
CREATE TABLE dosen (
  nidn       VARCHAR(10)  NOT NULL,
  nama_dosen VARCHAR(60)  NOT NULL,
  email      VARCHAR(80)  NOT NULL,
  PRIMARY KEY (nidn),
  UNIQUE KEY uq_dosen_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabel: mahasiswa
-- prodi disimpan sebagai teks (sesuai ERD, bukan tabel relasi)
-- ---------------------------------------------------------------------
CREATE TABLE mahasiswa (
  nim      VARCHAR(10) NOT NULL,
  nama     VARCHAR(60) NOT NULL,
  prodi    VARCHAR(40) NOT NULL,
  angkatan SMALLINT    NOT NULL,
  PRIMARY KEY (nim),
  KEY idx_mahasiswa_prodi (prodi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabel: matakuliah
-- ---------------------------------------------------------------------
CREATE TABLE matakuliah (
  kode_mk VARCHAR(6)       NOT NULL,
  nama_mk VARCHAR(60)      NOT NULL,
  sks     TINYINT UNSIGNED NOT NULL,
  nidn    VARCHAR(10)      NOT NULL,
  PRIMARY KEY (kode_mk),
  KEY idx_matakuliah_nidn (nidn),
  CONSTRAINT fk_matakuliah_dosen FOREIGN KEY (nidn)
    REFERENCES dosen (nidn) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_matakuliah_sks CHECK (sks > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabel: krs
-- PK gabungan (nim, kode_mk, semester) -> boleh mengulang MK di
-- semester berbeda, tapi duplikat di semester yang sama ditolak.
-- nilai_angka NULL = nilai belum diisi (tidak dihitung dalam rata-rata).
-- ---------------------------------------------------------------------
CREATE TABLE krs (
  nim         VARCHAR(10)  NOT NULL,
  kode_mk     VARCHAR(6)   NOT NULL,
  semester    VARCHAR(20)  NOT NULL,
  nilai_angka DECIMAL(5,2) NULL DEFAULT NULL,
  PRIMARY KEY (nim, kode_mk, semester),
  KEY idx_krs_kode_mk (kode_mk),
  KEY idx_krs_semester (semester),
  CONSTRAINT fk_krs_mahasiswa FOREIGN KEY (nim)
    REFERENCES mahasiswa (nim) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_krs_matakuliah FOREIGN KEY (kode_mk)
    REFERENCES matakuliah (kode_mk) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_krs_nilai CHECK (nilai_angka IS NULL OR (nilai_angka >= 0 AND nilai_angka <= 100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- DATA AWAL
-- =====================================================================

INSERT INTO dosen (nidn, nama_dosen, email) VALUES
  ('0101018501', 'Dr. Hartono, M.Kom.',   'hartono@univ.ac.id'),
  ('0202028602', 'Rina Wulandari, M.T.',  'rina.wulandari@univ.ac.id'),
  ('0303038703', 'Bayu Aji, M.Kom.',      'bayu.aji@univ.ac.id'),
  ('0404048804', 'Sinta Dewi, M.Si.',     'sinta.dewi@univ.ac.id');

INSERT INTO mahasiswa (nim, nama, prodi, angkatan) VALUES
  ('2301010001', 'Adi Nugroho',    'Informatika',      2023),
  ('2302020002', 'Bunga Lestari',  'Sistem Informasi', 2023),
  ('2401010002', 'Bella Safitri',  'Informatika',      2024),
  ('2401010003', 'Candra Wibowo',  'Informatika',      2024),
  ('2402020004', 'Dian Permata',   'Sistem Informasi', 2024),
  ('2401010005', 'Eka Saputra',    'Informatika',      2024),
  ('2501010006', 'Fajar Ramadhan', 'Informatika',      2025),
  ('2502020007', 'Gita Maharani',  'Sistem Informasi', 2025),
  ('2501010008', 'Hendra Wijaya',  'Informatika',      2025);

INSERT INTO matakuliah (kode_mk, nama_mk, sks, nidn) VALUES
  ('IF201', 'Basis Data',                     3, '0101018501'),
  ('IF202', 'Struktur Data',                  4, '0202028602'),
  ('IF203', 'Pemrograman Web',                3, '0202028602'),
  ('IF204', 'Jaringan Komputer',              3, '0303038703'),
  ('IF205', 'Kecerdasan Buatan',              4, '0101018501'),
  ('SI201', 'Analisis dan Perancangan Sistem', 3, '0404048804');

-- KRS semester 2026/2027 Ganjil (semester aktif pada wireframe)
-- Kecerdasan Buatan (IF205) sengaja tanpa peserta -> Soal 2h baris abu-abu
INSERT INTO krs (nim, kode_mk, semester, nilai_angka) VALUES
  -- Basis Data: 5 peserta, tertinggi 92, terendah 45, tidak lulus 2 (Soal 2g/2j)
  ('2401010002', 'IF201', '2026/2027 Ganjil', 92.00),
  ('2401010003', 'IF201', '2026/2027 Ganjil', 74.00),
  ('2402020004', 'IF201', '2026/2027 Ganjil', 52.00),
  ('2301010001', 'IF201', '2026/2027 Ganjil', 45.00),
  ('2501010006', 'IF201', '2026/2027 Ganjil', 85.00),
  -- Struktur Data: 3 peserta, tertinggi 81, terendah 50, tidak lulus 1
  ('2401010002', 'IF202', '2026/2027 Ganjil', 81.00),
  ('2401010005', 'IF202', '2026/2027 Ganjil', 50.00),
  ('2502020007', 'IF202', '2026/2027 Ganjil', 75.00),
  -- Pemrograman Web: 3 peserta, tertinggi 87, terendah 68, tidak lulus 0
  ('2401010002', 'IF203', '2026/2027 Ganjil', 87.00),
  ('2501010008', 'IF203', '2026/2027 Ganjil', 68.00),
  ('2401010003', 'IF203', '2026/2027 Ganjil', 79.00),
  -- Jaringan Komputer: 1 peserta
  ('2301010001', 'IF204', '2026/2027 Ganjil', 88.00),
  -- Analisis dan Perancangan Sistem: 2 peserta
  ('2302020002', 'SI201', '2026/2027 Ganjil', 83.00),
  ('2402020004', 'SI201', '2026/2027 Ganjil', 70.00);

-- KRS semester lampau (dipakai Soal 2c: rata-rata semua semester)
INSERT INTO krs (nim, kode_mk, semester, nilai_angka) VALUES
  -- Adi mengulang Basis Data di 2026/2027 Ganjil (nilai 45)
  ('2301010001', 'IF201', '2025/2026 Ganjil', 88.00),
  -- Candra sudah menempuh Struktur Data sebelumnya
  ('2401010003', 'IF202', '2025/2026 Ganjil', 69.00),
  -- Contoh KRS dengan nilai belum diisi (NULL) -> tampil "-", tidak dihitung rata-rata
  ('2402020004', 'IF202', '2025/2026 Ganjil', NULL),
  -- Kecerdasan Buatan punya riwayat nilai walau kosong di semester aktif
  ('2301010001', 'IF205', '2025/2026 Ganjil', 88.00),
  ('2302020002', 'IF205', '2025/2026 Genap',  92.00);
