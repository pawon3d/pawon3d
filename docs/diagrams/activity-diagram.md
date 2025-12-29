# Dokumentasi Activity Diagram

## Pendahuluan

Activity diagram menggambarkan alur kerja (workflow) dari proses-proses utama dalam Sistem Manajemen Toko Kue Pawon3D. Diagram ini menunjukkan urutan aktivitas, keputusan, dan percabangan yang terjadi dalam setiap proses bisnis. Dokumentasi disusun berdasarkan pembagian increment dan aktor yang terlibat.

---

## Activity Diagram Increment 1: Modul Inti Operasional

### AD-001: Login Pengguna
**Aktor**: Semua pengguna terdaftar  
**Deskripsi**: Proses autentikasi pengguna untuk mengakses sistem.

### AD-002: Mengelola Kategori (Inventori)
**Aktor**: Bagian Inventori  
**Deskripsi**: Proses pengelolaan data kategori produk.

### AD-003: Mengelola Bahan Baku (Inventori)
**Aktor**: Bagian Inventori  
**Deskripsi**: Proses pengelolaan data bahan baku.

### AD-004: Mengelola Produk & Komposisi (Inventori)
**Aktor**: Bagian Inventori  
**Deskripsi**: Proses pengelolaan produk beserta resep/komposisinya.

### AD-005: Mengelola Supplier (Inventori)
**Aktor**: Bagian Inventori  
**Deskripsi**: Proses pengelolaan data pemasok bahan baku.

### AD-006: Mengelola Satuan & Konversi (Inventori)
**Aktor**: Bagian Inventori  
**Deskripsi**: Proses pengelolaan satuan produk/bahan dan faktor konversinya.

### AD-007: Proses Belanja (Inventori)
**Aktor**: Bagian Inventori  
**Deskripsi**: Proses perencanaan dan pelaksanaan belanja bahan baku.

### AD-008: Proses Produksi (Produksi)
**Aktor**: Bagian Produksi  
**Deskripsi**: Proses eksekusi produksi berdasarkan pesanan atau stok siap beli.

### AD-009: Transaksi Penjualan (Kasir)
**Aktor**: Kasir  
**Deskripsi**: Proses transaksi penjualan produk.

### AD-010: Shift Kasir (Kasir)
**Aktor**: Kasir  
**Deskripsi**: Proses pembukaan dan penutupan shift kasir harian.

---

## Activity Diagram Increment 2: Modul Pendukung Operasional

### AD-011: Pengelolaan Pengguna (Admin)
**Aktor**: Admin  
**Deskripsi**: Proses pengelolaan data pengguna sistem.

### AD-012: Pengelolaan Peran & Hak Akses (Admin)
**Aktor**: Admin  
**Deskripsi**: Proses pengelolaan peran dan permission hak akses.

### AD-013: Aktivasi Akun (Pengguna Baru)
**Aktor**: Pengguna Baru  
**Deskripsi**: Proses aktivasi akun oleh pengguna baru melalui email.

### AD-014: Stock Opname (Inventori)
**Aktor**: Bagian Inventori  
**Deskripsi**: Proses penghitungan dan penyesuaian stok fisik.

### AD-015: Refund / Pengembalian Dana (Kasir)
**Aktor**: Kasir  
**Deskripsi**: Proses pembatalan transaksi dan pengembalian dana.

### AD-016: Akses Laporan & Ekspor (Semua)
**Aktor**: Semua pengguna terautentikasi  
**Deskripsi**: Proses melihat dan mengekspor laporan ke format PDF/Excel.

---

## Daftar Berkas Diagram Activity

Berikut adalah daftar berkas PlantUML untuk setiap diagram activity:

| ID | Nama Proses | Berkas (PUML) |
|---|---|---|
| AD-001 | Login Pengguna | [activity-inc-1-login.puml](puml/activity-inc-1-login.puml) |
| AD-002 | Mengelola Kategori | [activity-inc-1-kategori.puml](puml/activity-inc-1-kategori.puml) |
| AD-003 | Mengelola Bahan Baku | [activity-inc-1-bahan-baku.puml](puml/activity-inc-1-bahan-baku.puml) |
| AD-004 | Mengelola Produk & Komposisi | [activity-inc-1-produk.puml](puml/activity-inc-1-produk.puml) |
| AD-005 | Mengelola Supplier | [activity-inc-1-supplier.puml](puml/activity-inc-1-supplier.puml) |
| AD-006 | Mengelola Satuan | [activity-inc-1-satuan.puml](puml/activity-inc-1-satuan.puml) |
| AD-007 | Proses Belanja | [activity-inc-1-belanja.puml](puml/activity-inc-1-belanja.puml) |
| AD-008 | Proses Produksi | [activity-inc-1-produksi.puml](puml/activity-inc-1-produksi.puml) |
| AD-009 | Transaksi Penjualan | [activity-inc-1-transaksi.puml](puml/activity-inc-1-transaksi.puml) |
| AD-010 | Shift Kasir | [activity-inc-1-shift.puml](puml/activity-inc-1-shift.puml) |
| AD-011 | Pengelolaan Pengguna | [activity-inc-2-pengguna.puml](puml/activity-inc-2-pengguna.puml) |
| AD-012 | Pengelolaan Peran | [activity-inc-2-peran.puml](puml/activity-inc-2-peran.puml) |
| AD-013 | Aktivasi Akun | [activity-inc-2-aktivasi.puml](puml/activity-inc-2-aktivasi.puml) |
| AD-014 | Stock Opname | [activity-inc-2-stock-opname.puml](puml/activity-inc-2-stock-opname.puml) |
| AD-015 | Refund | [activity-inc-2-refund.puml](puml/activity-inc-2-refund.puml) |
| AD-016 | Laporan | [activity-inc-2-laporan.puml](puml/activity-inc-2-laporan.puml) |

---

## Kesimpulan

Activity diagram Sistem Pawon3D mencakup 16 proses bisnis utama yang menggambarkan alur kerja operasional secara mendetail. Dokumentasi ini memberikan gambaran menyeluruh tentang fungsi sistem dari berbagai perspektif aktor.
