# CLASS DIAGRAM

## Sistem Informasi Manajemen Toko Kue (revisi Des 2025)

Struktur kelas disesuaikan dengan skema basis data terbaru (SQLite) dan domain aplikasi Laravel 12 + Livewire. Paket di bawah mengelompokkan model domain utama.

- **User & Auth**: pengguna, role/permission (Spatie), notifikasi.
- **Customer & Points**: pelanggan dan riwayat poin berbasis nomor telepon.
- **Product Management**: produk siap-beli/pesanan, kategori (pivot), komposisi bahan, biaya tambahan.
- **Material & Inventory**: master bahan, batch FIFO, satuan, kategori bahan, log pergerakan stok.
- **Transaction**: transaksi POS/pesanan, detail item, pembayaran multi-channel, refund, shift kasir.
- **Production**: antrian produksi untuk pesanan/ketersediaan, detail produksi, pekerja produksi.
- **Expense**: belanja bahan baku dari supplier beserta detail.
- **Stock Count**: hitung stok / rusak / hilang.
- **Store Settings**: profil toko dan dokumen legal.

```plantuml
@startuml Class Diagram - Sistem Manajemen Toko Kue
skinparam classAttributeIconSize 0
skinparam classFontSize 11
skinparam classAttributeFontSize 10

package "User & Authentication" #LightBlue {
    class User {
        - id: uuid <<PK>>
        - name: string
        - email: string
        - phone: string
        - password: string
        - image: string
        - gender: string
        - is_active: bool
        - invitation_token: string
        - invitation_sent_at: datetime
        - activated_at: datetime
        - remember_token: string
        --
        + roles(): BelongsToMany
        + permissions(): BelongsToMany
        + notifications(): HasMany
        + transactions(): HasMany
        + productions(): HasMany
        + inventoryLogs(): HasMany
        + shiftsOpened(): HasMany
        + shiftsClosed(): HasMany
    }

    class Role {
        - id: int <<PK>>
        - name: string
        - guard_name: string
        - max_users: int
        --
        + permissions(): BelongsToMany
        + users(): BelongsToMany
    }

    class Permission {
        - id: int <<PK>>
        - name: string
        - guard_name: string
    }

    class Notification {
        - id: uuid <<PK>>
        - user_id: uuid <<FK>>
        - title: string
        - body: text
        - type: string
        - status: int
        - is_read: bool
        --
        + user(): BelongsTo
    }
}

package "Customer & Points" #LightGreen {
    class Customer {
        - id: uuid <<PK>>
        - phone: string <<unique>>
        - name: string
        - points: numeric
        --
        + transactions(): HasMany
        + pointsHistories(): HasMany
    }

    class PointsHistory {
        - id: uuid <<PK>>
        - phone: string <<FK>>
        - transaction_id: uuid <<FK>>
        - action_id: uuid
        - action: string
        - points: numeric
        - image: string
        --
        + customer(): BelongsTo
        + transaction(): BelongsTo
    }
}

package "Product Management" #LightYellow {
    class Product {
        - id: uuid <<PK>>
        - name: string
        - description: text
        - price: numeric
        - stock: numeric
        - method: json
        - product_image: string
        - is_recipe: bool
        - is_active: bool
        - is_ready: bool
        - is_recommended: bool
        - is_other: bool
        - pcs: numeric
        - pcs_capital: numeric
        - capital: numeric
        - suhu_ruangan: numeric
        - suhu_dingin: numeric
        - suhu_beku: numeric
        --
        + categories(): BelongsToMany
        + compositions(): HasMany
        + transactionDetails(): HasMany
        + productionDetails(): HasMany
        + otherCosts(): HasMany
    }

    class Category {
        - id: uuid <<PK>>
        - name: string
        - is_active: bool
        --
        + products(): BelongsToMany
    }

    class ProductCategory {
        - id: uuid <<PK>>
        - product_id: uuid <<FK>>
        - category_id: uuid <<FK>>
    }

    class ProductComposition {
        - id: uuid <<PK>>
        - product_id: uuid <<FK>>
        - material_id: uuid <<FK>>
        - unit_id: uuid <<FK>>
        - material_quantity: numeric
        --
        + product(): BelongsTo
        + material(): BelongsTo
        + unit(): BelongsTo
    }

    class OtherCost {
        - id: uuid <<PK>>
        - product_id: uuid <<FK>>
        - type_cost_id: uuid <<FK>>
        - price: numeric
        --
        + product(): BelongsTo
        + typeCost(): BelongsTo
    }

    class TypeCost {
        - id: uuid <<PK>>
        - name: string
        --
        + otherCosts(): HasMany
    }
}

package "Material & Inventory" #LightSalmon {
    class Material {
        - id: uuid <<PK>>
        - name: string
        - description: string
        - image: string
        - expiry_date: date
        - status: string
        - is_active: bool
        - is_recipe: bool
        - minimum: numeric
        --
        + batches(): HasMany
        + details(): HasMany
        + compositions(): HasMany
        + inventoryLogs(): HasMany
        + categories(): BelongsToMany
    }

    class MaterialBatch {
        - id: uuid <<PK>>
        - material_id: uuid <<FK>>
        - unit_id: uuid <<FK>>
        - batch_number: string
        - date: date
        - batch_quantity: numeric
        --
        + material(): BelongsTo
        + unit(): BelongsTo
        + inventoryLogs(): HasMany
    }

    class MaterialDetail {
        - id: uuid <<PK>>
        - material_id: uuid <<FK>>
        - unit_id: uuid <<FK>>
        - is_main: bool
        - quantity: numeric
        - supply_quantity: numeric
        - supply_price: numeric
        --
        + material(): BelongsTo
        + unit(): BelongsTo
    }

    class Unit {
        - id: uuid <<PK>>
        - name: string
        - alias: string
        - group: string
        - base_unit_id: uuid <<FK>>
        - conversion_factor: numeric
        --
        + base(): BelongsTo
        + children(): HasMany
    }

    class IngredientCategory {
        - id: uuid <<PK>>
        - name: string
        - is_active: bool
        --
        + materials(): BelongsToMany
    }

    class IngredientCategoryDetail {
        - id: uuid <<PK>>
        - ingredient_category_id: uuid <<FK>>
        - material_id: uuid <<FK>>
    }

    class InventoryLog {
        - id: uuid <<PK>>
        - material_id: uuid <<FK>>
        - material_batch_id: uuid <<FK>>
        - user_id: uuid <<FK>>
        - action: string
        - quantity_change: numeric
        - quantity_after: numeric
        - reference_type: string
        - reference_id: uuid
        - note: text
        --
        + material(): BelongsTo
        + batch(): BelongsTo
        + user(): BelongsTo
    }
}

package "Transaction" #LightCyan {
    class Transaction {
        - id: uuid <<PK>>
        - user_id: uuid <<FK>>
        - customer_id: uuid <<FK>>
        - invoice_number: string
        - name: string
        - phone: string
        - date/time: date,time
        - start_date: date
        - end_date: date
        - note: text
        - payment_status: string
        - status: string
        - method: string
        - total_amount: numeric
        - total_refund: numeric
        - created_by_shift: uuid <<FK>>
        - refund_by_shift: uuid <<FK>>
        - points_used: int
        - points_discount: numeric
        - created_at/updated_at: datetime
        --
        + details(): HasMany
        + payments(): HasMany
        + refunds(): HasMany
        + production(): HasOne
        + user(): BelongsTo
        + customer(): BelongsTo
        + createdShift(): BelongsTo
        + refundShift(): BelongsTo
    }

    class TransactionDetail {
        - id: uuid <<PK>>
        - transaction_id: uuid <<FK>>
        - product_id: uuid <<FK>>
        - quantity: numeric
        - price: numeric
        - refund_quantity: numeric
        --
        + transaction(): BelongsTo
        + product(): BelongsTo
    }

    class Payment {
        - id: uuid <<PK>>
        - transaction_id: uuid <<FK>>
        - payment_channel_id: uuid <<FK>>
        - payment_method: string
        - paid_amount: numeric
        - image: string
        - paid_at: datetime
        - receipt_number: string
        --
        + transaction(): BelongsTo
        + channel(): BelongsTo
    }

    class PaymentChannel {
        - id: uuid <<PK>>
        - type: string
        - group: string
        - bank_name: string
        - account_number: string
        - account_name: string
        - qris_image: string
        - is_active: bool
    }

    class Refund {
        - id: uuid <<PK>>
        - transaction_id: uuid <<FK>>
        - payment_channel_id: uuid <<FK>>
        - refund_by_shift: uuid <<FK>>
        - reason: string
        - proof_image: string
        - refund_method: string
        - account_number: string
        - total_amount: numeric
        - refunded_at: datetime
        --
        + transaction(): BelongsTo
        + channel(): BelongsTo
        + shift(): BelongsTo
    }

    class Shift {
        - id: uuid <<PK>>
        - shift_number: string
        - opened_by: uuid <<FK>>
        - closed_by: uuid <<FK>>
        - start_time: datetime
        - end_time: datetime
        - status: string
        - initial_cash: numeric
        - final_cash: numeric
        - total_sales: numeric
        - total_refunds: numeric
        - total_discounts: numeric
        --
        + opener(): BelongsTo
        + closer(): BelongsTo
        + transactions(): HasMany
        + refunds(): HasMany
    }
}

package "Production" #LightGray {
    class Production {
        - id: uuid <<PK>>
        - production_number: string
        - transaction_id: uuid <<FK>>
        - method: string
        - date/time: date,time
        - start_date: date
        - end_date: date
        - note: string
        - status: string
        - is_start: bool
        - is_finish: bool
        --
        + transaction(): BelongsTo
        + details(): HasMany
        + workers(): HasMany
    }

    class ProductionDetail {
        - id: uuid <<PK>>
        - production_id: uuid <<FK>>
        - product_id: uuid <<FK>>
        - quantity_plan: numeric
        - quantity_get: numeric
        - quantity_fail: numeric
        - cycle: numeric
        --
        + production(): BelongsTo
        + product(): BelongsTo
    }

    class ProductionWorker {
        - id: uuid <<PK>>
        - production_id: uuid <<FK>>
        - user_id: uuid <<FK>>
        --
        + production(): BelongsTo
        + user(): BelongsTo
    }
}

package "Expense" #LightOrange {
    class Expense {
        - id: uuid <<PK>>
        - expense_number: string
        - expense_date: date
        - end_date: date
        - supplier_id: uuid <<FK>>
        - note: string
        - status: string
        - grand_total_expect: numeric
        - grand_total_actual: numeric
        - is_start: bool
        - is_finish: bool
        --
        + supplier(): BelongsTo
        + details(): HasMany
    }

    class ExpenseDetail {
        - id: uuid <<PK>>
        - expense_id: uuid <<FK>>
        - material_id: uuid <<FK>>
        - unit_id: uuid <<FK>>
        - quantity_expect: numeric
        - quantity_get: numeric
        - is_quantity_get: bool
        - price_expect: numeric
        - price_get: numeric
        - total_expect: numeric
        - total_actual: numeric
        - expiry_date: date
        --
        + expense(): BelongsTo
        + material(): BelongsTo
        + unit(): BelongsTo
    }

    class Supplier {
        - id: uuid <<PK>>
        - name: string
        - description: string
        - contact_name: string
        - phone: string
        - image: string
        - street: string
        - landmark: string
        - maps_link: string
    }
}

package "Stock Count" #Thistle {
    class Hitung {
        - id: uuid <<PK>>
        - hitung_number: string
        - action: string
        - note: string
        - status: string
        - hitung_date: date
        - hitung_date_finish: date
        - is_start: bool
        - is_finish: bool
        - grand_total: numeric
        - loss_grand_total: numeric
        - user_id: uuid <<FK>>
        --
        + user(): BelongsTo
        + details(): HasMany
    }

    class HitungDetail {
        - id: uuid <<PK>>
        - hitung_id: uuid <<FK>>
        - material_id: uuid <<FK>>
        - material_batch_id: uuid <<FK>>
        - quantity_expect: numeric
        - quantity_actual: numeric
        - total: numeric
        - loss_total: numeric
        --
        + hitung(): BelongsTo
        + material(): BelongsTo
        + batch(): BelongsTo
    }
}

package "Store Settings" #HoneyDew {
    class StoreProfile {
        - id: uuid <<PK>>
        - logo: string
        - name: string
        - tagline: string
        - type: string
        - banner: string
        - product: string
        - description: text
        - building: string
        - location: string
        - address: string
        - contact: string
        - email: string
        - website: string
        - social_instagram: string
        - social_facebook: string
        - social_whatsapp: string
        - product_image: string
    }

    class StoreDocument {
        - id: uuid <<PK>>
        - document_name: string
        - document_number: string
        - document_file: string
        - valid_from: date
        - valid_until: date
    }
}

' === RELATIONSHIPS ===
User ||--o{ Notification
User ||--o{ Transaction
User ||--o{ InventoryLog
User ||--o{ ProductionWorker
User ||--o{ Hitung
User ||--o{ Shift : opens/closes
Role ||--o{ Permission : role_has_permissions
User ||--o{ Role : model_has_roles
User ||--o{ Permission : model_has_permissions

Customer ||--o{ Transaction
Customer ||--o{ PointsHistory
Transaction ||--o{ PointsHistory

Product ||--o{ ProductComposition
Material ||--o{ ProductComposition
Unit ||--o{ ProductComposition
Product ||--o{ ProductCategory
Category ||--o{ ProductCategory
Product ||--o{ TransactionDetail
Product ||--o{ ProductionDetail
Product ||--o{ OtherCost
TypeCost ||--o{ OtherCost

Material ||--o{ MaterialBatch
Unit ||--o{ MaterialBatch
Material ||--o{ MaterialDetail
Unit ||--o{ MaterialDetail
IngredientCategory ||--o{ IngredientCategoryDetail
Material ||--o{ IngredientCategoryDetail
Material ||--o{ InventoryLog
MaterialBatch ||--o{ InventoryLog
Unit ||--o{ MaterialBatch

Transaction ||--o{ TransactionDetail
Transaction ||--o{ Payment
Transaction ||--o{ Refund
PaymentChannel ||--o{ Payment
PaymentChannel ||--o{ Refund
Shift ||--o{ Transaction : created_by_shift
Shift ||--o{ Transaction : refund_by_shift
Shift ||--o{ Refund : refund_by_shift

Transaction ||--|| Production : optional
Production ||--o{ ProductionDetail
Production ||--o{ ProductionWorker

Expense ||--o{ ExpenseDetail
Supplier ||--o{ Expense
Material ||--o{ ExpenseDetail
Unit ||--o{ ExpenseDetail

Hitung ||--o{ HitungDetail
Material ||--o{ HitungDetail
MaterialBatch ||--o{ HitungDetail

@enduml
```
