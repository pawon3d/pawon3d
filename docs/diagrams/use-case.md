# Use Case Diagram - Sistem Pawon3D

## Pendahuluan

Use case diagram merupakan representasi visual yang menggambarkan interaksi antara aktor dengan sistem. Diagram ini menyajikan fungsionalitas sistem dari perspektif pengguna serta mengidentifikasi batasan dan cakupan sistem yang dikembangkan.

## Aktor Sistem

Berdasarkan hasil implementasi, Sistem Pawon3D mengidentifikasi lima aktor utama yang berinteraksi dengan sistem:

| Aktor | Deskripsi |
|-------|-----------|
| **Pengunjung** | Pengguna publik yang mengakses halaman landing tanpa autentikasi |
| **Kasir** | Pengguna yang menangani transaksi penjualan dan pembayaran |
| **Bagian Produksi** | Pengguna yang mengelola proses produksi |
| **Bagian Inventori** | Pengguna yang mengelola bahan baku, produk, dan belanja |
| **Admin** | Pengguna dengan akses penuh untuk manajemen pengguna dan pengaturan sistem |

---

## Use Case Diagram - Increment 1

Increment 1 mencakup fungsionalitas inti operasional yang diperlukan untuk menjalankan proses bisnis dasar toko kue.

### Diagram

```
Referensi: docs/diagrams/puml/use-case-increment1.puml
```

---

## Spesifikasi Use Case - Increment 1

### UC-01: Mengelola Kategori

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Kategori |
| Use Case ID | UC-01 |
| Aktor | Bagian Inventori |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Bagian Inventori untuk mengelola data kategori produk, kategori bahan baku, dan jenis biaya dalam sistem |
| Kondisi Awal | Aktor telah login ke sistem dan memiliki hak akses untuk mengelola kategori |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Aktor mengakses menu Kategori | 2. Sistem menampilkan daftar kategori yang tersedia |
| 3. Aktor memilih opsi Tambah Kategori | 4. Sistem menampilkan formulir input kategori |
| 5. Aktor mengisi nama dan deskripsi kategori | 6. Sistem memvalidasi data yang dimasukkan |
| 7. Aktor menyimpan data kategori | 8. Sistem menyimpan data ke database |
| | 9. Sistem menampilkan notifikasi keberhasilan |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika aktor memilih Ubah Kategori, sistem menampilkan formulir dengan data kategori yang dipilih untuk diperbarui |
| Alt. 2. Jika aktor memilih Hapus Kategori, sistem menampilkan konfirmasi penghapusan |
| Alt. 3. Jika nama kategori sudah ada, sistem menampilkan pesan kesalahan duplikasi |
| Alt. 4. Jika kategori memiliki relasi dengan data lain, sistem mencegah penghapusan |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan fungsi lengkap untuk mengelola kategori sebagai data master dalam sistem |
| Kondisi Akhir | Data kategori tersimpan atau terperbarui dalam database dan ditampilkan pada daftar kategori |

---

### UC-02: Mengelola Satuan

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Satuan |
| Use Case ID | UC-02 |
| Aktor | Bagian Inventori |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Bagian Inventori untuk mengelola satuan ukur beserta faktor konversi antar satuan dalam sistem |
| Kondisi Awal | Aktor telah login ke sistem dan memiliki hak akses untuk mengelola satuan |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Aktor mengakses menu Satuan | 2. Sistem menampilkan daftar satuan yang tersedia |
| 3. Aktor memilih opsi Tambah Satuan | 4. Sistem menampilkan formulir input satuan |
| 5. Aktor mengisi nama, alias, grup, dan faktor konversi | 6. Sistem memvalidasi data yang dimasukkan |
| 7. Aktor memilih satuan dasar (jika ada) | 8. Sistem menghitung faktor konversi relatif |
| 9. Aktor menyimpan data satuan | 10. Sistem menyimpan data ke database |
| | 11. Sistem menampilkan notifikasi keberhasilan |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika aktor memilih Ubah Satuan, sistem menampilkan formulir dengan data satuan yang dipilih |
| Alt. 2. Jika satuan merupakan satuan dasar, field satuan dasar dikosongkan |
| Alt. 3. Jika faktor konversi tidak valid, sistem menampilkan pesan kesalahan |
| Alt. 4. Jika satuan digunakan dalam data lain, sistem mencegah penghapusan |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan pengelolaan satuan ukur dengan dukungan konversi otomatis antar satuan |
| Kondisi Akhir | Data satuan tersimpan dalam database dengan konfigurasi konversi yang benar |

---

### UC-03: Mengelola Supplier

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Supplier |
| Use Case ID | UC-03 |
| Aktor | Bagian Inventori |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Bagian Inventori untuk mengelola data pemasok bahan baku termasuk informasi kontak dan alamat |
| Kondisi Awal | Aktor telah login ke sistem dan memiliki hak akses untuk mengelola supplier |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Aktor mengakses menu Supplier | 2. Sistem menampilkan daftar supplier yang tersedia |
| 3. Aktor memilih opsi Tambah Supplier | 4. Sistem menampilkan formulir input supplier |
| 5. Aktor mengisi nama, kontak, alamat, dan informasi lainnya | 6. Sistem memvalidasi kelengkapan data |
| 7. Aktor mengunggah gambar supplier (opsional) | 8. Sistem menyimpan file gambar |
| 9. Aktor menyimpan data supplier | 10. Sistem menyimpan data ke database |
| | 11. Sistem menampilkan notifikasi keberhasilan |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika aktor memilih Ubah Supplier, sistem menampilkan formulir dengan data supplier yang dipilih |
| Alt. 2. Jika aktor memilih Cetak PDF, sistem menghasilkan dokumen PDF daftar supplier |
| Alt. 3. Jika data tidak lengkap, sistem menampilkan pesan kesalahan validasi |
| Alt. 4. Jika supplier memiliki transaksi belanja, sistem mencegah penghapusan |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan pengelolaan data supplier untuk keperluan pengadaan bahan baku |
| Kondisi Akhir | Data supplier tersimpan dalam database dan dapat digunakan untuk transaksi belanja |

---

### UC-04: Mengelola Bahan Baku

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Bahan Baku |
| Use Case ID | UC-04 |
| Aktor | Bagian Inventori |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Bagian Inventori untuk mengelola data bahan baku termasuk penetapan minimum stok dan pemantauan status ketersediaan |
| Kondisi Awal | Aktor telah login ke sistem dan memiliki hak akses untuk mengelola bahan baku |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Aktor mengakses menu Bahan Baku | 2. Sistem menampilkan daftar bahan baku dengan status ketersediaan |
| 3. Aktor memilih opsi Tambah Bahan Baku | 4. Sistem menampilkan formulir input bahan baku |
| 5. Aktor mengisi nama, deskripsi, dan minimum stok | 6. Sistem memvalidasi data yang dimasukkan |
| 7. Aktor menyimpan data bahan baku | 8. Sistem menyimpan data dengan status awal Kosong |
| | 9. Sistem menampilkan notifikasi keberhasilan |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika aktor memilih Ubah Bahan Baku, sistem menampilkan formulir dengan data yang dipilih |
| Alt. 2. Jika aktor memilih Lihat Batch, sistem menampilkan daftar batch dengan kuantitas dan tanggal kedaluwarsa |
| Alt. 3. Jika aktor memilih Cetak PDF, sistem menghasilkan dokumen PDF daftar bahan baku |
| Alt. 4. Jika nama bahan sudah ada, sistem menampilkan pesan kesalahan duplikasi |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan pengelolaan bahan baku dengan manajemen batch dan status ketersediaan otomatis |
| Kondisi Akhir | Data bahan baku tersimpan dalam database dengan status yang sesuai kondisi stok |

---

### UC-05: Mengelola Belanja

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Belanja |
| Use Case ID | UC-05 |
| Aktor | Bagian Inventori |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Bagian Inventori untuk mengelola proses pengadaan bahan baku dari perencanaan hingga pencatatan hasil belanja |
| Kondisi Awal | Aktor telah login ke sistem dan memiliki hak akses untuk mengelola belanja |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Aktor mengakses menu Belanja | 2. Sistem menampilkan daftar rencana belanja |
| 3. Aktor memilih opsi Buat Rencana Belanja | 4. Sistem menampilkan formulir dengan generator nomor belanja |
| 5. Aktor memilih supplier dan menambahkan item bahan | 6. Sistem menampilkan daftar item yang ditambahkan |
| 7. Aktor menyimpan rencana belanja | 8. Sistem menyimpan data dengan status Rencana |
| 9. Aktor memilih rencana dan memulai belanja | 10. Sistem menampilkan formulir input hasil belanja |
| 11. Aktor memasukkan kuantitas aktual, harga, dan tanggal kedaluwarsa | 12. Sistem menghitung total belanja |
| 13. Aktor menyelesaikan proses belanja | 14. Sistem membuat batch baru untuk setiap bahan |
| | 15. Sistem memperbarui status ketersediaan bahan |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika aktor memilih Lihat Riwayat, sistem menampilkan daftar belanja yang telah selesai |
| Alt. 2. Jika aktor memilih Cetak Detail, sistem menghasilkan dokumen PDF detail belanja |
| Alt. 3. Jika kuantitas aktual berbeda dari rencana, sistem menyesuaikan total |
| Alt. 4. Jika belanja dibatalkan, sistem mengembalikan status ke Rencana |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan alur lengkap pengadaan bahan baku dengan pembaruan stok otomatis |
| Kondisi Akhir | Data belanja tersimpan, batch bahan baku terbuat, dan status ketersediaan terperbarui |

---

### UC-06: Mengelola Produk

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Produk |
| Use Case ID | UC-06 |
| Aktor | Bagian Inventori |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Bagian Inventori untuk mengelola katalog produk beserta komposisi bahan dan biaya tambahan |
| Kondisi Awal | Aktor telah login ke sistem dan memiliki hak akses untuk mengelola produk |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Aktor mengakses menu Produk | 2. Sistem menampilkan daftar produk dengan harga modal |
| 3. Aktor memilih opsi Tambah Produk | 4. Sistem menampilkan formulir input produk |
| 5. Aktor mengisi nama, kategori, harga, dan metode penjualan | 6. Sistem memvalidasi data yang dimasukkan |
| 7. Aktor menambahkan komposisi bahan baku | 8. Sistem menampilkan daftar komposisi |
| 9. Aktor menambahkan biaya tambahan (opsional) | 10. Sistem menghitung harga modal |
| 11. Aktor menyimpan data produk | 12. Sistem menyimpan produk dan relasi komposisi |
| | 13. Sistem menampilkan notifikasi keberhasilan |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika aktor memilih Ubah Produk, sistem menampilkan formulir dengan data produk untuk diperbarui |
| Alt. 2. Jika aktor mengubah komposisi, sistem menghitung ulang harga modal |
| Alt. 3. Jika bahan baku tidak tersedia, sistem menampilkan peringatan |
| Alt. 4. Jika produk memiliki transaksi, sistem mencegah penghapusan |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan pengelolaan produk dengan perhitungan harga modal otomatis |
| Kondisi Akhir | Data produk tersimpan dengan komposisi bahan dan harga modal yang terhitung |

---

### UC-07: Mengelola Produksi

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Produksi |
| Use Case ID | UC-07 |
| Aktor | Bagian Produksi |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Bagian Produksi untuk mengelola proses produksi pesanan maupun produksi siap beli |
| Kondisi Awal | Aktor telah login ke sistem dan memiliki hak akses untuk mengelola produksi |

#### Bagian 3 – Skenario Utama (Produksi Pesanan)

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Aktor mengakses menu Produksi | 2. Sistem menampilkan antrian pesanan yang perlu diproduksi |
| 3. Aktor memilih pesanan untuk diproduksi | 4. Sistem menampilkan detail produk pesanan |
| 5. Aktor memilih pekerja yang bertugas | 6. Sistem mencatat penugasan pekerja |
| 7. Aktor memulai produksi | 8. Sistem memverifikasi ketersediaan bahan baku |
| | 9. Sistem mengurangi stok bahan dengan metode FIFO |
| 10. Aktor menyelesaikan produksi | 11. Sistem memperbarui status produksi dan transaksi |
| | 12. Sistem mencatat log inventori |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika aktor memilih Produksi Siap Beli, sistem menampilkan formulir produksi untuk stok |
| Alt. 2. Jika bahan baku tidak mencukupi, sistem menampilkan peringatan dengan daftar bahan yang kurang |
| Alt. 3. Jika aktor memilih Lihat Riwayat, sistem menampilkan daftar produksi yang telah selesai |
| Alt. 4. Jika produksi dibatalkan, sistem mengembalikan stok bahan baku |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan pengelolaan produksi dengan pengurangan stok otomatis menggunakan metode FIFO |
| Kondisi Akhir | Produksi selesai, stok bahan berkurang, dan status pesanan terperbarui |

---

### UC-08: Mengelola Transaksi

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Transaksi |
| Use Case ID | UC-08 |
| Aktor | Kasir |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Kasir untuk mengelola transaksi penjualan dengan tiga metode: pesanan kotak, pesanan reguler, dan siap beli |
| Kondisi Awal | Aktor telah login ke sistem dan memiliki hak akses untuk mengelola transaksi |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Aktor mengakses menu Transaksi | 2. Sistem menampilkan daftar transaksi |
| 3. Aktor memilih metode transaksi | 4. Sistem menampilkan formulir sesuai metode |
| 5. Aktor memasukkan data pelanggan (opsional) | 6. Sistem mencari atau membuat data pelanggan |
| 7. Aktor memilih produk dan kuantitas | 8. Sistem menghitung total transaksi |
| 9. Aktor menyimpan pesanan | 10. Sistem menghasilkan nomor invoice |
| 11. Aktor memproses pembayaran | 12. Sistem mencatat pembayaran dan menghasilkan nomor kwitansi |
| 13. Aktor mencetak struk | 14. Sistem menghasilkan dokumen struk |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika metode Pesanan Kotak/Reguler, transaksi masuk antrian produksi |
| Alt. 2. Jika metode Siap Beli, sistem mengurangi stok produk langsung |
| Alt. 3. Jika pelanggan menggunakan poin, sistem menghitung diskon |
| Alt. 4. Jika pembayaran kurang, sistem menampilkan pesan kesalahan |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan sistem POS lengkap dengan dukungan tiga metode transaksi dan poin pelanggan |
| Kondisi Akhir | Transaksi tersimpan, pembayaran tercatat, dan poin pelanggan terperbarui |

---

### UC-09: Melihat Landing Page

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Melihat Landing Page |
| Use Case ID | UC-09 |
| Aktor | Pengunjung |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Pengunjung untuk mengakses halaman publik toko tanpa memerlukan autentikasi |
| Kondisi Awal | Pengunjung mengakses URL halaman utama toko |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Pengunjung mengakses halaman utama | 2. Sistem menampilkan informasi umum toko |
| 3. Pengunjung memilih menu Katalog | 4. Sistem menampilkan daftar produk yang tersedia |
| 5. Pengunjung memilih produk tertentu | 6. Sistem menampilkan detail produk |
| 7. Pengunjung memilih menu FAQ | 8. Sistem menampilkan halaman pertanyaan umum |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika produk tidak tersedia, sistem menampilkan pesan kosong |
| Alt. 2. Jika halaman tidak ditemukan, sistem menampilkan halaman 404 |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan akses publik untuk informasi toko dan katalog produk |
| Kondisi Akhir | Pengunjung dapat melihat informasi toko dan produk yang tersedia |

---

## Use Case Diagram - Increment 2

Increment 2 mencakup fungsionalitas pendukung yang meningkatkan kapabilitas sistem.

### Diagram

```
Referensi: docs/diagrams/puml/use-case-increment2.puml
```

---

## Spesifikasi Use Case - Increment 2

### UC-10: Mengelola Pengguna

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Pengguna |
| Use Case ID | UC-10 |
| Aktor | Admin |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Admin untuk mengelola pengguna sistem melalui mekanisme undangan email |
| Kondisi Awal | Aktor telah login sebagai Admin dengan hak akses pengelolaan pengguna |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Admin mengakses menu Pekerja | 2. Sistem menampilkan daftar pengguna |
| 3. Admin memilih opsi Tambah Pekerja | 4. Sistem menampilkan formulir input pengguna |
| 5. Admin mengisi nama, email, peran, dan jenis kelamin | 6. Sistem memvalidasi email unik |
| 7. Admin menyimpan data pengguna | 8. Sistem menyimpan data dengan status tidak aktif |
| | 9. Sistem mengirimkan email undangan aktivasi |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika Admin memilih Ubah Pekerja, sistem menampilkan formulir edit pengguna |
| Alt. 2. Jika Admin memilih Toggle Status, sistem mengubah status aktif/nonaktif pengguna |
| Alt. 3. Jika email sudah terdaftar, sistem menampilkan pesan kesalahan |
| Alt. 4. Jika kuota peran penuh, sistem mencegah penambahan pengguna |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan pengelolaan pengguna dengan mekanisme undangan yang aman |
| Kondisi Akhir | Data pengguna tersimpan dan email undangan terkirim |

---

### UC-11: Mengelola Peran

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Peran |
| Use Case ID | UC-11 |
| Aktor | Admin |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Admin untuk mengelola peran dan konfigurasi hak akses dalam sistem |
| Kondisi Awal | Aktor telah login sebagai Admin dengan hak akses pengelolaan peran |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Admin mengakses menu Peran | 2. Sistem menampilkan daftar peran yang tersedia |
| 3. Admin memilih opsi Tambah/Ubah Peran | 4. Sistem menampilkan formulir konfigurasi peran |
| 5. Admin mengisi nama peran dan maksimum pengguna | 6. Sistem menampilkan daftar permission |
| 7. Admin memilih permission yang diizinkan | 8. Sistem mencatat konfigurasi permission |
| 9. Admin menyimpan data peran | 10. Sistem menyimpan data peran dan permission |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika peran memiliki pengguna aktif, sistem membatasi perubahan tertentu |
| Alt. 2. Jika nama peran sudah ada, sistem menampilkan pesan kesalahan |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan pengelolaan peran dengan sistem permission yang granular |
| Kondisi Akhir | Data peran tersimpan dengan konfigurasi permission yang sesuai |

---

### UC-12: Mengaktifkan Akun

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengaktifkan Akun |
| Use Case ID | UC-12 |
| Aktor | Pengguna Baru |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Pengguna Baru untuk mengaktifkan akun melalui tautan undangan email |
| Kondisi Awal | Pengguna baru menerima email undangan dari sistem |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Pengguna baru mengklik tautan aktivasi | 2. Sistem memvalidasi token undangan |
| 3. | 4. Sistem menampilkan formulir pengaturan password |
| 5. Pengguna memasukkan password baru | 6. Sistem memvalidasi kekuatan password |
| 7. Pengguna mengkonfirmasi password | 8. Sistem memvalidasi kesesuaian password |
| 9. Pengguna menyimpan aktivasi | 10. Sistem mengaktifkan akun dan menghapus token |
| | 11. Sistem mengarahkan ke halaman login |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika token tidak valid, sistem menampilkan pesan kesalahan |
| Alt. 2. Jika token kedaluwarsa (lebih dari 7 hari), sistem menampilkan pesan kedaluwarsa |
| Alt. 3. Jika password tidak memenuhi kriteria, sistem menampilkan pesan validasi |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan proses aktivasi akun yang aman dengan validasi token |
| Kondisi Akhir | Akun pengguna aktif dan dapat digunakan untuk login |

---

### UC-13: Mengelola Pelanggan

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Pelanggan |
| Use Case ID | UC-13 |
| Aktor | Kasir |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Kasir untuk melihat data pelanggan dan mengelola sistem poin loyalitas |
| Kondisi Awal | Aktor telah login ke sistem dan memiliki hak akses data pelanggan |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Kasir mengakses menu Pelanggan | 2. Sistem menampilkan daftar pelanggan dengan total poin |
| 3. Kasir memilih pelanggan tertentu | 4. Sistem menampilkan detail pelanggan |
| | 5. Sistem menampilkan riwayat transaksi pelanggan |
| | 6. Sistem menampilkan riwayat poin pelanggan |
| 7. Kasir menggunakan poin saat transaksi | 8. Sistem menghitung diskon dari poin |
| | 9. Sistem mengurangi poin dan mencatat riwayat |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika Kasir mencari pelanggan, sistem memfilter berdasarkan nomor telepon |
| Alt. 2. Jika poin tidak mencukupi, sistem membatasi jumlah yang dapat digunakan |
| Alt. 3. Pelanggan baru ditambahkan otomatis saat transaksi dengan nomor telepon |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan pengelolaan pelanggan dengan sistem poin loyalitas terintegrasi |
| Kondisi Akhir | Data pelanggan dan poin terperbarui sesuai transaksi |

---

### UC-14: Mengelola Stock Opname

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Stock Opname |
| Use Case ID | UC-14 |
| Aktor | Bagian Inventori |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Bagian Inventori untuk melakukan penghitungan stok fisik dan rekonsiliasi data inventori |
| Kondisi Awal | Aktor telah login ke sistem dan memiliki hak akses stock opname |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Aktor mengakses menu Hitung | 2. Sistem menampilkan daftar rencana hitung |
| 3. Aktor membuat rencana hitung baru | 4. Sistem menghasilkan nomor hitung |
| 5. Aktor memilih bahan yang akan dihitung | 6. Sistem menampilkan kuantitas sistem |
| 7. Aktor memulai penghitungan | 8. Sistem mengubah status menjadi Dalam Proses |
| 9. Aktor memasukkan kuantitas aktual | 10. Sistem menghitung selisih |
| 11. Aktor menyelesaikan penghitungan | 12. Sistem menyesuaikan batch berdasarkan hasil |
| | 13. Sistem mencatat log inventori |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika aktor memilih Lihat Riwayat, sistem menampilkan daftar stock opname selesai |
| Alt. 2. Jika tidak ada selisih, sistem tidak mengubah batch |
| Alt. 3. Jika penghitungan dibatalkan, sistem mengembalikan status |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan proses stock opname dengan penyesuaian batch otomatis |
| Kondisi Akhir | Data inventori direkonsiliasi dengan kondisi aktual |

---

### UC-15: Melihat Dashboard

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Melihat Dashboard |
| Use Case ID | UC-15 |
| Aktor | Semua Pengguna |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan semua pengguna untuk melihat ringkasan operasional sesuai perannya |
| Kondisi Awal | Aktor telah login ke sistem |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Pengguna mengakses halaman Dashboard | 2. Sistem mengidentifikasi peran pengguna |
| | 3. Sistem mengumpulkan data statistik sesuai peran |
| | 4. Sistem menampilkan ringkasan dalam bentuk widget dan grafik |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika pengguna Kasir, sistem menampilkan statistik transaksi |
| Alt. 2. Jika pengguna Bagian Produksi, sistem menampilkan statistik produksi |
| Alt. 3. Jika pengguna Bagian Inventori, sistem menampilkan status inventori |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan tampilan ringkasan yang disesuaikan dengan peran pengguna |
| Kondisi Akhir | Pengguna dapat melihat informasi operasional yang relevan |

---

### UC-16: Mengelola Laporan

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Laporan |
| Use Case ID | UC-16 |
| Aktor | Semua Pengguna |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan pengguna untuk mengakses dan mengekspor laporan operasional |
| Kondisi Awal | Aktor telah login ke sistem dan memiliki hak akses laporan sesuai perannya |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Pengguna mengakses halaman Laporan | 2. Sistem menampilkan jenis laporan yang tersedia |
| 3. Pengguna memilih jenis laporan | 4. Sistem menampilkan data dengan filter default |
| 5. Pengguna mengatur filter periode | 6. Sistem mengambil data sesuai filter |
| 7. Pengguna memilih ekspor PDF/Excel | 8. Sistem menghasilkan file laporan |
| | 9. Sistem menyediakan file untuk diunduh |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika memilih Laporan Kasir, sistem menampilkan statistik transaksi |
| Alt. 2. Jika memilih Laporan Produksi, sistem menampilkan rekap output produksi |
| Alt. 3. Jika memilih Laporan Inventori, sistem menampilkan status stok bahan |
| Alt. 4. Jika data kosong, sistem menampilkan pesan tidak ada data |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan akses laporan dengan dukungan ekspor ke PDF dan Excel |
| Kondisi Akhir | Pengguna memperoleh laporan sesuai filter yang ditentukan |

---

### UC-17: Melihat Notifikasi

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Melihat Notifikasi |
| Use Case ID | UC-17 |
| Aktor | Semua Pengguna |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan pengguna untuk melihat dan mengelola notifikasi sistem |
| Kondisi Awal | Aktor telah login ke sistem |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Pengguna mengakses menu Notifikasi | 2. Sistem menampilkan daftar notifikasi |
| 3. Pengguna memilih notifikasi tertentu | 4. Sistem menampilkan detail notifikasi |
| 5. Pengguna menandai sebagai dibaca | 6. Sistem memperbarui status notifikasi |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika Bagian Inventori memilih Lihat Alur Persediaan, sistem menampilkan log perubahan inventori |
| Alt. 2. Jika tidak ada notifikasi baru, sistem menampilkan daftar kosong |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan sistem notifikasi untuk informasi penting dalam sistem |
| Kondisi Akhir | Pengguna dapat melihat dan menandai notifikasi |

---

### UC-18: Mengelola Pengaturan

#### Bagian 1 – Informasi Umum Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Nama Use Case | Mengelola Pengaturan |
| Use Case ID | UC-18 |
| Aktor | Admin |

#### Bagian 2 – Deskripsi Use Case

| Elemen | Deskripsi |
|--------|-----------|
| Deskripsi Use Case | Use case ini memungkinkan Admin untuk mengonfigurasi pengaturan sistem dan profil usaha |
| Kondisi Awal | Aktor telah login sebagai Admin dengan hak akses pengaturan |

#### Bagian 3 – Skenario Utama

| Event (Aksi Aktor) | Respon Sistem |
|--------------------|---------------|
| 1. Admin mengakses menu Pengaturan | 2. Sistem menampilkan halaman pengaturan |
| 3. Admin mengubah profil usaha | 4. Sistem menampilkan formulir profil usaha |
| 5. Admin mengisi nama, alamat, kontak toko | 6. Sistem memvalidasi data |
| 7. Admin menyimpan pengaturan | 8. Sistem menyimpan perubahan |

#### Bagian 4 – Skenario Alternatif

| Skenario Alternatif |
|---------------------|
| Alt. 1. Jika Admin memilih Metode Pembayaran, sistem menampilkan daftar channel pembayaran |
| Alt. 2. Jika Admin mengaktifkan/menonaktifkan channel, sistem memperbarui status |
| Alt. 3. Jika pengguna memilih Ubah Profil Saya, sistem menampilkan formulir profil pribadi |

#### Bagian 5 – Penutup

| Elemen | Deskripsi |
|--------|-----------|
| Kesimpulan | Use case ini menyediakan konfigurasi pengaturan sistem dan profil usaha |
| Kondisi Akhir | Pengaturan sistem terperbarui sesuai konfigurasi yang disimpan |

---

## Catatan Implementasi

Berikut adalah catatan teknis terkait implementasi use case dalam sistem:

1. Seluruh use case memerlukan autentikasi kecuali use case Melihat Landing Page (UC-09) yang dapat diakses secara publik
2. Akses terhadap setiap use case dikontrol oleh sistem permission berbasis peran yang dapat dikonfigurasi oleh Admin
3. Beberapa use case dapat diakses oleh lebih dari satu aktor sesuai dengan konfigurasi permission yang diterapkan
