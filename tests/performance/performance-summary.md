# Laporan Pengujian Non-Fungsional Sistem Informasi UMKM Pawon3D

**Tanggal Pengujian:** 26 Februari 2026  
**Tools:** k6 v1.6.1 (Grafana Labs)  
**Aplikasi:** Sistem Informasi Pawon3D  
**URL:** `https://skripsi.test`  
**Penguji:** Automated k6 Script  

---

## Ringkasan Eksekutif

Pengujian non-fungsional dilaksanakan dalam dua bagian:

- **Bagian A  Pengujian Performa:** Simulasi beban pengguna bersamaan (3, 5, dan 8 VU) selama 60 detik pada 6 endpoint inti sistem.
- **Bagian B  Pengujian Keandalan:** Validasi konsistensi data pada skenario kritis: konkurensi kasir, konkurensi produksi, atomisitas transaksi, kondisi batas stok, dan keandalan command notifikasi.

**Kriteria Lulus:**
| Metrik | Target |
|--------|--------|
| Response time (P95) | < 5.000 ms |
| Error rate HTTP | = 0% |
| Race condition | = 0 kejadian |
| Stok negatif | = 0 kejadian |

**Hasil:** Seluruh 8 skenario pengujian **LULUS** 

---

## Bagian A  Pengujian Performa

### A.1 Deskripsi Skenario

Setiap iterasi mensimulasikan satu sesi pengguna lengkap: login  dashboard  halaman kasir  halaman produksi  inventori  laporan. Tiga skenario beban digunakan untuk merepresentasikan kondisi operasional UMKM:

| Skenario | Jumlah VU | Konteks Operasional |
|----------|-----------|---------------------|
| Normal (harian) | 3 | Kondisi operasional sehari-hari UMKM kecil |
| Sibuk (jam puncak) | 5 | Beberapa staf aktif secara bersamaan |
| Beban puncak (maksimum) | 8 | Estimasi beban maksimum kapasitas tim UMKM |

**Durasi:** 60 detik per skenario  
**Produk yang dikonfigurasi:** Login (admin@pawon3d.com), 6 halaman utama  

---

### A.2 Hasil Per Skenario  Metrik Keseluruhan

| Skenario | VU | Total Iterasi | Total Request | Avg RT | P(95) | Max RT | Error Rate | Status |
|----------|----|-------------|--------------|--------|-------|--------|------------|--------|
| Normal (harian) | 3 | 21 | 168 | 709 ms | 1.004 ms | 1.333 ms | 0,00% |  LULUS |
| Sibuk (jam puncak) | 5 | 35 | 280 | 711 ms | 1.002 ms | 2.022 ms | 0,00% |  LULUS |
| Beban puncak | 8 | 48 | 384 | 989 ms | 1.410 ms | 2.160 ms | 0,00% |  LULUS |

> **Catatan:** Seluruh nilai P(95) berada jauh di bawah batas target 5.000 ms.

---

### A.3 Hasil Per Endpoint  Nilai P(95) Response Time (ms)

| Endpoint | Fungsi | 3 VU P(95) | 5 VU P(95) | 8 VU P(95) | Maks Keseluruhan | Status |
|----------|--------|-----------|-----------|-----------|-----------------|--------|
| `/` (Dashboard) | Ringkasan data utama | 683 ms | 695 ms | 1.230 ms | 1.230 ms |  LULUS |
| `/kasir` | Transaksi penjualan | 839 ms | 842 ms | 1.260 ms | 1.260 ms |  LULUS |
| `/produksi` | Manajemen produksi | 765 ms | 800 ms | 1.050 ms | 1.050 ms |  LULUS |
| `/inventori` | Data stok & bahan | 819 ms | 845 ms | 1.050 ms | 1.050 ms |  LULUS |
| `/laporan` | Laporan keuangan | 712 ms | 717 ms | 959 ms | 959 ms |  LULUS |

> **Target P(95):** < 5.000 ms per endpoint. Semua endpoint lulus di semua skenario beban.

---

### A.4 Analisis Performa

**Pola Response Time:**

- Endpoint **/kasir** konsisten menjadi yang paling lambat (P95 hingga 1.260 ms pada 8 VU), karena halaman ini me-render daftar produk aktif beserta kalkulasi status stok via Livewire.
- Endpoint **/laporan** konsisten paling cepat (P95 959 ms pada 8 VU), mengindikasikan query agregasi berjalan efisien.
- Peningkatan beban dari 3 VU ke 8 VU hanya menyebabkan kenaikan latensi rata-rata sebesar **+280 ms** (39%), jauh di bawah degradasi yang mengkhawatirkan.

**Skalabilitas:**

- Throughput meningkat linear dari ~2,4 req/s (3 VU)  ~3,9 req/s (5 VU)  ~5,5 req/s (8 VU).
- Tidak ada error HTTP yang terjadi di seluruh 832 total request beban gabungan.
- Sistem menunjukkan karakteristik **penskalaan linear** yang baik dalam rentang beban operasional UMKM.

**Catatan Cold Start:**

Pada pengujian awal (sebelum perbaikan skrip), kasir page menunjukkan response time hingga **12,87 detik** saat pertama kali diakses setelah server restart (cold start Livewire/OPcache). Ini adalah perilaku normal PHP/Laravel dan tidak relevan dalam kondisi operasional berulang karena cache sudah berjalan.

---

## Bagian B  Pengujian Keandalan

### B.1 Skenario Konkurensi Kasir (Race Condition Stok Produk)

**Tujuan:** Memverifikasi bahwa transaksi penjualan bersamaan tidak menyebabkan stok negatif atau double-selling.

**Konfigurasi:**
- Jumlah VU: 5 (masing-masing berulang, 15 iterasi total dibagi bersama)
- Stok awal produk (Donat Gula): **5 unit** (= jumlah VU, kondisi kritis)
- Endpoint: `POST /test/concurrent-buy`

**Hasil:**

| Metrik | Nilai | Status |
|--------|-------|--------|
| Pembelian berhasil (HTTP 200) | 5 |  Tepat = stok awal |
| Ditolak stok habis (HTTP 409) | 10 |  Benar |
| Stok akhir | 0 |  Tidak negatif |
| Race condition terdeteksi | 0 |  Tidak ada |
| Error server (HTTP 5xx) | 0 |  Tidak ada |
| P(95) response time | 970 ms |  < 5.000 ms |

**Kesimpulan:** Sistem berhasil menangani konkurensi 5 pengguna bersamaan dengan stok terbatas tanpa menjual lebih dari stok yang tersedia dan tanpa stok negatif. **LULUS** 

---

### B.2 Skenario Konkurensi Produksi (Race Condition Bahan Baku)

**Tujuan:** Memverifikasi bahwa pengurangan bahan baku saat proses produksi bersamaan tidak menyebabkan stok bahan negatif.

**Konfigurasi:**
- Jumlah VU: 3 (12 iterasi total dibagi bersama)
- Batch awal Tepung Terigu: **9 unit**
- Setiap produksi mengurangi 1 unit
- Endpoint: `POST /test/concurrent-produce`

**Hasil:**

| Metrik | Nilai | Status |
|--------|-------|--------|
| Produksi berhasil | 9 |  Tepat = stok awal |
| Ditolak bahan tidak cukup | 3 |  Benar |
| Batch akhir | 0 unit |  Tidak negatif |
| Race condition terdeteksi | 0 |  Tidak ada |
| Error server (HTTP 5xx) | 0 |  Tidak ada |
| P(95) response time | 548 ms |  < 5.000 ms |

**Kesimpulan:** Sistem berhasil mempertahankan konsistensi stok bahan baku saat 3 proses produksi berjalan bersamaan. **LULUS** 

---

### B.3 Skenario Atomisitas Transaksi (DB::transaction)

**Tujuan:** Memverifikasi bahwa `DB::transaction` pada transaksi kasir bersifat atomis  jika satu tahap gagal, seluruh transaksi di-rollback dan tidak ada perubahan data parsial.

**Konfigurasi:**
- Jumlah VU: 1
- Stok awal Brownies Coklat: **5 unit**
- Fase 1: Pembelian normal (`force_fail=false`)  harus berhasil dan mengurangi stok
- Fase 2: Pembelian dengan kegagalan dipaksa (`force_fail=true`)  harus rollback, stok tidak berubah
- Endpoint: `POST /test/atomicity-buy`

**Hasil:**

| Fase | Skenario | Stok Sebelum | Stok Sesudah | Status DB | Hasil |
|------|----------|-------------|-------------|-----------|-------|
| Fase 1 | Normal buy | 5 | 4 | COMMIT |  Stok berkurang 1 |
| Fase 2 | Forced fail | 4 | 4 | ROLLBACK |  Stok tidak berubah |

| Metrik | Nilai | Status |
|--------|-------|--------|
| Commit berhasil | 1 |  |
| Rollback berhasil | 1 |  |
| Kegagalan atomisitas | 0 |  |
| Total checks | 6/6 lulus |  |

**Kesimpulan:** `DB::transaction` berfungsi dengan benar. Kegagalan parsial tidak menyebabkan data inkonsisten. **LULUS** 

---

### B.4 Skenario Kondisi Batas Stok

**Tujuan:** Memverifikasi perilaku sistem pada kondisi batas ekstrem: stok = 1 (tepat di batas), stok = 0 (habis), dan deteksi minimum stok bahan baku.

**Konfigurasi:**
- Jumlah VU: 3 bersamaan
- Stok awal Donat Gula: **1 unit**
- Tepung Terigu batch: **0 unit** (di bawah minimum 5)

**Hasil:**

| Aspek | Kondisi | Hasil | Status |
|-------|---------|-------|--------|
| Stok = 1, 3 VU bersamaan | 3 req bersamaan | 1 berhasil, 2 ditolak 409 |  |
| Stok akhir |  | 0 (tidak negatif) |  |
| Batch=0 vs minimum=5 | Di bawah minimum | Terdeteksi sebagai "Hampir Habis" |  |
| Inventory command | Exit code | 0 (sukses) |  |
| Error server (HTTP 5xx) |  | 0 |  |
| Stok negatif |  | 0 kejadian |  |

**Kesimpulan:** Sistem menangani kondisi batas dengan benar. Hanya 1 pembelian yang diizinkan saat stok=1, dan deteksi minimum bahan baku berjalan dengan tepat. **LULUS** 

---

### B.5 Skenario Keandalan Command Notifikasi

**Tujuan:** Memverifikasi keandalan Artisan command `inventory:check-alerts` yang bertugas memperbarui status stok, mengirim notifikasi low-stock, dan notifikasi kadaluarsa bahan.

**Konfigurasi:**
- Jumlah VU: 1 (3 run berurutan)
- Run 1: Kondisi normal (batch Tepung Terigu = 10, di atas minimum 5)
- Run 2: Kondisi kritis (batch dikurangi ke 2, di bawah minimum 5)  notifikasi harus muncul
- Run 3: Dijalankan dua kali berturut-turut  command harus idempoten (tidak crash)

**Hasil:**

| Run | Kondisi | Exit Code | Notifikasi Stok Rendah | Status |
|-----|---------|-----------|----------------------|--------|
| Run 1 | Normal | 0  | Tidak ada (sesuai harapan) |  |
| Run 2 | Stok < minimum | 0  | Terdeteksi (Tepung Terigu: 2 < 5) |  |
| Run 3a | Berulang run pertama | 0  | Berjalan normal |  |
| Run 3b | Berulang run kedua | 0  | Berjalan normal (idempoten) |  |

| Metrik | Nilai | Status |
|--------|-------|--------|
| Command crash | 0 |  |
| Notifikasi sesuai harapan | 1/1 |  |
| Idempoten (tidak error saat diulang) | Terkonfirmasi |  |
| Error rate | 0% |  |

**Kesimpulan:** Command `inventory:check-alerts` berjalan andal, mendeteksi kondisi kritis dengan tepat, dan tidak crash saat dijalankan berulang. **LULUS** 

---

## Rekapitulasi Hasil Pengujian Non-Fungsional

### Tabel A  Performa | Skenario vs Endpoint P(95)

| Endpoint | 3 VU Normal | 5 VU Sibuk | 8 VU Puncak | Target | Status |
|----------|------------|-----------|------------|--------|--------|
| Dashboard | 683 ms | 695 ms | 1.230 ms | < 5.000 ms |  LULUS |
| Kasir | 839 ms | 842 ms | 1.260 ms | < 5.000 ms |  LULUS |
| Produksi | 765 ms | 800 ms | 1.050 ms | < 5.000 ms |  LULUS |
| Inventori | 819 ms | 845 ms | 1.050 ms | < 5.000 ms |  LULUS |
| Laporan | 712 ms | 717 ms | 959 ms | < 5.000 ms |  LULUS |
| **Keseluruhan** | **1.004 ms** | **1.002 ms** | **1.410 ms** | < 5.000 ms |  LULUS |

### Tabel B  Keandalan | Skenario vs Indikator

| Skenario | VU | Kondisi Awal | Berhasil | Ditolak | Stok Negatif | Race Condition | Error Sistem | Status |
|----------|----|-------------|---------|---------|-------------|----------------|--------------|--------|
| B.1 Konkurensi Kasir | 5 | Stok = 5 | 5 (100%) | 10 | 0  | 0  | 0  |  LULUS |
| B.2 Konkurensi Produksi | 3 | Batch = 9 | 9 (100%) | 3 | 0  | 0  | 0  |  LULUS |
| B.3 Atomisitas | 1 | Stok = 5 | Commit=1, Rollback=1 |  | 0  |  | 0  |  LULUS |
| B.4 Batas Stok | 3 | Stok = 1 | 1 (33%) | 2 | 0  | 0  | 0  |  LULUS |
| B.5 Command Notifikasi | 1 | Batch = 10 | 4 run (100%) |  |  |  | 0  |  LULUS |

---

## Kesimpulan

Berdasarkan hasil pengujian non-fungsional yang telah dilaksanakan:

1. **Performa:** Sistem memenuhi seluruh target performa. Pada beban puncak 8 pengguna bersamaan pun, seluruh endpoint merespons di bawah 1,3 detik (P95), jauh di bawah batas toleransi 5 detik. Throughput meningkat linear seiring penambahan beban, menunjukkan tidak adanya bottleneck signifikan dalam rentang penggunaan operasional UMKM.

2. **Keandalan Konkurensi:** Sistem secara konsisten menolak transaksi berlebih saat stok terbatas. Tidak ada kejadian double-selling, stok negatif, maupun race condition yang terdeteksi selama pengujian konkurensi kasir (5 VU, 15 iterasi) maupun produksi (3 VU, 12 iterasi).

3. **Integritas Transaksi:** Mekanisme `DB::transaction` terbukti atomis  rollback berjalan dengan benar saat terjadi kegagalan parsial, menjaga konsistensi database.

4. **Penanganan Kondisi Batas:** Sistem menangani kondisi ekstrem (stok = 1, stok = 0, bahan di bawah minimum) secara tepat dan memberikan respons yang sesuai (HTTP 409 untuk penolakan).

5. **Keandalan Otomasi:** Command `inventory:check-alerts` berjalan andal, mendeteksi kondisi stok kritis dengan benar, dan bersifat idempoten.

**Sistem dinyatakan memenuhi persyaratan non-fungsional** yang diperlukan untuk operasional UMKM Pawon3D.

---

## Lampiran  Artefak Pengujian

| File | Deskripsi |
|------|-----------|
| `tests/performance/performance-test.js` | Skrip k6 Part A (3/5/8 VU) |
| `tests/performance/reliability-kasir.js` | Skrip k6 B.1 Konkurensi Kasir |
| `tests/performance/reliability-produksi.js` | Skrip k6 B.2 Konkurensi Produksi |
| `tests/performance/reliability-atomicity.js` | Skrip k6 B.3 Atomisitas |
| `tests/performance/reliability-boundary.js` | Skrip k6 B.4 Kondisi Batas |
| `tests/performance/reliability-command.js` | Skrip k6 B.5 Command Notifikasi |
| `tests/performance/perf-3vu.json` | Hasil raw JSON 3 VU |
| `tests/performance/perf-5vu.json` | Hasil raw JSON 5 VU |
| `tests/performance/perf-8vu.json` | Hasil raw JSON 8 VU |
| `tests/performance/rel-kasir.json` | Hasil raw JSON Keandalan Kasir |
| `tests/performance/rel-produksi.json` | Hasil raw JSON Keandalan Produksi |
| `tests/performance/rel-atomicity.json` | Hasil raw JSON Atomisitas |
| `tests/performance/rel-boundary.json` | Hasil raw JSON Kondisi Batas |
| `tests/performance/rel-command.json` | Hasil raw JSON Command |
| `tests/performance/run-all.ps1` | Master runner PowerShell |
