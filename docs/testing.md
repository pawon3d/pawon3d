# Pengujian Sistem - Black Box Testing

## Pendahuluan

Pengujian sistem dilakukan menggunakan metode Black Box Testing untuk memverifikasi fungsionalitas sistem dari perspektif pengguna. Metode ini berfokus pada validasi input dan output sistem tanpa memperhatikan struktur internal kode program. Pengujian disusun berdasarkan pembagian increment dan peran aktor yang berinteraksi dengan sistem.

---

## Pengujian Increment 1

Increment 1 mencakup fungsionalitas inti operasional sistem Pawon3D yang meliputi pengelolaan data master, inventori, produksi, dan transaksi penjualan.

### Pengujian oleh Pengunjung

Pengujian berikut dilakukan untuk memverifikasi fungsi yang dapat diakses oleh Pengunjung pada halaman publik tanpa autentikasi.

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-001 | Pengunjung mengakses halaman utama landing page | Sistem menampilkan halaman utama dengan informasi toko | Halaman utama ditampilkan dengan informasi toko lengkap | [x] Berhasil |
| TC-002 | Pengunjung mengakses halaman katalog produk | Sistem menampilkan daftar produk yang tersedia | Daftar produk ditampilkan dengan gambar dan harga | [x] Berhasil |
| TC-003 | Pengunjung memilih produk untuk melihat detail | Sistem menampilkan halaman detail produk | Detail produk ditampilkan termasuk deskripsi | [x] Berhasil |
| TC-004 | Pengunjung mengakses halaman FAQ | Sistem menampilkan daftar pertanyaan umum | Halaman FAQ ditampilkan dengan daftar pertanyaan dan jawaban | [x] Berhasil |

**Narasi Pengujian:**

Pengujian terhadap fungsi yang dapat diakses oleh Pengunjung dilakukan menggunakan metode Black Box Testing dengan memverifikasi respons sistem terhadap aksi pengguna. Pengunjung berperan sebagai pengguna publik yang mengakses halaman landing page tanpa memerlukan proses autentikasi. Hasil pengujian menunjukkan bahwa seluruh skenario pengujian berhasil dilaksanakan dengan output yang sesuai dengan ekspektasi. Halaman utama, katalog produk, detail produk, dan FAQ dapat diakses dan menampilkan informasi yang benar. Hasil ini mengkonfirmasi bahwa fungsionalitas landing page pada Increment 1 telah terimplementasi dengan baik.

---

### Pengujian oleh Bagian Inventori

Pengujian berikut dilakukan untuk memverifikasi fungsi pengelolaan data master, bahan baku, produk, dan belanja yang diakses oleh Bagian Inventori.

#### Pengujian Autentikasi

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-005 | Bagian Inventori login dengan kredensial valid | Sistem mengarahkan ke halaman dashboard | Pengguna diarahkan ke dashboard sesuai peran | [x] Berhasil |
| TC-006 | Bagian Inventori login dengan kredensial tidak valid | Sistem menampilkan pesan kesalahan | Pesan "Email atau password salah" ditampilkan | [x] Berhasil |
| TC-007 | Bagian Inventori logout dari sistem | Sistem mengakhiri sesi dan mengarahkan ke halaman login | Sesi berakhir dan pengguna diarahkan ke login | [x] Berhasil |

#### Pengujian Pengelolaan Kategori

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-008 | Bagian Inventori mengakses halaman kategori | Sistem menampilkan daftar kategori | Daftar kategori ditampilkan dalam tabel | [x] Berhasil |
| TC-009 | Bagian Inventori menambah kategori baru dengan data valid | Sistem menyimpan dan menampilkan notifikasi sukses | Kategori tersimpan dan notifikasi ditampilkan | [x] Berhasil |
| TC-010 | Bagian Inventori menambah kategori dengan nama duplikat | Sistem menampilkan pesan kesalahan duplikasi | Pesan "Nama kategori sudah ada" ditampilkan | [x] Berhasil |
| TC-011 | Bagian Inventori mengubah data kategori | Sistem memperbarui dan menampilkan notifikasi sukses | Data kategori terperbarui | [x] Berhasil |
| TC-012 | Bagian Inventori menghapus kategori tanpa relasi | Sistem menghapus dan menampilkan notifikasi sukses | Kategori terhapus dari daftar | [x] Berhasil |

#### Pengujian Pengelolaan Satuan

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-013 | Bagian Inventori mengakses halaman satuan | Sistem menampilkan daftar satuan dengan faktor konversi | Daftar satuan ditampilkan dengan lengkap | [x] Berhasil |
| TC-014 | Bagian Inventori menambah satuan dasar baru | Sistem menyimpan satuan dengan faktor konversi 1 | Satuan dasar tersimpan | [x] Berhasil |
| TC-015 | Bagian Inventori menambah satuan turunan dengan konversi | Sistem menyimpan satuan dengan faktor konversi | Satuan turunan tersimpan dengan konversi benar | [x] Berhasil |

#### Pengujian Pengelolaan Supplier

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-016 | Bagian Inventori mengakses halaman supplier | Sistem menampilkan daftar supplier | Daftar supplier ditampilkan | [x] Berhasil |
| TC-017 | Bagian Inventori menambah supplier dengan data lengkap | Sistem menyimpan dan menampilkan notifikasi sukses | Supplier tersimpan dengan data lengkap | [x] Berhasil |
| TC-018 | Bagian Inventori mengubah data supplier | Sistem memperbarui data supplier | Data supplier terperbarui | [x] Berhasil |
| TC-019 | Bagian Inventori mencetak daftar supplier ke PDF | Sistem menghasilkan file PDF | File PDF diunduh dengan daftar supplier | [x] Berhasil |

#### Pengujian Pengelolaan Bahan Baku

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-020 | Bagian Inventori mengakses halaman bahan baku | Sistem menampilkan daftar bahan dengan status | Daftar bahan baku dengan status ketersediaan ditampilkan | [x] Berhasil |
| TC-021 | Bagian Inventori menambah bahan baku baru | Sistem menyimpan dengan status awal Kosong | Bahan baku tersimpan dengan status Kosong | [x] Berhasil |
| TC-022 | Bagian Inventori mengubah data bahan baku | Sistem memperbarui data bahan baku | Data bahan baku terperbarui | [x] Berhasil |
| TC-023 | Bagian Inventori melihat daftar batch bahan | Sistem menampilkan batch dengan kuantitas dan tanggal | Daftar batch ditampilkan | [x] Berhasil |
| TC-024 | Bagian Inventori mencetak daftar bahan ke PDF | Sistem menghasilkan file PDF | File PDF diunduh dengan daftar bahan | [x] Berhasil |

#### Pengujian Pengelolaan Belanja

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-025 | Bagian Inventori mengakses halaman belanja | Sistem menampilkan daftar rencana belanja | Daftar rencana belanja ditampilkan | [x] Berhasil |
| TC-026 | Bagian Inventori membuat rencana belanja baru | Sistem menghasilkan nomor belanja dan menyimpan rencana | Rencana tersimpan dengan nomor BP-YYMMDD-XXXX | [x] Berhasil |
| TC-027 | Bagian Inventori memulai proses belanja | Sistem menampilkan formulir input hasil belanja | Formulir hasil belanja ditampilkan | [x] Berhasil |
| TC-028 | Bagian Inventori menyelesaikan belanja dengan data lengkap | Sistem membuat batch baru dan memperbarui status bahan | Batch terbuat dan status bahan terperbarui | [x] Berhasil |
| TC-029 | Bagian Inventori melihat riwayat belanja | Sistem menampilkan daftar belanja yang selesai | Riwayat belanja ditampilkan | [x] Berhasil |
| TC-030 | Bagian Inventori mencetak detail belanja ke PDF | Sistem menghasilkan file PDF detail belanja | File PDF diunduh dengan detail belanja | [x] Berhasil |

#### Pengujian Pengelolaan Produk

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-031 | Bagian Inventori mengakses halaman produk | Sistem menampilkan daftar produk dengan harga modal | Daftar produk ditampilkan | [x] Berhasil |
| TC-032 | Bagian Inventori menambah produk dengan komposisi | Sistem menyimpan produk dan menghitung harga modal | Produk tersimpan dengan harga modal terhitung | [x] Berhasil |
| TC-033 | Bagian Inventori menambah biaya tambahan pada produk | Sistem memperbarui perhitungan harga modal | Harga modal terperbarui dengan biaya tambahan | [x] Berhasil |
| TC-034 | Bagian Inventori mengubah komposisi produk | Sistem memperbarui komposisi dan harga modal | Komposisi dan harga modal terperbarui | [x] Berhasil |

**Narasi Pengujian:**

Pengujian terhadap fungsi yang dapat diakses oleh Bagian Inventori dilakukan secara komprehensif menggunakan metode Black Box Testing. Bagian Inventori berperan sebagai pengguna yang bertanggung jawab atas pengelolaan data master, inventori bahan baku, dan katalog produk. Pengujian mencakup proses autentikasi, pengelolaan kategori, satuan, supplier, bahan baku, belanja, dan produk. Hasil pengujian menunjukkan bahwa seluruh skenario pengujian berhasil dilaksanakan dengan output yang sesuai ekspektasi. Sistem dapat memvalidasi input, menyimpan data dengan benar, menghitung status ketersediaan bahan secara otomatis, serta menghasilkan dokumen PDF. Hasil ini mengkonfirmasi bahwa fungsionalitas inventori pada Increment 1 telah terimplementasi sesuai kebutuhan operasional.

---

### Pengujian oleh Bagian Produksi

Pengujian berikut dilakukan untuk memverifikasi fungsi pengelolaan produksi yang diakses oleh Bagian Produksi.

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-035 | Bagian Produksi login ke sistem | Sistem mengarahkan ke dashboard produksi | Dashboard produksi ditampilkan | [x] Berhasil |
| TC-036 | Bagian Produksi mengakses antrian produksi | Sistem menampilkan pesanan yang perlu diproduksi | Antrian pesanan ditampilkan | [x] Berhasil |
| TC-037 | Bagian Produksi memilih pesanan untuk diproduksi | Sistem menampilkan detail produk pesanan | Detail pesanan ditampilkan | [x] Berhasil |
| TC-038 | Bagian Produksi memilih pekerja yang bertugas | Sistem mencatat penugasan pekerja | Pekerja tercatat dalam produksi | [x] Berhasil |
| TC-039 | Bagian Produksi memulai produksi dengan bahan cukup | Sistem mengurangi stok bahan dan mengubah status | Stok bahan berkurang dan status berubah | [x] Berhasil |
| TC-040 | Bagian Produksi memulai produksi dengan bahan tidak cukup | Sistem menampilkan peringatan bahan tidak cukup | Peringatan ditampilkan dengan daftar bahan kurang | [x] Berhasil |
| TC-041 | Bagian Produksi menyelesaikan produksi pesanan | Sistem memperbarui status produksi dan transaksi | Status terperbarui menjadi Selesai | [x] Berhasil |
| TC-042 | Bagian Produksi membuat produksi siap beli | Sistem menghasilkan nomor produksi dan menyimpan data | Produksi siap beli tersimpan | [x] Berhasil |
| TC-043 | Bagian Produksi menyelesaikan produksi siap beli | Sistem menambah stok produk | Stok produk bertambah | [x] Berhasil |
| TC-044 | Bagian Produksi melihat riwayat produksi | Sistem menampilkan daftar produksi yang selesai | Riwayat produksi ditampilkan | [x] Berhasil |

**Narasi Pengujian:**

Pengujian terhadap fungsi yang dapat diakses oleh Bagian Produksi dilakukan menggunakan metode Black Box Testing untuk memverifikasi alur produksi. Bagian Produksi berperan sebagai pengguna yang bertanggung jawab atas pelaksanaan produksi berdasarkan pesanan maupun untuk stok siap beli. Pengujian mencakup skenario produksi dengan bahan baku yang mencukupi maupun tidak mencukupi. Hasil pengujian menunjukkan bahwa sistem dapat mengurangi stok bahan secara otomatis menggunakan metode FIFO, menampilkan peringatan ketika bahan tidak cukup, serta memperbarui status produksi dan transaksi dengan benar. Hasil ini mengkonfirmasi bahwa fungsionalitas produksi pada Increment 1 telah terimplementasi sesuai alur operasional.

---

### Pengujian oleh Kasir

Pengujian berikut dilakukan untuk memverifikasi fungsi transaksi penjualan yang diakses oleh Kasir.

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-045 | Kasir login ke sistem | Sistem mengarahkan ke dashboard kasir | Dashboard kasir ditampilkan | [x] Berhasil |
| TC-046 | Kasir mengakses daftar transaksi | Sistem menampilkan daftar transaksi | Daftar transaksi ditampilkan | [x] Berhasil |
| TC-047 | Kasir membuat pesanan kotak baru | Sistem menghasilkan nomor invoice OK-YYMMDD-XXXX | Invoice terbuat dengan format benar | [x] Berhasil |
| TC-048 | Kasir membuat pesanan reguler baru | Sistem menghasilkan nomor invoice OR-YYMMDD-XXXX | Invoice terbuat dengan format benar | [x] Berhasil |
| TC-049 | Kasir membuat transaksi siap beli | Sistem menghasilkan nomor invoice OS-YYMMDD-XXXX | Invoice terbuat dengan format benar | [x] Berhasil |
| TC-050 | Kasir menambahkan produk ke transaksi | Sistem menghitung subtotal dan total | Total transaksi terhitung dengan benar | [x] Berhasil |
| TC-051 | Kasir memasukkan data pelanggan | Sistem mencari atau membuat data pelanggan | Data pelanggan tersimpan | [x] Berhasil |
| TC-052 | Kasir memproses pembayaran dengan jumlah cukup | Sistem mencatat pembayaran dan menghasilkan kwitansi | Pembayaran tercatat dengan nomor kwitansi | [x] Berhasil |
| TC-053 | Kasir memproses pembayaran dengan jumlah kurang | Sistem menampilkan pesan pembayaran kurang | Pesan kesalahan ditampilkan | [x] Berhasil |
| TC-054 | Kasir mencetak struk pembayaran | Sistem menghasilkan file struk | Struk pembayaran dicetak/diunduh | [x] Berhasil |
| TC-055 | Kasir melihat riwayat transaksi | Sistem menampilkan daftar transaksi selesai | Riwayat transaksi ditampilkan | [x] Berhasil |

**Narasi Pengujian:**

Pengujian terhadap fungsi yang dapat diakses oleh Kasir dilakukan menggunakan metode Black Box Testing untuk memverifikasi sistem Point of Sale. Kasir berperan sebagai pengguna yang bertanggung jawab atas transaksi penjualan dan pembayaran. Pengujian mencakup tiga metode transaksi yaitu pesanan kotak, pesanan reguler, dan siap beli. Hasil pengujian menunjukkan bahwa sistem dapat menghasilkan nomor invoice dengan format yang sesuai metode transaksi, menghitung total dengan benar, memvalidasi jumlah pembayaran, serta menghasilkan struk. Hasil ini mengkonfirmasi bahwa fungsionalitas transaksi pada Increment 1 telah terimplementasi sesuai kebutuhan operasional kasir.

---

## Pengujian Increment 2

Increment 2 mencakup fungsionalitas pendukung yang meningkatkan kapabilitas sistem meliputi manajemen pengguna, pelanggan, stock opname, dashboard, laporan, dan pengaturan.

### Pengujian oleh Admin

Pengujian berikut dilakukan untuk memverifikasi fungsi manajemen pengguna dan pengaturan sistem yang diakses oleh Admin.

#### Pengujian Pengelolaan Pengguna

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-056 | Admin login ke sistem | Sistem mengarahkan ke dashboard admin | Dashboard admin ditampilkan | [x] Berhasil |
| TC-057 | Admin mengakses halaman pekerja | Sistem menampilkan daftar pengguna | Daftar pengguna ditampilkan | [x] Berhasil |
| TC-058 | Admin menambah pengguna baru dengan email valid | Sistem menyimpan dan mengirim email undangan | Pengguna tersimpan dan email terkirim | [x] Berhasil |
| TC-059 | Admin menambah pengguna dengan email duplikat | Sistem menampilkan pesan kesalahan | Pesan "Email sudah terdaftar" ditampilkan | [x] Berhasil |
| TC-060 | Admin mengubah data pengguna | Sistem memperbarui data pengguna | Data pengguna terperbarui | [x] Berhasil |
| TC-061 | Admin mengubah status aktif pengguna | Sistem toggle status aktif/nonaktif | Status pengguna berubah | [x] Berhasil |

#### Pengujian Pengelolaan Peran

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-062 | Admin mengakses halaman peran | Sistem menampilkan daftar peran | Daftar peran ditampilkan | [x] Berhasil |
| TC-063 | Admin menambah peran baru dengan permission | Sistem menyimpan peran dan permission | Peran tersimpan dengan permission | [x] Berhasil |
| TC-064 | Admin mengubah permission peran | Sistem memperbarui konfigurasi permission | Permission terperbarui | [x] Berhasil |

#### Pengujian Pengaturan Sistem

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-065 | Admin mengakses halaman pengaturan | Sistem menampilkan halaman pengaturan | Halaman pengaturan ditampilkan | [x] Berhasil |
| TC-066 | Admin mengubah profil usaha | Sistem menyimpan data profil usaha | Profil usaha terperbarui | [x] Berhasil |
| TC-067 | Admin mengaktifkan metode pembayaran | Sistem mengubah status channel pembayaran | Channel pembayaran aktif | [x] Berhasil |
| TC-068 | Admin menonaktifkan metode pembayaran | Sistem mengubah status channel pembayaran | Channel pembayaran nonaktif | [x] Berhasil |

**Narasi Pengujian:**

Pengujian terhadap fungsi yang dapat diakses oleh Admin dilakukan menggunakan metode Black Box Testing untuk memverifikasi fungsionalitas manajemen pengguna dan pengaturan sistem. Admin berperan sebagai pengguna dengan otoritas tertinggi yang bertanggung jawab atas konfigurasi sistem. Pengujian mencakup pengelolaan pengguna dengan mekanisme undangan email, pengelolaan peran dengan sistem permission, serta pengaturan profil usaha dan metode pembayaran. Hasil pengujian menunjukkan bahwa sistem dapat memvalidasi email duplikat, mengirimkan email undangan, mengelola peran dengan permission yang granular, serta menyimpan pengaturan sistem. Hasil ini mengkonfirmasi bahwa fungsionalitas manajemen pada Increment 2 telah terimplementasi dengan baik.

---

### Pengujian oleh Pengguna Baru

Pengujian berikut dilakukan untuk memverifikasi proses aktivasi akun oleh Pengguna Baru.

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-069 | Pengguna baru mengakses link aktivasi valid | Sistem menampilkan formulir pengaturan password | Formulir password ditampilkan | [x] Berhasil |
| TC-070 | Pengguna baru mengakses link aktivasi expired | Sistem menampilkan pesan token kedaluwarsa | Pesan kedaluwarsa ditampilkan | [x] Berhasil |
| TC-071 | Pengguna baru mengatur password dengan kriteria valid | Sistem mengaktifkan akun | Akun diaktifkan dan diarahkan ke login | [x] Berhasil |
| TC-072 | Pengguna baru mengatur password yang tidak sesuai kriteria | Sistem menampilkan pesan validasi | Pesan validasi password ditampilkan | [x] Berhasil |

**Narasi Pengujian:**

Pengujian terhadap proses aktivasi akun dilakukan menggunakan metode Black Box Testing untuk memverifikasi mekanisme undangan yang aman. Pengguna Baru berperan sebagai individu yang menerima undangan dari Admin untuk bergabung ke dalam sistem. Pengujian mencakup skenario token valid, token kedaluwarsa, serta validasi password. Hasil pengujian menunjukkan bahwa sistem dapat memvalidasi token dengan benar, menolak token yang kedaluwarsa, serta memvalidasi kekuatan password. Hasil ini mengkonfirmasi bahwa fungsionalitas aktivasi akun pada Increment 2 telah terimplementasi dengan aman.

---

### Pengujian oleh Kasir (Increment 2)

Pengujian berikut dilakukan untuk memverifikasi fungsi pengelolaan pelanggan dan poin yang diakses oleh Kasir.

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-073 | Kasir mengakses halaman pelanggan | Sistem menampilkan daftar pelanggan dengan poin | Daftar pelanggan ditampilkan | [x] Berhasil |
| TC-074 | Kasir mencari pelanggan berdasarkan nomor telepon | Sistem menampilkan hasil pencarian | Pelanggan ditemukan dan ditampilkan | [x] Berhasil |
| TC-075 | Kasir melihat detail pelanggan | Sistem menampilkan riwayat transaksi dan poin | Detail pelanggan ditampilkan | [x] Berhasil |
| TC-076 | Kasir menggunakan poin pelanggan saat transaksi | Sistem menghitung diskon dari poin | Diskon terhitung dan total ter
| TC-077 | Kasir menggunakan poin melebihi saldo | Sistem menampilkan pesan poin tidak cukup | Pesan kesalahan ditampilkan | [x] Berhasil |

**Narasi Pengujian:**

Pengujian terhadap fungsi pelanggan dan poin yang diakses oleh Kasir dilakukan menggunakan metode Black Box Testing. Kasir berperan dalam memfasilitasi penggunaan poin loyalitas pelanggan sebagai diskon saat transaksi. Pengujian mencakup pencarian pelanggan, penampilan riwayat, dan penggunaan poin. Hasil pengujian menunjukkan bahwa sistem dapat menghitung diskon dari poin dengan benar serta memvalidasi saldo poin yang tersedia. Hasil ini mengkonfirmasi bahwa fungsionalitas pelanggan dan poin pada Increment 2 telah terimplementasi dengan baik.

---

### Pengujian oleh Bagian Inventori (Increment 2)

Pengujian berikut dilakukan untuk memverifikasi fungsi stock opname yang diakses oleh Bagian Inventori.

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-078 | Bagian Inventori mengakses halaman hitung | Sistem menampilkan daftar rencana hitung | Daftar rencana hitung ditampilkan | [x] Berhasil |
| TC-079 | Bagian Inventori membuat rencana stock opname | Sistem menghasilkan nomor hitung HC-YYMMDD-XXXX | Rencana tersimpan dengan nomor hitung | [x] Berhasil |
| TC-080 | Bagian Inventori memulai penghitungan stok | Sistem menampilkan kuantitas sistem per item | Kuantitas sistem ditampilkan | [x] Berhasil |
| TC-081 | Bagian Inventori memasukkan kuantitas aktual | Sistem menghitung selisih otomatis | Selisih terhitung dengan benar | [x] Berhasil |
| TC-082 | Bagian Inventori menyelesaikan stock opname | Sistem menyesuaikan batch dan mencatat log | Batch terperbarui dan log tercatat | [x] Berhasil |
| TC-083 | Bagian Inventori melihat riwayat stock opname | Sistem menampilkan daftar stock opname selesai | Riwayat stock opname ditampilkan | [x] Berhasil |
| TC-084 | Bagian Inventori melihat alur persediaan | Sistem menampilkan log perubahan inventori | Log inventori ditampilkan | [x] Berhasil |

**Narasi Pengujian:**

Pengujian terhadap fungsi stock opname yang diakses oleh Bagian Inventori dilakukan menggunakan metode Black Box Testing. Bagian Inventori berperan dalam memastikan keakuratan data inventori melalui penghitungan fisik. Pengujian mencakup pembuatan rencana, pelaksanaan penghitungan, dan penyesuaian batch. Hasil pengujian menunjukkan bahwa sistem dapat menghitung selisih dengan benar dan menyesuaikan data batch berdasarkan hasil penghitungan aktual. Hasil ini mengkonfirmasi bahwa fungsionalitas stock opname pada Increment 2 telah terimplementasi dengan baik.

---

### Pengujian oleh Semua Pengguna

Pengujian berikut dilakukan untuk memverifikasi fungsi umum yang dapat diakses oleh semua pengguna yang telah terotentikasi.

#### Pengujian Dashboard

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-085 | Pengguna mengakses halaman dashboard | Sistem menampilkan ringkasan sesuai peran | Ringkasan operasional ditampilkan | [x] Berhasil |
| TC-086 | Pengguna melihat statistik dalam periode tertentu | Sistem menampilkan data sesuai filter | Data terfilter ditampilkan | [x] Berhasil |

#### Pengujian Laporan

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-087 | Pengguna mengakses halaman laporan | Sistem menampilkan jenis laporan yang tersedia | Pilihan laporan ditampilkan | [x] Berhasil |
| TC-088 | Pengguna memfilter laporan berdasarkan periode | Sistem menampilkan data sesuai filter | Data laporan terfilter | [x] Berhasil |
| TC-089 | Pengguna mengekspor laporan ke PDF | Sistem menghasilkan file PDF | File PDF diunduh | [x] Berhasil |
| TC-090 | Pengguna mengekspor laporan ke Excel | Sistem menghasilkan file Excel | File Excel diunduh | [x] Berhasil |

#### Pengujian Notifikasi

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-091 | Pengguna mengakses halaman notifikasi | Sistem menampilkan daftar notifikasi | Daftar notifikasi ditampilkan | [x] Berhasil |
| TC-092 | Pengguna menandai notifikasi sebagai dibaca | Sistem memperbarui status notifikasi | Status notifikasi berubah menjadi dibaca | [x] Berhasil |

#### Pengujian Profil

| Test Case ID | Deskripsi | Output yang Diharapkan | Output yang Didapatkan | Status |
|--------------|-----------|------------------------|------------------------|--------|
| TC-093 | Pengguna mengakses halaman profil | Sistem menampilkan data profil pengguna | Data profil ditampilkan | [x] Berhasil |
| TC-094 | Pengguna mengubah data profil | Sistem menyimpan perubahan profil | Profil terperbarui | [x] Berhasil |

**Narasi Pengujian:**

Pengujian terhadap fungsi umum yang dapat diakses oleh semua pengguna dilakukan menggunakan metode Black Box Testing. Pengujian mencakup dashboard, laporan, notifikasi, dan profil pengguna. Hasil pengujian menunjukkan bahwa sistem dapat menampilkan ringkasan sesuai peran, menghasilkan laporan dalam format PDF dan Excel, mengelola notifikasi, serta memperbarui profil pengguna. Hasil ini mengkonfirmasi bahwa fungsionalitas pendukung pada Increment 2 telah terimplementasi dengan baik dan mendukung kebutuhan operasional seluruh pengguna.

---

## Ringkasan Hasil Pengujian

Berdasarkan seluruh skenario pengujian yang telah dilaksanakan, berikut adalah ringkasan hasil pengujian sistem:

| Increment | Jumlah Test Case | Berhasil | Gagal | Persentase Keberhasilan |
|-----------|------------------|----------|-------|-------------------------|
| Increment 1 | 55 | 55 | 0 | 100% |
| Increment 2 | 39 | 39 | 0 | 100% |
| **Total** | **94** | **94** | **0** | **100%** |

### Kesimpulan Pengujian

Pengujian sistem menggunakan metode Black Box Testing telah dilaksanakan secara komprehensif terhadap seluruh fungsionalitas yang diimplementasikan pada Increment 1 dan Increment 2. Pengujian dilakukan berdasarkan perspektif masing-masing aktor yaitu Pengunjung, Bagian Inventori, Bagian Produksi, Kasir, Admin, Pengguna Baru, dan Semua Pengguna.

Hasil pengujian menunjukkan bahwa seluruh 94 skenario pengujian berhasil dilaksanakan dengan output yang sesuai dengan ekspektasi. Sistem dapat memvalidasi input, memproses data dengan benar, menghasilkan output yang sesuai, serta menampilkan pesan kesalahan yang informatif ketika terjadi kondisi tidak valid.

Berdasarkan hasil pengujian, dapat disimpulkan bahwa Sistem Pawon3D telah berfungsi sesuai dengan spesifikasi kebutuhan dan siap digunakan untuk mendukung operasional toko kue.
