# Entity Relationship Diagram - Sistem Pawon3D

## Pendahuluan

Entity Relationship Diagram (ERD) menggambarkan struktur database sistem Pawon3D. Diagram ini menunjukkan entitas, atribut, dan relasi antar tabel dalam database MySQL.

## Konvensi Database

- **DBMS**: MySQL
- **Primary Key**: UUID (CHAR(36))
- **Foreign Key**: Menggunakan naming convention `{table}_id`
- **Timestamp**: `created_at` dan `updated_at` pada setiap tabel
- **Charset**: utf8mb4

---

## Increment 1: Entitas Fungsionalitas Inti

### Tabel Master Data

#### categories
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama kategori |
| description | TEXT | NULLABLE | Deskripsi |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### units
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama satuan |
| alias | VARCHAR(50) | NULLABLE | Singkatan |
| group | VARCHAR(100) | NULLABLE | Grup konversi |
| base_unit_id | CHAR(36) | FK → units.id | Satuan dasar |
| conversion_factor | DECIMAL(15,10) | DEFAULT 1 | Faktor konversi |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### ingredient_categories
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama kategori bahan |
| description | TEXT | NULLABLE | Deskripsi |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### type_costs
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama jenis biaya |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### suppliers
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama supplier |
| description | TEXT | NULLABLE | Deskripsi |
| contact_name | VARCHAR(255) | NULLABLE | Nama kontak |
| phone | VARCHAR(50) | NULLABLE | Telepon |
| street | VARCHAR(255) | NULLABLE | Alamat |
| landmark | VARCHAR(255) | NULLABLE | Patokan |
| maps_link | TEXT | NULLABLE | Link Maps |
| image | VARCHAR(255) | NULLABLE | Path gambar |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

### Tabel Bahan Baku

#### materials
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama bahan |
| description | TEXT | NULLABLE | Deskripsi |
| minimum | INTEGER | DEFAULT 0 | Stok minimum |
| status | ENUM | DEFAULT 'Kosong' | Status ketersediaan |
| is_active | BOOLEAN | DEFAULT TRUE | Status aktif |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

**Enum status**: 'Tersedia', 'Hampir Habis', 'Habis', 'Kosong', 'Expired'

---

#### material_batches
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| material_id | CHAR(36) | FK → materials.id | Bahan baku |
| unit_id | CHAR(36) | FK → units.id | Satuan |
| batch_quantity | DECIMAL(15,4) | NOT NULL | Kuantitas |
| date | DATE | NOT NULL | Tanggal kedaluwarsa |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### material_details
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| material_id | CHAR(36) | FK → materials.id | Bahan baku |
| unit_id | CHAR(36) | FK → units.id | Satuan |
| quantity | DECIMAL(15,4) | NOT NULL | Kuantitas |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

### Tabel Belanja

#### expenses
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| expense_number | VARCHAR(50) | UNIQUE | Nomor belanja |
| supplier_id | CHAR(36) | FK → suppliers.id | Supplier |
| status | VARCHAR(50) | NOT NULL | Status |
| total | DECIMAL(15,2) | DEFAULT 0 | Total |
| date | DATE | | Tanggal |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### expense_details
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| expense_id | CHAR(36) | FK → expenses.id | Belanja |
| material_id | CHAR(36) | FK → materials.id | Bahan |
| quantity | DECIMAL(15,4) | NOT NULL | Kuantitas |
| price | DECIMAL(15,2) | DEFAULT 0 | Harga |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

### Tabel Produk

#### products
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama produk |
| category_id | CHAR(36) | FK → categories.id | Kategori |
| price | DECIMAL(15,2) | DEFAULT 0 | Harga jual |
| stock | INTEGER | DEFAULT 0 | Stok |
| is_ready | BOOLEAN | DEFAULT FALSE | Tersedia |
| product_image | VARCHAR(255) | NULLABLE | Gambar |
| method | JSON | NULLABLE | Metode penjualan |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### product_compositions
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| product_id | CHAR(36) | FK → products.id | Produk |
| material_id | CHAR(36) | FK → materials.id | Bahan |
| unit_id | CHAR(36) | FK → units.id | Satuan |
| quantity | DECIMAL(15,4) | NOT NULL | Kuantitas |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### other_costs
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| product_id | CHAR(36) | FK → products.id | Produk |
| type_cost_id | CHAR(36) | FK → type_costs.id | Jenis biaya |
| amount | DECIMAL(15,2) | DEFAULT 0 | Jumlah biaya |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

### Tabel Produksi

#### productions
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| production_number | VARCHAR(50) | UNIQUE | Nomor produksi |
| transaction_id | CHAR(36) | FK → transactions.id | Transaksi (nullable) |
| method | VARCHAR(50) | NOT NULL | Metode |
| status | VARCHAR(50) | NOT NULL | Status |
| date | DATE | | Tanggal |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### production_details
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| production_id | CHAR(36) | FK → productions.id | Produksi |
| product_id | CHAR(36) | FK → products.id | Produk |
| quantity | INTEGER | NOT NULL | Jumlah |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### production_workers
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| production_id | CHAR(36) | FK → productions.id | Produksi |
| user_id | CHAR(36) | FK → users.id | Pekerja |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

### Tabel Transaksi

#### transactions
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| invoice_number | VARCHAR(50) | UNIQUE | Nomor invoice |
| user_id | CHAR(36) | FK → users.id | Kasir |
| customer_id | CHAR(36) | FK → customers.id | Pelanggan (nullable) |
| method | VARCHAR(50) | NOT NULL | Metode transaksi |
| status | VARCHAR(50) | NOT NULL | Status |
| total | DECIMAL(15,2) | DEFAULT 0 | Total |
| points_used | INTEGER | DEFAULT 0 | Poin digunakan |
| total_refund | DECIMAL(15,2) | DEFAULT 0 | Total refund |
| cancelled_at | TIMESTAMP | NULLABLE | Waktu batal |
| cancellation_reason | TEXT | NULLABLE | Alasan batal |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### transaction_details
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| transaction_id | CHAR(36) | FK → transactions.id | Transaksi |
| product_id | CHAR(36) | FK → products.id | Produk |
| quantity | INTEGER | NOT NULL | Jumlah |
| price | DECIMAL(15,2) | NOT NULL | Harga saat transaksi |
| refund_quantity | INTEGER | DEFAULT 0 | Jumlah refund |
| pcs_capital_snapshot | DECIMAL(15,2) | NULLABLE | Harga modal |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### payments
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| transaction_id | CHAR(36) | FK → transactions.id | Transaksi |
| payment_channel_id | CHAR(36) | FK → payment_channels.id | Channel |
| receipt_number | VARCHAR(50) | UNIQUE | Nomor kwitansi |
| amount | DECIMAL(15,2) | NOT NULL | Jumlah |
| payment_group | VARCHAR(50) | NULLABLE | Grup pembayaran |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### payment_channels
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama channel |
| is_active | BOOLEAN | DEFAULT TRUE | Status aktif |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

## Increment 2: Entitas Fungsionalitas Pendukung

### Tabel Pengguna

#### users
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama |
| email | VARCHAR(255) | UNIQUE | Email |
| password | VARCHAR(255) | | Password (hashed) |
| gender | VARCHAR(20) | NULLABLE | Jenis kelamin |
| is_active | BOOLEAN | DEFAULT FALSE | Status aktif |
| invitation_token | VARCHAR(255) | NULLABLE | Token undangan |
| invitation_sent_at | TIMESTAMP | NULLABLE | Waktu kirim |
| activated_at | TIMESTAMP | NULLABLE | Waktu aktivasi |
| remember_token | VARCHAR(100) | NULLABLE | |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

### Tabel Pelanggan

#### customers
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama |
| phone | VARCHAR(50) | UNIQUE | Telepon |
| points | INTEGER | DEFAULT 0 | Jumlah poin |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### points_histories
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| phone | VARCHAR(50) | FK → customers.phone | Pelanggan |
| transaction_id | CHAR(36) | FK → transactions.id | Transaksi |
| points_change | INTEGER | NOT NULL | Perubahan poin |
| description | TEXT | NULLABLE | Keterangan |
| image | VARCHAR(255) | NULLABLE | Bukti |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

### Tabel Stock Opname

#### hitungs
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| hitung_number | VARCHAR(50) | UNIQUE | Nomor hitung |
| user_id | CHAR(36) | FK → users.id | Petugas |
| status | VARCHAR(50) | NOT NULL | Status |
| date | DATE | | Tanggal |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### hitung_details
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| hitung_id | CHAR(36) | FK → hitungs.id | Hitung |
| material_id | CHAR(36) | FK → materials.id | Bahan |
| system_quantity | DECIMAL(15,4) | | Kuantitas sistem |
| actual_quantity | DECIMAL(15,4) | | Kuantitas aktual |
| difference | DECIMAL(15,4) | | Selisih |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

### Tabel Log

#### inventory_logs
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| material_id | CHAR(36) | FK → materials.id | Bahan |
| material_batch_id | CHAR(36) | FK → material_batches.id | Batch |
| user_id | CHAR(36) | FK → users.id | Petugas |
| action | VARCHAR(50) | NOT NULL | Aksi |
| quantity_change | DECIMAL(15,4) | NOT NULL | Perubahan |
| quantity_after | DECIMAL(15,4) | NOT NULL | Setelah |
| reference_type | VARCHAR(100) | NULLABLE | Tipe referensi |
| reference_id | CHAR(36) | NULLABLE | ID referensi |
| note | TEXT | NULLABLE | Catatan |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### notifications
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| user_id | CHAR(36) | FK → users.id | Penerima |
| title | VARCHAR(255) | NOT NULL | Judul |
| message | TEXT | NOT NULL | Isi |
| type | VARCHAR(50) | NULLABLE | Tipe |
| status | VARCHAR(50) | NULLABLE | Status |
| is_read | BOOLEAN | DEFAULT FALSE | Sudah dibaca |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

### Tabel Pengaturan

#### store_profiles
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | | Nama toko |
| email | VARCHAR(255) | | Email toko |
| phone | VARCHAR(50) | | Telepon |
| address | TEXT | | Alamat |
| (kolom lainnya) | | | |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

#### shifts
| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|------------|-----------|
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama shift |
| start_time | TIME | NOT NULL | Waktu mulai |
| end_time | TIME | NOT NULL | Waktu selesai |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

---

## Diagram Referensi

```
Referensi lengkap: docs/diagrams/puml/erd.puml
```

---

## Ringkasan Relasi

### Relasi One-to-Many
- categories → products
- suppliers → expenses
- materials → material_batches
- materials → expense_details
- products → product_compositions
- products → transaction_details
- transactions → transaction_details
- transactions → payments
- users → notifications
- customers → points_histories

### Relasi One-to-One
- transactions → productions (untuk pesanan)

### Relasi Self-Referential
- units → units (konversi satuan)
