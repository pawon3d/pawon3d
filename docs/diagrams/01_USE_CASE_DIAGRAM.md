# USE CASE DIAGRAM

## Sistem Informasi Manajemen Toko Kue

### Penjelasan Diagram

Use case diagram berikut menggambarkan keseluruhan interaksi antara aktor dengan Sistem Informasi Manajemen Toko Kue. Diagram ini melibatkan lima aktor utama, yaitu Pemilik, Kasir, Produksi, Inventori, dan Pelanggan. Setiap aktor memiliki hak akses yang berbeda-beda sesuai dengan tanggung jawab dan kebutuhan operasional masing-masing, yang telah diidentifikasi melalui analisis kebutuhan sebelumnya.

Berikut adalah penjelasan peran masing-masing aktor dalam sistem:

1. Pemilik: Memiliki akses penuh ke seluruh modul sistem, terutama untuk melihat laporan, mengelola pekerja, peran, pelanggan, dan pengaturan usaha.
2. Kasir: Bertanggung jawab atas operasional penjualan, termasuk mengelola pesanan, memproses pembayaran, mencetak struk, dan mengelola sesi penjualan.
3. Produksi: Menangani perencanaan dan pelaksanaan produksi, mulai dari merencanakan, memulai, hingga menyelesaikan proses produksi.
4. Inventori: Mengelola persediaan bahan baku, produk, belanja, stok hitung, serta menerima alert terkait stok rendah dan bahan expired.
5. Pelanggan: Aktor eksternal yang dapat mengakses landing page untuk melihat katalog produk, cara pemesanan, dan FAQ.

Sistem ini dibagi ke dalam enam modul utama, yaitu Modul Kasir, Modul Produksi, Modul Inventori, Modul Manajemen, Modul Notifikasi, dan Modul Landing Page. Relasi antar use case menggunakan stereotype extend untuk menunjukkan variasi perilaku dan include untuk menunjukkan ketergantungan fungsional.

---

### Diagram Use Case (PlantUML)

```plantuml
@startuml Use Case Diagram - Sistem Manajemen Toko Kue

left to right direction
skinparam packageStyle rectangle
skinparam actorStyle awesome

' === AKTOR ===
actor "Pemilik" as owner
actor "Kasir" as cashier
actor "Produksi" as production
actor "Inventori" as inventory
actor "Pelanggan" as customer

' === SISTEM ===
rectangle "Sistem Informasi Manajemen Toko Kue" {

    ' --- MODUL KASIR ---
    package "Modul Kasir" {
        usecase "Mengelola Pesanan" as UC_ORDER
        usecase "Memproses Pembayaran" as UC_PAYMENT
        usecase "Mencetak Struk" as UC_RECEIPT
        usecase "Membatalkan Pesanan" as UC_CANCEL_ORDER
        usecase "Mengelola Sesi Penjualan" as UC_SHIFT
        usecase "Melihat Laporan Kasir" as UC_REPORT_CASHIER
    }

    ' --- MODUL PRODUKSI ---
    package "Modul Produksi" {
        usecase "Merencanakan Produksi" as UC_PLAN_PROD
        usecase "Memulai Produksi" as UC_START_PROD
        usecase "Menyelesaikan Produksi" as UC_FINISH_PROD
        usecase "Membatalkan Produksi" as UC_CANCEL_PROD
        usecase "Melihat Antrian Produksi" as UC_QUEUE_PROD
        usecase "Melihat Laporan Produksi" as UC_REPORT_PROD
    }

    ' --- MODUL INVENTORI ---
    package "Modul Inventori" {
        usecase "Mengelola Produk" as UC_PRODUCT
        usecase "Mengelola Bahan Baku" as UC_MATERIAL
        usecase "Mengelola Belanja" as UC_EXPENSE
        usecase "Merencanakan Belanja" as UC_PLAN_EXP
        usecase "Memulai Belanja" as UC_START_EXP
        usecase "Menyelesaikan Belanja" as UC_FINISH_EXP
        usecase "Mengelola Stok Hitung" as UC_STOCK_COUNT
        usecase "Melihat Alur Persediaan" as UC_INVENTORY_FLOW
        usecase "Melihat Laporan Inventori" as UC_REPORT_INV
        usecase "Mengelola Kategori Persediaan" as UC_CAT_INV
        usecase "Mengelola Supplier" as UC_SUPPLIER
    }

    ' --- MODUL MANAJEMEN ---
    package "Modul Manajemen" {
        usecase "Mengelola Pekerja" as UC_WORKER
        usecase "Mengelola Peran & Hak Akses" as UC_ROLE
        usecase "Mengelola Pelanggan" as UC_CUSTOMER
        usecase "Mengelola Profil Usaha" as UC_STORE_PROFILE
        usecase "Mengelola Metode Pembayaran" as UC_PAYMENT_METHOD
    }

    ' --- MODUL NOTIFIKASI ---
    package "Modul Notifikasi" {
        usecase "Melihat Notifikasi" as UC_NOTIF
        usecase "Menerima Alert Stok Rendah" as UC_LOW_STOCK
        usecase "Menerima Alert Expired" as UC_EXPIRED
    }

    ' --- MODUL LANDING PAGE ---
    package "Modul Landing Page" {
        usecase "Melihat Katalog Produk" as UC_CATALOG
        usecase "Melihat Cara Pemesanan" as UC_HOW_ORDER
        usecase "Melihat FAQ" as UC_FAQ
    }
}

' === RELASI AKTOR - USE CASE ===

' Pemilik - akses penuh
owner --> UC_REPORT_CASHIER
owner --> UC_REPORT_PROD
owner --> UC_REPORT_INV
owner --> UC_WORKER
owner --> UC_ROLE
owner --> UC_CUSTOMER
owner --> UC_STORE_PROFILE
owner --> UC_PAYMENT_METHOD
owner --> UC_NOTIF

' Kasir
cashier --> UC_ORDER
cashier --> UC_PAYMENT
cashier --> UC_RECEIPT
cashier --> UC_CANCEL_ORDER
cashier --> UC_SHIFT
cashier --> UC_REPORT_CASHIER
cashier --> UC_NOTIF

' Produksi
production --> UC_PLAN_PROD
production --> UC_START_PROD
production --> UC_FINISH_PROD
production --> UC_CANCEL_PROD
production --> UC_QUEUE_PROD
production --> UC_REPORT_PROD
production --> UC_NOTIF

' Inventori
inventory --> UC_PRODUCT
inventory --> UC_MATERIAL
inventory --> UC_EXPENSE
inventory --> UC_STOCK_COUNT
inventory --> UC_INVENTORY_FLOW
inventory --> UC_REPORT_INV
inventory --> UC_CAT_INV
inventory --> UC_SUPPLIER
inventory --> UC_NOTIF
inventory --> UC_LOW_STOCK
inventory --> UC_EXPIRED

' Pelanggan (publik)
customer --> UC_CATALOG
customer --> UC_HOW_ORDER
customer --> UC_FAQ

' === RELASI EXTEND & INCLUDE ===

' Mengelola Belanja extends
UC_EXPENSE <|-- UC_PLAN_EXP : <<extend>>
UC_EXPENSE <|-- UC_START_EXP : <<extend>>
UC_EXPENSE <|-- UC_FINISH_EXP : <<extend>>

' Include relationships
UC_ORDER ..> UC_PAYMENT : <<include>>
UC_ORDER ..> UC_RECEIPT : <<include>>
UC_ORDER ..> UC_QUEUE_PROD : <<include>>
UC_START_PROD ..> UC_MATERIAL : <<include>>
UC_FINISH_PROD ..> UC_PRODUCT : <<include>>

@enduml
```

---

## Daftar Use Case

| No  | Kode  | Nama Use Case                 | Aktor              | Deskripsi Singkat                                  |
| --- | ----- | ----------------------------- | ------------------ | -------------------------------------------------- |
| 1   | UC-01 | Mengelola Pesanan             | Kasir              | Membuat, mengubah, dan menghapus pesanan pelanggan |
| 2   | UC-02 | Memproses Pembayaran          | Kasir              | Menerima pembayaran DP atau lunas                  |
| 3   | UC-03 | Mencetak Struk                | Kasir              | Mencetak struk transaksi                           |
| 4   | UC-04 | Membatalkan Pesanan           | Kasir              | Membatalkan pesanan dan proses refund              |
| 5   | UC-05 | Mengelola Sesi Penjualan      | Kasir              | Membuka dan menutup sesi/shift kasir               |
| 6   | UC-06 | Melihat Laporan Kasir         | Kasir, Pemilik     | Melihat ringkasan dan laporan transaksi            |
| 7   | UC-07 | Merencanakan Produksi         | Produksi           | Membuat rencana produksi siap beli                 |
| 8   | UC-08 | Memulai Produksi              | Produksi           | Memulai proses produksi                            |
| 9   | UC-09 | Menyelesaikan Produksi        | Produksi           | Menandai produksi selesai, update stok             |
| 10  | UC-10 | Membatalkan Produksi          | Produksi           | Membatalkan rencana produksi                       |
| 11  | UC-11 | Melihat Antrian Produksi      | Produksi           | Melihat daftar pesanan yang perlu diproduksi       |
| 12  | UC-12 | Melihat Laporan Produksi      | Produksi, Pemilik  | Melihat ringkasan dan laporan produksi             |
| 13  | UC-13 | Mengelola Produk              | Inventori          | Menambah, mengubah, menghapus produk               |
| 14  | UC-14 | Mengelola Bahan Baku          | Inventori          | Mengelola data bahan baku/material                 |
| 15  | UC-15 | Mengelola Belanja             | Inventori          | Mengelola pembelian bahan baku                     |
| 16  | UC-16 | Merencanakan Belanja          | Inventori          | Membuat rencana belanja bahan baku                 |
| 17  | UC-17 | Memulai Belanja               | Inventori          | Melakukan proses belanja                           |
| 18  | UC-18 | Menyelesaikan Belanja         | Inventori          | Menyelesaikan belanja, update stok                 |
| 19  | UC-19 | Mengelola Stok Hitung         | Inventori          | Hitung stok, catat rusak/hilang                    |
| 20  | UC-20 | Melihat Alur Persediaan       | Inventori          | Tracking pergerakan bahan baku                     |
| 21  | UC-21 | Melihat Laporan Inventori     | Inventori, Pemilik | Melihat ringkasan dan laporan inventori            |
| 22  | UC-22 | Mengelola Kategori Persediaan | Inventori          | Mengelola kategori bahan baku                      |
| 23  | UC-23 | Mengelola Supplier            | Inventori          | Mengelola data supplier                            |
| 24  | UC-24 | Mengelola Pekerja             | Pemilik            | CRUD data pekerja/karyawan                         |
| 25  | UC-25 | Mengelola Peran & Hak Akses   | Pemilik            | Mengelola role dan permission                      |
| 26  | UC-26 | Mengelola Pelanggan           | Pemilik            | Mengelola data pelanggan dan poin                  |
| 27  | UC-27 | Mengelola Profil Usaha        | Pemilik            | Mengatur profil toko                               |
| 28  | UC-28 | Mengelola Metode Pembayaran   | Pemilik            | Mengatur channel pembayaran                        |
| 29  | UC-29 | Melihat Notifikasi            | Semua User         | Melihat notifikasi sistem                          |
| 30  | UC-30 | Menerima Alert Stok Rendah    | Inventori          | Notifikasi otomatis stok hampir habis              |
| 31  | UC-31 | Menerima Alert Expired        | Inventori          | Notifikasi bahan akan/sudah expired                |
| 32  | UC-32 | Melihat Katalog Produk        | Pelanggan          | Melihat daftar produk di landing page              |
| 33  | UC-33 | Melihat Cara Pemesanan        | Pelanggan          | Melihat panduan cara memesan                       |
| 34  | UC-34 | Melihat FAQ                   | Pelanggan          | Melihat pertanyaan yang sering ditanyakan          |

---

## Narasi Use Case

### UC-01: Mengelola Pesanan

| Komponen                | Deskripsi                                                                                                                                                                                                                                                                                    |
| ----------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Nama Use Case**       | Mengelola Pesanan                                                                                                                                                                                                                                                                            |
| **Kode**                | UC-01                                                                                                                                                                                                                                                                                        |
| **Aktor**               | Kasir                                                                                                                                                                                                                                                                                        |
| **Deskripsi**           | Use case ini memungkinkan kasir untuk membuat, melihat, mengubah, dan menghapus pesanan pelanggan. Pesanan dapat berupa pesanan reguler (custom dengan jadwal), pesanan kotak (box), atau siap beli (dari stok).                                                                             |
| **Pre-condition**       | - Kasir sudah login ke sistem<br>- Kasir memiliki permission `kasir.pesanan.kelola`<br>- Sesi penjualan aktif                                                                                                                                                                                |
| **Post-condition**      | - Pesanan tersimpan dalam database<br>- Stok produk terupdate (untuk siap beli)<br>- Notifikasi terkirim ke bagian terkait                                                                                                                                                                   |
| **Skenario Utama**      | 1. Kasir memilih jenis pesanan<br>2. Kasir memilih produk dari katalog<br>3. Kasir memasukkan jumlah pesanan<br>4. Kasir memasukkan data pelanggan (opsional)<br>5. Sistem menghitung total<br>6. Kasir memproses pembayaran<br>7. Sistem menyimpan pesanan<br>8. Sistem mengirim notifikasi |
| **Skenario Alternatif** | **A1: Pesanan Reguler/Kotak**<br>- Kasir memasukkan jadwal pengambilan<br>- Pesanan masuk antrian produksi<br><br>**A2: Stok Tidak Cukup**<br>- Sistem menampilkan peringatan<br>- Kasir menyesuaikan jumlah                                                                                 |
| **Exception**           | - E1: Produk tidak tersedia - sistem menampilkan error                                                                                                                                                                                                                                       |

---

### UC-02: Memproses Pembayaran

| Komponen                | Deskripsi                                                                                                                                                                                                                                                                                                                                  |
| ----------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **Nama Use Case**       | Memproses Pembayaran                                                                                                                                                                                                                                                                                                                       |
| **Kode**                | UC-02                                                                                                                                                                                                                                                                                                                                      |
| **Aktor**               | Kasir                                                                                                                                                                                                                                                                                                                                      |
| **Deskripsi**           | Use case ini memungkinkan kasir untuk menerima pembayaran dari pelanggan. Pembayaran bisa berupa uang muka (DP) atau lunas.                                                                                                                                                                                                                |
| **Pre-condition**       | - Pesanan sudah dibuat<br>- Kasir sudah login<br>- Metode pembayaran aktif tersedia                                                                                                                                                                                                                                                        |
| **Post-condition**      | - Status pembayaran terupdate<br>- Riwayat pembayaran tersimpan<br>- Notifikasi terkirim<br>- Poin pelanggan bertambah (jika lunas)                                                                                                                                                                                                        |
| **Skenario Utama**      | 1. Kasir memilih pesanan yang akan dibayar<br>2. Kasir memilih metode pembayaran<br>3. Kasir memasukkan jumlah yang dibayar<br>4. Sistem memvalidasi pembayaran<br>5. Sistem mengupdate status pembayaran<br>6. Sistem menghitung kembalian (jika cash)<br>7. Sistem menambah poin pelanggan (jika lunas)<br>8. Sistem mengirim notifikasi |
| **Skenario Alternatif** | **A1: Pembayaran DP**<br>- Status menjadi "Uang Muka"<br>- Pesanan menunggu pelunasan<br><br>**A2: Pelunasan**<br>- Status menjadi "Lunas"<br>- Poin pelanggan bertambah (Rp 10.000 = 1 poin)                                                                                                                                              |
| **Exception**           | - E1: Jumlah pembayaran kurang - sistem menampilkan error                                                                                                                                                                                                                                                                                  |

---

### UC-08: Memulai Produksi

| Komponen                | Deskripsi                                                                                                                                                                                                                                                                                                                                           |
| ----------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Nama Use Case**       | Memulai Produksi                                                                                                                                                                                                                                                                                                                                    |
| **Kode**                | UC-08                                                                                                                                                                                                                                                                                                                                               |
| **Aktor**               | Produksi                                                                                                                                                                                                                                                                                                                                            |
| **Deskripsi**           | Use case ini memungkinkan bagian produksi untuk memulai proses produksi. Sistem akan mengurangi stok bahan baku sesuai komposisi produk.                                                                                                                                                                                                            |
| **Pre-condition**       | - Produksi sudah login<br>- Ada rencana produksi yang akan dimulai<br>- Bahan baku tersedia                                                                                                                                                                                                                                                         |
| **Post-condition**      | - Status produksi menjadi "Proses"<br>- Stok bahan baku berkurang<br>- Notifikasi terkirim                                                                                                                                                                                                                                                          |
| **Skenario Utama**      | 1. Produksi melihat antrian produksi<br>2. Produksi memilih produksi yang akan dimulai<br>3. Sistem memeriksa ketersediaan bahan baku<br>4. Produksi menentukan pekerja yang terlibat<br>5. Produksi mengkonfirmasi mulai produksi<br>6. Sistem mengurangi stok bahan baku<br>7. Sistem mengupdate status produksi<br>8. Sistem mengirim notifikasi |
| **Skenario Alternatif** | **A1: Bahan Baku Kurang**<br>- Sistem menampilkan peringatan<br>- Produksi dapat menyesuaikan jumlah                                                                                                                                                                                                                                                |
| **Exception**           | - E1: Bahan baku habis - produksi tidak dapat dimulai                                                                                                                                                                                                                                                                                               |

---

### UC-18: Menyelesaikan Belanja

| Komponen                | Deskripsi                                                                                                                                                                                                                                                                                                                                                                                                                    |
| ----------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Nama Use Case**       | Menyelesaikan Belanja                                                                                                                                                                                                                                                                                                                                                                                                        |
| **Kode**                | UC-18                                                                                                                                                                                                                                                                                                                                                                                                                        |
| **Aktor**               | Inventori                                                                                                                                                                                                                                                                                                                                                                                                                    |
| **Deskripsi**           | Use case ini memungkinkan bagian inventori untuk menyelesaikan proses belanja bahan baku. Sistem akan menambah stok bahan baku sesuai dengan yang dibeli.                                                                                                                                                                                                                                                                    |
| **Pre-condition**       | - Inventori sudah login<br>- Ada belanja dengan status "Proses"<br>- Permission `inventori.belanja.mulai`                                                                                                                                                                                                                                                                                                                    |
| **Post-condition**      | - Status belanja menjadi "Selesai"<br>- Stok bahan baku bertambah<br>- Batch baru terbuat<br>- Notifikasi terkirim                                                                                                                                                                                                                                                                                                           |
| **Skenario Utama**      | 1. Inventori melihat daftar belanja yang sedang diproses<br>2. Inventori memilih belanja yang akan diselesaikan<br>3. Inventori mengisi jumlah aktual yang dibeli<br>4. Inventori mengisi harga aktual<br>5. Inventori mengisi tanggal expired (jika ada)<br>6. Sistem membuat batch baru untuk setiap bahan<br>7. Sistem mengupdate stok bahan baku<br>8. Sistem mengupdate status belanja<br>9. Sistem mengirim notifikasi |
| **Skenario Alternatif** | **A1: Jumlah Aktual Berbeda**<br>- Sistem mencatat selisih rencana vs aktual<br>- Grand total terupdate                                                                                                                                                                                                                                                                                                                      |
| **Exception**           | - E1: Detail kosong - sistem menampilkan error                                                                                                                                                                                                                                                                                                                                                                               |

---

### UC-19: Mengelola Stok Hitung

| Komponen                | Deskripsi                                                                                                                                                                                                                                                                                                                                                             |
| ----------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Nama Use Case**       | Mengelola Stok Hitung                                                                                                                                                                                                                                                                                                                                                 |
| **Kode**                | UC-19                                                                                                                                                                                                                                                                                                                                                                 |
| **Aktor**               | Inventori                                                                                                                                                                                                                                                                                                                                                             |
| **Deskripsi**           | Use case ini memungkinkan bagian inventori untuk melakukan penghitungan stok fisik, mencatat bahan rusak, dan mencatat bahan hilang. Ini penting untuk menjaga akurasi data inventori.                                                                                                                                                                                |
| **Pre-condition**       | - Inventori sudah login<br>- Permission `inventori.hitung.kelola`                                                                                                                                                                                                                                                                                                     |
| **Post-condition**      | - Stok bahan baku terupdate<br>- Riwayat aksi tercatat<br>- Log inventori tersimpan<br>- Notifikasi terkirim                                                                                                                                                                                                                                                          |
| **Skenario Utama**      | 1. Inventori membuat rencana hitung (pilih aksi: hitung/rusak/hilang)<br>2. Inventori memilih bahan baku yang akan dihitung<br>3. Inventori memulai proses hitung<br>4. Inventori memasukkan jumlah aktual/rusak/hilang<br>5. Sistem menghitung selisih<br>6. Sistem mengupdate stok bahan baku<br>7. Sistem menyimpan log inventori<br>8. Sistem mengirim notifikasi |
| **Skenario Alternatif** | **A1: Catat Rusak**<br>- Stok berkurang sesuai jumlah rusak<br><br>**A2: Catat Hilang**<br>- Stok berkurang sesuai jumlah hilang                                                                                                                                                                                                                                      |
| **Exception**           | - E1: Jumlah melebihi stok - sistem menampilkan error                                                                                                                                                                                                                                                                                                                 |

---

### UC-26: Mengelola Pelanggan

| Komponen                | Deskripsi                                                                                                                                                                                                                                                       |
| ----------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Nama Use Case**       | Mengelola Pelanggan                                                                                                                                                                                                                                             |
| **Kode**                | UC-26                                                                                                                                                                                                                                                           |
| **Aktor**               | Pemilik                                                                                                                                                                                                                                                         |
| **Deskripsi**           | Use case ini memungkinkan pemilik untuk mengelola data pelanggan termasuk informasi kontak dan poin loyalitas.                                                                                                                                                  |
| **Pre-condition**       | - Pemilik sudah login<br>- Permission `manajemen.pelanggan.kelola`                                                                                                                                                                                              |
| **Post-condition**      | - Data pelanggan tersimpan/terupdate<br>- Riwayat poin tersedia                                                                                                                                                                                                 |
| **Skenario Utama**      | 1. Pemilik mengakses menu Pelanggan<br>2. Pemilik dapat melihat daftar pelanggan<br>3. Pemilik dapat menambah pelanggan baru<br>4. Pemilik dapat mengubah data pelanggan<br>5. Pemilik dapat melihat riwayat poin<br>6. Pemilik dapat melihat riwayat transaksi |
| **Skenario Alternatif** | **A1: Pelanggan dari Transaksi**<br>- Pelanggan otomatis ditambahkan saat transaksi dengan nomor telepon baru                                                                                                                                                   |
| **Exception**           | - E1: Nomor telepon duplikat - sistem menampilkan error                                                                                                                                                                                                         |

---

### UC-30: Menerima Alert Stok Rendah

| Komponen           | Deskripsi                                                                                                                                                                                                                                     |
| ------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Nama Use Case**  | Menerima Alert Stok Rendah                                                                                                                                                                                                                    |
| **Kode**           | UC-30                                                                                                                                                                                                                                         |
| **Aktor**          | Inventori                                                                                                                                                                                                                                     |
| **Deskripsi**      | Use case ini adalah fitur otomatis yang mengirim notifikasi ketika stok bahan baku mencapai batas minimum.                                                                                                                                    |
| **Pre-condition**  | - Scheduled command aktif (08:00 setiap hari)<br>- Bahan baku memiliki nilai minimum yang diset                                                                                                                                               |
| **Post-condition** | - Notifikasi terkirim ke user dengan permission inventori<br>- Status bahan diupdate menjadi "Hampir Habis"                                                                                                                                   |
| **Skenario Utama** | 1. Sistem menjalankan pengecekan otomatis (08:00)<br>2. Sistem memeriksa stok vs minimum setiap bahan<br>3. Jika stok <= minimum, sistem mengupdate status<br>4. Sistem membuat notifikasi alert<br>5. Notifikasi dikirim ke bagian inventori |
| **Trigger**        | Scheduled command `inventory:check-alerts`                                                                                                                                                                                                    |
