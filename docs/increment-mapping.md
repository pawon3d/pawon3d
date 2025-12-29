# Pemetaan Increment Sistem Pawon3D

## Pendahuluan

Dokumen ini menyajikan pemetaan fitur dan modul Sistem Manajemen Toko Kue Pawon3D ke dalam dua tahapan pengembangan berdasarkan metode incremental. Pembagian increment didasarkan pada tingkat kepentingan fungsionalitas, kompleksitas teknis, serta ketergantungan antar modul. Increment 1 mencakup fondasi sistem dan fungsionalitas inti penjualan, sementara Increment 2 meliputi fitur-fitur pendukung operasional yang lebih kompleks.

---

## Increment 1: Modul Dasar dan Penjualan

Increment 1 mencakup modul-modul fundamental yang menjadi fondasi operasional sistem. Komponen-komponen pada tahap ini memungkinkan pengguna untuk mengakses sistem, mengelola data master, serta melaksanakan transaksi penjualan dasar.

### 1.1 Modul Autentikasi dan Otorisasi

| Komponen | Deskripsi |
|----------|-----------|
| Login | Proses autentikasi pengguna ke dalam sistem |
| Aktivasi Akun | Aktivasi akun baru melalui tautan undangan via email |
| Manajemen Peran | Pengelolaan role dan permission untuk kontrol akses |
| Manajemen Pengguna | Pengelolaan data pekerja/karyawan |

### 1.2 Modul Halaman Publik

| Komponen | Deskripsi |
|----------|-----------|
| Landing Page | Halaman utama yang menampilkan informasi toko |
| Katalog Produk | Daftar produk yang dapat diakses publik |
| Detail Produk | Informasi lengkap satu produk |
| FAQ | Halaman pertanyaan yang sering diajukan |

### 1.3 Modul Data Master

| Komponen | Deskripsi |
|----------|-----------|
| Kategori Produk | Pengelolaan kategori untuk mengelompokkan produk |
| Produk | Pengelolaan data produk termasuk harga dan gambar |
| Satuan Ukur | Pengelolaan satuan dengan sistem konversi otomatis |
| Jenis Biaya | Pengelolaan jenis-jenis biaya operasional |

### 1.4 Modul Transaksi Penjualan (Point of Sale)

| Komponen | Deskripsi |
|----------|-----------|
| Pesanan Reguler | Pembuatan pesanan produk reguler |
| Pesanan Kotak | Pembuatan pesanan dalam bentuk paket/kotak |
| Siap Beli | Penjualan produk yang sudah tersedia stok |
| Manajemen Shift | Pembukaan dan penutupan sesi penjualan kasir |
| Pembayaran | Pemrosesan pembayaran dengan multi-channel |
| Struk Digital | Pencetakan struk transaksi |

### 1.5 Modul Pelanggan

| Komponen | Deskripsi |
|----------|-----------|
| Data Pelanggan | Pengelolaan informasi data pelanggan |
| Sistem Poin Loyalitas | Akumulasi dan penggunaan poin pelanggan |
| Riwayat Transaksi | Pencatatan histori transaksi per pelanggan |

### 1.6 Modul Dashboard Dasar

| Komponen | Deskripsi |
|----------|-----------|
| Ringkasan Umum | Tampilan ringkasan data operasional harian |
| Notifikasi | Sistem pemberitahuan untuk pengguna |

---

## Increment 2: Modul Inventori dan Produksi

Increment 2 mencakup modul-modul yang mendukung pengelolaan persediaan bahan baku, proses produksi, serta pelaporan. Komponen-komponen pada tahap ini memerlukan fondasi dari Increment 1 dan memiliki kompleksitas teknis yang lebih tinggi.

### 2.1 Modul Bahan Baku dan Inventori

| Komponen | Deskripsi |
|----------|-----------|
| Bahan Baku | Pengelolaan data bahan baku/material |
| Batch Material | Pencatatan stok bahan per batch dengan tanggal kedaluwarsa |
| Kategori Persediaan | Pengelompokan bahan baku berdasarkan kategori |
| Konversi Satuan | Sistem konversi otomatis antar satuan ukur |
| Komposisi Produk | Definisi resep/bahan penyusun tiap produk |
| Status Stok | Pemantauan status ketersediaan bahan (kosong, menipis, tersedia) |

### 2.2 Modul Supplier

| Komponen | Deskripsi |
|----------|-----------|
| Data Supplier | Pengelolaan informasi pemasok bahan baku |
| Kontak Supplier | Penyimpanan informasi kontak dan lokasi supplier |

### 2.3 Modul Belanja (Expense)

| Komponen | Deskripsi |
|----------|-----------|
| Perencanaan Belanja | Pembuatan rencana pembelian bahan baku |
| Pelaksanaan Belanja | Pencatatan realisasi pembelian aktual |
| Riwayat Belanja | Histori seluruh transaksi pembelian |
| Perbandingan Harga | Perbandingan harga ekspektasi dengan harga aktual |

### 2.4 Modul Produksi

| Komponen | Deskripsi |
|----------|-----------|
| Rencana Produksi | Pembuatan jadwal dan target produksi |
| Produksi Pesanan | Eksekusi produksi berdasarkan pesanan pelanggan |
| Produksi Siap Beli | Produksi produk untuk stok penjualan langsung |
| Antrian Produksi | Pengelolaan urutan prioritas produksi |
| Pekerja Produksi | Pencatatan pekerja yang terlibat dalam produksi |
| Pengurangan Stok Otomatis | Pengurangan bahan baku secara otomatis berdasarkan resep |

### 2.5 Modul Stock Opname (Hitung)

| Komponen | Deskripsi |
|----------|-----------|
| Perencanaan Hitung | Pembuatan jadwal penghitungan stok |
| Pelaksanaan Hitung | Pencatatan hasil penghitungan fisik |
| Penyesuaian Stok | Koreksi selisih antara stok sistem dan stok fisik |
| Riwayat Hitung | Histori seluruh aktivitas stock opname |

### 2.6 Modul Alur Persediaan

| Komponen | Deskripsi |
|----------|-----------|
| Log Inventori | Pencatatan seluruh pergerakan stok bahan |
| Traceback | Penelusuran asal-usul dan penggunaan bahan |

### 2.7 Modul Refund

| Komponen | Deskripsi |
|----------|-----------|
| Pengembalian Dana | Pemrosesan refund transaksi |
| Bukti Refund | Dokumentasi bukti pengembalian |

### 2.8 Modul Laporan

| Komponen | Deskripsi |
|----------|-----------|
| Laporan Kasir | Laporan penjualan dan shift kasir |
| Laporan Produksi | Laporan hasil dan efisiensi produksi |
| Laporan Inventori | Laporan pergerakan dan status stok |
| Export PDF | Ekspor laporan dalam format PDF |
| Export Excel | Ekspor laporan dalam format Excel |

### 2.9 Modul Pengaturan Lanjutan

| Komponen | Deskripsi |
|----------|-----------|
| Profil Usaha | Pengelolaan informasi bisnis dan jam operasional |
| Metode Pembayaran | Konfigurasi channel pembayaran yang tersedia |
| Dokumen Toko | Pengelolaan dokumen-dokumen usaha |

---

## Rasional Pembagian Increment

### Kriteria Increment 1

1. **Fondasi Sistem**: Komponen autentikasi dan otorisasi merupakan prasyarat untuk seluruh fungsionalitas lainnya.
2. **Operasional Inti**: Transaksi penjualan merupakan fungsi utama yang menghasilkan pendapatan usaha.
3. **Kompleksitas Rendah-Menengah**: Modul-modul pada Increment 1 memiliki alur proses yang relatif sederhana.
4. **Ketergantungan Minimal**: Dapat berfungsi secara independen tanpa memerlukan modul Increment 2.

### Kriteria Increment 2

1. **Ketergantungan pada Increment 1**: Produksi membutuhkan data produk, belanja membutuhkan supplier, laporan membutuhkan data transaksi.
2. **Kompleksitas Tinggi**: Sistem batch, konversi satuan, dan pengurangan stok otomatis memerlukan logika bisnis yang kompleks.
3. **Fungsi Pendukung**: Bersifat mendukung optimalisasi operasional, bukan fungsi inti penghasil pendapatan.
4. **Integrasi Multi-Modul**: Memerlukan koordinasi antara beberapa entitas data sekaligus.

---

## Diagram Ketergantungan Modul

```
Increment 1                          Increment 2
┌─────────────────┐                 ┌────────────────────┐
│  Autentikasi    │◄────────────────┤  Semua Modul       │
│  & Otorisasi    │                 │  Increment 2       │
└────────┬────────┘                 └────────────────────┘
         │
         ▼
┌─────────────────┐                 ┌────────────────────┐
│  Data Master    │◄────────────────┤  Bahan Baku        │
│  (Kategori,     │                 │  (Material, Batch) │
│   Produk,       │                 └────────┬───────────┘
│   Satuan)       │                          │
└────────┬────────┘                          ▼
         │                          ┌────────────────────┐
         ▼                          │  Supplier          │
┌─────────────────┐                 └────────┬───────────┘
│  Transaksi      │                          │
│  Penjualan      │◄──────────────────┐      ▼
└────────┬────────┘                   │ ┌────────────────────┐
         │                            │ │  Belanja           │
         ▼                            │ └────────┬───────────┘
┌─────────────────┐                   │          │
│  Shift Kasir    │                   │          ▼
└────────┬────────┘                   │ ┌────────────────────┐
         │                            │ │  Produksi          │
         ▼                            │ └────────┬───────────┘
┌─────────────────┐                   │          │
│  Pelanggan &    │                   │          ▼
│  Poin Loyalitas │                   │ ┌────────────────────┐
└─────────────────┘                   │ │  Stock Opname      │
                                      │ └────────┬───────────┘
                                      │          │
                                      │          ▼
                                      │ ┌────────────────────┐
                                      └─┤  Laporan           │
                                        └────────────────────┘
```

---

## Kesimpulan

Pemetaan increment pada sistem Pawon3D disusun berdasarkan prinsip ketergantungan modul dan prioritas fungsionalitas bisnis. Increment 1 menyediakan fondasi operasional yang memungkinkan toko beroperasi secara dasar, sedangkan Increment 2 melengkapi sistem dengan kemampuan pengelolaan inventori, produksi, dan pelaporan yang komprehensif. Pembagian ini memastikan bahwa setiap tahapan pengembangan menghasilkan sistem yang dapat dioperasikan dan memberikan nilai tambah bagi pengguna.
