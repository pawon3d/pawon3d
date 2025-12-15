# Pemetaan Increment - Sistem Pawon3D

## Pendahuluan

Dokumen ini menyajikan pemetaan fitur sistem Pawon3D ke dalam dua increment berdasarkan metode pengembangan perangkat lunak incremental. Pembagian increment dilakukan berdasarkan urgensi fungsionalitas, tingkat ketergantungan antar modul, dan rencana pengembangan awal sistem.

---

## Increment 1: Fungsionalitas Inti Operasional

Increment pertama mencakup modul-modul inti yang diperlukan untuk menjalankan operasi dasar toko kue. Modul-modul pada increment ini merupakan fondasi yang memungkinkan proses bisnis utama berjalan secara lengkap dari hulu ke hilir.

### 1.1 Manajemen Kategori dan Satuan Ukur

Modul ini menyediakan pengelolaan data master kategori produk serta satuan ukur yang digunakan dalam sistem. Fitur konversi satuan otomatis memungkinkan perhitungan kuantitas bahan baku dengan satuan berbeda dalam satu grup konversi.

**Komponen:**
- Kategori produk dan kategori bahan baku (ingredient category)
- Satuan ukur dengan sistem konversi hierarkis
- Jenis biaya (type cost) untuk kategorisasi biaya tambahan

### 1.2 Manajemen Supplier

Modul ini menangani pengelolaan data pemasok bahan baku. Informasi supplier mencakup identitas, kontak, dan lokasi lengkap untuk keperluan operasional pembelanjaan.

**Komponen:**
- Data identitas dan kontak supplier
- Informasi alamat dan lokasi
- Riwayat transaksi pembelanjaan

### 1.3 Manajemen Bahan Baku

Modul ini mengelola data bahan baku yang digunakan dalam produksi. Sistem mengimplementasikan manajemen batch dengan metode FIFO (First In First Out) untuk memastikan rotasi stok yang optimal dan pelacakan tanggal kedaluwarsa.

**Komponen:**
- Data bahan baku (material) dengan status ketersediaan
- Batch bahan baku dengan quantity dan tanggal kedaluwarsa
- Sistem konversi satuan otomatis antar batch
- Pengurangan stok otomatis saat produksi

### 1.4 Manajemen Belanja (Expense)

Modul ini menangani proses pengadaan bahan baku dari supplier. Sistem mendukung alur perencanaan belanja hingga pencatatan detail pembelian per item.

**Komponen:**
- Rencana belanja dengan daftar item
- Pencatatan belanja aktual dengan detail harga dan kuantitas
- Pembaruan batch bahan baku secara otomatis
- Riwayat belanja untuk pelaporan

### 1.5 Manajemen Produk dan Komposisi

Modul ini mengelola katalog produk beserta komposisi bahan baku yang diperlukan untuk memproduksi setiap produk. Data komposisi menjadi dasar perhitungan kebutuhan bahan dan harga modal.

**Komponen:**
- Data produk dengan kategori dan harga
- Komposisi produk (product composition) dengan kuantitas bahan
- Biaya tambahan per produk (other costs)
- Perhitungan harga modal otomatis

### 1.6 Sistem Produksi

Modul ini menangani proses produksi dari penerimaan pesanan hingga penyelesaian produksi. Sistem mendukung dua alur produksi: produksi berdasarkan pesanan dan produksi untuk stok siap beli.

**Komponen:**
- Produksi pesanan (dari transaksi kotak/reguler)
- Produksi siap beli (untuk stok)
- Detail produksi per produk
- Penugasan pekerja produksi
- Pengurangan bahan baku otomatis

### 1.7 Sistem Transaksi/Kasir (POS)

Modul ini merupakan sistem Point of Sale untuk menangani transaksi penjualan. Terdapat tiga metode transaksi yang didukung sesuai karakteristik pesanan.

**Komponen dan Metode Transaksi:**
- **Pesanan Kotak (OK)**: Pesanan dalam jumlah besar dengan kemasan kotak
- **Pesanan Reguler (OR)**: Pesanan satuan dengan waktu pengerjaan
- **Siap Beli (OS)**: Penjualan produk yang tersedia di stok

**Fitur Transaksi:**
- Pembuatan pesanan dengan detail produk
- Sistem pembayaran multi-channel
- Penomoran invoice otomatis
- Pencetakan struk dan nota

### 1.8 Landing Page Publik

Modul ini menyediakan antarmuka publik untuk menampilkan katalog produk kepada pelanggan. Halaman ini dapat diakses tanpa autentikasi.

**Komponen:**
- Halaman utama dengan informasi toko
- Katalog produk dengan detail
- Halaman FAQ (Frequently Asked Questions)

---

## Increment 2: Fungsionalitas Pendukung dan Pengembangan

Increment kedua mencakup modul-modul pendukung yang meningkatkan kapabilitas sistem serta fitur-fitur yang berkembang setelah perencanaan awal.

### 2.1 Manajemen Pengguna dan Peran

Modul ini menangani autentikasi dan otorisasi pengguna dengan sistem peran berbasis permission yang granular.

**Komponen:**
- Manajemen data pengguna dengan status aktif
- Sistem undangan dan aktivasi akun via email
- Peran (role) dengan konfigurasi permission
- Pembatasan jumlah pengguna per peran

### 2.2 Manajemen Pelanggan dan Sistem Poin

Modul ini mengelola data pelanggan beserta sistem loyalitas berbasis poin untuk mendorong pembelian berulang.

**Komponen:**
- Data pelanggan dengan riwayat transaksi
- Akumulasi poin dari transaksi
- Penggunaan poin sebagai diskon
- Riwayat perubahan poin

### 2.3 Stock Opname/Inventarisasi (Hitung)

Modul ini menyediakan fitur penghitungan stok fisik untuk rekonsiliasi data inventori dengan kondisi aktual.

**Komponen:**
- Perencanaan hitung stok
- Pencatatan hasil penghitungan per item
- Penyesuaian stok berdasarkan hasil hitung
- Riwayat stock opname

### 2.4 Dashboard dan Pelaporan

Modul ini menyediakan ringkasan operasional dan laporan terstruktur untuk keperluan analisis dan dokumentasi.

**Komponen:**
- Dashboard ringkasan umum
- Laporan kasir dengan statistik transaksi
- Laporan produksi dengan rekap output
- Laporan inventori dengan status stok
- Ekspor laporan ke format PDF dan Excel

### 2.5 Notifikasi

Modul ini menangani pemberitahuan kepada pengguna terkait aktivitas dan kondisi penting dalam sistem.

**Komponen:**
- Notifikasi sistem untuk berbagai event
- Status baca notifikasi
- Pengelompokan berdasarkan tipe

### 2.6 Log Aktivitas

Modul ini mencatat seluruh aktivitas perubahan data untuk keperluan audit dan pelacakan.

**Komponen:**
- Pencatatan otomatis create, update, delete
- Identifikasi pengguna pelaku perubahan
- Detail perubahan field

### 2.7 Pengaturan Sistem

Modul ini menyediakan konfigurasi profil usaha dan metode pembayaran yang diterima.

**Komponen:**
- Profil usaha (nama, alamat, kontak)
- Dokumen usaha
- Metode pembayaran (payment channel)
- Manajemen shift kasir

---

## Rasionalisasi Pembagian Increment

Pembagian modul ke dalam dua increment didasarkan pada pertimbangan berikut:

1. **Ketergantungan Fungsional**: Modul Increment 1 membentuk alur operasional lengkap dari pengadaan bahan hingga penjualan. Modul Increment 2 merupakan fitur pendukung yang dapat ditambahkan tanpa mengganggu operasi inti.

2. **Prioritas Bisnis**: Increment 1 memenuhi kebutuhan esensial operasional toko kue. Increment 2 menambahkan fitur manajemen dan analitik yang meningkatkan efisiensi.

3. **Kompleksitas Teknis**: Sistem peran-permission dan pelaporan pada Increment 2 memerlukan fondasi data yang dihasilkan oleh modul-modul Increment 1.

4. **Rencana Pengembangan Awal**: Pembagian ini konsisten dengan rencana pengembangan awal dalam proposal skripsi, dengan fitur tambahan yang berkembang dikategorikan ke Increment 2.
