<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Daftar Toko Persediaan</h1>
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

    <div class="flex items-center border bg-white shadow-lg rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Toko Persediaan digunakan untuk menetapkan asal barang yang dibeli beserta harga yang dikeluarkan. Toko
                persediaan dapat didatangi langsung untuk belanja atau dapat dilakukan lewat telepon atau whatsapp
                kepada toko.
            </p>
        </div>
    </div>
    <div class="mt-4 bg-white shadow-lg rounded-lg p-4">
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
                <a href="{{ route('supplier.tambah') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150"
                    wire:navigate>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Toko
                </a>
            </div>
        </div>
        <div class="flex justify-between items-center mb-7">
            <div class="p-4 flex">
                <flux:dropdown>
                    <flux:button variant="ghost">
                        @if($filterStatus)
                        {{ $filterStatus === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                        @else
                        Semua Toko
                        @endif
                        ({{ $suppliers->total() }})
                        <flux:icon.chevron-down variant="mini" />
                    </flux:button>
                    <flux:menu>
                        <flux:menu.radio.group wire:model.live="filterStatus">
                            <flux:menu.radio value="">Semua Toko</flux:menu.radio>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Toko Persediaan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Kontak
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nomor Telepon
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('supplier.edit', $supplier->id) }}"
                                    class="hover:bg-gray-50 cursor-pointer">
                                    {{ $supplier->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $supplier->contact_name }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                {{ $supplier->phone }}
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
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Toko Persediaan</flux:heading>
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
</div>