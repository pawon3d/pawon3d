<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center">
            <a href="{{ route('bahan-baku') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">Daftar Kategori Persediaan</h1>
        </div>
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
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Kategori Persediaan digunakan untuk mengelompokkan barang berdasarkan ciri khas atau kriteria tertentu.
                Seunit barang dapat memiliki lebih dari satu kategori. Pastikan kategori dan barang yang dikelompokkan
                benar dan tepat.
            </p>
        </div>
    </div>
    <div class="flex justify-between items-center mb-7">
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
            <flux:button type="button" variant="primary" wire:click="showAddModal" icon="plus">Tambah
                Kategori</flux:button>
        </div>
    </div>
    <div class="flex justify-between items-center mb-7">
        <div class="p-4 flex">
            <flux:dropdown>
                <flux:button variant="ghost">
                    @if ($filterStatus)
                    {{ $filterStatus === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                    @else
                    Semua Kategori
                    @endif
                    {{-- {{ $filterStatus ? ' (' . $categories->total() . ')' : ' (' . $categories->count() . ')' }}
                    --}}
                    ({{ $categories->total() }})
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
        </div>
        <div class="flex gap-2 items-center">
            <flux:dropdown>
                <flux:button variant="ghost">
                    Urutkan Kategori
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
    </div>

    <div class="bg-white rounded-xl border">


        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('name')">Nama
                            Kategori
                            {{ $sortDirection === 'asc' && $sortField === 'name' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('is_active')">Status
                            Status Kategori
                            {{ $sortDirection === 'asc' && $sortField === 'is_active' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('details_count')">
                            Jenis Bahan
                            {{ $sortDirection === 'asc' && $sortField === 'details_count' ? '↑' : '↓' }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap hover:bg-gray-50 cursor-pointer"
                            wire:click="edit('{{ $category->id }}')">
                            {{ $category->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $category->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            {{ $category->details_count }}
                        </td>
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
            {{ $categories->links() }}
        </div>
    </div>

    <flux:modal name="tambah-kategori" class="w-full max-w-md" wire:model="showModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Kategori</flux:heading>
            </div>
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                    <input type="text" id="name" wire:model.lazy="name"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required />
                    @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button type="button" icon="x-mark">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="button" icon="save" variant="primary" wire:click="store">Simpan Kategori
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="rincian-kategori" class="w-full max-w-md" wire:model="showEditModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Rincian Kategori</flux:heading>
            </div>
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                    <input type="text" id="name" wire:model.lazy="name"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required />
                    @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="products" class="block text-sm font-medium text-gray-700">Jumlah Penggunaan</label>
                    <input type="text" id="products" wire:model.lazy="products"
                        class="mt-1 block w-full border-gray-300 bg-gray-200 rounded-md shadow-sm" disabled />
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:modal.trigger name="delete-category" class="mr-4">
                    <flux:button variant="ghost" icon="trash" />
                </flux:modal.trigger>

                <flux:modal name="delete-category" class="min-w-[22rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Hapus Kategori</flux:heading>

                            <flux:text class="mt-2">
                                <p>Apakah Anda yakin ingin menghapus kategori ini?</p>
                            </flux:text>
                        </div>

                        <div class="flex gap-2">
                            <flux:spacer />

                            <flux:modal.close>
                                <flux:button variant="ghost">Batal</flux:button>
                            </flux:modal.close>

                            <flux:button type="button" variant="danger" wire:click="delete">Hapus</flux:button>
                        </div>
                    </div>
                </flux:modal>
                <flux:button type="button" icon="x-mark" wire:click="$set('showEditModal', false)">Batal
                </flux:button>
                <flux:button type="button" icon="save" variant="primary" wire:click="update">Simpan Kategori
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Kategori</flux:heading>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @foreach ($activityLogs as $log)
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
</div>