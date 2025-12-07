# Analisis Redundansi Kode

> Dokumen ini berisi temuan redundansi kode dalam project.  
> Dibuat: 2025-12-07

---

## Folder: `app/Livewire/Production/`

### 1. `Mulai.php` vs `MulaiSiapBeli.php` ⚠️ Tinggi

| Aspek | Detail |
|-------|--------|
| **Kesamaan** | ~90% struktur identik |
| **Method duplikat** | `mount()`, `riwayatPembaruan()`, `updatedProductionDetails()`, `parseFraction()`, `save()`, `markAllReceived()`, `render()` |
| **Perbedaan** | `Mulai.php` redirect ke `produksi.rincian` + membuat InventoryLog; `MulaiSiapBeli.php` redirect ke `produksi.rincian-siap-beli` |
| **Saran** | Buat base class `BaseMulai` atau gabung dengan parameter `$method` |

---

### 2. `TambahProduksiPesanan.php` vs `EditProduksiPesanan.php` ⚠️ Tinggi

| Aspek | Detail |
|-------|--------|
| **Kesamaan** | Method `start()` ~90% identik |
| **Logic duplikat** | Validasi bahan baku, create production, create workers, create details |
| **Perbedaan** | `TambahProduksiPesanan` create baru; `EditProduksiPesanan` update existing |
| **Saran** | Gabung dengan pola `isEditMode` seperti di `TambahSiapBeli.php` |

---

### 3. `Rincian.php` vs `RincianSiapBeli.php` ⚠️ Sedang

| Method | Status |
|--------|--------|
| `buatCatatan()` | Identik |
| `simpanCatatan()` | Identik |
| `confirmDelete()` | Identik |
| `delete()` | Hampir sama, beda redirect |
| `start()` | Logika serupa |

**Saran:** Extract ke trait `HasProductionNotes` dan `HasProductionDeletion`

---

### 4. `Tambah.php`, `TambahSiapBeli.php`, `Edit.php` ⚠️ Sedang

Method yang sama:
- `addProduct()`
- `removeProduct()`
- `setProduct()`
- `updatedProductionDetails()`
- `calculateTotals()`

**Saran:** Buat trait `ManagesProductionDetails`

---

## Catatan

- Redundansi ini **tidak harus di-refactor** jika tim lebih nyaman dengan kode terpisah per-fitur
- Refactoring membawa **risiko regresi** dan perlu testing menyeluruh
- Prioritaskan refactor jika sering ada bug yang harus diperbaiki di multiple file

---

*Dokumen ini untuk referensi pengembangan. Tidak ada perubahan kode yang dilakukan.*
