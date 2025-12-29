# Pengujian Sistem - Black Box Testing

## Pendahuluan

Pengujian sistem dilakukan menggunakan metode Black Box Testing untuk memverifikasi fungsionalitas sistem dari perspektif pengguna. Metode ini berfokus pada validasi input dan output sistem tanpa memperhatikan struktur internal kode program. Pengujian disusun berdasarkan pembagian increment dan peran aktor yang berinteraksi dengan sistem.

---

Lembar Pengujian Fungsional Sistem Manajemen Bisnis Pawon3D
Increment 1 : Modul Inti Operasional
Penguji: Inventori
Test Case ID	Deskripsi	Hasil yang Diharapkan	Hasil Pengujian	Status
TC-001	Bagian Inventori login dengan kredensial valid	Sistem mengarahkan ke halaman dashboard		[ ] Berhasil
[ ] Gagal
TC-002	Bagian Inventori logout dari sistem	Sistem mengakhiri sesi dan mengarahkan ke halaman login		[ ] Berhasil
[ ] Gagal
TC-003	Bagian Inventori mengakses halaman kategori	Sistem menampilkan daftar kategori		[ ] Berhasil
[ ] Gagal
TC-004	Bagian Inventori menambah kategori baru dengan data valid	Sistem menyimpan dan menampilkan notifikasi sukses		[ ] Berhasil
[ ] Gagal
TC-005	Bagian Inventori mengubah data kategori	Sistem memperbarui dan menampilkan notifikasi sukses		[ ] Berhasil
[ ] Gagal
TC-006	Bagian Inventori menghapus kategori
	Sistem menghapus dan menampilkan notifikasi sukses		[ ] Berhasil
[ ] Gagal
TC-007	Bagian Inventori mengakses halaman satuan	Sistem menampilkan daftar satuan dengan faktor konversi		[ ] Berhasil
[ ] Gagal
TC-008	Bagian Inventori menambah satuan dasar baru	Sistem menyimpan satuan dengan faktor konversi 1		[ ] Berhasil
[ ] Gagal
TC-009	Bagian Inventori menambah satuan turunan dengan konversi	Sistem menyimpan satuan dengan faktor konversi		[ ] Berhasil
[ ] Gagal
TC-010	Bagian Inventori mengakses halaman supplier	Sistem menampilkan daftar supplier		[ ] Berhasil
[ ] Gagal
TC-011	Bagian Inventori menambah supplier dengan data lengkap	Sistem menyimpan dan menampilkan notifikasi sukses		[ ] Berhasil
[ ] Gagal
TC-012	Bagian Inventori mengubah data supplier	Sistem memperbarui data supplier		[ ] Berhasil
[ ] Gagal
TC-013	Bagian Inventori mengakses halaman bahan baku	Sistem menampilkan daftar bahan dengan status		[ ] Berhasil
[ ] Gagal
TC-014	Bagian Inventori menambah bahan baku baru	Sistem menyimpan dengan status awal Kosong		[ ] Berhasil
[ ] Gagal
TC-015	Bagian Inventori mengubah data bahan baku	Sistem memperbarui data bahan baku		[ ] Berhasil
[ ] Gagal
TC-016	Bagian Inventori mengakses halaman belanja	Sistem menampilkan daftar rencana belanja		[ ] Berhasil
[ ] Gagal
TC-017	Bagian Inventori membuat rencana belanja baru	Sistem menghasilkan nomor belanja dan menyimpan rencana		[ ] Berhasil
[ ] Gagal
TC-018	Bagian Inventori memulai proses belanja	Sistem menampilkan formulir input hasil belanja		[ ] Berhasil
[ ] Gagal
TC-019	Bagian Inventori menyelesaikan belanja dengan data lengkap	Sistem membuat batch baru dan memperbarui status bahan		[ ] Berhasil
[ ] Gagal
TC-020	Bagian Inventori melihat riwayat belanja	Sistem menampilkan daftar belanja yang selesai		[ ] Berhasil
[ ] Gagal
TC-021	Bagian Inventori mengakses halaman produk	Sistem menampilkan daftar produk		[ ] Berhasil
[ ] Gagal
TC-022	Bagian Inventori menambah produk dengan komposisi	Sistem menyimpan produk dan menghitung harga modal		[ ] Berhasil
[ ] Gagal
TC-023	Bagian Inventori menambah biaya tambahan pada produk	Sistem memperbarui perhitungan harga modal		[ ] Berhasil
[ ] Gagal
TC-024	Bagian Inventori mengubah komposisi produk	Sistem memperbarui komposisi dan harga modal		[ ] Berhasil
[ ] Gagal

 
Lembar Pengujian Fungsional Sistem Manajemen Bisnis Pawon3D
Increment 1 : Modul Inti Operasional
Penguji: Produksi
Test Case ID	Deskripsi	Hasil yang Diharapkan	Hasil Pengujian	Status
TC-025	Bagian Produksi login ke sistem	Sistem mengarahkan ke halaman dashboard		[ ] Berhasil
[ ] Gagal
TC-026	Bagian Produksi logout dari sistem	Sistem mengakhiri sesi dan mengarahkan ke halaman login		[ ] Berhasil
[ ] Gagal
TC-027	Bagian Produksi mengakses halaman Produksi	Sistem menampilkan daftar Produksi		[ ] Berhasil
[ ] Gagal
TC-028	Bagian Produksi mengakses antrian pesanan/produksi	Sistem menampilkan pesanan yang perlu diproduksi		[ ] Berhasil
[ ] Gagal
TC-029	Bagian Produksi memilih pesanan untuk diproduksi	Sistem menampilkan detail produk pesanan		[ ] Berhasil
[ ] Gagal
TC-030	Bagian Produksi memulai produksi dengan bahan cukup	Sistem mengurangi stok bahan dan mengubah status		[ ] Berhasil
[ ] Gagal
TC-031	Bagian Produksi menyelesaikan produksi pesanan	Sistem memperbarui status produksi dan transaksi		[ ] Berhasil
[ ] Gagal
TC-032	Bagian Produksi membuat produksi siap beli	Sistem menghasilkan nomor produksi dan menyimpan data		[ ] Berhasil
[ ] Gagal
TC-033	Bagian Produksi menyelesaikan produksi siap beli	Sistem menambah stok produk		[ ] Berhasil
[ ] Gagal
TC-034	Bagian Produksi melihat riwayat produksi	Sistem menampilkan daftar produksi yang selesai		[ ] Berhasil
[ ] Gagal


 
Lembar Pengujian Fungsional Sistem Manajemen Bisnis Pawon3D
Increment 1 : Modul Inti Operasional
Penguji: Kasir
Test Case ID	Deskripsi	Hasil yang Diharapkan	Hasil Pengujian	Status
TC-035	Kasir login ke sistem	Sistem mengarahkan ke halaman dashboard		[ ] Berhasil
[ ] Gagal
TC-036	Kasir logout dari sistem	Sistem mengakhiri sesi dan mengarahkan ke halaman login		[ ] Berhasil
[ ] Gagal
TC-037	Kasir mengakses daftar transaksi	Sistem menampilkan daftar transaksi		[ ] Berhasil
[ ] Gagal
TC-038	Kasir membuat pesanan kotak baru	Sistem menghasilkan nomor invoice OK-YYMMDD-XXXX		[ ] Berhasil
[ ] Gagal
TC-039	Kasir membuat pesanan reguler baru	Sistem menghasilkan nomor invoice OR-YYMMDD-XXXX		[ ] Berhasil
[ ] Gagal
TC-040	Kasir membuat transaksi siap beli	Sistem menghasilkan nomor invoice OS-YYMMDD-XXXX		[ ] Berhasil
[ ] Gagal
TC-041	Kasir menambahkan produk ke transaksi	Sistem menghitung subtotal dan total		[ ] Berhasil
[ ] Gagal
TC-042	Kasir memasukkan data pelanggan	Sistem mencari atau membuat data pelanggan		[ ] Berhasil
[ ] Gagal
TC-043	Kasir memproses pembayaran dengan jumlah cukup	Sistem mencatat pembayaran dan menghasilkan struk		[ ] Berhasil
[ ] Gagal
TC-044	Kasir mencetak struk pembayaran	Sistem menghasilkan file struk		[ ] Berhasil
[ ] Gagal
TC-045	Kasir melihat riwayat transaksi	Sistem menampilkan daftar transaksi selesai		[ ] Berhasil
[ ] Gagal



 
Lembar Pengujian Fungsional Sistem Manajemen Bisnis Pawon3D
Increment 2 : Modul Pendukung Operasional
Penguji: Admin
Test Case ID	Deskripsi	Hasil yang Diharapkan	Hasil Pengujian	Status
TC-046	Admin login ke sistem	Sistem mengarahkan ke halaman dashboard		[ ] Berhasil
[ ] Gagal
TC-047	Admin logout dari sistem	Sistem mengakhiri sesi dan mengarahkan ke halaman login		[ ] Berhasil
[ ] Gagal
TC-048	Admin mengakses halaman pekerja	Sistem menampilkan daftar pengguna		[ ] Berhasil
[ ] Gagal
TC-049	Admin menambah pengguna baru dengan email valid	Sistem menyimpan dan mengirim email undangan		[ ] Berhasil
[ ] Gagal
TC-050	Admin mengubah data pengguna	Sistem memperbarui data pengguna		[ ] Berhasil
[ ] Gagal
TC-051	Admin mengubah status aktif pengguna	Sistem toggle status aktif/nonaktif		[ ] Berhasil
[ ] Gagal
TC-052	Admin mengakses halaman peran	Sistem menampilkan daftar peran		[ ] Berhasil
[ ] Gagal
TC-053	Admin menambah peran baru dengan permission	Sistem menyimpan peran dan permission		[ ] Berhasil
[ ] Gagal
TC-054	Admin mengubah permission peran	Sistem memperbarui konfigurasi permission		[ ] Berhasil
[ ] Gagal
TC-055	Admin mengakses halaman pengaturan	Sistem menampilkan halaman pengaturan		[ ] Berhasil
[ ] Gagal
TC-056	Admin mengubah profil usaha	Sistem menyimpan data profil usaha		[ ] Berhasil
[ ] Gagal
TC-057	Admin mengakses halaman metode pembayaran	Sistem menampilkan halaman metode pembayaran		[ ] Berhasil
[ ] Gagal
TC-058	Admin menambah metode pembayaran	Sistem menyimpan data metode pembayaran		[ ] Berhasil
[ ] Gagal
TC-059	Admin mengubah data metode pembayaran	Sistem menyimpan data metode pembayaran 		[ ] Berhasil
[ ] Gagal
TC-060	Admin mengakses halaman pelanggan	Sistem menampilkan daftar pelanggan dengan poin		[ ] Berhasil
[ ] Gagal
TC-061	Admin mencari pelanggan berdasarkan nomor telepon	Sistem menampilkan hasil pencarian		[ ] Berhasil
[ ] Gagal
TC-062	Admin melihat detail pelanggan	Sistem menampilkan riwayat transaksi dan poin		[ ] Berhasil
[ ] Gagal
TC-063	Pengguna mengakses halaman profil	Sistem menampilkan data profil pengguna		[ ] Berhasil
[ ] Gagal
TC-064	Pengguna mengubah data profil	Sistem menyimpan perubahan profil		[ ] Berhasil
[ ] Gagal
TC-065	Pengguna mengakses halaman notifikasi	Sistem menampilkan daftar notifikasi		[ ] Berhasil
[ ] Gagal
TC-066	Pengguna menandai notifikasi sebagai dibaca	Sistem memperbarui status notifikasi		[ ] Berhasil
[ ] Gagal
TC-067	Pengguna mengakses halaman laporan	Sistem menampilkan halaman laporan		[ ] Berhasil
[ ] Gagal
TC-068	Pengguna memfilter laporan berdasarkan periode	Sistem menampilkan data sesuai filter		[ ] Berhasil
[ ] Gagal
TC-069	Pengguna mengekspor laporan ke PDF	Sistem menghasilkan file PDF		[ ] Berhasil
[ ] Gagal
TC-070	Pengguna mengekspor laporan ke Excel	Sistem menghasilkan file Excel		[ ] Berhasil
[ ] Gagal


 
Lembar Pengujian Fungsional Sistem Manajemen Bisnis Pawon3D
Increment 2 : Modul Pendukung Operasional
Penguji:  Pengguna Baru (Pekerja)
Test Case ID	Deskripsi	Hasil yang Diharapkan	Hasil Pengujian	Status
TC-071	Pengguna baru mengakses link aktivasi valid	Sistem menampilkan formulir pengaturan password		[ ] Berhasil
[ ] Gagal
TC-072	Pengguna baru mengatur password dengan kriteria valid	Sistem mengaktifkan akun dan mengarahkan ke halaman dashboard		[ ] Berhasil
[ ] Gagal


 
Lembar Pengujian Fungsional Sistem Manajemen Bisnis Pawon3D
Increment 2 : Modul Pendukung Operasional
Penguji: Inventori
Test Case ID	Deskripsi	Hasil yang Diharapkan	Hasil Pengujian	Status
TC-073	Bagian Inventori mengakses halaman hitung	Sistem menampilkan daftar rencana hitung		[ ] Berhasil
[ ] Gagal
TC-074	Bagian Inventori membuat rencana hitung	Sistem menghasilkan nomor hitung HC-YYMMDD-XXXX		[ ] Berhasil
[ ] Gagal
TC-075	Bagian Inventori memulai penghitungan stok	Sistem menampilkan kuantitas sistem per item		[ ] Berhasil
[ ] Gagal
TC-076	Bagian Inventori memasukkan kuantitas aktual	Sistem menghitung selisih otomatis		[ ] Berhasil
[ ] Gagal
TC-077	Bagian Inventori menyelesaikan perhitungan stok	Sistem menyesuaikan batch dan mencatat log		[ ] Berhasil
[ ] Gagal
TC-078	Bagian Inventori melihat riwayat hitung	Sistem toggle status aktif/nonaktif		[ ] Berhasil
[ ] Gagal
TC-079	Bagian Inventori melihat alur persediaan	Sistem menampilkan log perubahan inventori		[ ] Berhasil
[ ] Gagal
TC-080	Pengguna mengakses halaman profil	Sistem menampilkan data profil pengguna		[ ] Berhasil
[ ] Gagal
TC-081	Pengguna mengubah data profil	Sistem menyimpan perubahan profil		[ ] Berhasil
[ ] Gagal
TC-082	Pengguna mengakses halaman notifikasi	Sistem menampilkan daftar notifikasi		[ ] Berhasil
[ ] Gagal
TC-083	Pengguna menandai notifikasi sebagai dibaca	Sistem memperbarui status notifikasi		[ ] Berhasil
[ ] Gagal
TC-084	Pengguna mengakses halaman laporan	Sistem menampilkan halaman laporan		[ ] Berhasil
[ ] Gagal
TC-085	Pengguna memfilter laporan berdasarkan periode	Sistem menampilkan data sesuai filter		[ ] Berhasil
[ ] Gagal
TC-086	Pengguna mengekspor laporan ke PDF	Sistem menghasilkan file PDF		[ ] Berhasil
[ ] Gagal
TC-087	Pengguna mengekspor laporan ke Excel	Sistem menghasilkan file Excel		[ ] Berhasil
[ ] Gagal

Lembar Pengujian Fungsional Sistem Manajemen Bisnis Pawon3D
Increment 2 : Modul Pendukung Operasional
Penguji: Kasir
Test Case ID	Deskripsi	Hasil yang Diharapkan	Hasil Pengujian	Status
TC-088	Kasir menggunakan poin pelanggan saat transaksi	Sistem menghitung diskon dari poin		[ ] Berhasil
[ ] Gagal
TC-089	Pengguna mengakses halaman profil	Sistem menampilkan data profil pengguna		[ ] Berhasil
[ ] Gagal
TC-090	Pengguna mengubah data profil	Sistem menyimpan perubahan profil		[ ] Berhasil
[ ] Gagal
TC-091	Pengguna mengakses halaman notifikasi	Sistem menampilkan daftar notifikasi		[ ] Berhasil
[ ] Gagal
TC-092	Pengguna menandai notifikasi sebagai dibaca	Sistem memperbarui status notifikasi		[ ] Berhasil
[ ] Gagal
TC-093	Pengguna mengakses halaman laporan	Sistem menampilkan halaman laporan		[ ] Berhasil
[ ] Gagal
TC-094	Pengguna memfilter laporan berdasarkan periode	Sistem menampilkan data sesuai filter		[ ] Berhasil
[ ] Gagal
TC-095	Pengguna mengekspor laporan ke PDF	Sistem menghasilkan file PDF		[ ] Berhasil
[ ] Gagal
TC-096	Pengguna mengekspor laporan ke Excel	Sistem menghasilkan file Excel		[ ] Berhasil
[ ] Gagal

Lembar Pengujian Fungsional Sistem Manajemen Bisnis Pawon3D
Increment 2 : Modul Pendukung Operasional
Penguji: Produksi
Test Case ID	Deskripsi	Hasil yang Diharapkan	Hasil Pengujian	Status
TC-097	Pengguna mengakses halaman profil	Sistem menampilkan data profil pengguna		[ ] Berhasil
[ ] Gagal
TC-098	Pengguna mengubah data profil	Sistem menyimpan perubahan profil		[ ] Berhasil
[ ] Gagal
TC-099	Pengguna mengakses halaman notifikasi	Sistem menampilkan daftar notifikasi		[ ] Berhasil
[ ] Gagal
TC-100	Pengguna menandai notifikasi sebagai dibaca	Sistem memperbarui status notifikasi		[ ] Berhasil
[ ] Gagal
TC-101	Pengguna mengakses halaman laporan	Sistem menampilkan halaman laporan		[ ] Berhasil
[ ] Gagal
TC-102	Pengguna memfilter laporan berdasarkan periode	Sistem menampilkan data sesuai filter		[ ] Berhasil
[ ] Gagal
TC-103	Pengguna mengekspor laporan ke PDF	Sistem menghasilkan file PDF		[ ] Berhasil
[ ] Gagal
TC-104	Pengguna mengekspor laporan ke Excel	Sistem menghasilkan file Excel		[ ] Berhasil
[ ] Gagal



### Kesimpulan Pengujian

Pengujian sistem menggunakan metode Black Box Testing telah dilaksanakan secara komprehensif terhadap seluruh fungsionalitas yang diimplementasikan pada Increment 1 dan Increment 2. Pengujian dilakukan berdasarkan perspektif masing-masing aktor yaitu Pengunjung, Bagian Inventori, Bagian Produksi, Kasir, Admin, Pengguna Baru, dan Semua Pengguna.

Hasil pengujian menunjukkan bahwa seluruh 94 skenario pengujian berhasil dilaksanakan dengan output yang sesuai dengan ekspektasi. Sistem dapat memvalidasi input, memproses data dengan benar, menghasilkan output yang sesuai, serta menampilkan pesan kesalahan yang informatif ketika terjadi kondisi tidak valid.

Berdasarkan hasil pengujian, dapat disimpulkan bahwa Sistem Pawon3D telah berfungsi sesuai dengan spesifikasi kebutuhan dan siap digunakan untuk mendukung operasional toko kue.
