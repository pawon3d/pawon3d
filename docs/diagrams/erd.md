# Dokumentasi Entity Relationship Diagram (ERD)

## Pendahuluan

Entity Relationship Diagram (ERD) Sistem Manajemen Toko Kue Pawon3D menggambarkan struktur basis data relasional yang digunakan oleh sistem. Diagram ini menunjukkan entitas-entitas, atribut-atributnya, serta hubungan antar entitas yang telah diimplementasikan menggunakan MySQL sebagai Database Management System (DBMS).

---

## Karakteristik Umum Database

- **DBMS**: MySQL
- **Primary Key**: UUID (Universal Unique Identifier) untuk semua tabel utama
- **Foreign Key**: Menggunakan UUID dengan referential integrity
- **Timestamps**: Semua tabel memiliki created_at dan updated_at

---

## Daftar Entitas

Sistem Pawon3D memiliki total **31 entitas** yang terbagi ke dalam beberapa kategori:

### Kategori Autentikasi & Pengguna (5 entitas)
1. **users** - Data pengguna sistem
2. **roles** - Peran pengguna
3. **permissions** - Hak akses
4. **model_has_roles** - Pivot user-role
5. **role_has_permissions** - Pivot role-permission

### Kategori Pelanggan (2 entitas)
6. **customers** - Data pelanggan
7. **points_histories** - Riwayat poin loyalitas

### Kategori Produk & Kategori (6 entitas)
8. **categories** - Kategori produk
9. **products** - Data produk
10. **product_categories** - Pivot produk-kategori
11. **type_costs** - Jenis biaya tambahan
12. **other_costs** - Biaya tambahan per produk
13. **units** - Satuan ukur dengan konversi

### Kategori Transaksi (5 entitas)
14. **shifts** - Sesi kerja kasir
15. **transactions** - Transaksi penjualan
16. **transaction_details** - Detail item transaksi
17. **payment_channels** - Channel pembayaran
18. **payments** - Data pembayaran

### Kategori Bahan Baku & Inventori (8 entitas)
19. **materials** - Data bahan baku
20. **material_details** - Konfigurasi satuan per bahan
21. **material_batches** - Batch stok bahan
22. **product_compositions** - Resep/komposisi produk
23. **ingredient_categories** - Kategori bahan baku
24. **ingredient_category_details** - Pivot kategori-bahan
25. **inventory_logs** - Log pergerakan stok

### Kategori Supplier & Belanja (3 entitas)
26. **suppliers** - Data pemasok
27. **expenses** - Transaksi belanja
28. **expense_details** - Detail item belanja

### Kategori Produksi (3 entitas)
29. **productions** - Data produksi
30. **production_details** - Detail produk yang diproduksi
31. **production_workers** - Pekerja produksi

### Kategori Stock Opname (2 entitas)
32. **hitungs** - Data stock opname
33. **hitung_details** - Detail item stock opname

### Kategori Lainnya (5 entitas)
34. **refunds** - Pengembalian dana
35. **store_profiles** - Profil usaha
36. **store_documents** - Dokumen usaha
37. **notifications** - Notifikasi pengguna
38. **activity_log** - Log aktivitas sistem

---

## Entitas Inti

### users

| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PRIMARY KEY | UUID |
| name | VARCHAR(50) | NOT NULL | Nama pengguna |
| email | VARCHAR(100) | UNIQUE, NOT NULL | Email login |
| phone | VARCHAR(20) | NULLABLE | Telepon |
| password | VARCHAR(255) | NOT NULL | Password hash |
| image | VARCHAR(255) | NULLABLE | Path foto |
| gender | VARCHAR(20) | NULLABLE | Jenis kelamin |
| is_active | BOOLEAN | DEFAULT FALSE | Status aktif |
| invitation_token | VARCHAR(64) | NULLABLE | Token aktivasi |
| invitation_sent_at | TIMESTAMP | NULLABLE | Waktu kirim undangan |
| activated_at | TIMESTAMP | NULLABLE | Waktu aktivasi |

---

### customers

| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PRIMARY KEY | UUID |
| phone | VARCHAR(20) | UNIQUE, NULLABLE | Telepon unik |
| name | VARCHAR(50) | NULLABLE | Nama pelanggan |
| points | DECIMAL(9,0) | DEFAULT 0 | Poin loyalitas |

---

### products

| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PRIMARY KEY | UUID |
| name | VARCHAR(50) | NOT NULL | Nama produk |
| description | VARCHAR(50) | NULLABLE | Deskripsi |
| price | DECIMAL(10,0) | DEFAULT 0 | Harga jual |
| stock | DECIMAL(10,0) | DEFAULT 0 | Stok tersedia |
| method | JSON | NULLABLE | Metode penjualan |
| product_image | VARCHAR(255) | NULLABLE | Path gambar |
| is_recipe | BOOLEAN | DEFAULT FALSE | Produk resep |
| is_active | BOOLEAN | DEFAULT FALSE | Status aktif |
| capital | DECIMAL(10,0) | DEFAULT 0 | Modal total |
| pcs | DECIMAL(10,0) | DEFAULT 0 | Pcs per produksi |
| pcs_capital | DECIMAL(10,0) | DEFAULT 0 | Modal per pcs |

---

### type_costs

| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PRIMARY KEY | UUID |
| name | VARCHAR(255) | NULLABLE | Nama jenis biaya |

---

### other_costs

| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PRIMARY KEY | UUID |
| product_id | CHAR(36) | FOREIGN KEY → products.id | Referensi produk |
| type_cost_id | CHAR(36) | FOREIGN KEY → type_costs.id | Referensi jenis biaya |
| price | DECIMAL(15,2) | DEFAULT 0 | Nilai biaya |

---

### transactions

| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PRIMARY KEY | UUID |
| user_id | CHAR(36) | FOREIGN KEY → users.id | Kasir |
| customer_id | CHAR(36) | FOREIGN KEY → customers.id | Pelanggan |
| invoice_number | VARCHAR(30) | UNIQUE | Nomor invoice |
| name | VARCHAR(50) | NULLABLE | Nama pelanggan |
| phone | VARCHAR(20) | NULLABLE | Telepon |
| date | DATE | NULLABLE | Tanggal transaksi |
| time | TIME | NULLABLE | Waktu transaksi |
| payment_status | VARCHAR(20) | NULLABLE | Status pembayaran |
| status | VARCHAR(20) | NULLABLE | Status transaksi |
| method | VARCHAR(20) | DEFAULT 'pesanan-reguler' | Metode |
| total_amount | DECIMAL(15,0) | NULLABLE | Total nilai |
| points_used | INTEGER | DEFAULT 0 | Poin digunakan |
| points_discount | DECIMAL(15,2) | DEFAULT 0 | Diskon poin |
| total_refund | DECIMAL(15,0) | DEFAULT 0 | Total refund |
| created_by_shift | CHAR(36) | FOREIGN KEY → shifts.id | Shift pembuatan |

---

### payment_channels

| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PRIMARY KEY | UUID |
| type | VARCHAR(10) | NULLABLE | Tipe (bank/e-wallet) |
| group | VARCHAR(50) | NULLABLE | Grup pembayaran |
| bank_name | VARCHAR(50) | NULLABLE | Nama bank |
| account_number | VARCHAR(255) | NULLABLE | Nomor rekening |
| account_name | VARCHAR(50) | NULLABLE | Nama rekening |
| qris_image | VARCHAR(255) | NULLABLE | Gambar QRIS |
| is_active | BOOLEAN | DEFAULT TRUE | Status aktif |

---

### materials

| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PRIMARY KEY | UUID |
| name | VARCHAR(50) | NOT NULL | Nama bahan |
| description | VARCHAR(255) | NULLABLE | Deskripsi |
| image | VARCHAR(255) | NULLABLE | Gambar |
| expiry_date | DATE | NULLABLE | Kedaluwarsa terdekat |
| status | VARCHAR(20) | DEFAULT 'kosong' | Status stok |
| is_active | BOOLEAN | DEFAULT FALSE | Status aktif |
| minimum | DECIMAL(15,5) | DEFAULT 0 | Stok minimum |

---

### store_documents

| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PRIMARY KEY | UUID |
| document_name | VARCHAR(255) | NULLABLE | Nama dokumen |
| document_number | VARCHAR(255) | NULLABLE | Nomor dokumen |
| document_file | VARCHAR(255) | NULLABLE | Path file |
| valid_from | DATE | NULLABLE | Berlaku dari |
| valid_until | DATE | NULLABLE | Berlaku sampai |

---

### activity_log

| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | BIGINT | PRIMARY KEY | Auto increment |
| log_name | VARCHAR(255) | NULLABLE | Nama log |
| description | TEXT | NOT NULL | Deskripsi aktivitas |
| subject_type | VARCHAR(255) | NULLABLE | Tipe subjek |
| subject_id | CHAR(36) | NULLABLE | ID subjek |
| causer_type | VARCHAR(255) | NULLABLE | Tipe pelaku |
| causer_id | CHAR(36) | NULLABLE | ID pelaku |
| properties | JSON | NULLABLE | Properti tambahan |
| event | VARCHAR(255) | NULLABLE | Jenis event |
| batch_uuid | CHAR(36) | NULLABLE | UUID batch |

---

## Diagram Relasi

```
[users] 1──┬──* [notifications]
           ├──* [model_has_roles]
           ├──* [production_workers]
           ├──* [hitungs]
           ├──* [inventory_logs]
           └──* [shifts] (opened_by, closed_by)

[roles] 1──┬──* [model_has_roles]
           └──* [role_has_permissions]

[permissions] 1──* [role_has_permissions]

[customers] 1──┬──* [transactions]
               └──* [points_histories]

[categories] *──┬──* [products] (via product_categories)

[products] 1──┬──* [transaction_details]
              ├──* [production_details]
              ├──* [product_compositions]
              └──* [other_costs]

[type_costs] 1──* [other_costs]

[units] 1──┬──* [material_details]
           ├──* [material_batches]
           ├──* [product_compositions]
           ├──* [expense_details]
           ├──* [hitung_details]
           └──1 [units] (self-reference: base_unit)

[materials] 1──┬──* [material_details]
               ├──* [material_batches]
               ├──* [product_compositions]
               ├──* [expense_details]
               ├──* [ingredient_category_details]
               ├──* [hitung_details]
               └──* [inventory_logs]

[ingredient_categories] 1──* [ingredient_category_details]

[suppliers] 1──* [expenses]
[expenses] 1──* [expense_details]

[transactions] 1──┬──* [transaction_details]
                  ├──1 [productions]
                  ├──* [payments]
                  ├──1 [refunds]
                  └──* [points_histories]

[productions] 1──┬──* [production_details]
                 └──* [production_workers]

[hitungs] 1──* [hitung_details]

[shifts] 1──┬──* [transactions] (created_by, refund_by, cancelled_by)
            └──* [refunds] (refund_by_shift)

[payment_channels] 1──┬──* [payments]
                      └──* [refunds]

[material_batches] 1──* [inventory_logs]
```

---

## Diagram ERD

Diagram ERD lengkap tersedia dalam format PlantUML pada berkas berikut:

- `puml/erd.puml`

---

## Kesimpulan

ERD Sistem Pawon3D terdiri dari 38 entitas yang menggambarkan seluruh struktur data sistem. Relasi antar entitas menggunakan foreign key dengan UUID untuk menjaga integritas referensial. Sistem mencakup semua aspek operasional toko kue mulai dari autentikasi, transaksi, inventori, produksi, hingga pelaporan.
