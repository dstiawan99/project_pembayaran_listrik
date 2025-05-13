-- Dump Database Aplikasi Pembayaran Listrik
-- Version 1.0

CREATE DATABASE IF NOT EXISTS pembayaran_listrik;
USE pembayaran_listrik;

-- Struktur Tabel

CREATE TABLE IF NOT EXISTS user_level (
  id_level INT AUTO_INCREMENT PRIMARY KEY,
  nama_level VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS user (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(100) NOT NULL,
  nama_admin VARCHAR(100) NOT NULL,
  id_level INT NOT NULL,
  FOREIGN KEY (id_level) REFERENCES user_level(id_level) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS tarif (
  id_tarif INT AUTO_INCREMENT PRIMARY KEY,
  daya INT NOT NULL,
  tarifperkwh DECIMAL(10,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS pelanggan (
  id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(100) NOT NULL,
  nomor_kwh VARCHAR(20) NOT NULL,
  nama_pelanggan VARCHAR(100) NOT NULL,
  alamat TEXT NOT NULL,
  id_tarif INT NOT NULL,
  FOREIGN KEY (id_tarif) REFERENCES tarif(id_tarif) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS penggunaan (
  id_penggunaan INT AUTO_INCREMENT PRIMARY KEY,
  id_pelanggan INT NOT NULL,
  bulan VARCHAR(2) NOT NULL,
  tahun VARCHAR(4) NOT NULL,
  meter_awal INT NOT NULL,
  meter_akhir INT NOT NULL,
  FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tagihan (
  id_tagihan INT AUTO_INCREMENT PRIMARY KEY,
  id_penggunaan INT NOT NULL,
  id_pelanggan INT NOT NULL,
  bulan VARCHAR(2) NOT NULL,
  tahun VARCHAR(4) NOT NULL,
  jumlah_meter INT NOT NULL,
  status ENUM('belum_bayar', 'lunas') NOT NULL DEFAULT 'belum_bayar',
  FOREIGN KEY (id_penggunaan) REFERENCES penggunaan(id_penggunaan) ON DELETE CASCADE,
  FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS pembayaran (
  id_pembayaran INT AUTO_INCREMENT PRIMARY KEY,
  id_tagihan INT NOT NULL,
  tanggal_pembayaran DATE NOT NULL,
  bulan_bayar VARCHAR(2) NOT NULL,
  biaya_admin DECIMAL(10,2) NOT NULL DEFAULT 2500.00,
  total_bayar DECIMAL(10,2) NOT NULL,
  id_user INT NOT NULL,
  FOREIGN KEY (id_tagihan) REFERENCES tagihan(id_tagihan) ON DELETE CASCADE,
  FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE RESTRICT
);

-- Data Awal

-- Level User
INSERT INTO user_level (nama_level) VALUES 
('admin'),
('pelanggan');

-- User Admin
INSERT INTO user (username, password, nama_admin, id_level) VALUES 
('admin', MD5('admin123'), 'Administrator', 1);

-- Tarif Listrik
INSERT INTO tarif (daya, tarifperkwh) VALUES 
(450, 415.00),
(900, 605.00),
(1300, 790.00),
(2200, 1210.00),
(3500, 1440.00),
(5500, 1699.00);

-- Pelanggan Contoh
INSERT INTO pelanggan (username, password, nomor_kwh, nama_pelanggan, alamat, id_tarif) VALUES 
('pelanggan1', MD5('12345'), '12345678901', 'Budi Santoso', 'Jl. Merdeka No. 123, Jakarta', 2),
('pelanggan2', MD5('12345'), '23456789012', 'Siti Aminah', 'Jl. Pahlawan No. 45, Bandung', 3),
('pelanggan3', MD5('12345'), '34567890123', 'Ahmad Hidayat', 'Jl. Sudirman No. 67, Surabaya', 4);

-- Contoh Penggunaan
INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir) VALUES 
(1, '01', '2023', 0, 120),
(1, '02', '2023', 120, 250),
(2, '01', '2023', 0, 150),
(3, '01', '2023', 0, 200);

-- Contoh Tagihan
INSERT INTO tagihan (id_penggunaan, id_pelanggan, bulan, tahun, jumlah_meter, status) VALUES 
(1, 1, '01', '2023', 120, 'lunas'),
(2, 1, '02', '2023', 130, 'belum_bayar'),
(3, 2, '01', '2023', 150, 'lunas'),
(4, 3, '01', '2023', 200, 'belum_bayar');

-- Contoh Pembayaran
INSERT INTO pembayaran (id_tagihan, tanggal_pembayaran, bulan_bayar, biaya_admin, total_bayar, id_user) VALUES 
(1, '2023-01-25', '01', 2500.00, 75100.00, 1),
(3, '2023-01-27', '01', 2500.00, 121000.00, 1);