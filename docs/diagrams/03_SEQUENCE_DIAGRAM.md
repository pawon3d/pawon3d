# SEQUENCE DIAGRAM

## Sistem Informasi Manajemen Toko Kue

---

## 1. Sequence Diagram: Membuat Pesanan Siap Beli

```plantuml
@startuml Sequence Diagram - Membuat Pesanan Siap Beli

actor Kasir
participant "POS Page\n(Livewire)" as POS
participant "Transaction\nModel" as Transaction
participant "TransactionDetail\nModel" as TxDetail
participant "Product\nModel" as Product
participant "Customer\nModel" as Customer
participant "Payment\nModel" as Payment
participant "PointsHistory\nModel" as Points
participant "NotificationService" as Notif
database Database as DB

== Memilih Produk ==
Kasir -> POS: Pilih produk
POS -> Product: query()->where('is_ready', true)
Product -> DB: SELECT * FROM products
DB --> Product: products data
Product --> POS: filtered products
POS --> Kasir: Tampilkan produk tersedia

Kasir -> POS: addToCart(productId)
POS -> Product: find(productId)
Product --> POS: product data
POS -> POS: Tambah ke cart array
POS --> Kasir: Update tampilan cart

== Memproses Pembayaran ==
Kasir -> POS: Masukkan data pelanggan (phone)
POS -> Customer: firstOrCreate(['phone' => phone])
Customer -> DB: SELECT/INSERT customers
DB --> Customer: customer data
Customer --> POS: customer

Kasir -> POS: Pilih metode pembayaran
Kasir -> POS: Masukkan jumlah bayar
Kasir -> POS: processPayment()

== Menyimpan Transaksi ==
POS -> Transaction: create(data)
Transaction -> DB: INSERT INTO transactions
note right
  Data transaksi:
  - invoice_number (auto-generate)
  - user_id
  - customer_id
  - total_amount
  - payment_status
  - status: 'Selesai'
end note
DB --> Transaction: transaction created

POS -> TxDetail: createMany(cart items)
loop Untuk setiap item di cart
    TxDetail -> DB: INSERT INTO transaction_details
end
DB --> TxDetail: details created

== Update Stok Produk ==
loop Untuk setiap item
    POS -> Product: decrement('stock', quantity)
    Product -> DB: UPDATE products SET stock = stock - qty
    DB --> Product: updated
end

== Menyimpan Pembayaran ==
POS -> Payment: create(payment data)
Payment -> DB: INSERT INTO payments
DB --> Payment: payment created

== Menambah Poin (jika Lunas) ==
alt Pembayaran Lunas
    POS -> Customer: increment('points', earned)
    Customer -> DB: UPDATE customers SET points = points + earned
    DB --> Customer: updated

    POS -> Points: create(history)
    Points -> DB: INSERT INTO points_histories
    DB --> Points: history created
end

== Kirim Notifikasi ==
POS -> Notif: orderCompleted(invoice_number)
Notif -> DB: INSERT INTO notifications
DB --> Notif: notification created

POS --> Kasir: Tampilkan konfirmasi\ndan opsi cetak struk

@enduml
```

---

## 2. Sequence Diagram: Membuat Pesanan Reguler/Kotak

```plantuml
@startuml Sequence Diagram - Membuat Pesanan Reguler

actor Kasir
participant "Transaction Page\n(Livewire)" as TxPage
participant "Transaction\nModel" as Transaction
participant "TransactionDetail\nModel" as TxDetail
participant "Production\nModel" as Production
participant "ProductionDetail\nModel" as ProdDetail
participant "Customer\nModel" as Customer
participant "Payment\nModel" as Payment
participant "NotificationService" as Notif
database Database as DB

== Membuat Pesanan ==
Kasir -> TxPage: Pilih metode (pesanan-reguler/pesanan-kotak)
Kasir -> TxPage: Pilih produk dan jumlah
Kasir -> TxPage: Masukkan jadwal pengambilan
Kasir -> TxPage: Masukkan data pelanggan

TxPage -> Customer: firstOrCreate(['phone' => phone])
Customer --> TxPage: customer

Kasir -> TxPage: processOrder()

== Simpan Transaksi ==
TxPage -> Transaction: create(data)
note right
  Status: 'Antrian'
  Method: 'pesanan-reguler'/'pesanan-kotak'
end note
Transaction -> DB: INSERT INTO transactions
DB --> Transaction: transaction

TxPage -> TxDetail: createMany(items)
TxDetail -> DB: INSERT INTO transaction_details
DB --> TxDetail: details

== Buat Record Produksi ==
TxPage -> Production: create(data)
note right
  production_number: auto-generate
  transaction_id: link ke transaksi
  method: sama dengan transaksi
  status: 'Antrian'
end note
Production -> DB: INSERT INTO productions
DB --> Production: production

TxPage -> ProdDetail: createMany(items)
loop Untuk setiap item
    ProdDetail -> DB: INSERT INTO production_details
end
DB --> ProdDetail: details

== Proses Pembayaran (DP/Lunas) ==
alt Bayar DP
    TxPage -> Payment: create(dp_data)
    Payment -> DB: INSERT INTO payments
    TxPage -> Notif: paymentDownPayment(invoice, amount)
else Bayar Lunas
    TxPage -> Payment: create(full_data)
    Payment -> DB: INSERT INTO payments
    TxPage -> Notif: paymentCompleted(invoice, amount)
end

== Kirim Notifikasi ==
TxPage -> Notif: orderQueued(invoice_number)
Notif -> DB: INSERT INTO notifications (untuk kasir)
Notif -> DB: INSERT INTO notifications (untuk produksi)

TxPage --> Kasir: Pesanan berhasil dibuat\nMasuk antrian produksi

@enduml
```

---

## 3. Sequence Diagram: Memulai dan Menyelesaikan Produksi

```plantuml
@startuml Sequence Diagram - Proses Produksi

actor Produksi as User
participant "Antrian Page\n(Livewire)" as Queue
participant "Mulai Page\n(Livewire)" as Start
participant "Production\nModel" as Production
participant "ProductionDetail\nModel" as ProdDetail
participant "ProductionWorker\nModel" as Worker
participant "Product\nModel" as Product
participant "ProductComposition\nModel" as Composition
participant "MaterialBatch\nModel" as Batch
participant "InventoryLog\nModel" as Log
participant "Transaction\nModel" as Transaction
participant "NotificationService" as Notif
database Database as DB

== Melihat Antrian ==
User -> Queue: Buka halaman antrian
Queue -> Production: where('status', 'Antrian')->get()
Production -> DB: SELECT * FROM productions
DB --> Production: productions list
Production --> Queue: antrian produksi
Queue --> User: Tampilkan antrian

== Memulai Produksi ==
User -> Start: Pilih produksi
Start -> Production: find(id)->with('details.product')
Production -> DB: SELECT with relationships
DB --> Production: production + details
Production --> Start: production data

Start -> Start: Cek ketersediaan bahan

loop Untuk setiap produk di production_details
    Start -> Composition: where('product_id', id)->get()
    Composition -> DB: SELECT * FROM product_compositions
    DB --> Composition: komposisi bahan

    loop Untuk setiap bahan
        Start -> Batch: where('material_id', id)\n->sum('quantity')
        Batch -> DB: SUM(quantity) FROM material_batches
        DB --> Batch: total stok

        alt Stok Cukup
            Start -> Start: OK
        else Stok Tidak Cukup
            Start --> User: Peringatan: Bahan kurang
        end
    end
end

User -> Start: Pilih pekerja terlibat
User -> Start: Konfirmasi mulai produksi

Start -> Worker: createMany(workers)
Worker -> DB: INSERT INTO production_workers
DB --> Worker: workers created

== Kurangi Stok Bahan (FIFO) ==
loop Untuk setiap bahan
    Start -> Batch: orderBy('expired_at', 'asc')\n->orderBy('created_at', 'asc')
    note right
      FIFO: Batch dengan
      expired terdekat
      digunakan duluan
    end note
    Batch -> DB: SELECT FROM material_batches
    DB --> Batch: batches sorted

    Start -> Batch: decrement/delete
    Batch -> DB: UPDATE/DELETE material_batches
    DB --> Batch: updated

    Start -> Log: create(log_data)
    note right
      action: 'produksi'
      type: 'out'
    end note
    Log -> DB: INSERT INTO inventory_logs
end

Start -> Production: update(['status' => 'Proses', 'is_start' => true])
Production -> DB: UPDATE productions
DB --> Production: updated

Start -> Notif: productionProcessing(production_number)
Notif -> DB: INSERT INTO notifications
Start --> User: Produksi dimulai

== Menyelesaikan Produksi ==
User -> Start: Klik selesai produksi

Start -> Production: update(['status' => 'Selesai', 'is_finish' => true])
Production -> DB: UPDATE productions
DB --> Production: updated

== Tambah Stok Produk ==
loop Untuk setiap production_detail
    Start -> Product: increment('stock', qty)
    Product -> DB: UPDATE products SET stock = stock + qty
    DB --> Product: updated
end

== Update Status Transaksi (jika dari pesanan) ==
alt Ada transaction_id
    Start -> Transaction: update(['status' => 'Dapat Diambil'])
    Transaction -> DB: UPDATE transactions
    DB --> Transaction: updated

    Start -> Notif: orderReadyForPickup(invoice_number)
    Notif -> DB: INSERT notifications (untuk kasir)
end

Start -> Notif: productionCompleted(production_number)
Notif -> DB: INSERT INTO notifications

Start --> User: Produksi selesai

@enduml
```

---

## 4. Sequence Diagram: Proses Belanja Bahan Baku

```plantuml
@startuml Sequence Diagram - Proses Belanja

actor Inventori as User
participant "Expense Page\n(Livewire)" as ExpPage
participant "Expense\nModel" as Expense
participant "ExpenseDetail\nModel" as ExpDetail
participant "Material\nModel" as Material
participant "MaterialBatch\nModel" as Batch
participant "InventoryLog\nModel" as Log
participant "NotificationService" as Notif
database Database as DB

== Membuat Rencana Belanja ==
User -> ExpPage: Buka halaman tambah belanja
User -> ExpPage: Pilih supplier
User -> ExpPage: Tambah bahan baku dan jumlah

ExpPage -> Expense: create(expense_data)
note right
  expense_number: auto-generate
  status: 'Rencana'
  grand_total_expect: total perkiraan
end note
Expense -> DB: INSERT INTO expenses
DB --> Expense: expense

ExpPage -> ExpDetail: createMany(details)
loop Untuk setiap bahan
    ExpDetail -> DB: INSERT INTO expense_details
    note right
      quantity_expect: jumlah rencana
      price_expect: harga perkiraan
    end note
end
DB --> ExpDetail: details

ExpPage -> Notif: expensePlanned(expense_number)
Notif -> DB: INSERT INTO notifications

ExpPage --> User: Rencana belanja tersimpan

== Memulai Belanja ==
User -> ExpPage: Pilih belanja dan mulai
ExpPage -> Expense: update(['status' => 'Proses'])
Expense -> DB: UPDATE expenses
DB --> Expense: updated

ExpPage -> Notif: expenseProcessing(expense_number)
Notif -> DB: INSERT INTO notifications

ExpPage --> User: Belanja dimulai

== Menyelesaikan Belanja ==
User -> ExpPage: Isi jumlah aktual tiap bahan
User -> ExpPage: Isi harga aktual tiap bahan
User -> ExpPage: Isi tanggal expired (opsional)

loop Untuk setiap detail
    ExpPage -> ExpDetail: update(actual_data)
    note right
      quantity_actual: jumlah real
      price_actual: harga real
      expired_at: tanggal expired
    end note
    ExpDetail -> DB: UPDATE expense_details
end

User -> ExpPage: Selesaikan belanja

== Buat Batch Baru ==
loop Untuk setiap bahan
    ExpPage -> Batch: create(batch_data)
    note right
      material_id
      quantity: jumlah aktual
      expired_at
      price: harga per unit
    end note
    Batch -> DB: INSERT INTO material_batches
    DB --> Batch: batch created

    ExpPage -> Log: create(log_data)
    note right
      action: 'belanja'
      type: 'in'
    end note
    Log -> DB: INSERT INTO inventory_logs
end

== Update Status Bahan ==
loop Untuk setiap bahan
    ExpPage -> Material: Hitung total stok
    ExpPage -> Material: update(['status' => 'Tersedia'])
    Material -> DB: UPDATE materials
end

== Update Expense ==
ExpPage -> Expense: update(final_data)
note right
  status: 'Selesai'
  grand_total_actual: total aktual
end note
Expense -> DB: UPDATE expenses
DB --> Expense: updated

ExpPage -> Notif: expenseCompleted(expense_number)
Notif -> DB: INSERT INTO notifications

ExpPage --> User: Belanja selesai, stok terupdate

@enduml
```

---

## 5. Sequence Diagram: Proses Hitung Stok (Stock Opname)

```plantuml
@startuml Sequence Diagram - Hitung Stok

actor Inventori as User
participant "Hitung Page\n(Livewire)" as HitungPage
participant "Hitung\nModel" as Hitung
participant "HitungDetail\nModel" as HitungDetail
participant "Material\nModel" as Material
participant "MaterialBatch\nModel" as Batch
participant "InventoryLog\nModel" as Log
participant "NotificationService" as Notif
database Database as DB

== Membuat Rencana Hitung ==
User -> HitungPage: Buka halaman tambah hitung
User -> HitungPage: Pilih aksi (hitung/rusak/hilang)
User -> HitungPage: Pilih bahan-bahan

HitungPage -> Hitung: create(hitung_data)
note right
  hitung_number: auto-generate
  action: 'hitung'/'rusak'/'hilang'
  status: 'Rencana'
  user_id
end note
Hitung -> DB: INSERT INTO hitungs
DB --> Hitung: hitung

loop Untuk setiap bahan
    HitungPage -> Material: find(id)
    Material -> DB: SELECT * FROM materials

    HitungPage -> Batch: where('material_id', id)->sum('quantity')
    Batch -> DB: SUM(quantity)
    DB --> Batch: current_stock

    HitungPage -> HitungDetail: create(detail)
    note right
      material_id
      quantity_system: stok sistem saat ini
      quantity_actual: null (diisi nanti)
    end note
    HitungDetail -> DB: INSERT INTO hitung_details
end

HitungPage -> Notif: stockCountPlanned(hitung_number)
Notif -> DB: INSERT INTO notifications

HitungPage --> User: Rencana hitung tersimpan

== Memulai Hitung ==
User -> HitungPage: Pilih hitung dan mulai
HitungPage -> Hitung: update(['status' => 'Proses'])
Hitung -> DB: UPDATE hitungs
DB --> Hitung: updated

HitungPage -> Notif: stockCountProcessing(hitung_number)

HitungPage --> User: Proses hitung dimulai

== Input Hasil Hitung ==
User -> HitungPage: Masukkan jumlah aktual per bahan

loop Untuk setiap detail
    HitungPage -> HitungDetail: update(['quantity_actual' => input])
    HitungDetail -> DB: UPDATE hitung_details
end

== Menyelesaikan Hitung ==
User -> HitungPage: Selesaikan hitung

loop Untuk setiap detail
    HitungPage -> HitungDetail: Hitung selisih
    note right
      selisih = quantity_actual - quantity_system
    end note

    alt Aksi: Hitung (Stock Opname)
        alt Selisih > 0 (Kelebihan)
            HitungPage -> Batch: create(adjustment batch)
            Batch -> DB: INSERT material_batches

            HitungPage -> Log: create(log)
            note right
              action: 'penyesuaian'
              type: 'in'
            end note
        else Selisih < 0 (Kekurangan)
            HitungPage -> Batch: Kurangi stok (FIFO)
            Batch -> DB: UPDATE/DELETE material_batches

            HitungPage -> Log: create(log)
            note right
              action: 'penyesuaian'
              type: 'out'
            end note
        end

    else Aksi: Rusak/Hilang
        HitungPage -> Batch: Kurangi stok sebesar quantity_actual
        Batch -> DB: UPDATE/DELETE material_batches

        HitungPage -> Log: create(log)
        note right
          action: 'rusak'/'hilang'
          type: 'out'
        end note
    end

    HitungPage -> Material: Update status
    Material -> DB: UPDATE materials
end

HitungPage -> Hitung: update(['status' => 'Selesai'])
Hitung -> DB: UPDATE hitungs
DB --> Hitung: updated

HitungPage -> Notif: stockCountCompleted(hitung_number)
Notif -> DB: INSERT INTO notifications

HitungPage --> User: Hitung selesai, stok terupdate

@enduml
```

---

## 6. Sequence Diagram: Pengecekan Otomatis Stok & Expired

```plantuml
@startuml Sequence Diagram - Pengecekan Otomatis

participant "Laravel Scheduler" as Scheduler
participant "CheckInventoryAlerts\nCommand" as Command
participant "Material\nModel" as Material
participant "MaterialBatch\nModel" as Batch
participant "User\nModel" as User
participant "NotificationService" as Notif
database Database as DB

== Trigger Harian (08:00) ==
Scheduler -> Command: handle()
note right
  Dijalankan via cron:
  * 8 * * * php artisan schedule:run
end note

== Cek Stok Rendah ==
Command -> Material: where('is_active', true)->get()
Material -> DB: SELECT * FROM materials WHERE is_active = 1
DB --> Material: active materials

loop Untuk setiap material
    Command -> Batch: where('material_id', id)\n->where('quantity', '>', 0)->sum('quantity')
    Batch -> DB: SUM(quantity) FROM material_batches
    DB --> Batch: total_stock

    alt total_stock = 0
        Command -> Material: update(['status' => 'Kosong'])
        Material -> DB: UPDATE materials SET status = 'Kosong'
        Command -> Command: Tambah ke daftar alert

    else total_stock <= minimum
        Command -> Material: update(['status' => 'Hampir Habis'])
        Material -> DB: UPDATE materials SET status = 'Hampir Habis'
        Command -> Command: Tambah ke daftar alert

    else total_stock > minimum
        Command -> Material: update(['status' => 'Tersedia'])
        Material -> DB: UPDATE materials SET status = 'Tersedia'
    end
end

alt Ada material dengan stok rendah
    Command -> Notif: lowStockAlert(materials_list)
    Notif -> User: getWithPermission('inventori.*')
    User -> DB: SELECT users with inventori permissions
    DB --> User: users list

    loop Untuk setiap user inventori
        Notif -> DB: INSERT INTO notifications
        note right
          title: Alert Stok
          body: Daftar bahan hampir habis
          type: inventori
        end note
    end
end

== Cek Expired ==
Command -> Batch: where('expired_at', '<=', today())\n->where('quantity', '>', 0)->get()
Batch -> DB: SELECT FROM material_batches
DB --> Batch: expired batches

loop Untuk setiap batch expired
    Command -> Material: find(batch.material_id)
    Command -> Material: update(['status' => 'Expired'])
    Material -> DB: UPDATE materials SET status = 'Expired'
    Command -> Command: Tambah ke daftar alert expired
end

Command -> Batch: where('expired_at', '<=', today + 7 days)\n->where('expired_at', '>', today())
Batch -> DB: SELECT (akan expired dalam 7 hari)
DB --> Batch: expiring soon batches

loop Untuk setiap batch akan expired
    Command -> Command: Tambah ke daftar alert akan expired
end

alt Ada material expired/akan expired
    Command -> Notif: expiringAlert(materials_list)
    Notif -> User: getWithPermission('inventori.*')

    loop Untuk setiap user inventori
        Notif -> DB: INSERT INTO notifications
        note right
          title: Alert Expired
          body: Daftar bahan expired/akan expired
          type: inventori
        end note
    end
end

Command --> Scheduler: Command completed

@enduml
```

---

## 7. Sequence Diagram: Login dan Autentikasi

```plantuml
@startuml Sequence Diagram - Login

actor User
participant "Login Page\n(Blade)" as Login
participant "LoginRequest\nController" as LoginReq
participant "Auth\nFacade" as Auth
participant "User\nModel" as UserModel
participant "Session" as Session
database Database as DB

== Akses Halaman Login ==
User -> Login: GET /login
Login --> User: Tampilkan form login

== Submit Login ==
User -> Login: POST (email, password)
Login -> LoginReq: authenticate()

LoginReq -> LoginReq: validate()
note right
  Validasi:
  - email: required, email
  - password: required
end note

alt Validasi Gagal
    LoginReq --> Login: ValidationException
    Login --> User: Tampilkan error validasi
end

LoginReq -> Auth: attempt(['email' => $email, 'password' => $password])

Auth -> UserModel: where('email', email)->first()
UserModel -> DB: SELECT * FROM users WHERE email = ?
DB --> UserModel: user data

alt User Tidak Ditemukan
    Auth --> LoginReq: false
    LoginReq -> LoginReq: RateLimiter::hit()
    LoginReq --> Login: throw ValidationException
    Login --> User: Email atau password salah
end

Auth -> Auth: Hash::check(password, user.password)

alt Password Salah
    Auth --> LoginReq: false
    LoginReq -> LoginReq: RateLimiter::hit()
    LoginReq --> Login: throw ValidationException
    Login --> User: Email atau password salah
end

Auth -> Session: regenerate()
Session --> Auth: new session id

Auth -> Session: store(user_id)
Auth --> LoginReq: true

LoginReq -> LoginReq: RateLimiter::clear()

== Redirect ke Dashboard ==
LoginReq --> Login: redirect('/dashboard')
Login -> User: GET /dashboard

note right of User
  Middleware 'auth' memeriksa session
  Middleware 'permission:*' memeriksa
  roles/permissions untuk akses menu
end note

@enduml
```

---

## 8. Sequence Diagram: Mencetak Struk

```plantuml
@startuml Sequence Diagram - Cetak Struk

actor Kasir
participant "Receipt Page\n(Livewire)" as Receipt
participant "Transaction\nModel" as Transaction
participant "TransactionDetail\nModel" as TxDetail
participant "Payment\nModel" as Payment
participant "StoreProfile\nModel" as Store
participant "PdfController" as PDF
participant "NotificationService" as Notif
database Database as DB

== Buka Halaman Cetak Struk ==
Kasir -> Receipt: GET /cetak-struk/{id}

Receipt -> Transaction: find(id)->with(['details.product', 'payments', 'customer'])
Transaction -> DB: SELECT with relationships
DB --> Transaction: transaction data

Receipt -> Store: first()
Store -> DB: SELECT * FROM store_profiles LIMIT 1
DB --> Store: store profile
Store --> Receipt: store data

Receipt --> Kasir: Tampilkan preview struk

== Generate PDF ==
Kasir -> PDF: GET /transaksi/cetak/{id}

PDF -> Transaction: find(id)->with(...)
Transaction --> PDF: transaction + details

PDF -> Store: first()
Store --> PDF: store profile

PDF -> PDF: Generate PDF dengan DomPDF
note right
  Data struk:
  - Logo & nama toko
  - Alamat toko
  - No. Invoice
  - Tanggal
  - Item pesanan
  - Total
  - Metode pembayaran
  - Kasir
end note

PDF --> Kasir: Download/tampilkan PDF

== Kirim Notifikasi (opsional) ==
Kasir -> Receipt: Klik "Tandai Sudah Dicetak"
Receipt -> Notif: receiptPrinted(receipt_number, invoice_number)
Notif -> DB: INSERT INTO notifications

Receipt --> Kasir: Struk berhasil dicetak

@enduml
```

---

## 9. Sequence Diagram: Mengelola Peran dan Hak Akses

```plantuml
@startuml Sequence Diagram - Kelola Peran

actor Pemilik as User
participant "Peran Page\n(Livewire)" as RolePage
participant "SpatieRole\nModel" as Role
participant "Permission\nModel" as Permission
participant "User\nModel" as UserModel
database Database as DB

== Melihat Daftar Peran ==
User -> RolePage: GET /peran
RolePage -> Role: with('permissions')->get()
Role -> DB: SELECT * FROM roles\nJOIN role_has_permissions
DB --> Role: roles with permissions
Role --> RolePage: roles list
RolePage --> User: Tampilkan daftar peran

== Membuat Peran Baru ==
User -> RolePage: Klik "Tambah Peran"
RolePage -> Permission: all()
Permission -> DB: SELECT * FROM permissions
DB --> Permission: permissions list
Permission --> RolePage: permissions grouped by category
RolePage --> User: Tampilkan form dengan checkbox permissions

User -> RolePage: Input nama peran
User -> RolePage: Pilih permissions
User -> RolePage: Submit

RolePage -> Role: create(['name' => name, 'guard_name' => 'web'])
Role -> DB: INSERT INTO roles
DB --> Role: role created

RolePage -> Role: syncPermissions(selected_permissions)
Role -> DB: INSERT INTO role_has_permissions
DB --> Role: permissions synced

RolePage --> User: Peran berhasil dibuat

== Mengubah Peran ==
User -> RolePage: Pilih peran untuk edit
RolePage -> Role: find(id)->with('permissions')
Role --> RolePage: role data

User -> RolePage: Update nama/permissions
User -> RolePage: Submit

RolePage -> Role: update(['name' => name])
Role -> DB: UPDATE roles
DB --> Role: updated

RolePage -> Role: syncPermissions(new_permissions)
Role -> DB: DELETE FROM role_has_permissions WHERE role_id = ?
Role -> DB: INSERT INTO role_has_permissions
DB --> Role: permissions synced

RolePage --> User: Peran berhasil diperbarui

== Menghapus Peran ==
User -> RolePage: Pilih peran untuk hapus
RolePage -> UserModel: whereHas('roles', role_id)->count()
UserModel -> DB: SELECT COUNT(*) FROM model_has_roles
DB --> UserModel: user count

alt Ada user yang menggunakan peran
    RolePage --> User: Tidak dapat dihapus\n(masih digunakan)
else Tidak ada user
    RolePage -> Role: delete()
    Role -> DB: DELETE FROM roles WHERE id = ?
    Role -> DB: DELETE FROM role_has_permissions WHERE role_id = ?
    DB --> Role: deleted

    RolePage --> User: Peran berhasil dihapus
end

@enduml
```
