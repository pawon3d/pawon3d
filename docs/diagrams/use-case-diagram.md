# Dokumentasi Use Case Diagram

## Pendahuluan

Use case diagram Sistem Manajemen Toko Kue Pawon3D menggambarkan interaksi antara aktor-aktor dengan sistem. Diagram ini menunjukkan fungsionalitas sistem dari perspektif pengguna dan dikategorikan berdasarkan pembagian increment.

---

## Aktor Sistem

Sistem Pawon3D melibatkan beberapa aktor dengan hak akses berbeda:

| Aktor | Deskripsi |
|-------|-----------|
| **Bagian Inventori** | Pengguna yang mengelola data master, bahan baku, supplier, produk, dan belanja |
| **Bagian Produksi** | Pengguna yang mengelola proses produksi |
| **Kasir** | Pengguna yang menangani transaksi penjualan |
| **Admin** | Pengguna dengan hak akses untuk mengelola pengguna, peran, dan pengaturan sistem |
| **Pengguna Baru** | Pengguna yang baru diundang dan melakukan aktivasi akun |

---

## Use Case Increment 1: Modul Inti Operasional

### Bagian Inventori

| ID UC | Kelompok Fitur | Use Case Fungsional | Deskripsi Singkat |
|-------|----------------|---------------------|-------------------|
| UC-01 | **Autentikasi** | Login & Logout | Masuk dan keluar dari sistem menggunakan email dan password |
| UC-02 | **Data Master** | Kelola Kategori Produk | Mengatur pengelompokan produk berdasarkan jenis |
| UC-03 | | Kelola Satuan & Konversi | Mengatur satuan ukuran bahan dan faktor konversinya |
| UC-04 | | Kelola Supplier | Mengelola data pemasok bahan baku |
| UC-05 | **Inventori** | Kelola Bahan Baku | Mengelola stok dan data teknis bahan dasar produksi |
| UC-06 | | Kelola Belanja | Merencanakan dan mencatat pembelian bahan baku dari supplier |
| UC-07 | **Produk** | Kelola Produk & Komposisi | Mengatur produk jadi beserta resep/komposisi bahan bakunya |

---

### Bagian Produksi

| ID UC | Kelompok Fitur | Use Case Fungsional | Deskripsi Singkat |
|-------|----------------|---------------------|-------------------|
| UC-01 | **Autentikasi** | Login & Logout | Autentikasi untuk mengakses modul produksi |
| UC-08 | **Produksi** | Kelola Antrian Produksi | Memantau dan memilih pesanan yang harus diproduksi |
| UC-09 | | Eksekusi & Riwayat Produksi | Mencatat pemakaian bahan dan hasil produksi fisik |

---

### Kasir

| ID UC | Kelompok Fitur | Use Case Fungsional | Deskripsi Singkat |
|-------|----------------|---------------------|-------------------|
| UC-01 | **Autentikasi** | Login & Logout | Autentikasi untuk mengakses modul kasir/POS |
| UC-10 | **Transaksi** | Membuat Pesanan | Memproses penjualan produk (Kotak, Reguler, Siap Beli) |
| UC-11 | | Proses Pembayaran & Riwayat | Mencatat pembayaran dan memantau riwayat transaksi |

---

## Use Case Increment 2: Modul Pendukung Operasional

### Admin

| ID UC | Kelompok Fitur | Use Case Fungsional | Deskripsi Singkat |
|-------|----------------|---------------------|-------------------|
| UC-12 | **Manajemen User** | Kelola Pengguna | Mengundang pekerja baru dan mengelola status akun |
| UC-13 | | Kelola Peran | Mengatur hak akses pengguna berdasarkan tugasnya |
| UC-14 | **Pengaturan** | Profil & Pembayaran | Mengatur identitas toko dan metode pembayaran |
| UC-15 | **CRM** | Kelola Pelanggan & Poin | Mengelola database pelanggan dan sistem loyalitas poin |
| UC-16 | **Laporan** | Akses & Ekspor Laporan | Melihat dan mengunduh laporan (PDF & Excel) |

---

### Pengguna Baru

| ID UC | Kelompok Fitur | Use Case Fungsional | Deskripsi Singkat |
|-------|----------------|---------------------|-------------------|
| UC-17 | **Aktivasi** | Aktivasi Akun | Mengatur password awal melalui tautan undangan email |

---

### Bagian Inventori (Increment 2)

| ID UC | Kelompok Fitur | Use Case Fungsional | Deskripsi Singkat |
|-------|----------------|---------------------|-------------------|
| UC-18 | **Stock Opname** | Kelola Stock Opname | Melakukan penghitungan stok fisik secara periodik |
| UC-19 | | Kelola Alur Persediaan | Memantau log perubahan stok secara rinci |

---

### Kasir (Increment 2)

| ID UC | Kelompok Fitur | Use Case Fungsional | Deskripsi Singkat |
|-------|----------------|---------------------|-------------------|
| UC-20 | **Loyalty** | Penggunaan Poin | Menggunakan saldo poin pelanggan sebagai potongan harga |
| UC-21 | **Refund** | Proses Refund | Membatalkan transaksi dan mengembalikan dana |

---

## Berkas Diagram Use Case

Diagram use case lengkap tersedia dalam format PlantUML pada berkas berikut:

- **Combined Version**: [use-case-combined.puml](puml/use-case-combined.puml)
- **Increment 1**: [use-case-increment-1.puml](puml/use-case-increment-1.puml)
- **Increment 2**: [use-case-increment-2.puml](puml/use-case-increment-2.puml)

---

## Spesifikasi Use Case

### 1. Modul Increment 1 (Inti Operasional)

#### UC-01: Login ke Sistem
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Login ke Sistem |
| **ID Use Case** | UC-01 |
| **Aktor** | Semua Pengguna |
| **Deskripsi** | Masuk ke sistem dengan email dan password |
| **Kondisi Awal** | Pengguna berada di halaman login |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Memasukkan email dan password | 2. Memvalidasi kredensial |
| | 3. Menekan tombol Login | 4. Membuat sesi user |
| | | 5. Mengarahkan ke Dashboard |
| **Skenario Alternatif** | Alt. 1: Login gagal karena email/password salah. Sistem tampilkan error. |
| **Kesimpulan** | Pengguna berhasil masuk ke dashboard |
| **Kondisi Akhir** | Sesi pengguna aktif di sistem |

#### UC-02: Kelola Kategori Produk
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Kelola Kategori Produk |
| **ID Use Case** | UC-02 |
| **Aktor** | Bagian Inventori |
| **Deskripsi** | Mengelola pengelompokan produk |
| **Kondisi Awal** | Aktor berada di halaman Kategori Produk |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Menambah/mengedit nama kategori | 2. Memvalidasi input |
| | 3. Menyimpan data | 4. Memperbarui daftar kategori |
| **Skenario Alternatif** | Alt. 1: Nama kategori duplikat. Sistem tampilkan error. |
| **Kesimpulan** | Daftar kategori produk diperbarui |
| **Kondisi Akhir** | Kategori baru siap digunakan pada produk |

#### UC-03: Kelola Satuan & Konversi
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Kelola Satuan & Konversi |
| **ID Use Case** | UC-03 |
| **Aktor** | Bagian Inventori |
| **Deskripsi** | Mengelola unit ukuran dan faktor konversinya |
| **Kondisi Awal** | Aktor berada di halaman Satuan |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Menentukan satuan dasar (base unit) | 2. Menyimpan satuan dasar |
| | 3. Menambahkan satuan turunan & nilai konversi | 4. Memvalidasi rasio konversi |
| **Skenario Alternatif** | Alt. 1: Rasio konversi nol atau negatif. Sistem menolak. |
| **Kesimpulan** | Sistem memiliki standar konversi satuan |
| **Kondisi Akhir** | Satuan siap digunakan untuk bahan baku |

#### UC-04: Kelola Supplier
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Kelola Supplier |
| **ID Use Case** | UC-04 |
| **Aktor** | Bagian Inventori |
| **Deskripsi** | Mengelola data mitra/pemasok bahan baku |
| **Kondisi Awal** | Aktor berada di halaman Supplier |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Memasukkan identitas supplier | 2. Validasi format telepon/kontak |
| | 3. Menyimpan data | 4. Memperbarui daftar supplier |
| **Skenario Alternatif** | Alt. 1: Supplier masih memiliki transaksi aktif. Sistem cegah penghapusan. |
| **Kesimpulan** | Data master supplier diperbarui |
| **Kondisi Akhir** | Supplier siap dihubungkan dengan transaksi belanja |

#### UC-05: Kelola Bahan Baku
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Kelola Bahan Baku |
| **ID Use Case** | UC-05 |
| **Aktor** | Bagian Inventori |
| **Deskripsi** | Mengelola data teknis dan stok bahan baku dasar |
| **Kondisi Awal** | Aktor berada di halaman Manajemen Bahan Baku |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Memasukkan data bahan baku baru | 2. Memvalidasi keunikan nama bahan |
| | 3. Menentukan stok minimum | 4. Menyimpan data ke database |
| | | 5. Menampilkan daftar bahan terbaru |
| **Skenario Alternatif** | Alt. 1: Stok bahan di bawah minimum. Sistem tampilkan status "Menipis". |
| **Kesimpulan** | Database bahan baku diperbarui |
| **Kondisi Akhir** | Data bahan siap digunakan untuk produksi dan belanja |

#### UC-06: Kelola Belanja
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Kelola Belanja |
| **ID Use Case** | UC-06 |
| **Aktor** | Bagian Inventori |
| **Deskripsi** | Merencanakan dan mencatat pembelian bahan baku |
| **Kondisi Awal** | Aktor berada di halaman Belanja |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Membuat rencana belanja | 2. Generate nomor belanja otomatis |
| | 3. Memasukkan item belanja | 4. Menghitung estimasi total biaya |
| | 5. Input realisasi belanja fisik | 6. Update stok batch dan total aktual |
| **Skenario Alternatif** | Alt. 1: Harga aktual berbeda dengan rencana. Sistem mencatat selisih biaya. |
| **Kesimpulan** | Stok bahan fisik bertambah |
| **Kondisi Akhir** | Transaksi belanja selesai dan stok batch tercipta |

#### UC-07: Kelola Produk & Komposisi
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Kelola Produk & Komposisi |
| **ID Use Case** | UC-07 |
| **Aktor** | Bagian Inventori |
| **Deskripsi** | Mengatur produk jadi dan resep bahan bakunya |
| **Kondisi Awal** | Aktor berada di halaman Produk |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Memasukkan data produk & harga jual | 2. Validasi input |
| | 3. Menentukan komposisi bahan baku (resep) | 4. Menyimpan data resep |
| **Skenario Alternatif** | Alt. 1: Bahan baku yang dipilih tidak tersedia. Sistem beri peringatan. |
| **Kesimpulan** | Produk memiliki data harga dan resep yang valid |
| **Kondisi Akhir** | Produk siap diproduksi dan dijual |

#### UC-08: Kelola Antrian Produksi
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Kelola Antrian Produksi |
| **ID Use Case** | UC-08 |
| **Aktor** | Bagian Produksi |
| **Deskripsi** | Memantau dan mengatur prioritas pengerjaan pesanan |
| **Kondisi Awal** | Aktor berada di halaman Antrian Produksi |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Melihat daftar pesanan masuk | 2. Menampilkan detil item per pesanan |
| | 3. Mengatur status "Prioritas" | 4. Memperbarui urutan antrian |
| **Skenario Alternatif** | Alt. 1: Pesanan dibatalkan oleh kasir. Sistem hapus dari antrian. |
| **Kesimpulan** | Alur kerja produksi terorganisir |
| **Kondisi Akhir** | Item siap dieksekusi oleh bagian produksi |

#### UC-09: Eksekusi & Riwayat Produksi
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Eksekusi & Riwayat Produksi |
| **ID Use Case** | UC-09 |
| **Aktor** | Bagian Produksi |
| **Deskripsi** | Melaksanakan proses produksi pesanan atau stok siap beli |
| **Kondisi Awal** | Aktor berada di halaman Produksi |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Memilih pesanan yang akan diproduksi | 2. Menampilkan komposisi resep bahan |
| | 3. Menekan tombol "Mulai Produksi" | 4. Memotong stok bahan (FIFO) |
| | | 5. Mengubah status menjadi "Dalam Proses" |
| | 6. Menekan tombol "Selesaikan Produksi" | 7. Menambah stok produk jadi |
| | | 8. Mencatat log produksi |
| **Skenario Alternatif** | Alt. 1: Stok bahan tidak mencukupi untuk resep. Sistem batalkan proses. |
| **Kesimpulan** | Stok produk jadi bertambah dalam sistem |
| **Kondisi Akhir** | Status produksi menjadi "Selesai" |

#### UC-10: Membuat Pesanan (POS)
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Membuat Pesanan |
| **ID Use Case** | UC-10 |
| **Aktor** | Kasir |
| **Deskripsi** | Proses pencatatan transaksi penjualan produk |
| **Kondisi Awal** | Aktor berada di halaman Transaksi (POS) |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Memilih produk pesanan | 2. Menghitung total harga otomatis |
| | 3. Memasukkan data pelanggan | 4. Menyimpan draf transaksi |
| | 5. Menekan tombol Bayar | 6. Menampilkan form pembayaran |
| **Skenario Alternatif** | Alt. 1: Stok produk tidak cukup. Sistem beri peringatan. |
| **Kesimpulan** | Transaksi tercatat di sistem |
| **Kondisi Akhir** | Status transaksi menjadi "Menunggu Pembayaran" |

#### UC-11: Proses Pembayaran & Riwayat
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Proses Pembayaran |
| **ID Use Case** | UC-11 |
| **Aktor** | Kasir |
| **Deskripsi** | Mencatat realisasi pembayaran transaksi penjualan |
| **Kondisi Awal** | Aktor berada di formulir Pembayaran |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Memilih metode pembayaran | 2. Menampilkan portal sesuai channel |
| | 3. Memasukkan jumlah bayar | 4. Menghitung kembalian otomatis |
| | 5. Mengonfirmasi pembayaran | 6. Update status transaksi "Lunas" |
| | | 7. Generate struk digital |
| **Skenario Alternatif** | Alt. 1: Pembayaran kurang. Sistem menampilkan sisa tagihan. |
| **Kesimpulan** | Transaksi berhasil dilunasi |
| **Kondisi Akhir** | Struk siap dicetak dan data keuangan tercatat |

### 2. Modul Increment 2 (Pendukung Operasional)

#### UC-12: Kelola Pengguna & Undangan
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Kelola Pengguna & Undangan |
| **ID Use Case** | UC-12 |
| **Aktor** | Admin |
| **Deskripsi** | Mengelola akses pekerja melalui sistem undangan email |
| **Kondisi Awal** | Aktor berada di halaman Manajemen Pekerja |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Menambah email pengguna baru | 2. Membuat token aktivasi unik |
| | 3. Memilih peran (Role) | 4. Mengirim undangan via email |
| | | 5. Mencatat user dengan status "Pending" |
| **Skenario Alternatif** | Alt. 1: Email sudah terdaftar. Sistem tampilkan pesan error. |
| **Kesimpulan** | Pekerja baru menerima akses ke sistem |
| **Kondisi Akhir** | Undangan terkirim dan menunggu aktivasi |

#### UC-13: Kelola Peran & Hak Akses
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Kelola Peran & Hak Akses |
| **ID Use Case** | UC-13 |
| **Aktor** | Admin |
| **Deskripsi** | Mengelola permission dan batasan akses per peran pengguna |
| **Kondisi Awal** | Aktor berada di halaman Manajemen Peran |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Memilih peran (Role) | 2. Menampilkan daftar permission |
| | 3. Mengatur centang akses modul | 4. Melakukan sinkronisasi izin (sync) |
| | 5. Menyimpan konfigurasi | 6. Update permission secara realtime |
| **Skenario Alternatif** | Alt. 1: Mencoba menghapus peran yang memiliki user. Sistem mencegah. |
| **Kesimpulan** | Struktur hak akses diperbarui |
| **Kondisi Akhir** | Perubahan izin langsung berlaku bagi user tersebut |

#### UC-14: Profil & Pembayaran
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Profil & Pembayaran |
| **ID Use Case** | UC-14 |
| **Aktor** | Admin |
| **Deskripsi** | Mengatur identitas toko dan channel pembayaran yang tersedia |
| **Kondisi Awal** | Aktor berada di halaman Pengaturan |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Update logo & nama toko | 2. Update metadata toko |
| | 3. Aktifkan/Matikan channel pembayaran | 4. Simpan pengaturan pembayaran |
| **Skenario Alternatif** | Alt. 1: Format logo tidak didukung. Sistem tolak upload. |
| **Kesimpulan** | Identitas toko dan metode bayar terupdate |
| **Kondisi Akhir** | Perubahan muncul di struk dan halaman POS |

#### UC-15: Kelola Pelanggan & Poin
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Kelola Pelanggan & Poin |
| **ID Use Case** | UC-15 |
| **Aktor** | Admin |
| **Deskripsi** | Mengelola database pelanggan dan histori perolehan poin |
| **Kondisi Awal** | Aktor berada di halaman Manajemen Pelanggan |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Memilih data pelanggan | 2. Menampilkan riwayat poin & belanja |
| | 3. Melakukan koreksi poin manual | 4. Mencatat log riwayat poin |
| | | 5. Menampilkan notifikasi sukses |
| **Skenario Alternatif** | Alt. 1: Pelanggan belanja tanpa akun. Sistem sarankan pendaftaran. |
| **Kesimpulan** | Data loyalitas pelanggan tersimpan |
| **Kondisi Akhir** | Saldo poin pelanggan diperbarui |

#### UC-16: Akses & Ekspor Laporan
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Akses & Ekspor Laporan |
| **ID Use Case** | UC-16 |
| **Aktor** | Semua Pengguna Berizin |
| **Deskripsi** | Melihat dan mengunduh data operasional dalam format fisik |
| **Kondisi Awal** | Aktor berada di halaman Laporan |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Memilih periode (range tanggal) | 2. Melakukan query data ke database |
| | 3. Melihat pratinjau data | 4. Melakukan kalkulasi total/rata-rata |
| | 5. Memilih format (PDF/Excel) | 6. Generate file dokumen otomatis |
| | | 7. Mengirim file ke browser (download) |
| **Skenario Alternatif** | Alt. 1: Data kosong pada periode terpilih. Sistem tampilkan pesan nihil. |
| **Kesimpulan** | Laporan fisik berhasil dihasilkan |
| **Kondisi Akhir** | File laporan tersimpan di perangkat aktor |

#### UC-17: Aktivasi Akun
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Aktivasi Akun |
| **ID Use Case** | UC-17 |
| **Aktor** | Pengguna Baru |
| **Deskripsi** | Proses aktivasi akun bagi pengguna baru melalui email |
| **Kondisi Awal** | Pengguna membuka tautan aktivasi dari email |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Mengisi password baru | 2. Memvalidasi kekuatan password |
| | 3. Verifikasi password | 4. Mengubah status user menjadi "Active" |
| | | 5. Login otomatis & redirect ke Dashboard |
| **Skenario Alternatif** | Alt. 1: Token aktivasi kedaluwarsa. Sistem minta request ulang. |
| **Kesimpulan** | Pengguna memiliki akun aktif |
| **Kondisi Akhir** | Pengguna dapat menggunakan sistem sesuai perannya |

#### UC-18: Stock Opname (Hitung Stok)
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Stock Opname (Hitung Stok) |
| **ID Use Case** | UC-18 |
| **Aktor** | Bagian Inventori |
| **Deskripsi** | Melakukan penyesuaian stok sistem dengan stok fisik |
| **Kondisi Awal** | Aktor berada di halaman Hitung Stok |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Membuat rencana hitung baru | 2. Menampilkan kuantitas sistem saat ini |
| | 3. Memasukkan kuantitas fisik | 4. Menghitung selisih kuantitas & nilai |
| | 5. Menekan "Selesaikan Hitung" | 6. Melakukan koreksi stok otomatis |
| | | 7. Mencatat log penyesuaian (Inventory Log) |
| **Skenario Alternatif** | Alt. 1: Selisih terlalu besar. Sistem meminta verifikasi ulang. |
| **Kesimpulan** | Stok sistem kembali akurat |
| **Kondisi Akhir** | Status stock opname menjadi "Selesai" |

#### UC-19: Kelola Alur Persediaan
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Kelola Alur Persediaan |
| **ID Use Case** | UC-19 |
| **Aktor** | Bagian Inventori |
| **Deskripsi** | Memantau mutasi dan riwayat pergerakan stok secara detail |
| **Kondisi Awal** | Aktor berada di halaman Log Persediaan |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Filter data per bahan/produk | 2. Menampilkan log masuk/keluar |
| | 3. Melihat detail referensi (ID Belanja/Produksi) | 4. Menampilkan saldo berjalan |
| **Skenario Alternatif** | Alt. 1: Filter tidak ditemukan. Sistem tampilkan daftar kosong. |
| **Kesimpulan** | Transparansi mutasi barang terjamin |
| **Kondisi Akhir** | Aktor memahami penyebab perubahan angka stok |

#### UC-20: Penggunaan Poin
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Penggunaan Poin |
| **ID Use Case** | UC-20 |
| **Aktor** | Kasir |
| **Deskripsi** | Penukaran poin loyalitas pelanggan menjadi diskon belanja |
| **Kondisi Awal** | Aktor memproses transaksi pelanggan yang punya poin |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Masukkan jumlah poin yang ditukar | 2. Konversi poin ke nominal Rupiah |
| | 3. Apply diskon ke total belanja | 4. Kurangi saldo poin pelanggan |
| **Skenario Alternatif** | Alt. 1: Poin melebihi saldo. Sistem menolak. |
| **Kesimpulan** | Pelanggan mendapatkan reward potongan harga |
| **Kondisi Akhir** | Total bayar transaksi berkurang |

#### UC-21: Refund (Pengembalian Dana)
| Elemen | Deskripsi |
| --- | --- |
| **Nama Use Case** | Refund (Pengembalian Dana) |
| **ID Use Case** | UC-21 |
| **Aktor** | Kasir |
| **Deskripsi** | Membatalkan transaksi lunas dan mengembalikan dana pelanggan |
| **Kondisi Awal** | Aktor berada di riwayat transaksi lunas |
| **Skenario Utama** | **Aksi Aktor** | **Respon Sistem** |
| | 1. Memilih transaksi lunas | 2. Menampilkan tombol "Refund" |
| | 3. Memasukkan alasan & bukti | 4. Melakukan validasi total refund |
| | 5. Memilih metode pengembalian | 6. Update status "Refunded/Canceled" |
| | | 7. Mengatur stok produk kembali (restock) |
| **Skenario Alternatif** | Alt. 1: Refund melebihi total bayar. Sistem mencegah aksi. |
| **Kesimpulan** | Dana dikembalikan dan transaksi dibatalkan |
| **Kondisi Akhir** | Data keuangan dan stok produk disesuaikan kembali |

---

## Kesimpulan

Use case diagram Sistem Pawon3D dikelompokkan berdasarkan fungsionalitas utama dengan spesifikasi terstruktur untuk memudahkan pemetaan alur kerja sistem secara bertahap (incremental).
