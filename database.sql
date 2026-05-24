-- ============================================================
-- SISTEM INFORMASI PUSKESMAS
-- Murni PHP 7.4 Native + MySQL + HTML + CSS + JavaScript
-- ============================================================

CREATE DATABASE IF NOT EXISTS puskesmas_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE puskesmas_db;

CREATE TABLE admin (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  username    VARCHAR(50)  NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,
  nama        VARCHAR(100) NOT NULL,
  email       VARCHAR(100),
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Password default: admin123
INSERT INTO admin (username, password, nama) VALUES
('admin','admin123','Administrator');

CREATE TABLE poli (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  nama_poli        VARCHAR(100) NOT NULL,
  deskripsi        TEXT,
  dokter           VARCHAR(100),
  jadwal           VARCHAR(100),
  jam_operasional  VARCHAR(50),
  kuota_per_sesi   INT DEFAULT 20,
  status           ENUM('aktif','nonaktif') DEFAULT 'aktif',
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO poli (nama_poli, deskripsi, dokter, jadwal, jam_operasional) VALUES
('Poli Umum',   'Pemeriksaan kesehatan umum dan konsultasi dokter',       'dr. Ahmad Sudirman',       'Senin - Sabtu', '08:00 - 15:00'),
('Poli Gigi',   'Perawatan gigi dan mulut oleh dokter gigi profesional',  'drg. Siti Nurhaliza',      'Senin - Jumat', '08:00 - 14:00'),
('Poli KIA/KB', 'Kesehatan ibu, anak, dan layanan keluarga berencana',    'dr. Dewi Kartika, Sp.OG', 'Senin - Jumat', '08:00 - 14:00'),
('Poli Anak',   'Pemeriksaan tumbuh kembang anak dan imunisasi',          'dr. Budi Santoso, Sp.A',  'Senin - Jumat', '08:00 - 12:00'),
('Poli Lansia', 'Pemeriksaan kesehatan rutin dan kontrol tekanan darah',  'dr. Hendra Wijaya',       'Selasa, Kamis', '08:00 - 12:00'),
('Poli Gizi',   'Konsultasi gizi dan program diet sehat',                 'Rina Amelia, S.Gz',       'Rabu, Jumat',   '09:00 - 13:00');

CREATE TABLE pendaftaran (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  kode_daftar       VARCHAR(30)  NOT NULL UNIQUE,
  nama_lengkap      VARCHAR(100) NOT NULL,
  nik               VARCHAR(16)  NOT NULL,
  tanggal_lahir     DATE         NOT NULL,
  jenis_kelamin     ENUM('Laki-laki','Perempuan') NOT NULL,
  no_telepon        VARCHAR(15)  NOT NULL,
  alamat            TEXT         NOT NULL,
  jenis_jaminan     ENUM('BPJS','Umum','Asuransi Lain') DEFAULT 'Umum',
  no_jaminan        VARCHAR(50),
  poli_id           INT          NOT NULL,
  tanggal_kunjungan DATE         NOT NULL,
  sesi              ENUM('Sesi 1 (08:00-10:00)','Sesi 2 (10:00-12:00)','Sesi 3 (13:00-15:00)') NOT NULL,
  nomor_antrian     INT,
  status            ENUM('Menunggu','Dikonfirmasi','Selesai','Dibatalkan') DEFAULT 'Menunggu',
  catatan           TEXT,
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (poli_id) REFERENCES poli(id)
);
CREATE INDEX idx_nik  ON pendaftaran(nik);
CREATE INDEX idx_kode ON pendaftaran(kode_daftar);