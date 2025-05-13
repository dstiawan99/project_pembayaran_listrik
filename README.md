# Aplikasi Pembayaran Listrik Pascabayar

Aplikasi web pembayaran listrik pascabayar menggunakan PHP Native dan template SB Admin 2.

## Kebutuhan Sistem

- PHP 7.0 atau lebih tinggi
- MySQL 5.6 atau lebih tinggi
- Web server Apache/Nginx

## Instalasi

1. Clone atau download repositori ini
2. Buat database baru di MySQL
3. Import file database dari folder `database/pembayaran_listrik.sql`
4. Konfigurasi koneksi database di file `config/koneksi.php`
5. Akses aplikasi melalui web browser

## Akun Default

- Admin:
  - Username: admin
  - Password: admin123

## Fitur

- Dashboard admin dan pelanggan
- Manajemen data pelanggan
- Manajemen tarif listrik
- Input penggunaan listrik
- Generate tagihan
- Pembayaran tagihan
- Cetak invoice dan laporan

## Struktur Project

Folder PATH listing

project_pembayaran_listrik
│ index.php
│ logout.php
│ README.md
│
├───admin
│ index.php
│ pelanggan.php
│ pembayaran.php
│ penggunaan.php
│ tagihan.php
│ tarif.php
│ user.php
│
├───assets
│ ├───css
│ │ custom.css
│ │
│ ├───img
│ ├───js
│ └───vendor
├───cetak
│ invoice.php
│ laporan_pembayaran.php
│
├───config
│ function.php
│ koneksi.php
│
├───database
├───pages
├───pelanggan
│ index.php
│ pembayaran.php
│ penggunaan.php
│ tagihan.php
│
├───proses
│ pelanggan_proses.php
│ pembayaran_proses.php
│ penggunaan_proses.php
│ tagihan_proses.php
│ tarif_proses.php
│ user_proses.php
│
└───template
footer.php
header.php

## Pengembangan

Aplikasi ini dibuat menggunakan:

- PHP Native
- MySQL
- Bootstrap 4
- SB Admin 2 Template
- jQuery
- DataTables
- Chart.js

## Lisensi

MIT License
