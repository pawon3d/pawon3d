# Activity Diagram - Sistem Pawon3D

## Pendahuluan

Activity diagram menggambarkan alur kerja (workflow) dari proses bisnis dalam sistem. Diagram ini menunjukkan urutan aktivitas, keputusan, dan percabangan yang terjadi dalam setiap proses.

---

## Increment 1: Activity Diagram Fungsionalitas Inti

### 1. Kelola Kategori dan Satuan

**Referensi:** `puml/activity-kelola-kategori.puml`

**Deskripsi:**
Proses pengelolaan data master kategori dan satuan ukur. Pengguna dapat menambah, mengubah, atau menghapus data dengan validasi duplikasi nama.

**Alur Utama:**
1. Pengguna membuka halaman kategori/satuan
2. Sistem menampilkan daftar data
3. Pengguna memilih aksi (tambah/ubah/hapus)
4. Sistem memvalidasi input
5. Sistem menyimpan perubahan ke database
6. Sistem mencatat log aktivitas

---

### 2. Kelola Supplier

**Referensi:** `puml/activity-kelola-supplier.puml`

**Deskripsi:**
Proses pengelolaan data supplier bahan baku. Mencakup pencatatan informasi kontak dan alamat lengkap supplier.

**Alur Utama:**
1. Pengguna mengakses halaman supplier
2. Sistem menampilkan daftar supplier
3. Pengguna memilih tambah/ubah supplier
4. Pengguna mengisi formulir data supplier
5. Sistem memvalidasi kelengkapan data
6. Sistem menyimpan data supplier
7. Sistem mencatat log aktivitas

---

### 3. Kelola Bahan Baku

**Referensi:** `puml/activity-kelola-bahan-baku.puml`

**Deskripsi:**
Proses pengelolaan data bahan baku. Sistem menghitung status ketersediaan berdasarkan batch yang ada.

**Alur Utama:**
1. Pengguna mengakses halaman bahan baku
2. Sistem menampilkan daftar bahan dengan status
3. Pengguna memilih tambah/ubah bahan baku
4. Pengguna mengisi data bahan dan minimum stok
5. Sistem memvalidasi data
6. Sistem menyimpan dan menghitung status bahan
7. Sistem mencatat log aktivitas

---

### 4. Proses Belanja Bahan Baku

**Referensi:** `puml/activity-belanja.puml`

**Deskripsi:**
Proses pengadaan bahan baku dari tahap perencanaan hingga pencatatan hasil belanja.

**Alur Utama:**
1. Pengguna membuat rencana belanja
2. Pengguna memilih supplier dan item bahan
3. Sistem menyimpan rencana belanja
4. Pengguna memulai proses belanja
5. Pengguna mencatat harga dan kuantitas aktual
6. Sistem membuat batch baru untuk setiap bahan
7. Sistem memperbarui status ketersediaan bahan
8. Sistem mencatat log aktivitas

**Percabangan:**
- Jika kuantitas tidak sesuai rencana, pengguna dapat menyesuaikan
- Jika supplier tidak tersedia, pengguna dapat mengubah supplier

---

### 5. Kelola Produk dan Komposisi

**Referensi:** `puml/activity-kelola-produk.puml`

**Deskripsi:**
Proses pengelolaan katalog produk beserta komposisi bahan baku dan biaya tambahan.

**Alur Utama:**
1. Pengguna mengakses halaman produk
2. Sistem menampilkan daftar produk dengan harga modal
3. Pengguna memilih tambah/ubah produk
4. Pengguna mengisi data produk
5. Pengguna mengatur komposisi bahan
6. Pengguna mengatur biaya tambahan (opsional)
7. Sistem menghitung harga modal
8. Sistem menyimpan data produk
9. Sistem mencatat log aktivitas

---

### 6. Proses Produksi

**Referensi:** `puml/activity-produksi.puml`

**Deskripsi:**
Proses produksi dari antrian pesanan atau pembuatan stok siap beli hingga penyelesaian.

**Alur Utama - Produksi Pesanan:**
1. Sistem menampilkan antrian pesanan yang perlu diproduksi
2. Pengguna memilih pesanan untuk diproduksi
3. Pengguna memilih pekerja yang bertugas
4. Sistem memverifikasi ketersediaan bahan
5. Pengguna memulai produksi
6. Sistem mengurangi stok bahan secara FIFO
7. Pengguna menyelesaikan produksi
8. Sistem memperbarui status transaksi
9. Sistem mencatat log aktivitas

**Alur Utama - Produksi Siap Beli:**
1. Pengguna membuat produksi siap beli baru
2. Pengguna memilih produk dan kuantitas
3. Sistem memverifikasi ketersediaan bahan
4. Pengguna memulai produksi
5. Sistem mengurangi stok bahan secara FIFO
6. Pengguna menyelesaikan produksi
7. Sistem menambah stok produk siap jual
8. Sistem mencatat log aktivitas

**Percabangan:**
- Jika bahan tidak cukup, sistem menampilkan peringatan
- Pengguna dapat membatalkan atau melanjutkan dengan penyesuaian

---

### 7. Proses Transaksi/Penjualan

**Referensi:** `puml/activity-transaksi.puml`

**Deskripsi:**
Proses transaksi penjualan dari pembuatan pesanan hingga pembayaran selesai.

**Alur Utama - Pesanan Kotak/Reguler:**
1. Kasir memilih metode transaksi
2. Kasir memasukkan data pelanggan (opsional)
3. Kasir memilih produk dan kuantitas
4. Sistem menghitung total harga
5. Sistem membuat transaksi dengan status "menunggu"
6. Transaksi masuk ke antrian produksi
7. Setelah produksi selesai, kasir memproses pembayaran
8. Kasir memilih metode pembayaran
9. Sistem mencatat pembayaran
10. Sistem mencetak struk
11. Sistem mencatat log aktivitas

**Alur Utama - Siap Beli:**
1. Kasir memilih tanggal produksi
2. Kasir memilih produk dari stok tersedia
3. Sistem menghitung total harga
4. Kasir memasukkan data pelanggan (opsional)
5. Kasir memproses pembayaran
6. Sistem mengurangi stok produk
7. Sistem mencetak struk
8. Sistem mencatat log aktivitas

**Percabangan:**
- Jika menggunakan poin, sistem menghitung diskon
- Jika pembayaran kurang, sistem menolak transaksi

---

## Increment 2: Activity Diagram Fungsionalitas Pendukung

### 8. Kelola Pengguna dan Peran

**Referensi:** `puml/activity-kelola-user.puml`

**Deskripsi:**
Proses pengelolaan pengguna sistem melalui mekanisme undangan email dan aktivasi akun.

**Alur Utama:**
1. Admin mengakses halaman pekerja
2. Admin menambahkan user baru dengan email dan peran
3. Sistem mengirimkan email undangan
4. User baru mengakses link aktivasi
5. User mengatur password
6. Sistem mengaktifkan akun
7. User dapat login ke sistem

---

### 9. Kelola Pelanggan dan Poin

**Referensi:** `puml/activity-kelola-pelanggan.puml`

**Deskripsi:**
Proses pengelolaan data pelanggan dan sistem poin loyalitas.

**Alur Utama:**
1. Sistem mencatat pelanggan otomatis saat transaksi
2. Sistem menambahkan poin berdasarkan nilai transaksi
3. Kasir dapat melihat riwayat transaksi pelanggan
4. Kasir dapat menggunakan poin saat pembayaran
5. Sistem mengurangi poin yang digunakan

---

### 10. Stock Opname (Hitung)

**Referensi:** `puml/activity-stock-opname.puml`

**Deskripsi:**
Proses penghitungan stok fisik untuk rekonsiliasi data inventori.

**Alur Utama:**
1. Pengguna membuat rencana hitung
2. Pengguna memilih bahan yang akan dihitung
3. Pengguna memulai proses hitung
4. Pengguna mencatat kuantitas aktual per item
5. Sistem membandingkan dengan data sistem
6. Sistem menyesuaikan batch berdasarkan hasil hitung
7. Sistem mencatat log inventori

---

### 11. Pembuatan Laporan

**Referensi:** `puml/activity-laporan.puml`

**Deskripsi:**
Proses pembuatan dan ekspor laporan operasional.

**Alur Utama:**
1. Pengguna memilih jenis laporan
2. Pengguna mengatur filter periode
3. Sistem mengumpulkan data sesuai filter
4. Sistem menampilkan laporan
5. Pengguna memilih format ekspor (PDF/Excel)
6. Sistem menghasilkan file laporan
7. Pengguna mengunduh file

---

## Ringkasan Activity Diagram

| No | Diagram | Increment | File PUML |
|----|---------|-----------|-----------|
| 1 | Kelola Kategori | 1 | activity-kelola-kategori.puml |
| 2 | Kelola Supplier | 1 | activity-kelola-supplier.puml |
| 3 | Kelola Bahan Baku | 1 | activity-kelola-bahan-baku.puml |
| 4 | Proses Belanja | 1 | activity-belanja.puml |
| 5 | Kelola Produk | 1 | activity-kelola-produk.puml |
| 6 | Proses Produksi | 1 | activity-produksi.puml |
| 7 | Proses Transaksi | 1 | activity-transaksi.puml |
| 8 | Kelola User | 2 | activity-kelola-user.puml |
| 9 | Kelola Pelanggan | 2 | activity-kelola-pelanggan.puml |
| 10 | Stock Opname | 2 | activity-stock-opname.puml |
| 11 | Pembuatan Laporan | 2 | activity-laporan.puml |
