<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Daftar Produk</h1>
        <div class="flex gap-2 items-center">
            <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
            </button>

            <!-- Tombol Riwayat Pembaruan -->
            <button type="button" wire:click="riwayatPembaruan"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Riwayat Pembaruan
            </button>
        </div>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="exclamation-triangle" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">Pilih salah satu metode penjualan terlebih dahulu (Siap Beli, Pesanan
                Reguler, atau Pesanan Box), lalu tekan tombol "Tambah Produk" untuk menambahkan produk ke metode yang
                diinginkan.
            </p>
            <ul class="mt-2 list-disc pl-5">
                <li class="text-sm text-gray-500">
                    <strong>Siap Beli</strong>
                    untuk produk yang ada di rak penjualan yang bentuknya per potong atau per buah.
                </li>
                <li class="text-sm text-gray-500">
                    <strong>Pesanan Reguler</strong>
                    untuk produk pesanan yang bentuknya loyangan atau paketan.
                </li>
                <li class="text-sm text-gray-500">
                    <strong>Pesanan Kotak</strong>
                    untuk paket khusus atau snack box dengan banyak produk dalam satu kotak.
                </li>
            </ul>

        </div>
    </div>
    <div class="flex justify-between items-center mb-4">
        <!-- Search Input -->
        <div class="p-4 flex">
            <input wire:model.live="search" placeholder="Cari..."
                class="w-lg px-4 py-2 border border-accent rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <flux:button :loading="false" class="ml-2" variant="ghost">
                <flux:icon.funnel variant="mini" />
                <span>Filter</span>
            </flux:button>
        </div>
        <div class="flex gap-2 items-center">
            <a href="{{ route('produk.tambah') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150"
                wire:navigate>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produk
            </a>
            {{-- <flux:button type="button" wire:click="$set('showAddModal', true)"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produk
            </flux:button> --}}
        </div>
    </div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex gap-2 items-center">
            <flux:dropdown>
                <flux:button variant="ghost">
                    @if($filterStatus)
                    {{ $filterStatus === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                    @else
                    Semua Kategori
                    @endif
                    ({{ $products->total() }})
                    <flux:icon.chevron-down variant="mini" />
                </flux:button>
                <flux:menu>
                    <flux:menu.radio.group wire:model.live="filterStatus">
                        <flux:menu.radio value="">Semua Kategori</flux:menu.radio>
                        <flux:menu.radio value="aktif">Aktif</flux:menu.radio>
                        <flux:menu.radio value="nonaktif">Tidak Aktif</flux:menu.radio>
                    </flux:menu.radio.group>
                </flux:menu>
            </flux:dropdown>
            <flux:dropdown>
                <flux:button variant="ghost">
                    Urutkan Produk
                    <flux:icon.chevron-down variant="mini" />

                </flux:button>

                <flux:menu>
                    <flux:menu.radio.group wire:model="sortByCategory">
                        <flux:menu.radio value="name">Nama</flux:menu.radio>
                        <flux:menu.radio value="status">Status</flux:menu.radio>
                        <flux:menu.radio value="product" checked>Jenis Produk</flux:menu.radio>
                    </flux:menu.radio.group>
                </flux:menu>
            </flux:dropdown>
        </div>
        <div class="flex gap-2 mr-4 items-center">
            <span class="text-sm mr-2">Tampilan Produk:</span>

            <!-- Grid View -->
            <div class="relative">
                <input type="radio" name="viewMode" id="grid-view" value="grid" wire:model.live="viewMode"
                    class="absolute opacity-0 w-0 h-0">
                <label for="grid-view" class="cursor-pointer">
                    <flux:icon icon="squares-2x2"
                        class="{{ $viewMode === 'grid' ? 'text-gray-100 bg-gray-600' : 'text-gray-800 bg-white' }} rounded-xl border border-gray-600 hover:text-gray-100 hover:bg-gray-600 transition-colors size-8" />
                </label>
            </div>

            <!-- List View -->
            <div class="relative">
                <input type="radio" name="viewMode" id="list-view" value="list" wire:model.live="viewMode"
                    class="absolute opacity-0 w-0 h-0">
                <label for="list-view" class="cursor-pointer">
                    <flux:icon icon="list-bullet"
                        class="{{ $viewMode === 'list' ? 'text-gray-100 bg-gray-600' : 'text-gray-800 bg-white' }} rounded-xl border border-gray-600 hover:text-gray-100 hover:bg-gray-600 transition-colors size-8" />
                </label>
            </div>
        </div>
    </div>

    @if ($viewMode === 'grid')
    {{-- grid view --}}
    <div class="bg-white">
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4 mt-4">
            @foreach($products as $product)
            <div class="p-4 text-center">
                <a href="{{ route('produk.edit', $product->id) }}" class="hover:bg-gray-50 cursor-pointer">
                    <div class="flex justify-center mb-4">
                        @if($product->product_image)
                        <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->name }}"
                            class="w-full h-36 object-fill rounded-lg border border-gray-200" />
                        @else
                        <img src="{{ asset('img/no-img.jpg') }}" alt="Gambar Produk"
                            class="w-full h-36 object-fill rounded-lg border border-gray-200" />
                        @endif
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg montserrat-regular font-semibold mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-600 mb-4 text-sm montserrat-regular">
                            @if ($product->reviews->count() > 0)
                            {{ number_format($product->reviews->avg('rating'), 1) }}
                            <i class="bi bi-star-fill text-yellow-500"></i>
                            @else
                            Belum ada penilaian
                            @endif
                            @if ($product->reviews->count() > 10)
                            ({{ $product->reviews->count() }}+ Penilai)
                            @elseif ($product->reviews->count() > 0)
                            ({{ $product->reviews->count() }} Penilai)
                            @endif
                        </p>
                        <p class="text-gray-600 mb-4 text-sm montserrat-regular">Rp {{
                            number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        <div class="p-4">
            {{ $products->links() }}
        </div>
    </div>
    @elseif ($viewMode === 'list')
    {{-- list view --}}
    <div class="bg-white rounded-xl border">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Nama Produk</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Kategori</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Harga</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Stok</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('produk.edit', $product->id) }}" class="hover:bg-gray-50 cursor-pointer">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 space-x-2 whitespace-nowrap">{{
                            $product->category->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">Rp. {{ number_format($product->price, 0, ',', '.')
                            }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $product->stock }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $products->links() }}
        </div>
    </div>
    @endif


    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Produk</flux:heading>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @foreach($activityLogs as $log)
                <div class="border-b py-2">
                    <div class="text-sm font-medium">{{ $log->description }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $log->causer->name ?? 'System' }} -
                        {{ $log->created_at->format('d M Y H:i') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </flux:modal>



    <!-- Modal Tambah -->
    <flux:modal wire:model="showAddModal">
        <form wire:submit.prevent="store" enctype="multipart/form-data">

            <div class="space-y-4 mt-4">
                <!-- Preview Gambar -->
                @if($previewImage)
                <img src="{{ $previewImage }}" alt="Preview Produk"
                    class="w-24 h-24 object-cover mx-auto rounded-md border border-gray-200" />
                @endif

                <!-- Input Form -->
                <div class="form-group">
                    <flux:input label="Nama Produk" wire:model="name" type="text" />
                </div>

                <!-- Kategori -->
                <div class="form-group">
                    <flux:select label="Kategori" wire:model="category_id">
                        <flux:select.option value="">Pilih Kategori</flux:select.option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Harga & Stok -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <flux:input label="Harga" wire:model="price" type="number" />
                    </div>
                    <div class="form-group">
                        <flux:input label="Stok" wire:model="stock" type="number" />
                    </div>
                </div>

                <!-- Upload Gambar -->
                <div class="form-group">
                    <flux:input.group>
                        <flux:input label="Gambar Produk" type="file" wire:model="product_image" class="input-text mt-1"
                            accept="image/*" />
                        <flux:icon.loading wire:loading wire:target="product_image" />
                    </flux:input.group>
                </div>

                <!-- Switch Siap Beli -->
                <div class="form-group flex justify-between items-center">
                    <label class="text-sm font-medium text-gray-700">Siap Beli?</label>
                    <flux:switch wire:model.live="is_ready" class="toggle" />
                </div>

                <!-- Tabs Komposisi -->
                <div class="border-t pt-4">
                    <div class="flex justify-center gap-6 mb-4">
                        <button type="button"
                            class="tab-button mr-4 {{ $activeTab === 'material' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600' }}"
                            wire:click="$set('activeTab', 'material')">
                            Bahan Baku
                        </button>
                        <button type="button"
                            class="tab-button {{ $activeTab === 'processed_material' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600' }}"
                            wire:click="$set('activeTab', 'processed_material')">
                            Olahan
                        </button>
                    </div>

                    <!-- Komposisi -->
                    @foreach($product_compositions as $index => $composition)
                    <div class="flex gap-2 mb-2 items-center">
                        <!-- Pilih Bahan / Olahan -->
                        @if($activeTab === 'material')
                        <flux:select wire:model="product_compositions.{{ $index }}.material_id"
                            wire:change="setMaterial({{ $index }}, $event.target.value)" class="input-text flex-1">
                            <flux:select.option value="">Pilih Bahan Baku</flux:select.option>
                            @foreach($materials as $material)
                            <option value="{{ $material->id }}">
                                {{ $material->name }}
                            </option>
                            @endforeach
                        </flux:select>
                        @else
                        <flux:select wire:model="product_compositions.{{ $index }}.processed_material_id"
                            wire:change="setProcessedMaterial({{ $index }}, $event.target.value)"
                            class="input-text flex-1">
                            <flux:select.option value="">Pilih Olahan</flux:select.option>
                            @foreach($processedMaterials as $pm)
                            <option value="{{ $pm->id }}">
                                {{ $pm->name }}
                            </option>
                            @endforeach
                        </flux:select>
                        @endif

                        <!-- Input Jumlah -->
                        <div class="relative flex-1">
                            <flux:input type="number"
                                wire:model="product_compositions.{{ $index }}.{{ $activeTab === 'material' ? 'material_quantity' : 'processed_material_quantity' }}"
                                class="input-text w-full" />
                            @if($activeTab === 'material' && isset($composition['material_unit']))
                            <span class="flex items-center absolute top-0 right-0 px-2 py-2 text-gray-500 text-sm">
                                {{ $composition['material_unit'] }}
                            </span>
                            @endif
                        </div>

                        <!-- Tombol Hapus -->
                        <button type="button" wire:click="removeComposition({{ $index }})" class="text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3" />
                            </svg>
                        </button>
                    </div>
                    @endforeach

                    <!-- Tambah Komposisi -->
                    <flux:button type="button" wire:click="addComposition" class="btn-secondary mt-2">
                        + Tambah {{ $activeTab === 'material' ? 'Bahan Baku' : 'Olahan' }}
                    </flux:button>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="modal-footer flex justify-end mt-4">
                <flux:button type="submit" class="btn-primary">
                    Simpan
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Edit -->
    <flux:modal wire:model="showEditModal">
        <form wire:submit.prevent="update" enctype="multipart/form-data">

            <div class="space-y-4 mt-4">
                <!-- Preview Gambar -->
                @if($previewImage)
                <img src="{{ $previewImage }}" alt="Preview Produk"
                    class="w-24 h-24 object-cover mx-auto rounded-md border border-gray-200" />
                @endif

                <!-- Input Form -->
                <div class="form-group">
                    <flux:input label="Nama Produk" wire:model="name" type="text" />
                </div>

                <!-- Kategori -->
                <div class="form-group">
                    <flux:select label="Kategori" wire:model="category_id">
                        <flux:select.option value="">Pilih Kategori</flux:select.option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Harga & Stok -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <flux:input label="Harga" wire:model="price" type="number" />
                    </div>
                    <div class="form-group">
                        <flux:input label="Stok" wire:model="stock" type="number" />
                    </div>
                </div>

                <!-- Upload Gambar -->
                <div class="form-group">
                    <flux:input.group>
                        <flux:input label="Gambar Produk" type="file" wire:model="product_image" class="input-text mt-1"
                            accept="image/*" />
                        <flux:icon.loading wire:loading wire:target="product_image" />
                    </flux:input.group>
                </div>

                <!-- Switch Siap Beli -->
                <div class="form-group flex justify-between items-center">
                    <label class="text-sm font-medium text-gray-700">Siap Beli?</label>
                    <flux:switch wire:model.live="is_ready" class="toggle" />
                </div>

                <!-- Tabs Komposisi -->
                <div class="border-t pt-4">
                    <div class="flex justify-center gap-6 mb-4">
                        <button type="button"
                            class="tab-button mr-4 {{ $activeTab === 'material' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600' }}"
                            wire:click="$set('activeTab', 'material')">
                            Bahan Baku
                        </button>
                        <button type="button"
                            class="tab-button {{ $activeTab === 'processed_material' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600' }}"
                            wire:click="$set('activeTab', 'processed_material')">
                            Olahan
                        </button>
                    </div>

                    <!-- Komposisi -->
                    @foreach($product_compositions as $index => $composition)
                    <div class="flex gap-2 mb-2 items-center">
                        <!-- Pilih Bahan / Olahan -->
                        @if($activeTab === 'material')
                        <flux:select wire:model="product_compositions.{{ $index }}.material_id"
                            wire:change="setMaterial({{ $index }}, $event.target.value)" class="input-text flex-1">
                            <flux:select.option value="">Pilih Bahan Baku</flux:select.option>
                            @foreach($materials as $material)
                            <option value="{{ $material->id }}">
                                {{ $material->name }}
                            </option>
                            @endforeach
                        </flux:select>
                        @else
                        <flux:select wire:model="product_compositions.{{ $index }}.processed_material_id"
                            wire:change="setProcessedMaterial({{ $index }}, $event.target.value)"
                            class="input-text flex-1">
                            <flux:select.option value="">Pilih Olahan</flux:select.option>
                            @foreach($processedMaterials as $pm)
                            <option value="{{ $pm->id }}">
                                {{ $pm->name }}
                            </option>
                            @endforeach
                        </flux:select>
                        @endif

                        <!-- Input Jumlah -->
                        <div class="relative flex-1">
                            <flux:input type="number"
                                wire:model="product_compositions.{{ $index }}.{{ $activeTab === 'material' ? 'material_quantity' : 'processed_material_quantity' }}"
                                class="input-text w-full" />
                            @if($activeTab === 'material' && isset($composition['material_unit']))
                            <span class="flex items-center absolute top-0 right-0 px-2 py-2 text-gray-500 text-sm">
                                {{ $composition['material_unit'] }}
                            </span>
                            @endif
                        </div>

                        <!-- Tombol Hapus -->
                        <button type="button" wire:click="removeComposition({{ $index }})" class="text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3" />
                            </svg>
                        </button>
                    </div>
                    @endforeach

                    <!-- Tambah Komposisi -->
                    <flux:button type="button" wire:click="addComposition" class="btn-secondary mt-2">
                        + Tambah {{ $activeTab === 'material' ? 'Bahan Baku' : 'Olahan' }}
                    </flux:button>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="modal-footer flex justify-end mt-4">
                <flux:button type="submit" class="btn-primary">
                    Simpan
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Detail -->
    <flux:modal wire:model="showDetailModal">
        @if($detailData)
        <div class="space-y-4">
            <!-- Gambar Produk -->
            @if($detailData->product_image)
            <div class="text-center">
                <img src="{{ asset('storage/' . $detailData->product_image) }}" alt="Gambar Produk"
                    class="w-24 h-24 object-cover mx-auto rounded-md border border-gray-200" />
            </div>
            @endif

            <!-- Informasi Dasar -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $detailData->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Kategori</label>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $categories->find($detailData->category_id)?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Harga</label>
                    <p class="mt-1 text-sm text-gray-900">
                        Rp {{ number_format($detailData->price, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Stok</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $detailData->stock }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $detailData->is_ready ? 'Siap Beli dan Pesanan' : 'Hanya Pesanan' }}
                    </p>
                </div>
            </div>

            <!-- Tabel Komposisi -->
            <div class="border-t pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Komposisi Bahan</label>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    Jenis Bahan
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    Nama Bahan
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    Jumlah
                                </th>
                                @if($detailData->product_compositions->contains('material_id'))
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    Satuan
                                </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($detailData->product_compositions as $composition)
                            <tr>
                                <td class="px-4 py-2 text-sm">
                                    @if($composition->material_id)
                                    Bahan Baku
                                    @else
                                    Bahan Olahan
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    @if($composition->material_id)
                                    {{ $materials->find($composition->material_id)?->name }}
                                    @else
                                    {{ $processedMaterials->find($composition->processed_material_id)?->name }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    @if($composition->material_id)
                                    {{ $composition->material_quantity }}
                                    @else
                                    {{ $composition->processed_material_quantity }}
                                    @endif
                                </td>
                                @if($detailData->product_compositions->contains('material_id'))
                                <td class="px-4 py-2 text-sm">
                                    {{ $composition->material_unit ?? '-' }}
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-center text-sm text-gray-500">
                                    Tidak ada komposisi
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <div class="flex justify-end gap-2 mt-6">
            <flux:button type="button" wire:click="$set('showDetailModal', false)"
                class="px-4 py-2 border rounded-md hover:bg-gray-100">
                Tutup
            </flux:button>
        </div>
    </flux:modal>
</div>