# ACTIVITY DIAGRAM

## Sistem Informasi Manajemen Toko Kue (revisi Des 2025)

Ringkasan alur bisnis utama berdasarkan state aplikasi terbaru. Setiap diagram memakai PlantUML.

| No  | Nama Diagram                 | Modul             | Aktor     |
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
@startuml Activity - Login
|User|
start
:Isi email & password;
:Klik Login;
|System|
:Validasi format;
if (Valid?) then (ya)
    :Cari user by email;
    if (Ada user?) then (ya)
        :Verifikasi password;
        if (Benar?) then (ya)
            if (is_active?) then (ya)
                :Regenerate session;
                :Ambil roles & permissions;
                if (Punya permission?) then (ya)
                    :Redirect dashboard sesuai role;
                else (tidak)
                    :Redirect ke "menunggu peran";
                endif
            else (tidak)
                :Tampilkan error akun tidak aktif;
            endif
        else (tidak)
            :Error kredensial;
        endif
    else (tidak)
        :Error user tidak ditemukan;
    endif
else (tidak)
    :Error validasi;
endif
stop
@enduml
```

---

## 2) Aktivasi Akun

```plantuml
@startuml Activity - Aktivasi Akun
|Pekerja|
start
:Klik tautan aktivasi;
|System|
:Validasi token (exist, not expired, not used);
if (Valid?) then (ya)
    :Tampilkan form password;
    |Pekerja|
    :Isi password & konfirmasi;
    :Submit;
    |System|
    :Validasi aturan password;
    if (Lolos?) then (ya)
        :Set is_active=true;
        :Set activated_at now;
        :Hapus token;
        :Auto login;
        :Redirect dashboard;
    else (tidak)
        :Tampilkan error;
    endif
else (tidak)
    :Tampilkan error token;
endif
stop
@enduml
```

---

## 3) Transaksi Siap Beli

```plantuml
@startuml Activity - Transaksi Siap Beli
|Kasir|
start
:Buka POS (siap beli);
:Pilih produk siap jual;
|System|
:Cek stok tersedia;
if (Cukup?) then (ya)
    |Kasir|
    :Atur qty;
    :Input pelanggan (opsional);
    :Pilih metode bayar & channel;
    :Isi nominal bayar;
    |System|
    :Hitung total & kembalian;
    :Simpan transaksi (status Selesai);
    :Simpan pembayaran & receipt_number;
    :Kurangi stok produk;
    :Tambah poin pelanggan (jika ada);
    :Kirim notifikasi;
    |Kasir|
    :Cetak struk (opsional);
else (tidak)
    |System|
    :Tampilkan stok tidak cukup;
endif
stop
@enduml
```

---

## 4) Transaksi Pesanan (DP/Lunas)

```plantuml
@startuml Activity - Transaksi Pesanan
|Kasir|
start
:Buka POS (pesanan reguler/kotak);
:Pilih produk & qty;
:Set jadwal ambil;
:Input pelanggan (firstOrCreate by phone);
:Pilih bayar DP atau Lunas;
|System|
if (DP?) then (ya)
    :Simpan transaksi status Antrian, payment_status DP;
else (Lunas)
    :Simpan transaksi status Antrian, payment_status Lunas;
endif
:Simpan detail transaksi;
:Buat produksi (method mengikuti pesanan);
:Simpan pembayaran (jika ada);
:Kirim notifikasi ke produksi & kasir;
|Kasir|
:Selesai;
stop
@enduml
```

---

## 5) Shift Kasir (Buka/Tutup)

```plantuml
@startuml Activity - Shift Kasir
|Kasir|
start
:Buka halaman transaksi;
|System|
if (Ada shift aktif?) then (ya)
    |Kasir|
    :Lanjut transaksi;
else (tidak)
    |Kasir|
    :Klik "Buka Sesi"; :Input kas awal;
    |System|
    :Generate shift_number; :Set status Buka;
endif
repeat
    |Kasir| :Proses transaksi POS;
    |System| :Catat transaksi ke shift;
repeat while (Shift belum ditutup?)
|Kasir|
:Klik "Tutup Sesi"; :Input kas akhir;
|System|
:Hitung total_sales/refunds;
:Update status Tutup;
:Kirim notifikasi shift closed;
stop
@enduml
```

---

## 6) Produksi (Pesanan / Siap Beli)

```plantuml
@startuml Activity - Produksi
|Produksi|
start
:Buka antrian produksi;
|System|
:Tampilkan produksi status Antrian;
|Produksi|
:Pilih produksi;
|System|
:Muat detail + transaksi terkait;
:Ambil komposisi produk;
:Cek stok bahan (batch FIFO);
if (Stok cukup?) then (ya)
    |Produksi|
    :Tunjuk pekerja; :Mulai produksi;
    |System|
    :Set status Proses; :Kurangi batch (FIFO);
    :Catat inventory log (out produksi);
    |Produksi|
    :Input qty jadi & gagal;
    |System|
    :Update detail produksi;
    :Tambah stok produk jadi;
    :Update status produksi Selesai;
    :Jika terkait transaksi -> set status Dapat Diambil;
    :Kirim notifikasi selesai;
else (tidak)
    |System|
    :Tolak mulai (stok kurang);
endif
stop
@enduml
```

---

## 7) Belanja Bahan Baku

```plantuml
@startuml Activity - Belanja
|Inventori|
start
:Buat rencana belanja;
|System|
:Simpan expense status Rencana + detail;
|Inventori|
:Mulai belanja;
|System|
:Set status Proses;
|Inventori|
:Input qty/price aktual + expiry;
|System|
:Update detail;
|Inventori|
:Selesaikan belanja;
|System|
:Buat batch per detail; :Catat inventory log (in belanja);
:Set status Selesai; :Hitung grand_total_actual;
:Kirim notifikasi selesai;
stop
@enduml
```

---

## 8) Hitung Stok / Rusak / Hilang

```plantuml
@startuml Activity - Hitung Stok
|Inventori|
start
:Buat rencana hitung/rusak/hilang;
|System|
:Simpan hitung + detail (expect);
|Inventori|
:Mulai aksi;
|System|
:Set status Proses;
|Inventori|
:Input qty aktual / rusak / hilang per detail;
|System|
:Update detail;
:Sesuaikan batch (in/out sesuai aksi);
:Catat inventory log;
:Set status Selesai + grand_total/loss_total;
:Kirim notifikasi selesai;
stop
@enduml
```

---

## 9) Refund Transaksi

```plantuml
@startuml Activity - Refund
|Kasir|
start
:Pilih transaksi;
:Input alasan + metode refund;
|System|
:Muat payments & channel aktif (jika non-tunai);
:Buat record refund; :Update transaksi status Dibatalkan + total_refund;
:Kaitkan refund_by_shift (jika shift aktif);
:Kirim notifikasi refund;
|Kasir|
:Selesai;
stop
Ya)
        :Buat notifikasi alert expired;
        :Kirim ke user dengan permission inventori;
    else (Tidak)
    endif

end fork

:Log hasil pengecekan;
stop

@enduml
```
