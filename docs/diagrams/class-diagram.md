# Dokumentasi Class Diagram

## Pendahuluan

Class diagram Sistem Manajemen Toko Kue Pawon3D menggambarkan struktur statis sistem yang mencakup entitas-entitas utama, atribut, method, dan relasi antar kelas. Diagram disusun berdasarkan pembagian **Increment 1 (Modul Inti Operasional)** dan **Increment 2 (Modul Pendukung Operasional)**.

### Prinsip Pemisahan Increment

**Increment 1** berisi class dan field yang **langsung digunakan** dalam use case UC-01 sampai UC-11:

-   Data autentikasi dasar (User, Role, Permission dari seeder)
-   Data transaksi inti (Customer, Transaction, Payment, Shift)
-   Data operasional (Product, Material, Supplier, Production)

**Increment 2** berisi:

-   **Extension class**: Penambahan field pada class Increment 1 untuk fitur UC-12 sampai UC-27
-   **Class baru**: Class yang hanya digunakan di Increment 2 (Hitung, Refund, StoreProfile, dll)

---

## Increment 1: Modul Inti Operasional

### Package: Autentikasi & Pengguna

#### 1. User

Kelas yang merepresentasikan pengguna sistem. Pada Increment 1, hanya untuk autentikasi (UC-01).

| Atribut   | Tipe    | Deskripsi              |
| --------- | ------- | ---------------------- |
| id        | UUID    | Primary key            |
| name      | String  | Nama lengkap pengguna  |
| email     | String  | Email unik untuk login |
| phone     | String  | Nomor telepon          |
| password  | String  | Password terenkripsi   |
| image     | String  | Path foto profil       |
| gender    | String  | Jenis kelamin          |
| is_active | Boolean | Status aktif akun      |

**Method**: initials()

**Relasi**: belongsToMany(Role), hasMany(Transaction), hasMany(ProductionWorker), hasMany(Shift [opened_by, closed_by])

**Catatan**: Field aktivasi (invitation_token, invitation_sent_at, activated_at) dan method terkait ditambahkan di Increment 2.

---

#### 2. Role

Kelas yang merepresentasikan peran pengguna (dari seeder).

| Atribut    | Tipe       | Deskripsi                                      |
| ---------- | ---------- | ---------------------------------------------- |
| id         | BigInteger | Primary key                                    |
| name       | String     | Nama peran (Admin, Kasir, Inventori, Produksi) |
| guard_name | String     | Guard name                                     |
| max_users  | Integer    | Maksimal pengguna per peran                    |

**Relasi**: belongsToMany(User), belongsToMany(Permission)

---

#### 3. Permission

Kelas yang merepresentasikan hak akses (dari seeder).

| Atribut    | Tipe       | Deskripsi       |
| ---------- | ---------- | --------------- |
| id         | BigInteger | Primary key     |
| name       | String     | Nama permission |
| guard_name | String     | Guard name      |

**Relasi**: belongsToMany(Role)

---

### Package: Pelanggan

#### 4. Customer

Kelas yang merepresentasikan data pelanggan untuk transaksi.

| Atribut | Tipe   | Deskripsi          |
| ------- | ------ | ------------------ |
| id      | UUID   | Primary key        |
| phone   | String | Nomor telepon unik |
| name    | String | Nama pelanggan     |

**Relasi**: hasMany(Transaction)

**Catatan**: Field `points` dan relasi `hasMany(PointsHistory)` ditambahkan di Increment 2 (UC-15, UC-20).

---

### Package: Produk & Kategori

#### 5. Category

Kelas yang merepresentasikan kategori produk (UC-02).

| Atribut   | Tipe    | Deskripsi     |
| --------- | ------- | ------------- |
| id        | UUID    | Primary key   |
| name      | String  | Nama kategori |
| is_active | Boolean | Status aktif  |

**Relasi**: hasMany(ProductCategory), belongsToMany(Product)

---

#### 6. Product

Kelas yang merepresentasikan produk jadi (UC-04).

| Atribut        | Tipe    | Deskripsi                                                    |
| -------------- | ------- | ------------------------------------------------------------ |
| id             | UUID    | Primary key                                                  |
| name           | String  | Nama produk                                                  |
| description    | String  | Deskripsi singkat                                            |
| price          | Decimal | Harga jual                                                   |
| stock          | Decimal | Jumlah stok                                                  |
| method         | JSON    | Metode penjualan (pesanan-reguler, pesanan-kotak, siap-beli) |
| product_image  | String  | Path gambar produk                                           |
| is_recipe      | Boolean | Produk memerlukan resep                                      |
| is_active      | Boolean | Status aktif                                                 |
| is_recommended | Boolean | Produk rekomendasi                                           |
| is_other       | Boolean | Produk lain-lain                                             |
| capital        | Decimal | Modal total                                                  |
| pcs            | Decimal | Jumlah pcs per produksi                                      |
| pcs_capital    | Decimal | Modal per pcs                                                |
| suhu_ruangan   | Integer | Daya tahan suhu ruangan (hari)                               |
| suhu_dingin    | Integer | Daya tahan suhu dingin (hari)                                |
| suhu_beku      | Integer | Daya tahan suhu beku (hari)                                  |

**Method**: getAvailableStock()

**Relasi**: hasMany(ProductCategory), hasMany(ProductComposition), hasMany(TransactionDetail), hasMany(ProductionDetail), hasMany(OtherCost)

---

#### 7. ProductCategory

Kelas pivot untuk relasi Product dan Category.

| Atribut     | Tipe | Deskripsi             |
| ----------- | ---- | --------------------- |
| id          | UUID | Primary key           |
| product_id  | UUID | Referensi ke produk   |
| category_id | UUID | Referensi ke kategori |

**Relasi**: belongsTo(Product), belongsTo(Category)

---

#### 8. TypeCost

Kelas yang merepresentasikan jenis biaya tambahan produk.

| Atribut | Tipe   | Deskripsi                                       |
| ------- | ------ | ----------------------------------------------- |
| id      | UUID   | Primary key                                     |
| name    | String | Nama jenis biaya (transportasi, packaging, dll) |

**Relasi**: hasMany(OtherCost)

---

#### 9. OtherCost

Kelas yang merepresentasikan biaya tambahan produk.

| Atribut      | Tipe    | Deskripsi                |
| ------------ | ------- | ------------------------ |
| id           | UUID    | Primary key              |
| product_id   | UUID    | Referensi ke produk      |
| type_cost_id | UUID    | Referensi ke jenis biaya |
| name         | String  | Nama biaya               |
| price        | Decimal | Nilai biaya              |

**Relasi**: belongsTo(Product), belongsTo(TypeCost)

---

#### 10. Unit

Kelas yang merepresentasikan satuan ukur dan konversinya (UC-06).

| Atribut           | Tipe    | Deskripsi                                             |
| ----------------- | ------- | ----------------------------------------------------- |
| id                | UUID    | Primary key                                           |
| name              | String  | Nama satuan unik (kg, gram, liter, ml, pcs, box, dll) |
| alias             | String  | Singkatan satuan                                      |
| group             | String  | Grup satuan (berat, volume, jumlah)                   |
| base_unit_id      | UUID    | Satuan dasar untuk konversi                           |
| conversion_factor | Decimal | Faktor konversi ke satuan dasar                       |

**Method**: convertTo(targetUnit, value): Float

**Relasi**: belongsTo(Unit) [base_unit], hasMany(Unit) [children], hasMany(MaterialDetail), hasMany(MaterialBatch), hasMany(ProductComposition)

---

### Package: Bahan Baku

#### 11. Material

Kelas yang merepresentasikan bahan baku (UC-03).

| Atribut     | Tipe    | Deskripsi                                           |
| ----------- | ------- | --------------------------------------------------- |
| id          | UUID    | Primary key                                         |
| name        | String  | Nama bahan baku                                     |
| description | String  | Deskripsi                                           |
| image       | String  | Path gambar                                         |
| expiry_date | Date    | Tanggal kadaluarsa                                  |
| status      | String  | Status stok (aman, minim, kritis)                   |
| is_active   | Boolean | Status aktif                                        |
| is_recipe   | Boolean | Bahan baku jadi/siap pakai (untuk produk siap beli) |
| minimum     | Decimal | Batas minimal stok                                  |

**Method**: getTotalQuantityInUnit(unit), reduceQuantity(qty, unit), getUnitPriceInUnit(unit), recalculateStatus()

**Relasi**: hasMany(MaterialDetail), hasMany(MaterialBatch), hasMany(ProductComposition), hasMany(ExpenseDetail), hasMany(IngredientCategoryDetail)

---

#### 12. MaterialDetail

Kelas yang merepresentasikan harga bahan baku per satuan.

| Atribut      | Tipe    | Deskripsi               |
| ------------ | ------- | ----------------------- |
| id           | UUID    | Primary key             |
| material_id  | UUID    | Referensi ke bahan baku |
| unit_id      | UUID    | Referensi ke satuan     |
| supply_price | Decimal | Harga supply per satuan |
| is_main      | Boolean | Satuan utama            |

**Relasi**: belongsTo(Material), belongsTo(Unit)

---

#### 13. MaterialBatch

Kelas yang merepresentasikan batch pembelian bahan baku.

| Atribut        | Tipe    | Deskripsi               |
| -------------- | ------- | ----------------------- |
| id             | UUID    | Primary key             |
| material_id    | UUID    | Referensi ke bahan baku |
| unit_id        | UUID    | Satuan batch            |
| batch_number   | String  | Nomor batch             |
| date           | Date    | Tanggal batch           |
| batch_quantity | Decimal | Kuantitas batch         |

**Relasi**: belongsTo(Material), belongsTo(Unit)

---

#### 14. ProductComposition

Kelas yang merepresentasikan komposisi/resep produk (UC-04).

| Atribut           | Tipe    | Deskripsi               |
| ----------------- | ------- | ----------------------- |
| id                | UUID    | Primary key             |
| product_id        | UUID    | Referensi ke produk     |
| material_id       | UUID    | Referensi ke bahan baku |
| unit_id           | UUID    | Satuan penggunaan       |
| material_quantity | Decimal | Jumlah bahan            |

**Relasi**: belongsTo(Product), belongsTo(Material), belongsTo(Unit)

---

#### 15. IngredientCategory

Kelas yang merepresentasikan kategori bahan baku (UC-03).

| Atribut     | Tipe   | Deskripsi                                |
| ----------- | ------ | ---------------------------------------- |
| id          | UUID   | Primary key                              |
| name        | String | Nama kategori (tepung, gula, dairy, dll) |
| description | String | Deskripsi                                |

**Relasi**: hasMany(IngredientCategoryDetail), belongsToMany(Material)

---

#### 16. IngredientCategoryDetail

Kelas pivot untuk relasi Material dan IngredientCategory.

| Atribut                | Tipe | Deskripsi                   |
| ---------------------- | ---- | --------------------------- |
| id                     | UUID | Primary key                 |
| ingredient_category_id | UUID | Referensi ke kategori bahan |
| material_id            | UUID | Referensi ke bahan baku     |

**Relasi**: belongsTo(IngredientCategory), belongsTo(Material)

---

### Package: Supplier & Belanja

#### 17. Supplier

Kelas yang merepresentasikan supplier bahan baku (UC-05).

| Atribut      | Tipe   | Deskripsi        |
| ------------ | ------ | ---------------- |
| id           | UUID   | Primary key      |
| name         | String | Nama supplier    |
| description  | String | Deskripsi        |
| contact_name | String | Nama kontak      |
| phone        | String | Nomor telepon    |
| street       | String | Nama jalan       |
| landmark     | String | Patokan lokasi   |
| maps_link    | String | Link Google Maps |
| image        | String | Path gambar      |

**Relasi**: hasMany(Expense)

---

#### 18. Expense

Kelas yang merepresentasikan transaksi belanja bahan baku (UC-07).

| Atribut            | Tipe    | Deskripsi                             |
| ------------------ | ------- | ------------------------------------- |
| id                 | UUID    | Primary key                           |
| expense_number     | String  | Nomor belanja unik                    |
| expense_date       | Date    | Tanggal belanja                       |
| end_date           | Date    | Tanggal selesai                       |
| supplier_id        | UUID    | Referensi ke supplier                 |
| note               | String  | Catatan                               |
| status             | String  | Status (planning, ongoing, completed) |
| grand_total_expect | Decimal | Total estimasi                        |
| grand_total_actual | Decimal | Total aktual                          |
| is_start           | Boolean | Sudah dimulai                         |
| is_finish          | Boolean | Sudah selesai                         |

**Method**: generateExpenseNumber()

**Relasi**: belongsTo(Supplier), hasMany(ExpenseDetail)

---

#### 19. ExpenseDetail

Kelas yang merepresentasikan detail item belanja.

| Atribut         | Tipe    | Deskripsi               |
| --------------- | ------- | ----------------------- |
| id              | UUID    | Primary key             |
| expense_id      | UUID    | Referensi ke belanja    |
| material_id     | UUID    | Referensi ke bahan baku |
| unit_id         | UUID    | Satuan                  |
| quantity_expect | Decimal | Qty rencana             |
| quantity_get    | Decimal | Qty aktual              |
| is_quantity_get | Boolean | Sudah input qty aktual  |
| price_expect    | Decimal | Harga rencana           |
| price_get       | Decimal | Harga aktual            |
| total_expect    | Decimal | Total rencana           |
| total_actual    | Decimal | Total aktual            |
| expiry_date     | Date    | Tanggal kadaluarsa      |

**Relasi**: belongsTo(Expense), belongsTo(Material), belongsTo(Unit)

---

### Package: Produksi

#### 20. Production

Kelas yang merepresentasikan proses produksi (UC-08).

| Atribut           | Tipe    | Deskripsi                                 |
| ----------------- | ------- | ----------------------------------------- |
| id                | UUID    | Primary key                               |
| production_number | String  | Nomor produksi unik                       |
| transaction_id    | UUID    | Referensi ke transaksi pesanan (nullable) |
| method            | String  | Metode produksi                           |
| date              | Date    | Tanggal produksi                          |
| time              | Time    | Waktu produksi                            |
| start_date        | Date    | Tanggal mulai                             |
| end_date          | Date    | Tanggal selesai                           |
| note              | String  | Catatan                                   |
| status            | String  | Status (pending, ongoing, completed)      |
| is_start          | Boolean | Sudah dimulai                             |
| is_finish         | Boolean | Sudah selesai                             |

**Method**: generateProductionNumber()

**Relasi**: belongsTo(Transaction), hasMany(ProductionDetail), hasMany(ProductionWorker)

---

#### 21. ProductionDetail

Kelas yang merepresentasikan detail item produksi.

| Atribut       | Tipe    | Deskripsi              |
| ------------- | ------- | ---------------------- |
| id            | UUID    | Primary key            |
| production_id | UUID    | Referensi ke produksi  |
| product_id    | UUID    | Referensi ke produk    |
| quantity_plan | Decimal | Qty rencana            |
| quantity_get  | Decimal | Qty berhasil           |
| quantity_fail | Decimal | Qty gagal              |
| cycle         | Decimal | Jumlah siklus produksi |

**Relasi**: belongsTo(Production), belongsTo(Product)

---

#### 22. ProductionWorker

Kelas yang merepresentasikan pekerja produksi.

| Atribut       | Tipe | Deskripsi             |
| ------------- | ---- | --------------------- |
| id            | UUID | Primary key           |
| production_id | UUID | Referensi ke produksi |
| user_id       | UUID | Referensi ke user     |

**Relasi**: belongsTo(Production), belongsTo(User)

---

### Package: Transaksi

#### 23. Transaction

Kelas yang merepresentasikan transaksi penjualan (UC-09, UC-10).

| Atribut          | Tipe    | Deskripsi                                                    |
| ---------------- | ------- | ------------------------------------------------------------ |
| id               | UUID    | Primary key                                                  |
| user_id          | UUID    | Kasir                                                        |
| customer_id      | UUID    | Pelanggan                                                    |
| invoice_number   | String  | Nomor invoice unik                                           |
| name             | String  | Nama pelanggan                                               |
| phone            | String  | Telepon pelanggan                                            |
| date             | Date    | Tanggal transaksi                                            |
| time             | Time    | Waktu transaksi                                              |
| start_date       | Date    | Tanggal mulai pesanan                                        |
| end_date         | Date    | Tanggal selesai pesanan                                      |
| note             | Text    | Catatan                                                      |
| payment_status   | String  | Status pembayaran (unpaid, partial, paid)                    |
| status           | String  | Status transaksi (draft, pending, completed, cancelled)      |
| method           | String  | Metode transaksi (pesanan-reguler, pesanan-kotak, siap-beli) |
| total_amount     | Decimal | Total nilai transaksi                                        |
| created_by_shift | UUID    | Shift saat transaksi dibuat                                  |

**Method**: generateInvoiceNumber()

**Relasi**: belongsTo(User), belongsTo(Customer), belongsTo(Shift), hasMany(TransactionDetail), hasMany(Payment), hasOne(Production)

**Catatan**: Field `points_used`, `points_discount`, `total_refund` dan relasi terkait ditambahkan di Increment 2.

---

#### 24. TransactionDetail

Kelas yang merepresentasikan detail item transaksi.

| Atribut              | Tipe         | Deskripsi              |
| -------------------- | ------------ | ---------------------- |
| id                   | UUID         | Primary key            |
| transaction_id       | UUID         | Referensi ke transaksi |
| product_id           | UUID         | Referensi ke produk    |
| quantity             | SmallInteger | Jumlah item            |
| price                | Decimal      | Harga per item         |
| pcs_capital_snapshot | Decimal      | Snapshot modal per pcs |

**Relasi**: belongsTo(Transaction), belongsTo(Product)

**Catatan**: Field `refund_quantity` ditambahkan di Increment 2 (UC-21).

---

#### 25. Payment

Kelas yang merepresentasikan pembayaran transaksi (UC-10).

| Atribut            | Tipe      | Deskripsi                                  |
| ------------------ | --------- | ------------------------------------------ |
| id                 | UUID      | Primary key                                |
| receipt_number     | String    | Nomor bukti pembayaran                     |
| transaction_id     | UUID      | Referensi ke transaksi                     |
| payment_channel_id | UUID      | Referensi ke channel pembayaran            |
| payment_method     | String    | Metode pembayaran                          |
| payment_group      | String    | Grup pembayaran (cash, transfer, e-wallet) |
| paid_amount        | Decimal   | Jumlah dibayar                             |
| image              | String    | Bukti pembayaran                           |
| paid_at            | Timestamp | Waktu pembayaran                           |

**Method**: generateReceiptNumber()

**Relasi**: belongsTo(Transaction), belongsTo(PaymentChannel)

---

#### 26. PaymentChannel

Kelas yang merepresentasikan channel/metode pembayaran (dari seeder).

| Atribut        | Tipe    | Deskripsi                       |
| -------------- | ------- | ------------------------------- |
| id             | UUID    | Primary key                     |
| type           | String  | Tipe channel                    |
| group          | String  | Grup (cash, transfer, e-wallet) |
| bank_name      | String  | Nama bank                       |
| account_number | String  | Nomor rekening                  |
| account_name   | String  | Nama rekening                   |
| qris_image     | String  | Gambar QRIS                     |
| is_active      | Boolean | Status aktif                    |

**Relasi**: hasMany(Payment), hasMany(Refund)

**Catatan**: Pada Increment 1, payment channel hanya dibaca dari seeder. CRUD management ditambahkan di Increment 2 (UC-14).

---

#### 27. Shift

Kelas yang merepresentasikan sesi kerja kasir (UC-11).

| Atribut      | Tipe     | Deskripsi                  |
| ------------ | -------- | -------------------------- |
| id           | UUID     | Primary key                |
| shift_number | String   | Nomor shift auto-increment |
| opened_by    | UUID     | User yang membuka          |
| closed_by    | UUID     | User yang menutup          |
| start_time   | DateTime | Waktu mulai shift          |
| end_time     | DateTime | Waktu selesai shift        |
| status       | String   | Status (active, closed)    |
| initial_cash | Decimal  | Modal kas awal             |
| final_cash   | Decimal  | Kas akhir                  |
| total_sales  | Decimal  | Total penjualan            |

**Relasi**: belongsTo(User) [opened_by], belongsTo(User) [closed_by], hasMany(Transaction)

**Catatan**: Field `total_refunds` dan `total_discounts` ditambahkan di Increment 2.

---

## Increment 2: Modul Pendukung Operasional

### Extension Class (Penambahan Field pada Increment 1)

#### 28. User (Extension)

**Field tambahan untuk UC-17 (Aktivasi Akun)**:

-   `invitation_token`: String - Token undangan aktivasi
-   `invitation_sent_at`: Timestamp - Waktu pengiriman undangan
-   `activated_at`: Timestamp - Waktu aktivasi akun

**Method tambahan**:

-   `sendInvitation()`: void
-   `activateWithPassword(password)`: void
-   `hasValidInvitationToken()`: Boolean

**Relasi tambahan**: hasMany(Notification), hasMany(Hitung), hasMany(InventoryLog)

---

#### 29. Customer (Extension)

**Field tambahan untuk UC-15, UC-20 (Pelanggan & Poin)**:

-   `points`: Decimal - Jumlah poin loyalitas

**Method tambahan**:

-   `addPoints(amount)`: void
-   `usePoints(amount)`: Boolean

**Relasi tambahan**: hasMany(PointsHistory)

---

#### 30. Transaction (Extension)

**Field tambahan untuk UC-20, UC-21 (Poin & Refund)**:

-   `points_used`: Integer - Poin yang digunakan
-   `points_discount`: Decimal - Diskon dari penggunaan poin
-   `total_refund`: Decimal - Total nilai refund

**Relasi tambahan**: hasMany(PointsHistory), hasOne(Refund)

---

#### 31. TransactionDetail (Extension)

**Field tambahan untuk UC-21 (Refund)**:

-   `refund_quantity`: SmallInteger - Jumlah item yang di-refund

**Method tambahan**:

-   `getRemainingQuantity()`: Integer

---

#### 32. Shift (Extension)

**Field tambahan untuk UC-20, UC-21 (tracking refund & discount)**:

-   `total_refunds`: Decimal - Total refund dalam shift
-   `total_discounts`: Decimal - Total diskon dari poin dalam shift

**Method tambahan**:

-   `calculateTotals()`: void

---

### Class Baru Increment 2

#### 33. PointsHistory

Kelas yang merepresentasikan riwayat poin pelanggan (UC-15, UC-20).

| Atribut        | Tipe    | Deskripsi                         |
| -------------- | ------- | --------------------------------- |
| id             | UUID    | Primary key                       |
| phone          | String  | Referensi ke customer             |
| transaction_id | UUID    | Referensi ke transaksi            |
| action_id      | String  | ID aksi                           |
| action         | String  | Jenis aksi (earned/used/adjusted) |
| points         | Integer | Jumlah poin                       |
| image          | String  | Bukti gambar                      |

**Relasi**: belongsTo(Customer), belongsTo(Transaction)

---

#### 34. Notification

Kelas yang merepresentasikan notifikasi pengguna (UC-27).

| Atribut | Tipe        | Deskripsi                                                  |
| ------- | ----------- | ---------------------------------------------------------- |
| id      | UUID        | Primary key                                                |
| user_id | UUID        | Referensi ke user                                          |
| title   | String      | Judul notifikasi                                           |
| body    | Text        | Isi notifikasi                                             |
| type    | String      | Tipe notifikasi (default: 'kasir')                         |
| is_read | Boolean     | Status dibaca (default: false)                             |
| status  | TinyInteger | Status notifikasi (0: canceled, 1: processing, 2: success) |

**Method**: markAsRead()

**Relasi**: belongsTo(User)

---

#### 35. Hitung

Kelas yang merepresentasikan stock opname (UC-18).

| Atribut            | Tipe    | Deskripsi           |
| ------------------ | ------- | ------------------- |
| id                 | UUID    | Primary key         |
| user_id            | UUID    | User yang melakukan |
| hitung_number      | String  | Nomor stock opname  |
| action             | String  | Jenis aksi          |
| note               | String  | Catatan             |
| status             | String  | Status              |
| hitung_date        | Date    | Tanggal mulai       |
| hitung_date_finish | Date    | Tanggal selesai     |
| is_start           | Boolean | Sudah dimulai       |
| is_finish          | Boolean | Sudah selesai       |
| grand_total        | Decimal | Total nilai         |
| loss_grand_total   | Decimal | Total kerugian      |

**Method**: generateHitungNumber()

**Relasi**: belongsTo(User), hasMany(HitungDetail)

---

#### 36. HitungDetail

Kelas yang merepresentasikan detail item stock opname.

| Atribut             | Tipe    | Deskripsi                 |
| ------------------- | ------- | ------------------------- |
| id                  | UUID    | Primary key               |
| hitung_id           | UUID    | Referensi ke stock opname |
| material_id         | UUID    | Referensi ke bahan baku   |
| unit_id             | UUID    | Satuan                    |
| quantity_system     | Decimal | Qty sistem                |
| quantity_actual     | Decimal | Qty aktual                |
| quantity_difference | Decimal | Selisih                   |
| price               | Decimal | Harga                     |
| total_loss          | Decimal | Total kerugian            |

**Relasi**: belongsTo(Hitung), belongsTo(Material), belongsTo(Unit)

---

#### 37. InventoryLog

Kelas yang merepresentasikan log pergerakan stok (UC-19).

| Atribut           | Tipe    | Deskripsi                                    |
| ----------------- | ------- | -------------------------------------------- |
| id                | UUID    | Primary key                                  |
| material_id       | UUID    | Referensi ke bahan baku                      |
| material_batch_id | UUID    | Referensi ke batch                           |
| user_id           | UUID    | User yang melakukan                          |
| action            | String  | Jenis aksi (tambah, kurang, opname)          |
| quantity_change   | Decimal | Perubahan qty                                |
| quantity_after    | Decimal | Qty setelah perubahan                        |
| reference_type    | String  | Tipe referensi (Expense, Production, Hitung) |
| reference_id      | String  | ID referensi                                 |
| note              | Text    | Catatan                                      |

**Relasi**: belongsTo(Material), belongsTo(MaterialBatch), belongsTo(User)

---

#### 38. Refund

Kelas yang merepresentasikan pengembalian dana (UC-21).

| Atribut            | Tipe     | Deskripsi              |
| ------------------ | -------- | ---------------------- |
| id                 | UUID     | Primary key            |
| transaction_id     | UUID     | Referensi ke transaksi |
| reason             | String   | Alasan refund          |
| proof_image        | String   | Bukti gambar           |
| refund_method      | String   | Metode refund          |
| payment_channel_id | UUID     | Channel pengembalian   |
| account_number     | String   | Nomor rekening         |
| total_amount       | Decimal  | Total nilai refund     |
| refund_by_shift    | UUID     | Shift saat refund      |
| refunded_at        | DateTime | Waktu refund           |

**Relasi**: belongsTo(Transaction), belongsTo(PaymentChannel), belongsTo(Shift)

---

#### 39. StoreProfile

Kelas yang merepresentasikan profil toko (UC-14, UC-22).

| Atribut          | Tipe   | Deskripsi      |
| ---------------- | ------ | -------------- |
| id               | UUID   | Primary key    |
| logo             | String | Logo toko      |
| name             | String | Nama toko      |
| tagline          | String | Tagline        |
| type             | String | Jenis usaha    |
| banner           | String | Banner         |
| product          | String | Jenis produk   |
| description      | Text   | Deskripsi toko |
| building         | String | Nama gedung    |
| location         | String | Lokasi         |
| address          | String | Alamat lengkap |
| contact          | String | Kontak telepon |
| email            | String | Email toko     |
| website          | String | Website        |
| social_instagram | String | Instagram      |
| social_facebook  | String | Facebook       |
| social_whatsapp  | String | WhatsApp       |

---

#### 40. StoreDocument

Kelas yang merepresentasikan dokumen usaha (UC-14).

| Atribut         | Tipe   | Deskripsi                     |
| --------------- | ------ | ----------------------------- |
| id              | UUID   | Primary key                   |
| document_name   | String | Nama dokumen (SIUP, TDP, dll) |
| document_number | String | Nomor dokumen                 |
| document_file   | String | Path file dokumen             |
| valid_from      | Date   | Berlaku dari                  |
| valid_until     | Date   | Berlaku sampai                |

---

#### 41. ActivityLog

Kelas yang merepresentasikan log aktivitas sistem (UC-16).

| Atribut      | Tipe       | Deskripsi                               |
| ------------ | ---------- | --------------------------------------- |
| id           | BigInteger | Primary key                             |
| log_name     | String     | Nama log kategori                       |
| description  | Text       | Deskripsi aktivitas                     |
| subject_type | String     | Tipe model subjek                       |
| subject_id   | UUID       | ID subjek                               |
| causer_type  | String     | Tipe model pelaku                       |
| causer_id    | UUID       | ID pelaku                               |
| properties   | JSON       | Data sebelum & sesudah                  |
| event        | String     | Jenis event (created, updated, deleted) |
| batch_uuid   | UUID       | UUID batch                              |

---

## Diagram Relasi Utama

### Relasi Inti Increment 1

```
User --< Transaction
User --< Shift (opened_by, closed_by)
User --< ProductionWorker

Customer --< Transaction

Product --< ProductComposition >-- Material
Product --< ProductionDetail >-- Production
Product --< TransactionDetail >-- Transaction

Material --< MaterialBatch
Material --< MaterialDetail >-- Unit
Material --< ExpenseDetail >-- Expense >-- Supplier

Transaction --< TransactionDetail
Transaction --< Payment >-- PaymentChannel
Transaction --> Production (optional)
Transaction >-- Shift

Unit --> Unit (self-reference: base_unit)
```

### Relasi Tambahan Increment 2

```
User --< Notification (Inc 2)
User --< Hitung (Inc 2)
User --< InventoryLog (Inc 2)

Customer --< PointsHistory (Inc 2)
Transaction --< PointsHistory (Inc 2)
Transaction --> Refund (Inc 2)

Material --< InventoryLog (Inc 2)
MaterialBatch --< InventoryLog (Inc 2)
```

---

## File Diagram

Diagram class tersedia dalam format PlantUML:

-   **Increment 1**: `puml/class-diagram-increment-1.puml` → SVG: 119 KB
-   **Increment 2**: `puml/class-diagram-increment-2.puml` → SVG: 63 KB
-   **Combined**: `puml/class-diagram.puml`

---

## Kesimpulan

Class diagram Sistem Pawon3D terdiri dari:

-   **Increment 1**: 27 class untuk modul inti operasional (UC-01 sampai UC-11)
-   **Increment 2**: 5 extension class + 9 class baru untuk modul pendukung (UC-12 sampai UC-27)
-   **Total**: 41 class dengan 27 class dasar + 14 class/extension tambahan

Sistem menggunakan UUID sebagai primary key untuk semua entitas utama dan mengimplementasikan pola Eloquent Model dari framework Laravel. Pemisahan increment memastikan bahwa fitur dasar operasional dapat berfungsi independen sebelum fitur pendukung diimplementasikan.
