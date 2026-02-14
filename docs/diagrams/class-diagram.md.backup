# Dokumentasi Class Diagram

## Pendahuluan

Class diagram Sistem Manajemen Toko Kue Pawon3D menggambarkan struktur statis sistem yang mencakup entitas-entitas utama, atribut, method, dan relasi antar kelas. Diagram ini disusun berdasarkan model-model Eloquent yang telah diimplementasikan dalam proyek.

---

## Kelas Increment 1

### 1. User

Kelas yang merepresentasikan pengguna sistem.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | String | Nama lengkap pengguna |
| email | String | Email unik untuk login |
| phone | String | Nomor telepon |
| password | String | Password terenkripsi |
| image | String | Path foto profil |
| gender | String | Jenis kelamin |
| is_active | Boolean | Status aktif akun |
| invitation_token | String | Token undangan aktivasi |
| invitation_sent_at | Timestamp | Waktu pengiriman undangan |
| activated_at | Timestamp | Waktu aktivasi akun |

**Method**: sendInvitation(), activateWithPassword(), hasValidInvitationToken(), isActivated(), toggleActive(), initials()

**Relasi**: belongsToMany(Role), hasMany(Notification), hasMany(ProductionWorker), hasMany(Hitung), hasMany(InventoryLog)

---

### 2. Role

Kelas yang merepresentasikan peran pengguna.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | BigInteger | Primary key |
| name | String | Nama peran |
| guard_name | String | Guard name |
| max_users | Integer | Maksimal pengguna per peran |

**Relasi**: belongsToMany(User), belongsToMany(Permission)

---

### 3. Permission

Kelas yang merepresentasikan hak akses.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | BigInteger | Primary key |
| name | String | Nama permission |
| guard_name | String | Guard name |

**Relasi**: belongsToMany(Role)

---

### 4. Customer

Kelas yang merepresentasikan data pelanggan.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| phone | String | Nomor telepon unik |
| name | String | Nama pelanggan |
| points | Decimal | Jumlah poin loyalitas |

**Relasi**: hasMany(Transaction), hasMany(PointsHistory)

---

### 5. PointsHistory

Kelas yang merepresentasikan riwayat poin pelanggan.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| phone | String | Referensi ke customer |
| transaction_id | UUID | Referensi ke transaksi |
| action_id | String | ID aksi |
| action | String | Jenis aksi (earned/used) |
| points | Integer | Jumlah poin |
| image | String | Bukti gambar |

**Relasi**: belongsTo(Customer), belongsTo(Transaction)

---

### 6. Category

Kelas yang merepresentasikan kategori produk.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | String | Nama kategori |
| is_active | Boolean | Status aktif |

**Relasi**: hasMany(ProductCategory), belongsToMany(Product)

---

### 7. Product

Kelas yang merepresentasikan produk.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | String | Nama produk |
| description | String | Deskripsi singkat |
| price | Decimal | Harga jual |
| stock | Decimal | Jumlah stok |
| method | JSON | Metode penjualan |
| product_image | String | Path gambar produk |
| is_recipe | Boolean | Produk memerlukan resep |
| is_active | Boolean | Status aktif |
| is_recommended | Boolean | Produk rekomendasi |
| capital | Decimal | Modal total |
| pcs | Decimal | Jumlah pcs per produksi |
| pcs_capital | Decimal | Modal per pcs |

**Relasi**: hasMany(ProductCategory), hasMany(ProductComposition), hasMany(TransactionDetail), hasMany(ProductionDetail), hasMany(OtherCost)

---

### 8. TypeCost

Kelas yang merepresentasikan jenis biaya tambahan.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | String | Nama jenis biaya |

**Relasi**: hasMany(OtherCost)

---

### 9. OtherCost

Kelas yang merepresentasikan biaya tambahan produk.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| product_id | UUID | Referensi ke produk |
| type_cost_id | UUID | Referensi ke jenis biaya |
| price | Decimal | Nilai biaya |

**Relasi**: belongsTo(Product), belongsTo(TypeCost)

---

### 10. Unit

Kelas yang merepresentasikan satuan ukur.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | String | Nama satuan unik |
| alias | String | Singkatan satuan |
| group | String | Grup satuan |
| base_unit_id | UUID | Satuan dasar untuk konversi |
| conversion_factor | Decimal | Faktor konversi |

**Method**: convertTo(targetUnit, value)

**Relasi**: belongsTo(Unit) [base_unit], hasMany(Unit) [children], hasMany(MaterialDetail), hasMany(MaterialBatch), hasMany(ProductComposition)

---

### 11. Shift

Kelas yang merepresentasikan sesi kerja kasir.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| shift_number | String | Nomor shift unik |
| opened_by | UUID | User yang membuka |
| closed_by | UUID | User yang menutup |
| start_time | DateTime | Waktu mulai |
| end_time | DateTime | Waktu selesai |
| status | String | Status shift |
| initial_cash | Decimal | Modal awal |
| final_cash | Decimal | Kas akhir |
| total_sales | Decimal | Total penjualan |
| total_refunds | Decimal | Total refund |
| total_discounts | Decimal | Total diskon |

**Relasi**: belongsTo(User) [opened_by, closed_by], hasMany(Transaction)

---

### 12. Transaction

Kelas yang merepresentasikan transaksi penjualan.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| user_id | UUID | Kasir |
| customer_id | UUID | Pelanggan |
| invoice_number | String | Nomor invoice unik |
| name | String | Nama pelanggan |
| phone | String | Telepon |
| date | Date | Tanggal transaksi |
| time | Time | Waktu transaksi |
| payment_status | String | Status pembayaran |
| status | String | Status transaksi |
| method | String | Metode transaksi |
| total_amount | Decimal | Total nilai |
| points_used | Integer | Poin digunakan |
| points_discount | Decimal | Diskon dari poin |
| total_refund | Decimal | Total refund |

**Method**: generateInvoiceNumber()

**Relasi**: belongsTo(User), belongsTo(Customer), hasMany(TransactionDetail), hasOne(Production), hasMany(Payment), hasOne(Refund), hasMany(PointsHistory), belongsTo(Shift)

---

### 13. TransactionDetail

Kelas yang merepresentasikan detail item transaksi.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| transaction_id | UUID | Referensi ke transaksi |
| product_id | UUID | Referensi ke produk |
| quantity | SmallInteger | Jumlah item |
| price | Decimal | Harga per item |
| refund_quantity | SmallInteger | Jumlah refund |
| pcs_capital_snapshot | Decimal | Snapshot modal |

**Relasi**: belongsTo(Transaction), belongsTo(Product)

---

### 14. PaymentChannel

Kelas yang merepresentasikan channel pembayaran.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| type | String | Tipe (bank/e-wallet) |
| group | String | Grup pembayaran |
| bank_name | String | Nama bank |
| account_number | String | Nomor rekening |
| account_name | String | Nama rekening |
| qris_image | String | Gambar QRIS |
| is_active | Boolean | Status aktif |

**Relasi**: hasMany(Payment), hasMany(Refund)

---

### 15. Payment

Kelas yang merepresentasikan pembayaran.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| receipt_number | String | Nomor bukti |
| transaction_id | UUID | Referensi ke transaksi |
| payment_channel_id | UUID | Referensi ke channel |
| payment_method | String | Metode pembayaran |
| payment_group | String | Grup pembayaran |
| paid_amount | Decimal | Jumlah bayar |
| image | String | Bukti bayar |
| paid_at | Timestamp | Waktu bayar |

**Method**: generateReceiptNumber()

**Relasi**: belongsTo(Transaction), belongsTo(PaymentChannel)

---

## Kelas Increment 2

### 16. Material

Kelas yang merepresentasikan bahan baku.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | String | Nama bahan baku |
| description | String | Deskripsi |
| image | String | Gambar bahan |
| expiry_date | Date | Tanggal kedaluwarsa terdekat |
| status | String | Status stok (kosong/menipis/tersedia) |
| is_active | Boolean | Status aktif |
| is_recipe | Boolean | Untuk link ke produk |
| minimum | Decimal | Stok minimum |

**Method**: getTotalQuantityInUnit(), reduceQuantity(), getUnitPriceInUnit(), recalculateStatus()

**Relasi**: hasMany(MaterialDetail), hasMany(MaterialBatch), hasMany(ProductComposition), hasMany(ExpenseDetail), hasMany(IngredientCategoryDetail), hasMany(HitungDetail), hasMany(InventoryLog)

---

### 17. MaterialDetail

Kelas yang merepresentasikan konfigurasi satuan per bahan.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| material_id | UUID | Referensi ke material |
| unit_id | UUID | Referensi ke unit |
| price | Decimal | Harga per satuan |
| is_main | Boolean | Satuan utama |

**Relasi**: belongsTo(Material), belongsTo(Unit)

---

### 18. MaterialBatch

Kelas yang merepresentasikan batch stok bahan.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| material_id | UUID | Referensi ke material |
| unit_id | UUID | Referensi ke unit |
| batch_number | String | Nomor batch |
| date | Date | Tanggal kedaluwarsa |
| batch_quantity | Decimal | Jumlah stok |

**Relasi**: belongsTo(Material), belongsTo(Unit), hasMany(InventoryLog)

---

### 19. ProductComposition

Kelas yang merepresentasikan komposisi resep produk.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| product_id | UUID | Referensi ke produk |
| material_id | UUID | Referensi ke material |
| unit_id | UUID | Referensi ke unit |
| material_quantity | Decimal | Jumlah bahan |

**Relasi**: belongsTo(Product), belongsTo(Material), belongsTo(Unit)

---

### 20. IngredientCategory

Kelas yang merepresentasikan kategori bahan baku.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | String | Nama kategori |
| description | String | Deskripsi |

**Relasi**: hasMany(IngredientCategoryDetail), belongsToMany(Material)

---

### 21. IngredientCategoryDetail

Kelas tabel pivot kategori-bahan.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| ingredient_category_id | UUID | Referensi ke kategori |
| material_id | UUID | Referensi ke material |

**Relasi**: belongsTo(IngredientCategory), belongsTo(Material)

---

### 22. Supplier

Kelas yang merepresentasikan pemasok bahan.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | String | Nama toko/supplier |
| description | String | Deskripsi |
| contact_name | String | Nama kontak |
| phone | String | Nomor telepon |
| street | String | Alamat jalan |
| landmark | String | Patokan lokasi |
| maps_link | String | Link Google Maps |
| image | String | Gambar toko |

**Relasi**: hasMany(Expense)

---

### 23. Expense

Kelas yang merepresentasikan transaksi belanja bahan.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| expense_number | String | Nomor belanja unik |
| expense_date | Date | Tanggal rencana |
| end_date | Date | Tanggal selesai |
| supplier_id | UUID | Referensi ke supplier |
| note | String | Catatan |
| status | String | Status (Draft/Dalam Proses/Selesai) |
| grand_total_expect | Decimal | Total estimasi |
| grand_total_actual | Decimal | Total realisasi |
| is_start | Boolean | Sudah dimulai |
| is_finish | Boolean | Sudah selesai |

**Method**: generateExpenseNumber()

**Relasi**: belongsTo(Supplier), hasMany(ExpenseDetail)

---

### 24. ExpenseDetail

Kelas yang merepresentasikan detail item belanja.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| expense_id | UUID | Referensi ke expense |
| material_id | UUID | Referensi ke material |
| unit_id | UUID | Referensi ke unit |
| quantity_expect | Decimal | Jumlah rencana |
| quantity_get | Decimal | Jumlah didapat |
| price_expect | Decimal | Harga estimasi |
| price_get | Decimal | Harga aktual |
| total_expect | Decimal | Total estimasi |
| total_actual | Decimal | Total aktual |
| expiry_date | Date | Tanggal kedaluwarsa batch |

**Relasi**: belongsTo(Expense), belongsTo(Material), belongsTo(Unit)

---

### 25. Production

Kelas yang merepresentasikan proses produksi.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| production_number | String | Nomor produksi |
| transaction_id | UUID | Referensi ke transaksi (jika dari pesanan) |
| method | String | Metode produksi |
| date | Date | Tanggal produksi |
| status | String | Status produksi |
| is_start | Boolean | Sudah dimulai |
| is_finish | Boolean | Sudah selesai |

**Method**: generateProductionNumber()

**Relasi**: belongsTo(Transaction), hasMany(ProductionDetail), hasMany(ProductionWorker)

---

### 26. ProductionDetail

Kelas yang merepresentasikan detail produk yang diproduksi.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| production_id | UUID | Referensi ke production |
| product_id | UUID | Referensi ke product |
| quantity_plan | Decimal | Jumlah rencana |
| quantity_get | Decimal | Jumlah berhasil |
| quantity_fail | Decimal | Jumlah gagal |
| cycle | Decimal | Siklus produksi |

**Relasi**: belongsTo(Production), belongsTo(Product)

---

### 27. ProductionWorker

Kelas yang merepresentasikan pekerja produksi.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| production_id | UUID | Referensi ke production |
| user_id | UUID | Referensi ke user |

**Relasi**: belongsTo(Production), belongsTo(User)

---

### 28. Hitung

Kelas yang merepresentasikan stock opname.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| user_id | UUID | Pelaksana |
| hitung_number | String | Nomor stock opname |
| action | String | Jenis aksi |
| note | String | Catatan |
| status | String | Status |
| hitung_date | Date | Tanggal rencana |
| hitung_date_finish | Date | Tanggal selesai |
| is_start | Boolean | Sudah dimulai |
| is_finish | Boolean | Sudah selesai |
| grand_total | Decimal | Total nilai |
| loss_grand_total | Decimal | Total kerugian |

**Method**: generateHitungNumber()

**Relasi**: belongsTo(User), hasMany(HitungDetail)

---

### 29. HitungDetail

Kelas yang merepresentasikan detail item stock opname.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| hitung_id | UUID | Referensi ke hitung |
| material_id | UUID | Referensi ke material |
| unit_id | UUID | Referensi ke unit |
| quantity_system | Decimal | Stok sistem |
| quantity_actual | Decimal | Stok fisik |
| quantity_difference | Decimal | Selisih |
| price | Decimal | Harga per satuan |
| total_loss | Decimal | Nilai kerugian |

**Relasi**: belongsTo(Hitung), belongsTo(Material), belongsTo(Unit)

---

### 30. InventoryLog

Kelas yang merepresentasikan log pergerakan stok.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| material_id | UUID | Referensi ke material |
| material_batch_id | UUID | Referensi ke batch |
| user_id | UUID | Pelaksana |
| action | String | Jenis aksi |
| quantity_change | Decimal | Perubahan (+/-) |
| quantity_after | Decimal | Stok setelah |
| reference_type | String | Tipe referensi |
| reference_id | String | ID referensi |
| note | Text | Catatan |

**Relasi**: belongsTo(Material), belongsTo(MaterialBatch), belongsTo(User)

---

### 31. Refund

Kelas yang merepresentasikan pengembalian dana.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| transaction_id | UUID | Referensi ke transaksi |
| reason | String | Alasan refund |
| proof_image | String | Bukti foto |
| refund_method | String | Metode pengembalian |
| payment_channel_id | UUID | Channel (jika transfer) |
| account_number | String | Nomor rekening |
| total_amount | Decimal | Total refund |
| refund_by_shift | UUID | Shift saat proses |
| refunded_at | DateTime | Waktu refund |

**Relasi**: belongsTo(Transaction), belongsTo(PaymentChannel), belongsTo(Shift)

---

### 32. StoreProfile

Kelas yang merepresentasikan profil usaha.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| logo | String | Logo toko |
| name | String | Nama toko |
| tagline | String | Tagline |
| type | String | Jenis usaha |
| banner | String | Banner |
| description | Text | Deskripsi |
| address | String | Alamat |
| contact | String | Kontak |
| email | String | Email |
| website | String | Website |
| social_instagram | String | Instagram |
| social_facebook | String | Facebook |
| social_whatsapp | String | WhatsApp |

---

### 33. StoreDocument

Kelas yang merepresentasikan dokumen usaha.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| document_name | String | Nama dokumen |
| document_number | String | Nomor dokumen |
| document_file | String | Path file |
| valid_from | Date | Berlaku dari |
| valid_until | Date | Berlaku sampai |

---

### 34. Notification

Kelas yang merepresentasikan notifikasi pengguna.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| user_id | UUID | Referensi ke user |
| title | String | Judul notifikasi |
| message | Text | Isi notifikasi |
| type | String | Tipe notifikasi |
| is_read | Boolean | Status dibaca |
| data | JSON | Data tambahan |

**Method**: markAsRead()

**Relasi**: belongsTo(User)

---

### 35. ActivityLog

Kelas yang merepresentasikan log aktivitas sistem (Spatie Activity Log).

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | BigInteger | Primary key |
| log_name | String | Nama log |
| description | Text | Deskripsi aktivitas |
| subject_type | String | Tipe subjek |
| subject_id | UUID | ID subjek |
| causer_type | String | Tipe pelaku |
| causer_id | UUID | ID pelaku |
| properties | JSON | Properti tambahan |
| event | String | Jenis event |
| batch_uuid | UUID | UUID batch |

---

## Diagram Class

Diagram class lengkap tersedia dalam format PlantUML pada berkas berikut:

- `puml/class-diagram.puml`

---

## Kesimpulan

Class diagram Sistem Pawon3D terdiri dari 35 kelas utama yang saling berelasi. Sistem menggunakan UUID sebagai primary key untuk semua entitas utama dan mengimplementasikan pola Eloquent Model dari framework Laravel.
