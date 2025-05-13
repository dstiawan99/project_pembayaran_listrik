CREATE DATABASE pembayaran_listrik;
USE pembayaran_listrik;

CREATE TABLE penggunaan (
  id_penggunaan INT AUTO_INCREMENT PRIMARY KEY,
  id_pelanggan INT,
  bulan VARCHAR(20),
  tahun VARCHAR(4),
  meter_awal INT,
  meter_akhir INT
);

CREATE TABLE pelanggan (
  id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50),
  password VARCHAR(100),
  nomor_kwh VARCHAR(20),
  nama_pelanggan VARCHAR(100),
  alamat TEXT,
  id_tarif INT
);

CREATE TABLE tagihan (
  id_tagihan INT AUTO_INCREMENT PRIMARY KEY,
  id_penggunaan INT,
  id_pelanggan INT,
  bulan VARCHAR(20),
  tahun VARCHAR(4),
  jumlah_meter INT,
  status VARCHAR(20) DEFAULT 'belum_bayar'
);

CREATE TABLE tarif (
  id_tarif INT AUTO_INCREMENT PRIMARY KEY,
  daya INT,
  tarifperkwh DECIMAL(10,2)
);

CREATE TABLE user (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50),
  password VARCHAR(100),
  nama_admin VARCHAR(100),
  id_level INT
);

CREATE TABLE user_level (
  id_level INT AUTO_INCREMENT PRIMARY KEY,
  nama_level VARCHAR(50)
);

CREATE TABLE pembayaran (
  id_pembayaran INT AUTO_INCREMENT PRIMARY KEY,
  id_tagihan INT,
  tanggal_pembayaran DATE,
  bulan_bayar VARCHAR(20),
  biaya_admin DECIMAL(10,2),
  total_bayar DECIMAL(10,2),
  id_user INT
);

-- Data awal
INSERT INTO user_level (nama_level) VALUES ('admin'), ('pelanggan');
INSERT INTO user (username, password, nama_admin, id_level) VALUES ('admin', MD5('admin123'), 'Administrator', 1);
INSERT INTO tarif (daya, tarifperkwh) VALUES (450, 415), (900, 605), (1300, 790), (2200, 1210);