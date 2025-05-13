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

├───admin
├───assets
│ ├───css
│ ├───img
│ ├───js
│ │ └───demo
│ ├───scss
│ │ ├───navs
│ │ └───utilities
│ └───vendor
│ ├───bootstrap
│ │ ├───js
│ │ └───scss
│ │ ├───mixins
│ │ ├───utilities
│ │ └───vendor
│ ├───chart.js
│ ├───datatables
│ ├───fontawesome-free
│ │ ├───css
│ │ ├───js
│ │ ├───less
│ │ ├───metadata
│ │ ├───scss
│ │ ├───sprites
│ │ ├───svgs
│ │ │ ├───brands
│ │ │ ├───regular
│ │ │ └───solid
│ │ └───webfonts
│ ├───jquery
│ └───jquery-easing
├───cetak
├───config
├───database
├───pages
├───pelanggan
├───proses
└───template

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
