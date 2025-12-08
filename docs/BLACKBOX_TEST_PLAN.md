# Rencana Blackbox Testing

## Tujuan

Memastikan seluruh alur utama aplikasi berfungsi sesuai kebutuhan bisnis tanpa melihat kode sumber (blackbox). Fokus pada input, output, dan perilaku UI/API.

## Ruang Lingkup

-   Akses publik (landing pages, aktivasi akun).
-   Akses terautentikasi (dashboard, modul pekerja, peran, pelanggan, inventori, produk, belanja, hitung, produksi, transaksi/POS, notifikasi, pengaturan, profil usaha, metode pembayaran, cetak/PDF).
-   Peran/otorisasi (spatie permission) untuk memastikan pembatasan akses.

## Pendekatan & Teknik

-   Pengujian fungsional berbasis skenario pengguna.
-   Input valid/invalid, batas nilai, dan alur alternatif.
-   Otorisasi per peran (akses diizinkan/ditolak).
-   Validasi file upload (tipe/ukuran).
-   Verifikasi output (tampilan UI, data tersimpan, PDF terbuat/terdownload, notifikasi tampil).

## Peran & Tanggung Jawab

-   QA: mengeksekusi skenario, dokumentasi hasil, membuka tiket bug.
-   Dev: reproduksi & perbaikan bug.
-   Product/Owner: verifikasi penerimaan fitur kritikal.

## Lingkungan Uji

-   URL: https://pawon3d.test (sesuaikan dengan lingkungan lokal Anda).
-   Browser: Chrome/Edge versi terbaru.
-   Data: akun uji dengan variasi peran (kasir, produksi, inventori, manajemen pekerja, admin lengkap).
-   File contoh: gambar .jpg/.png < 2MB untuk unggah pekerja/produk.

## Kriteria Masuk (Entry)

-   Build terpasang & dapat diakses.
-   Akun uji & peran sudah disiapkan.
-   Basis data dalam kondisi siap (seed data minimal produk, kategori, peran, pekerja).

## Kriteria Keluar (Exit)

-   Seluruh skenario prioritas tinggi selesai.
-   Tidak ada blocker/critical terbuka; major diminimalisir; minor ditriase.
-   Hasil uji didokumentasikan (pass/fail/evidence).

## Skenario Uji Utama (ringkas)

1. **Landing & Aktivasi Akun**

-   Buka halaman landing utama, produk, FAQ, cara pesan → semua termuat.
-   Detail produk publik dapat dibuka.
-   Aktivasi akun via tautan token valid → berhasil; token kadaluarsa/invalid → pesan gagal.

2. **Autentikasi & Otorisasi**

-   Login user sah → sukses ke dashboard/redirect ringkasan sesuai peran.
-   Login salah kredensial → pesan gagal.
-   Akses route terproteksi tanpa login → redirect login.
-   Coba akses modul tanpa izin per peran (kasir, produksi, inventori, pekerja) → ditolak/redirect menunggu-peran.

3. **Pekerja (Manajemen Pekerja)**

-   List pekerja tampil pagination/filter (jika ada).
-   Tambah pekerja dengan data valid + unggah foto <2MB → tersimpan, foto muncul, peran terpasang.
-   Validasi: email duplikat, password <8 atau tanpa campuran huruf/angka, file >2MB/tipe salah → gagal dengan pesan.
-   Edit pekerja: ubah data, peran, status pekerja (Aktif/Nonaktif) → tersimpan; log riwayat muncul.
-   Hapus pekerja → data hilang, foto terhapus (cek tidak dapat login lagi jika nonaktif/dihapus).
-   Cetak pekerja PDF → file terunduh/terbuka.

4. **Peran**

-   List peran & izin tampil.
-   Tambah/ubah peran dengan kombinasi permission → tersimpan.
-   Hapus peran yang tidak terpakai → sukses; peran terpakai → ditolak (jika ada pembatasan).
-   Cetak peran PDF → berhasil.

5. **Pelanggan**

-   List pelanggan tampil.
-   Lihat rincian pelanggan tertentu → data sesuai.

6. **Inventori & Katalog**

-   Kategori, Satuan Ukur, Jenis Biaya, Kategori Persediaan: list, tambah, ubah, cetak PDF masing-masing.
-   Produk: tambah/ubah dengan gambar, harga, stok awal; validasi file & field wajib; cetak PDF.
-   Bahan baku & Supplier: tambah/ubah/hapus, cetak PDF.

7. **Belanja (Expense)**

-   Buat rencana belanja, ubah, dan mulai/dapatkan belanja.
-   Validasi field wajib, tanggal, jumlah.
-   Lihat riwayat belanja; cetak PDF list & detail.

8. **Hitung (Stock Count)**

-   Buat rencana hitung, mulai aksi, edit, lihat rincian, riwayat.
-   Validasi angka/jumlah; cetak PDF list & detail.

9. **Produksi**

-   Tambah produksi (metode berbeda), edit, mulai produksi, lihat antrian & riwayat.
-   Produksi pesanan & siap beli: tambah/edit/rincian; cetak PDF list & detail.

10. **Transaksi & POS**

-   Buat pesanan via POS sesuai peran kasir → tersimpan, struk bisa dicetak.
-   Edit transaksi, lihat rincian pesanan/produk, riwayat sesi penjualan.
-   Cetak laporan transaksi & detail transaksi PDF.

11. **Dashboard & Ringkasan**

-   Ringkasan umum/kasir/produksi/inventori sesuai izin → termuat tanpa error; data agregat wajar.

12. **Notifikasi**

-   Halaman notifikasi memuat list; tandai terbaca satuan & massal → status berubah.

13. **Pengaturan**

-   Profil saya: perbarui profil (termasuk upload foto jika ada) → tersimpan.
-   Profil usaha: ubah data → tersimpan (peran yang berhak saja).
-   Metode pembayaran: tambah/ubah/hapus → tersimpan (peran yang berhak).
-   Panduan pengguna dapat dibuka.

14. **File & Upload**

-   Unggah foto pekerja/produk dengan batas 2MB dan tipe jpg/jpeg/png → sukses.
-   Coba unggah tipe salah atau >2MB → ditolak dengan pesan.

15. **PDF/Export**

-   Semua endpoint cetak menghasilkan file dan dapat dibuka (pekerja, peran, kategori, satuan, jenis biaya, kategori persediaan, produk, supplier, bahan baku, belanja, belanja detail, hitung, hitung detail, produksi, produksi detail, transaksi, transaksi detail).

## Data Uji yang Disarankan

-   Akun per peran: kasir, produksi, inventori, manajemen pekerja, admin penuh.
-   Sample produk/bahan/supplier/kategori untuk menghindari halaman kosong.
-   File gambar valid (jpg/png 1MB) dan tidak valid (pdf, 5MB) untuk uji negatif.

## Pelaporan Hasil

-   Catat: ID skenario, langkah, hasil (Pass/Fail), bukti (screenshot/PDF), catatan.
-   Bug dilaporkan dengan langkah reproduksi, data uji, harapan vs hasil aktual.

## Retest & Regression

-   Setelah perbaikan bug, jalankan retest kasus terkait.
-   Regression singkat pada modul yang terdampak perubahan (misal: pekerja → cek login/otorisasi & riwayat).
