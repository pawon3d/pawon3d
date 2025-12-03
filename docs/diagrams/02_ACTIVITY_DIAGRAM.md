# ACTIVITY DIAGRAM

## Sistem Informasi Manajemen Toko Kue

---

## 1. Activity Diagram: Proses Transaksi Penjualan

```plantuml
@startuml Activity Diagram - Proses Transaksi Penjualan

|Kasir|
start
:Membuka halaman POS;
:Memilih jenis pesanan;

if (Jenis pesanan?) then (Siap Beli)
    :Memilih produk dari katalog;
    :Menambahkan ke keranjang;

    if (Stok tersedia?) then (Ya)
        :Mengatur jumlah pesanan;
    else (Tidak)
        :Menampilkan peringatan stok habis;
        stop
    endif

else (Pesanan Reguler/Kotak)
    :Memilih produk dari katalog;
    :Menambahkan ke keranjang;
    :Mengatur jumlah pesanan;
    :Memasukkan jadwal pengambilan;
endif

:Memasukkan data pelanggan (opsional);

if (Pelanggan terdaftar?) then (Ya)
    :Mengambil data pelanggan;
    :Menampilkan poin tersedia;

    if (Gunakan poin?) then (Ya)
        :Menghitung diskon poin;
    else (Tidak)
    endif
else (Tidak)
    :Melanjutkan tanpa data pelanggan;
endif

:Menghitung total pesanan;
:Memilih metode pembayaran;

if (Jenis pembayaran?) then (Lunas)
    :Memasukkan jumlah bayar;
    :Menghitung kembalian;
    :Menyimpan transaksi (status: Lunas);

    if (Pelanggan terdaftar?) then (Ya)
        :Menambah poin pelanggan;
        note right
          1 poin = Rp 10.000
        end note
    else (Tidak)
    endif

else (Uang Muka)
    :Memasukkan jumlah DP;
    :Menyimpan transaksi (status: DP);
endif

|Sistem|
:Menyimpan data transaksi;
:Menyimpan detail transaksi;

if (Jenis pesanan?) then (Siap Beli)
    :Mengurangi stok produk;
    :Update status: Selesai;
else (Pesanan Reguler/Kotak)
    :Membuat record produksi;
    :Update status: Antrian;
    :Kirim notifikasi ke Produksi;
endif

:Kirim notifikasi ke Kasir;

|Kasir|
if (Cetak struk?) then (Ya)
    :Mencetak struk transaksi;
else (Tidak)
endif

stop

@enduml
```

---

## 2. Activity Diagram: Proses Produksi

```plantuml
@startuml Activity Diagram - Proses Produksi

|Produksi|
start
:Membuka halaman Antrian Produksi;
:Melihat daftar produksi;

if (Ada produksi menunggu?) then (Ya)
    :Memilih produksi yang akan dimulai;
    :Memeriksa detail produksi;

    |Sistem|
    :Memeriksa ketersediaan bahan baku;

    if (Bahan baku cukup?) then (Ya)
        :Menampilkan konfirmasi mulai;

        |Produksi|
        :Memilih pekerja yang terlibat;
        :Mengkonfirmasi mulai produksi;

        |Sistem|
        :Mengurangi stok bahan baku;
        note right
          Pengurangan berdasarkan
          komposisi produk
        end note
        :Mencatat log inventori;
        :Update status: Proses;
        :Kirim notifikasi (Produksi dimulai);

        |Produksi|
        :Melakukan proses produksi;

        if (Produksi selesai?) then (Ya)
            :Menandai produksi selesai;

            |Sistem|
            :Menambah stok produk jadi;
            :Update status: Selesai;
            :Kirim notifikasi (Produksi selesai);

            if (Dari pesanan?) then (Ya)
                :Update status transaksi: Dapat Diambil;
                :Kirim notifikasi ke Kasir;
            else (Siap Beli)
            endif

        else (Dibatalkan)
            |Produksi|
            :Membatalkan produksi;

            |Sistem|
            :Mengembalikan stok bahan baku;
            :Update status: Dibatalkan;
            :Kirim notifikasi (Produksi dibatalkan);
        endif

    else (Tidak)
        |Sistem|
        :Menampilkan peringatan bahan tidak cukup;
        :Menampilkan daftar bahan yang kurang;

        |Produksi|
        :Menunda produksi;
    endif

else (Tidak)
    :Tidak ada aksi;
endif

stop

@enduml
```

---

## 3. Activity Diagram: Proses Belanja Bahan Baku

```plantuml
@startuml Activity Diagram - Proses Belanja Bahan Baku

|Inventori|
start
:Membuka halaman Belanja;

fork
    :Melihat daftar belanja;
fork again
    :Membuat rencana belanja baru;
end fork

if (Membuat rencana baru?) then (Ya)
    :Memilih supplier;
    :Menambahkan bahan baku;
    :Memasukkan jumlah rencana;
    :Memasukkan harga perkiraan;
    :Menyimpan rencana belanja;

    |Sistem|
    :Generate nomor belanja (BP-YYMMDD-XXXX);
    :Menyimpan expense dengan status: Rencana;
    :Menyimpan expense details;
    :Kirim notifikasi (Rencana belanja dibuat);

else (Tidak)
endif

|Inventori|
:Memilih belanja untuk diproses;

if (Mulai belanja?) then (Ya)
    :Mengkonfirmasi mulai belanja;

    |Sistem|
    :Update status: Proses;
    :Kirim notifikasi (Belanja dimulai);

    |Inventori|
    :Melakukan pembelian;
    :Mengisi jumlah aktual;
    :Mengisi harga aktual;
    :Mengisi tanggal expired (jika ada);

    if (Jumlah sesuai rencana?) then (Ya)
        :Melanjutkan;
    else (Tidak)
        :Mencatat selisih;
    endif

    :Menyelesaikan belanja;

    |Sistem|
    :Membuat batch baru untuk setiap bahan;
    note right
      Batch mencatat:
      - Quantity
      - Expired date
      - Harga beli
    end note
    :Menambah stok bahan baku;
    :Menghitung grand total aktual;
    :Update status: Selesai;
    :Mencatat log inventori;
    :Kirim notifikasi (Belanja selesai);

else (Dibatalkan)
    |Inventori|
    :Membatalkan belanja;

    |Sistem|
    :Update status: Dibatalkan;
    :Kirim notifikasi (Belanja dibatalkan);
endif

stop

@enduml
```

---

## 4. Activity Diagram: Proses Hitung Stok

```plantuml
@startuml Activity Diagram - Proses Hitung Stok

|Inventori|
start
:Membuka halaman Hitung;
:Membuat rencana hitung baru;

:Memilih jenis aksi;
note right
  Jenis aksi:
  - Hitung (stock opname)
  - Rusak (catat rusak)
  - Hilang (catat hilang)
end note

if (Jenis aksi?) then (Hitung)
    :Memilih bahan yang akan dihitung;
    :Menyimpan rencana hitung;

    |Sistem|
    :Generate nomor hitung (HC-YYMMDD-XXXX);
    :Menyimpan hitung dengan status: Rencana;
    :Menyimpan hitung details;

    |Inventori|
    :Memulai penghitungan;

    |Sistem|
    :Update status: Proses;

    |Inventori|
    :Memasukkan jumlah aktual hasil hitung;

    |Sistem|
    :Menghitung selisih (sistem vs aktual);

    if (Ada selisih?) then (Ya)
        :Mencatat adjustment;
        :Update stok bahan baku;
        :Mencatat log inventori (adjustment);
    else (Tidak)
        :Tidak ada perubahan stok;
    endif

elseif (Jenis aksi?) then (Rusak)
    :Memilih bahan yang rusak;
    :Memasukkan jumlah rusak;
    :Menyimpan rencana;

    |Sistem|
    :Generate nomor hitung;
    :Menyimpan dengan status: Rencana;

    |Inventori|
    :Memulai proses catat rusak;
    :Mengkonfirmasi jumlah rusak;

    |Sistem|
    :Mengurangi stok bahan baku;
    :Mencatat log inventori (rusak);

else (Hilang)
    :Memilih bahan yang hilang;
    :Memasukkan jumlah hilang;
    :Menyimpan rencana;

    |Sistem|
    :Generate nomor hitung;
    :Menyimpan dengan status: Rencana;

    |Inventori|
    :Memulai proses catat hilang;
    :Mengkonfirmasi jumlah hilang;

    |Sistem|
    :Mengurangi stok bahan baku;
    :Mencatat log inventori (hilang);
endif

|Sistem|
:Update status hitung: Selesai;
:Update status bahan baku (jika perlu);
note right
  Status bahan:
  - Tersedia
  - Hampir Habis
  - Kosong
  - Expired
end note
:Kirim notifikasi (Hitung selesai);

stop

@enduml
```

---

## 5. Activity Diagram: Proses Notifikasi Otomatis (Alert Stok & Expired)

```plantuml
@startuml Activity Diagram - Notifikasi Otomatis

|Sistem (Scheduler)|
start
:Menjalankan scheduled command;
note right
  Dijalankan setiap hari
  pukul 08:00
end note

fork
    :Mengecek stok rendah;

    :Query bahan aktif;
    while (Untuk setiap bahan) is (Ada)
        :Hitung total stok dari batches;

        if (Stok <= Minimum?) then (Ya)
            if (Stok = 0?) then (Ya)
                :Update status: Kosong;
            else (Tidak)
                :Update status: Hampir Habis;
            endif
            :Catat untuk notifikasi;
        else (Tidak)
            :Update status: Tersedia;
        endif
    endwhile (Selesai)

    if (Ada bahan stok rendah?) then (Ya)
        :Buat notifikasi alert stok rendah;
        :Kirim ke user dengan permission inventori;
    else (Tidak)
    endif

fork again
    :Mengecek bahan expired;

    :Query batches aktif;
    while (Untuk setiap batch) is (Ada)
        if (Expired date <= Hari ini?) then (Ya)
            :Update status bahan: Expired;
            :Catat untuk notifikasi;
        elseif (Expired date <= 7 hari lagi?) then (Ya)
            :Catat untuk notifikasi (akan expired);
        else (Tidak)
        endif
    endwhile (Selesai)

    if (Ada bahan expired/akan expired?) then (Ya)
        :Buat notifikasi alert expired;
        :Kirim ke user dengan permission inventori;
    else (Tidak)
    endif

end fork

:Log hasil pengecekan;
stop

@enduml
```

---

## 6. Activity Diagram: Proses Login dan Otorisasi

```plantuml
@startuml Activity Diagram - Login dan Otorisasi

|User|
start
:Membuka halaman login;
:Memasukkan email;
:Memasukkan password;
:Klik tombol Login;

|Sistem|
:Validasi format input;

if (Format valid?) then (Ya)
    :Mencari user berdasarkan email;

    if (User ditemukan?) then (Ya)
        :Verifikasi password;

        if (Password benar?) then (Ya)
            :Membuat session;
            :Mengambil roles dan permissions;
            :Redirect ke Dashboard;

            |User|
            :Melihat Dashboard;
            :Melihat menu sesuai permission;
            note right
              Menu yang tampil berdasarkan:
              - kasir.* → Menu Kasir
              - produksi.* → Menu Produksi
              - inventori.* → Menu Inventori
              - manajemen.* → Menu Manajemen
            end note

        else (Tidak)
            :Menampilkan error "Password salah";
        endif

    else (Tidak)
        :Menampilkan error "User tidak ditemukan";
    endif

else (Tidak)
    :Menampilkan error validasi;
endif

stop

@enduml
```

---

## 7. Activity Diagram: Proses Pembatalan Pesanan dengan Refund

```plantuml
@startuml Activity Diagram - Pembatalan Pesanan dengan Refund

|Kasir|
start
:Membuka halaman Transaksi;
:Memilih pesanan yang akan dibatalkan;
:Menekan tombol Batalkan;

|Sistem|
:Memeriksa status pesanan;

if (Status pesanan?) then (Antrian/Proses)
    if (Ada produksi terkait?) then (Ya)
        :Mengecek status produksi;

        if (Produksi sudah dimulai?) then (Ya)
            :Membatalkan produksi;
            :Mengembalikan stok bahan baku;
        else (Belum)
            :Menghapus record produksi;
        endif
    else (Tidak)
    endif

    :Memeriksa pembayaran;

    if (Ada pembayaran?) then (Ya)
        :Menghitung total yang harus direfund;

        |Kasir|
        :Mengkonfirmasi refund;
        :Memproses pengembalian uang;

        |Sistem|
        :Membuat record refund;
        :Mencatat jumlah refund;

        if (Pelanggan dapat poin dari transaksi ini?) then (Ya)
            :Mengurangi poin pelanggan;
            :Mencatat riwayat poin (pengurangan);
        else (Tidak)
        endif

    else (Tidak)
        :Tidak ada refund;
    endif

    :Update status transaksi: Dibatalkan;
    :Kirim notifikasi (Pesanan dibatalkan);

elseif (Status pesanan?) then (Selesai)
    :Menampilkan error "Pesanan sudah selesai";
else (Dibatalkan)
    :Menampilkan error "Pesanan sudah dibatalkan";
endif

stop

@enduml
```

---

## 8. Activity Diagram: Proses Sesi Penjualan (Shift)

```plantuml
@startuml Activity Diagram - Sesi Penjualan (Shift)

|Kasir|
start
:Membuka halaman Transaksi;

if (Ada sesi aktif?) then (Tidak)
    :Menekan tombol "Buka Sesi";
    :Memasukkan modal awal (kas);
    :Mengkonfirmasi buka sesi;

    |Sistem|
    :Generate nomor shift;
    :Menyimpan shift baru;
    note right
      Data shift:
      - opened_by
      - initial_cash
      - status: Buka
    end note
    :Kirim notifikasi (Sesi dibuka);

    |Kasir|
    :Memulai transaksi;

    while (Selama sesi aktif) is (Ya)
        :Melakukan transaksi;

        |Sistem|
        :Mencatat transaksi dalam sesi;
    endwhile (Tutup sesi)

    |Kasir|
    :Menekan tombol "Tutup Sesi";

    |Sistem|
    :Menghitung total penjualan;
    :Menghitung total cash;
    :Menghitung total non-cash;

    |Kasir|
    :Memasukkan kas akhir aktual;
    :Mengkonfirmasi tutup sesi;

    |Sistem|
    :Menghitung selisih (expected vs actual);
    :Update shift (closed_by, final_cash);
    :Update status: Tutup;
    :Kirim notifikasi (Sesi ditutup);

else (Ya)
    :Melanjutkan transaksi di sesi aktif;
endif

stop

@enduml
```

---

## 9. Activity Diagram: Proses Reset Stok Produk Harian

```plantuml
@startuml Activity Diagram - Reset Stok Produk Harian

|Sistem (Scheduler)|
start
:Menjalankan scheduled command;
note right
  Dijalankan setiap hari
  pukul 00:00 (tengah malam)
end note

:Query semua produk;

while (Untuk setiap produk) is (Ada)
    if (Stok > 0?) then (Ya)
        :Reset stok menjadi 0;
        :Mencatat log reset;
    else (Tidak)
        :Skip (sudah 0);
    endif
endwhile (Selesai)

:Log hasil reset;
:Kirim notifikasi (Reset stok selesai);

stop

note right
  Alasan reset harian:
  Produk kue/roti bersifat
  perishable (mudah basi),
  sehingga stok tidak
  akumulatif antar hari.
end note

@enduml
```
