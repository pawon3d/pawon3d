# Class Diagram - Sistem Pawon3D

## Pendahuluan

Class diagram menggambarkan struktur statis sistem melalui representasi kelas-kelas beserta atribut, method, dan relasi antar kelas. Diagram ini mencerminkan implementasi model Eloquent dalam sistem Pawon3D.

## Konvensi Model Laravel

Seluruh model dalam sistem menggunakan konvensi berikut:
- **Primary Key**: UUID (string, non-incrementing)
- **Traits**: LogsActivity (Spatie) untuk pencatatan perubahan
- **Soft Delete**: Tidak diimplementasikan (hard delete)

---

## Increment 1: Model Fungsionalitas Inti

### 1. Category

Model untuk kategori produk.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | string | Nama kategori |
| description | text | Deskripsi kategori |
| created_at | timestamp | Waktu pembuatan |
| updated_at | timestamp | Waktu pembaruan |

**Relasi:**
- hasMany → Product

---

### 2. Unit

Model untuk satuan ukur dengan sistem konversi hierarkis.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | string | Nama satuan (gram, kilogram) |
| alias | string | Singkatan (g, kg) |
| group | string | Grup konversi (berat, volume) |
| base_unit_id | UUID | FK ke satuan dasar |
| conversion_factor | decimal | Faktor konversi ke satuan dasar |

**Relasi:**
- belongsTo → Unit (baseUnit)
- hasMany → Unit (derivedUnits)
- hasMany → MaterialDetail

**Method Penting:**
- `convertTo(value, targetUnit)` - Konversi nilai ke satuan lain
- `canAutoConvertTo(targetUnit)` - Cek kemampuan konversi
- `isBaseUnit()` - Cek apakah satuan dasar

---

### 3. Supplier

Model untuk data pemasok bahan baku.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | string | Nama toko/supplier |
| description | text | Deskripsi |
| contact_name | string | Nama kontak |
| phone | string | Nomor telepon |
| street | string | Alamat jalan |
| landmark | string | Patokan lokasi |
| maps_link | string | Link Google Maps |
| image | string | Path gambar |

**Relasi:**
- hasMany → Expense

---

### 4. Material

Model untuk bahan baku dengan manajemen batch.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | string | Nama bahan |
| description | text | Deskripsi |
| minimum | integer | Stok minimum |
| status | enum | Tersedia/Hampir Habis/Habis/Kosong/Expired |
| is_active | boolean | Status aktif |

**Relasi:**
- hasMany → MaterialDetail
- hasMany → MaterialBatch
- hasMany → IngredientCategoryDetail
- hasMany → ExpenseDetail

**Method Penting:**
- `getTotalQuantityInUnit(targetUnit)` - Hitung total stok dalam satuan tertentu
- `reduceQuantity(qty, unit, logData)` - Kurangi stok dengan FIFO
- `recalculateStatus()` - Hitung ulang status ketersediaan

---

### 5. MaterialBatch

Model untuk batch bahan baku.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| material_id | UUID | FK ke Material |
| unit_id | UUID | FK ke Unit |
| batch_quantity | decimal | Kuantitas batch |
| date | date | Tanggal kedaluwarsa |

**Relasi:**
- belongsTo → Material
- belongsTo → Unit

---

### 6. Expense

Model untuk transaksi belanja bahan baku.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| expense_number | string | Nomor belanja (BP-YYMMDD-XXXX) |
| supplier_id | UUID | FK ke Supplier |
| status | enum | Status belanja |
| total | decimal | Total belanja |
| date | date | Tanggal belanja |

**Relasi:**
- belongsTo → Supplier
- hasMany → ExpenseDetail

---

### 7. ExpenseDetail

Model untuk detail item belanja.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| expense_id | UUID | FK ke Expense |
| material_id | UUID | FK ke Material |
| quantity | decimal | Kuantitas |
| price | decimal | Harga satuan |

**Relasi:**
- belongsTo → Expense
- belongsTo → Material

---

### 8. Product

Model untuk produk kue.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | string | Nama produk |
| category_id | UUID | FK ke Category |
| price | decimal | Harga jual |
| stock | integer | Stok siap jual |
| is_ready | boolean | Status tersedia |
| product_image | string | Path gambar |
| method | array | Metode penjualan yang berlaku |

**Relasi:**
- belongsTo → Category
- hasMany → ProductCategory
- hasMany → ProductComposition
- hasMany → OtherCost
- hasMany → TransactionDetail
- hasMany → Production

---

### 9. ProductComposition

Model untuk komposisi bahan per produk.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| product_id | UUID | FK ke Product |
| material_id | UUID | FK ke Material |
| unit_id | UUID | FK ke Unit |
| quantity | decimal | Kuantitas bahan |

**Relasi:**
- belongsTo → Product
- belongsTo → Material
- belongsTo → Unit

---

### 10. Production

Model untuk proses produksi.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| production_number | string | Nomor produksi (PK/PR/PS-YYMMDD-XXXX) |
| transaction_id | UUID | FK ke Transaction (nullable) |
| method | enum | pesanan-kotak/pesanan-reguler/siap-beli |
| status | enum | Status produksi |
| date | date | Tanggal produksi |

**Relasi:**
- belongsTo → Transaction
- hasMany → ProductionDetail
- hasMany → ProductionWorker

---

### 11. Transaction

Model untuk transaksi penjualan.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| invoice_number | string | Nomor invoice (OK/OR/OS-YYMMDD-XXXX) |
| user_id | UUID | FK ke User (kasir) |
| customer_id | UUID | FK ke Customer (nullable) |
| method | enum | pesanan-kotak/pesanan-reguler/siap-beli |
| status | enum | Status transaksi |
| total | decimal | Total transaksi |
| points_used | integer | Poin yang digunakan |
| total_refund | decimal | Total refund |

**Relasi:**
- belongsTo → User
- belongsTo → Customer
- hasMany → TransactionDetail
- hasMany → Payment
- hasOne → Production
- hasOne → Refund

---

### 12. Payment

Model untuk pembayaran transaksi.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| transaction_id | UUID | FK ke Transaction |
| payment_channel_id | UUID | FK ke PaymentChannel |
| receipt_number | string | Nomor kwitansi |
| amount | decimal | Jumlah pembayaran |
| payment_group | string | Grup pembayaran |

**Relasi:**
- belongsTo → Transaction
- belongsTo → PaymentChannel

---

## Increment 2: Model Fungsionalitas Pendukung

### 13. User

Model untuk pengguna sistem.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | string | Nama lengkap |
| email | string | Email (unique) |
| password | string | Password (hashed) |
| gender | enum | Jenis kelamin |
| is_active | boolean | Status aktif |
| invitation_token | string | Token undangan |
| invitation_sent_at | timestamp | Waktu kirim undangan |
| activated_at | timestamp | Waktu aktivasi |

**Relasi:**
- hasMany → ProductionWorker
- hasMany → Notification
- morphMany → Role (via Spatie)

**Method Penting:**
- `sendInvitation()` - Kirim email undangan
- `activateWithPassword(password)` - Aktivasi akun
- `hasValidInvitationToken()` - Cek validitas token
- `toggleActive()` - Toggle status aktif

---

### 14. Customer

Model untuk pelanggan dengan sistem poin.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| name | string | Nama pelanggan |
| phone | string | Nomor telepon (unique) |
| points | integer | Jumlah poin |

**Relasi:**
- hasMany → Transaction
- hasMany → PointsHistory

---

### 15. PointsHistory

Model untuk riwayat perubahan poin.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| phone | string | FK ke Customer.phone |
| transaction_id | UUID | FK ke Transaction |
| points_change | integer | Perubahan poin (+/-) |
| description | text | Keterangan |
| image | string | Bukti (opsional) |

**Relasi:**
- belongsTo → Customer
- belongsTo → Transaction

---

### 16. Hitung

Model untuk stock opname.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| hitung_number | string | Nomor hitung (HC-YYMMDD-XXXX) |
| user_id | UUID | FK ke User |
| status | enum | Status hitung |
| date | date | Tanggal hitung |

**Relasi:**
- belongsTo → User
- hasMany → HitungDetail

---

### 17. InventoryLog

Model untuk log perubahan inventori.

| Atribut | Tipe | Deskripsi |
|---------|------|-----------|
| id | UUID | Primary key |
| material_id | UUID | FK ke Material |
| material_batch_id | UUID | FK ke MaterialBatch |
| user_id | UUID | FK ke User |
| action | enum | Aksi (produksi/penyesuaian/belanja) |
| quantity_change | decimal | Perubahan kuantitas |
| quantity_after | decimal | Kuantitas setelah |
| reference_type | string | Tipe referensi |
| reference_id | UUID | ID referensi |
| note | text | Catatan |

**Relasi:**
- belongsTo → Material
- belongsTo → MaterialBatch
- belongsTo → User

---

## Diagram Referensi

```
Referensi lengkap: docs/diagrams/puml/class-diagram.puml
```

---

## Ringkasan Relasi Utama

| Dari | Ke | Tipe | Kardinalitas |
|------|-----|------|--------------|
| Category | Product | One-to-Many | 1..* |
| Supplier | Expense | One-to-Many | 1..* |
| Material | MaterialBatch | One-to-Many | 1..* |
| Material | ProductComposition | One-to-Many | 0..* |
| Product | ProductComposition | One-to-Many | 0..* |
| Product | Production | One-to-Many | 0..* |
| Transaction | TransactionDetail | One-to-Many | 1..* |
| Transaction | Payment | One-to-Many | 1..* |
| Transaction | Production | One-to-One | 0..1 |
| Customer | Transaction | One-to-Many | 0..* |
| User | Transaction | One-to-Many | 0..* |
