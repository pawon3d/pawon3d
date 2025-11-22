# Cara Menggunakan Komponen Tabel

## 1. Tabel Dengan Paginasi (`table.paginated`)

### Contoh Penggunaan - Basic:

```blade
<x-table.paginated
    :headers="[
        ['label' => 'Nama Produk', 'sortable' => true, 'sort-by' => 'name'],
        ['label' => 'Kategori', 'sortable' => true, 'sort-by' => 'category_id'],
        ['label' => 'Harga', 'sortable' => true, 'sort-by' => 'price', 'align' => 'right'],
        ['label' => 'Stok', 'sortable' => true, 'sort-by' => 'stock'],
    ]"
    :paginator="$products"
    headerBg="#3f4e4f"
    headerText="#f8f4e1"
    bodyBg="#fafafa"
    bodyText="#666666"
    emptyMessage="Belum ada produk"
>
    @foreach($products as $product)
        <tr class="hover:bg-gray-50 cursor-pointer" wire:click="edit('{{ $product->id }}')">
            <td class="px-6 py-5 text-[#666666] font-medium text-sm">
                {{ $product->name }}
            </td>
            <td class="px-6 py-5 text-[#666666] font-medium text-sm">
                {{ $product->category->name }}
            </td>
            <td class="px-6 py-5 text-right text-[#666666] font-medium text-sm">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </td>
            <td class="px-6 py-5 text-[#666666] font-medium text-sm">
                {{ $product->stock }}
            </td>
        </tr>
    @endforeach
</x-table.paginated>
```

### Di Livewire Component:

```php
public $sortField = 'name';
public $sortDirection = 'asc';

public function sortBy($field): void
{
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortDirection = 'asc';
    }
    $this->sortField = $field;
    $this->resetPage();
}

public function render()
{
    return view('livewire.product.index', [
        'products' => Product::with('category')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10)
    ]);
}
```

### Props yang Tersedia - table.paginated:

-   `headers` (array, required) - Header kolom dengan konfigurasi:
    -   `label` (string) - Text yang ditampilkan
    -   `sortable` (bool, optional) - Apakah bisa di-sort
    -   `sort-by` (string, optional) - Field untuk sorting (required jika sortable=true)
    -   `align` (string, optional) - 'right' untuk align kanan, kosong untuk kiri
-   `paginator` (object, required) - Object paginator dari Laravel
-   `headerBg` (string, optional) - Background color header (hex: "#3f4e4f" atau class: "bg-gray-800")
-   `headerText` (string, optional) - Text color header (hex: "#f8f4e1" atau class: "text-white")
-   `bodyBg` (string, optional) - Background color body (hex: "#fafafa" atau class: "bg-white")
-   `bodyText` (string, optional) - Text color body (hex: "#666666" atau class: "text-gray-700")
-   `emptyMessage` (string, optional) - Pesan ketika data kosong (default: "Tidak ada data.")

**PENTING**: Gunakan slot default (bukan `<x-slot:rows>`) untuk isi tabel!

## 2. List Dengan Pagination Manual (`list.paginated`)

Untuk list items yang menggunakan pagination manual (bukan Laravel paginator), biasanya di dalam modal.

### Contoh Penggunaan:

```blade
<x-list.paginated
    :items="$usageMaterials"
    :columns="[
        [
            'label' => 'Barang Persediaan',
            'sortable' => true,
            'sort-method' => 'sortUsageMaterials',
        ],
    ]"
    :currentPage="$usagePage"
    previousMethod="previousUsagePage"
    nextMethod="nextUsagePage"
    headerBg="#3f4e4f"
    headerText="#f8f4e1"
    bodyBg="#fafafa"
    bodyText="#666666"
    emptyMessage="Tidak ada persediaan."
>
    @if ($usageMaterials && count($usageMaterials) > 0)
        @foreach ($usageMaterials as $material)
            <tr>
                <td class="px-6 py-5 text-[#666666] font-medium text-sm">
                    {{ $material->name }}
                </td>
                <td class="px-6 py-5 text-center w-[72px]">
                    <button type="button" wire:click="removeFromCategory('{{ $material->id }}')"
                        class="text-[#666666] hover:text-[#eb5757] transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        @endforeach
    @endif

    <x-slot name="actionColumn">
        <th class="px-6 py-5 w-[72px]"></th>
    </x-slot>
</x-list.paginated>
```

### Di Livewire Component:

```php
public $usagePage = 1;
public $usageSortDirection = 'asc';

public function sortUsageMaterials(): void
{
    $this->usageSortDirection = $this->usageSortDirection === 'asc' ? 'desc' : 'asc';
}

public function getUsageMaterialsProperty()
{
    return Material::whereIn('id', $this->materialIds)
        ->when($this->usageSearch, fn($q) => $q->where('name', 'like', "%{$this->usageSearch}%"))
        ->orderBy('name', $this->usageSortDirection)
        ->paginate(2, ['*'], 'usagePage', $this->usagePage);
}

public function previousUsagePage(): void
{
    if ($this->usagePage > 1) {
        $this->usagePage--;
    }
}

public function nextUsagePage(): void
{
    if ($this->usageMaterials && $this->usageMaterials->hasMorePages()) {
        $this->usagePage++;
    }
}
```

### Props yang Tersedia - list.paginated:

-   `items` (collection/paginator, required) - Data items (bisa Laravel paginator atau collection)
-   `columns` (array, required) - Konfigurasi kolom:
    -   `label` (string) - Text header
    -   `sortable` (bool, optional) - Apakah bisa di-sort
    -   `sort-method` (string, optional) - Nama method Livewire untuk sorting
-   `currentPage` (int, required) - Current page number
-   `previousMethod` (string, required) - Nama method Livewire untuk previous page
-   `nextMethod` (string, required) - Nama method Livewire untuk next page
-   `headerBg` (string, optional) - Background color header (hex atau class)
-   `headerText` (string, optional) - Text color header (hex atau class)
-   `bodyBg` (string, optional) - Background color body (hex atau class)
-   `bodyText` (string, optional) - Text color body (hex atau class)
-   `emptyMessage` (string, optional) - Pesan ketika data kosong
-   `actionColumn` (slot, optional) - Extra column header untuk tombol aksi

**NOTE**: Komponen ini otomatis detect Laravel paginator dan auto-calculate summary (from, to, total)!

## 3. Tabel Form (`table.form`)

⚠️ **Belum direfactor** - Masih menggunakan struktur lama. Akan diupdate nanti.

## Keuntungan:

✅ **Konsistensi** - Semua tabel punya styling yang sama  
✅ **Mudah Update** - Ubah di 1 tempat, semua tabel berubah  
✅ **Reusable** - Tinggal panggil komponen dengan props  
✅ **Support Hex Colors** - Bisa pakai hex (#3f4e4f) atau Tailwind class  
✅ **Sortable** - Built-in sorting dengan icon chevron  
✅ **Responsive** - Auto overflow-x untuk mobile  
✅ **Empty State** - Handle data kosong otomatis  
✅ **Pagination** - Built-in pagination controls

## Customization:

Nanti setelah dapat design dari Figma, tinggal ubah styling di file:

-   `resources/views/components/table/paginated.blade.php` - Untuk tabel dengan pagination
-   `resources/views/components/list/paginated.blade.php` - Untuk list dengan manual pagination
-   `resources/views/components/table/form.blade.php` - Untuk tabel dengan form inputs

Semua tabel di aplikasi akan otomatis update! 🎉
