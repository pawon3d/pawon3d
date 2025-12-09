# SEQUENCE DIAGRAM

## Sistem Informasi Manajemen Toko Kue (revisi)

Di bawah ini urutan interaksi utama yang mencerminkan state aplikasi saat ini.

| No  | Diagram                      | Modul             | Aktor     |
| --- | ---------------------------- | ----------------- | --------- |
| 1   | Login & Otorisasi            | Autentikasi       | Pekerja   |
| 2   | Aktivasi Akun                | Autentikasi       | Pekerja   |
| 3   | Transaksi Siap Beli          | Kasir             | Kasir     |
| 4   | Transaksi Pesanan (DP/Lunas) | Kasir -> Produksi | Kasir     |
| 5   | Shift Kasir (Buka/Tutup)     | Kasir             | Kasir     |
| 6   | Produksi (Pesanan/Siap Beli) | Produksi          | Produksi  |
| 7   | Belanja Bahan Baku           | Inventori         | Inventori |
| 8   | Hitung Stok / Rusak / Hilang | Inventori         | Inventori |
| 9   | Refund Transaksi             | Kasir             | Kasir     |

---

## 1) Login & Otorisasi

```plantuml
@startuml Sequence - Login
actor User
participant "Login Page\n(Livewire)" as Login
participant "Auth Facade" as Auth
participant "User Model" as UserModel
participant Session
database DB

User -> Login: GET /login
Login --> User: Form login

User -> Login: POST email, password
Login -> Auth: attempt(credentials)
Auth -> UserModel: where(email)
UserModel -> DB: SELECT * FROM users WHERE email=?
DB --> UserModel: user|null

alt User not found
    Auth --> Login: false
    Login --> User: Error kredensial
else Found
    Auth -> Auth: Hash::check()
    alt Password salah
        Auth --> Login: false
        Login --> User: Error kredensial
    else Password benar
        Auth -> UserModel: check is_active
        alt Tidak aktif
            Auth --> Login: false
            Login --> User: Akun tidak aktif
        else Aktif
            Auth -> Session: regenerate()
            Auth -> Session: store(user_id)
            Auth --> Login: true
            Login -> UserModel: hasAnyPermission()
            UserModel -> DB: SELECT permissions
            alt Ada permission
                Login --> User: Redirect dashboard (ringkasan sesuai role)
            else Tidak ada
                Login --> User: Redirect menunggu-peran
            end
        end
    end
end
@enduml
```

---

## 2) Aktivasi Akun

```plantuml
@startuml Sequence - Aktivasi Akun
actor Pekerja
participant "Aktivasi Page\n(Livewire)" as ActivatePage
participant "User Model" as UserModel
participant "Auth Facade" as Auth
participant Session
database DB

Pekerja -> ActivatePage: GET /aktivasi-akun/{token}
ActivatePage -> UserModel: where('invitation_token', token)->first()
UserModel -> DB: SELECT * FROM users WHERE invitation_token=?
DB --> UserModel: user|null

alt Token tidak valid/expired
    UserModel --> ActivatePage: null
    ActivatePage --> Pekerja: Error token tidak valid/expired
else Token valid
    UserModel --> ActivatePage: user data
    ActivatePage --> Pekerja: Form set password

    Pekerja -> ActivatePage: POST password, password_confirmation
    ActivatePage -> ActivatePage: validate(password rules)

    alt Validasi gagal
        ActivatePage --> Pekerja: Error validasi
    else Validasi berhasil
        ActivatePage -> UserModel: update(is_active=true, password=hash, activated_at=now)
        UserModel -> DB: UPDATE users SET is_active=1, password=?, activated_at=?
        DB --> UserModel: success

        ActivatePage -> UserModel: update(invitation_token=null)
        UserModel -> DB: UPDATE users SET invitation_token=NULL

        ActivatePage -> Auth: login(user)
        Auth -> Session: regenerate()
        Auth -> Session: store(user_id)

        ActivatePage -> UserModel: hasAnyPermission()
        UserModel -> DB: SELECT permissions

        alt Ada permission
            ActivatePage --> Pekerja: Redirect dashboard (ringkasan sesuai role)
        else Tidak ada permission
            ActivatePage --> Pekerja: Redirect menunggu-peran
        end
    end
end
@enduml
```

---

## 3) Transaksi Siap Beli

```plantuml
@startuml Sequence - Siap Beli
actor Kasir
participant "POS Page\n(Livewire)" as POS
participant Product
participant Customer
participant Transaction
participant TxDetail
participant Payment
participant PointsHistory as Points
participant Notification as Notif
database DB

== Pilih Produk ==
Kasir -> POS: Buka POS (siap beli)
POS -> Product: query is_ready = true & active
Product -> DB: SELECT products
DB --> Product: data produk
Product --> POS: produk siap jual
Kasir -> POS: addToCart(product, qty)
POS -> POS: hitung subtotal

== Pelanggan (opsional) ==
Kasir -> POS: input phone/name
POS -> Customer: firstOrCreate(phone)
Customer -> DB: SELECT/INSERT customers
DB --> Customer: customer
Customer --> POS: customer_id

== Pembayaran ==
Kasir -> POS: pilih metode & channel
Kasir -> POS: isi nominal bayar
POS -> POS: hitung total & kembalian

== Simpan Transaksi ==
POS -> Transaction: create(invoice_number, customer_id, user_id, method="siap-beli", status="Selesai", payment_status="Lunas", total_amount)
Transaction -> DB: INSERT transactions
DB --> Transaction: transaksi
POS -> TxDetail: createMany(cart)
TxDetail -> DB: INSERT transaction_details

loop setiap item
    POS -> Product: decrement(stock, qty)
    Product -> DB: UPDATE products
end

POS -> Payment: create(transaction_id, payment_method, payment_channel_id, paid_amount, receipt_number)
Payment -> DB: INSERT payments

alt Pelanggan terdaftar
    POS -> Points: create(phone, action="earn", points=floor(total/10000), transaction_id)
    Points -> DB: INSERT points_histories
    POS -> Customer: increment(points)
    Customer -> DB: UPDATE customers
end

POS -> Notif: orderCompleted(invoice_number)
Notif -> DB: INSERT notifications
POS --> Kasir: Tampilkan konfirmasi / cetak struk
@enduml
```

---

## 4) Transaksi Pesanan (DP/Lunas)

```plantuml
@startuml Sequence - Pesanan
actor Kasir
participant "Transaksi Page\n(Livewire)" as Page
participant Customer
participant Transaction
participant TxDetail
participant Production
participant ProdDetail
participant Payment
participant Notification as Notif
database DB

Kasir -> Page: pilih metode (pesanan-reguler/kotak)
Kasir -> Page: pilih produk, qty, jadwal ambil
Kasir -> Page: input pelanggan (phone)
Page -> Customer: firstOrCreate(phone)
Customer -> DB: SELECT/INSERT customers
DB --> Customer: customer

Kasir -> Page: pilih DP atau Lunas
Page -> Transaction: create(invoice_number, customer_id, method, status="Antrian", payment_status=DP/Lunas, schedule, total_amount)
Transaction -> DB: INSERT transactions
DB --> Transaction: transaksi

Page -> TxDetail: createMany(items)
TxDetail -> DB: INSERT transaction_details

== Produksi otomatis ==
Page -> Production: create(production_number, transaction_id, method, status="Antrian")
Production -> DB: INSERT productions
DB --> Production: production
Page -> ProdDetail: createMany(items)
ProdDetail -> DB: INSERT production_details

== Pembayaran ==
alt DP/Lunas dibayar sekarang
    Page -> Payment: create(transaction_id, payment_channel_id?, payment_method, paid_amount)
    Payment -> DB: INSERT payments
end

Page -> Notif: orderQueued(invoice_number)
Notif -> DB: INSERT notifications (kasir & produksi)
Page --> Kasir: Pesanan tersimpan
@enduml
```

---

## 5) Shift Kasir (Buka/Tutup)

```plantuml
@startuml Sequence - Shift Kasir
actor Kasir
participant "POS Page" as POS
participant Shift
participant Transaction
participant Payment
participant Notification as Notif
database DB

Kasir -> POS: buka POS
POS -> Shift: get active shift
Shift -> DB: SELECT latest open
DB --> Shift: shift|null

alt Tidak ada shift
    Kasir -> POS: isi kas awal
    POS -> Shift: create(shift_number, opened_by, initial_cash, status="Buka")
    Shift -> DB: INSERT shifts
    POS -> Notif: shiftOpened(shift_number)
    Notif -> DB: INSERT notifications
else Ada shift
    POS --> Kasir: gunakan shift aktif
end

loop transaksi selama shift
    Kasir -> POS: proses transaksi
    POS -> Transaction: create(..., created_by_shift)
    Transaction -> DB: INSERT transactions
    POS -> Payment: create(...)
    Payment -> DB: INSERT payments
end

Kasir -> POS: tutup sesi; input kas akhir
POS -> Shift: update(status="Tutup", final_cash, total_sales, total_refunds)
Shift -> DB: UPDATE shifts
POS -> Notif: shiftClosed(shift_number)
Notif -> DB: INSERT notifications
@enduml
```

---

## 6) Produksi (Pesanan / Siap Beli)

```plantuml
@startuml Sequence - Produksi
actor Produksi
participant "Antrian Produksi\n(Livewire)" as Queue
participant Production
participant ProdDetail
participant ProductComposition as Comp
participant MaterialBatch as Batch
participant InventoryLog as Log
participant Product
participant Transaction
participant Notification as Notif
database DB

Produksi -> Queue: buka antrian
Queue -> Production: where(status="Antrian")
Production -> DB: SELECT productions
DB --> Production: list
Queue --> Produksi: tampilkan

Produksi -> Queue: pilih produksi
Queue -> Production: load with details & transaction
Production -> DB: SELECT with relations
DB --> Production: data

Queue -> Comp: ambil komposisi per produk
Comp -> DB: SELECT product_compositions
DB --> Comp: komposisi
Queue -> Batch: sum quantity per material
Batch -> DB: SUM batch_quantity
DB --> Batch: stok
alt Stok kurang
    Queue --> Produksi: gagal mulai (stok kurang)
else Cukup
    Produksi -> Queue: pilih pekerja, mulai
    Queue -> Production: update(status="Proses", is_start=true)
    Production -> DB: UPDATE productions

    loop setiap bahan
        Queue -> Batch: pakai FIFO (expired asc)
        Batch -> DB: UPDATE/DELETE quantities
        Queue -> Log: create(action="produksi", type="out", material_id, quantity_change)
        Log -> DB: INSERT inventory_logs
    end

    Produksi -> Queue: input qty jadi/gagal
    Queue -> ProdDetail: update quantities
    ProdDetail -> DB: UPDATE production_details

    Queue -> Product: increment(stock, qty_get)
    Product -> DB: UPDATE products

    alt terkait transaksi
        Queue -> Transaction: update(status="Dapat Diambil")
        Transaction -> DB: UPDATE transactions
    end

    Queue -> Production: update(status="Selesai", is_finish=true)
    Production -> DB: UPDATE productions
    Queue -> Notif: productionCompleted(production_number)
    Notif -> DB: INSERT notifications
end
@enduml
```

---

## 7) Belanja Bahan Baku

```plantuml
@startuml Sequence - Belanja
actor Inventori
participant "Belanja Page\n(Livewire)" as Exp
participant Expense
participant ExpenseDetail as Detail
participant MaterialBatch as Batch
participant InventoryLog as Log
participant Notification as Notif
database DB

Inventori -> Exp: buat rencana belanja
Exp -> Expense: create(expense_number, supplier_id, status="Rencana", grand_total_expect)
Expense -> DB: INSERT expenses
Exp -> Detail: createMany(quantity_expect, price_expect, unit_id, material_id)
Detail -> DB: INSERT expense_details
Exp -> Notif: expensePlanned
Notif -> DB: INSERT notifications

Inventori -> Exp: mulai belanja
Exp -> Expense: update(status="Proses", is_start=true)
Expense -> DB: UPDATE expenses

Inventori -> Exp: isi qty/price aktual, expiry per detail
Exp -> Detail: update(quantity_get, price_get, expiry_date)
Detail -> DB: UPDATE expense_details

Inventori -> Exp: selesaikan
loop tiap detail
    Exp -> Batch: create(material_id, unit_id, batch_number, batch_quantity=quantity_get, expired_at=expiry_date)
    Batch -> DB: INSERT material_batches
    Exp -> Log: create(action="belanja", type="in", material_id, material_batch_id, quantity_change=quantity_get)
    Log -> DB: INSERT inventory_logs
end
Exp -> Expense: update(status="Selesai", is_finish=true, grand_total_actual)
Expense -> DB: UPDATE expenses
Exp -> Notif: expenseCompleted
Notif -> DB: INSERT notifications
@enduml
```

---

## 8) Hitung Stok / Rusak / Hilang

```plantuml
@startuml Sequence - Hitung Stok
actor Inventori
participant "Hitung Page\n(Livewire)" as Count
participant Hitung
participant HitungDetail as Detail
participant MaterialBatch as Batch
participant InventoryLog as Log
participant Notification as Notif
database DB

Inventori -> Count: buat rencana hitung (aksi hitung/rusak/hilang)
Count -> Hitung: create(hitung_number, action, status="Rencana")
Hitung -> DB: INSERT hitungs
Count -> Detail: createMany(material_id, material_batch_id, quantity_expect)
Detail -> DB: INSERT hitung_details

Inventori -> Count: mulai aksi
Count -> Hitung: update(status="Proses", is_start=true)
Hitung -> DB: UPDATE hitungs

Inventori -> Count: input qty aktual/rusak/hilang per detail
Count -> Detail: update(quantity_actual, loss_total?)
Detail -> DB: UPDATE hitung_details

loop tiap detail
    Count -> Batch: adjust quantity (in/out sesuai aksi)
    Batch -> DB: UPDATE material_batches
    Count -> Log: create(action=action, type=if action=="hitung" then "in/out" else "out", material_id, material_batch_id, quantity_change)
    Log -> DB: INSERT inventory_logs
end

Count -> Hitung: update(status="Selesai", is_finish=true, grand_total, loss_grand_total)
Hitung -> DB: UPDATE hitungs
Count -> Notif: stockCountCompleted
Notif -> DB: INSERT notifications
@enduml
```

---

## 9) Refund Transaksi

```plantuml
@startuml Sequence - Refund
actor Kasir
participant "Refund Page\n(Livewire)" as Ref
participant Transaction
participant Payment
participant Refund
participant PaymentChannel as Channel
participant Shift
participant Notification as Notif
database DB

Kasir -> Ref: pilih transaksi
Ref -> Transaction: load with payments
Transaction -> DB: SELECT
DB --> Transaction: data

Kasir -> Ref: isi alasan, metode refund (tunai/transfer/QR)
alt Transfer/QR
    Ref -> Channel: list active
    Channel -> DB: SELECT payment_channels WHERE is_active=1
    DB --> Channel: channels
    Ref -> Refund: set payment_channel_id, account_number
end

Ref -> Refund: create(transaction_id, refund_method, payment_channel_id?, account_number?, total_amount)
Refund -> DB: INSERT refunds

Ref -> Transaction: update(status="Dibatalkan", total_refund, refund_by_shift)
Transaction -> DB: UPDATE transactions

Ref -> Shift: link refund_by_shift (if session open)
Shift -> DB: SELECT/UPDATE

Ref -> Notif: refundCreated(invoice_number)
Notif -> DB: INSERT notifications
Ref --> Kasir: Refund tersimpan
@enduml
```
