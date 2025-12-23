# Sequence Diagram - Sistem Pawon3D

## Pendahuluan

Sequence diagram menggambarkan interaksi antar objek dalam urutan waktu. Diagram ini menunjukkan bagaimana komponen sistem berkomunikasi untuk menyelesaikan suatu proses.

## Komponen Sistem

Komponen utama yang terlibat dalam sequence diagram:

| Komponen | Tipe | Deskripsi |
|----------|------|-----------|
| User | Actor | Pengguna sistem |
| Livewire Component | Boundary | Antarmuka pengguna (View) |
| Controller/Service | Control | Logika bisnis |
| Model | Entity | Representasi data |
| Database | Storage | Penyimpanan data |

---

## Increment 1: Sequence Diagram Fungsionalitas Inti

### 1. Tambah Bahan Baku

**Referensi:** `puml/sequence-tambah-bahan-baku.puml`

**Aktor:** Bagian Inventori

**Partisipan:**
- Material/Form (Livewire)
- Material (Model)
- InventoryLog (Model)
- Database

**Alur Interaksi:**
1. User mengisi formulir bahan baku
2. Livewire memvalidasi input
3. Material model meng-generate UUID
4. Material disimpan ke database
5. InventoryLog mencatat aktivitas
6. Sistem menampilkan notifikasi sukses

---

### 2. Proses Belanja dari Supplier

**Referensi:** `puml/sequence-belanja.puml`

**Aktor:** Bagian Inventori

**Partisipan:**
- Expense/Form, Expense/Mulai (Livewire)
- Expense, ExpenseDetail (Model)
- MaterialBatch (Model)
- Material (Model)
- Database

**Alur Interaksi:**
1. User membuat rencana belanja
2. Expense model meng-generate nomor belanja
3. ExpenseDetail menyimpan daftar item
4. User memulai proses belanja
5. User mencatat hasil belanja per item
6. MaterialBatch dibuat untuk setiap item
7. Material.recalculateStatus() dipanggil
8. Status bahan diperbarui

---

### 3. Tambah Produk dengan Komposisi

**Referensi:** `puml/sequence-tambah-produk.puml`

**Aktor:** Bagian Inventori

**Partisipan:**
- Product/Form (Livewire)
- Product, ProductComposition, OtherCost (Model)
- Material (Model)
- Database

**Alur Interaksi:**
1. User mengisi data produk
2. User menambahkan komposisi bahan
3. User menambahkan biaya tambahan (opsional)
4. Product model meng-generate UUID
5. ProductComposition menyimpan relasi bahan
6. OtherCost menyimpan biaya tambahan
7. Sistem menghitung harga modal

---

### 4. Proses Produksi

**Referensi:** `puml/sequence-produksi.puml`

**Aktor:** Bagian Produksi

**Partisipan:**
- Production/Mulai (Livewire)
- Production, ProductionDetail, ProductionWorker (Model)
- Transaction (Model)
- Material (Model)
- InventoryLog (Model)
- Database

**Alur Interaksi:**
1. User memilih pesanan dari antrian
2. Production model meng-generate nomor produksi
3. User memilih pekerja
4. ProductionWorker menyimpan penugasan
5. User memulai produksi
6. Loop untuk setiap bahan dalam komposisi:
   - Material.reduceQuantity() mengurangi stok FIFO
   - InventoryLog mencatat pengurangan
7. User menyelesaikan produksi
8. Production.status diperbarui
9. Transaction.status diperbarui

---

### 5. Transaksi Pesanan (Kotak/Reguler)

**Referensi:** `puml/sequence-transaksi-pesanan.puml`

**Aktor:** Kasir

**Partisipan:**
- Transaction/Pesanan, Transaction/BuatPesanan (Livewire)
- Transaction, TransactionDetail (Model)
- Customer (Model)
- Production (Model)
- Database

**Alur Interaksi:**
1. User memilih metode pesanan
2. User memasukkan data pelanggan (opsional)
3. Customer dicari/dibuat jika belum ada
4. User memilih produk dan kuantitas
5. Transaction model meng-generate invoice number
6. TransactionDetail menyimpan item pesanan
7. Production dibuat otomatis
8. Transaksi masuk antrian produksi

---

### 6. Transaksi Siap Beli

**Referensi:** `puml/sequence-transaksi-siap-beli.puml`

**Aktor:** Kasir

**Partisipan:**
- Transaction/SiapBeli (Livewire)
- Transaction, TransactionDetail (Model)
- Product (Model)
- Customer (Model)
- Database

**Alur Interaksi:**
1. User memilih tanggal produksi
2. Sistem menampilkan stok tersedia
3. User memilih produk
4. User memasukkan data pelanggan (opsional)
5. Transaction model meng-generate invoice number
6. TransactionDetail menyimpan item
7. Product.stock dikurangi
8. Transaksi langsung ke proses pembayaran

---

### 7. Proses Pembayaran

**Referensi:** `puml/sequence-pembayaran.puml`

**Aktor:** Kasir

**Partisipan:**
- Transaction/RincianPesanan (Livewire)
- Transaction (Model)
- Payment (Model)
- PaymentChannel (Model)
- Customer, PointsHistory (Model)
- Database

**Alur Interaksi:**
1. User melihat detail transaksi
2. User memilih metode pembayaran
3. User memasukkan jumlah bayar
4. Alt: Jika menggunakan poin
   - Customer.points dikurangi
   - PointsHistory mencatat penggunaan
5. Payment model meng-generate receipt number
6. Payment disimpan ke database
7. Transaction.status diperbarui
8. Customer.points bertambah (dari transaksi baru)
9. Sistem mencetak struk

---

## Increment 2: Sequence Diagram Fungsionalitas Pendukung

### 8. Registrasi dan Aktivasi User

**Referensi:** `puml/sequence-registrasi-user.puml`

**Aktor:** Admin, User Baru

**Partisipan:**
- User/Form (Livewire)
- ActivateAccount (Livewire)
- User (Model)
- UserInvitationNotification
- Database

**Alur Interaksi:**
1. Admin mengisi data user baru
2. User model meng-generate UUID
3. User.sendInvitation() dipanggil
4. Invitation token di-generate
5. Email undangan dikirim
6. User baru mengakses link aktivasi
7. Sistem memvalidasi token
8. User mengatur password
9. User.activateWithPassword() dipanggil
10. Akun diaktifkan

---

### 9. Kelola Pelanggan

**Referensi:** `puml/sequence-kelola-pelanggan.puml`

**Aktor:** Kasir

**Partisipan:**
- Customer/Index, Customer/Show (Livewire)
- Customer (Model)
- Transaction (Model)
- PointsHistory (Model)
- Database

**Alur Interaksi:**
1. User mengakses halaman pelanggan
2. Sistem query Customer dengan relasi
3. User melihat detail pelanggan
4. Sistem menampilkan riwayat transaksi
5. Sistem menampilkan riwayat poin

---

### 10. Penggunaan Poin Pelanggan

**Referensi:** `puml/sequence-poin-pelanggan.puml`

**Aktor:** Kasir

**Partisipan:**
- Transaction/RincianPesanan (Livewire)
- Transaction (Model)
- Customer (Model)
- PointsHistory (Model)
- Database

**Alur Interaksi:**
1. Kasir memilih gunakan poin
2. Sistem menampilkan poin tersedia
3. Kasir memasukkan jumlah poin
4. Sistem menghitung diskon
5. Transaction.points_used diperbarui
6. Customer.points dikurangi
7. PointsHistory mencatat penggunaan
8. Total pembayaran diperbarui

---

### 11. Stock Opname

**Referensi:** `puml/sequence-stock-opname.puml`

**Aktor:** Bagian Inventori

**Partisipan:**
- Hitung/Form, Hitung/Mulai (Livewire)
- Hitung, HitungDetail (Model)
- MaterialBatch (Model)
- InventoryLog (Model)
- Database

**Alur Interaksi:**
1. User membuat rencana hitung
2. Hitung model meng-generate nomor hitung
3. HitungDetail menyimpan daftar item
4. User memulai penghitungan
5. Loop untuk setiap item:
   - User memasukkan kuantitas aktual
   - Sistem menghitung selisih
   - MaterialBatch.batch_quantity diperbarui
   - InventoryLog mencatat penyesuaian
6. Hitung.status diperbarui

---

### 12. Generate Laporan PDF/Excel

**Referensi:** `puml/sequence-generate-laporan.puml`

**Aktor:** Pengguna

**Partisipan:**
- Dashboard/LaporanKasir (Livewire)
- PdfController
- Export Class (Maatwebsite)
- DomPDF
- Model-model terkait
- Database

**Alur Interaksi:**
1. User mengatur filter periode
2. User memilih format ekspor
3. Alt: PDF
   - Controller memanggil DomPDF
   - View laporan di-render
   - PDF di-generate
4. Alt: Excel
   - Export class mengambil data
   - Spreadsheet di-generate
5. File diunduh oleh user

---

### 13. Login

**Referensi:** `puml/sequence-login.puml`

**Aktor:** Pengguna

**Partisipan:**
- Auth/Login (Livewire)
- Auth (Laravel)
- RateLimiter (Laravel)
- User (Model)
- Database

**Alur Interaksi:**
1. User memasukkan email dan password
2. Livewire memvalidasi format input
3. Sistem memeriksa Rate Limiter
4. Auth::attempt memanggil database untuk verifikasi kredensial
5. Jika berhasil, sistem mengambil data User
6. Sistem memverifikasi status `activated_at` dan `is_active`
7. Sistem me-regenerasi session dan mengarahkan pengguna ke dashboard

---

## Ringkasan Sequence Diagram

| No | Diagram | Increment | File PUML |
|----|---------|-----------|-----------|
| 1 | Tambah Bahan Baku | 1 | sequence-tambah-bahan-baku.puml |
| 2 | Proses Belanja | 1 | sequence-belanja.puml |
| 3 | Tambah Produk | 1 | sequence-tambah-produk.puml |
| 4 | Proses Produksi | 1 | sequence-produksi.puml |
| 5 | Transaksi Pesanan | 1 | sequence-transaksi-pesanan.puml |
| 6 | Transaksi Siap Beli | 1 | sequence-transaksi-siap-beli.puml |
| 7 | Proses Pembayaran | 1 | sequence-pembayaran.puml |
| 8 | Registrasi User | 2 | sequence-registrasi-user.puml |
| 9 | Kelola Pelanggan | 2 | sequence-kelola-pelanggan.puml |
| 10 | Penggunaan Poin | 2 | sequence-poin-pelanggan.puml |
| 11 | Stock Opname | 2 | sequence-stock-opname.puml |
| 12 | Generate Laporan | 2 | sequence-generate-laporan.puml |
| 13 | Login | 2 | sequence-login.puml |
