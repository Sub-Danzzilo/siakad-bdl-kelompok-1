-- =====================================================================
-- 02_tambahan.sql
-- Objek tambahan di luar tabel inti (tabel users, view, dsb).
-- Jalankan SETELAH 01_schema_dan_data.sql.
-- =====================================================================

USE db_akademik;

-- Tabel login (opsional, dikerjakan Anggota 1 bila sempat)
CREATE TABLE IF NOT EXISTS users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  peran ENUM('admin', 'dosen') NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contoh membuat hash password:
--   php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
-- INSERT INTO users (username, password_hash, nama) VALUES ('admin', '<hash>', 'Admin Prodi');
