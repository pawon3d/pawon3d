<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('hitung') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white"
                wire:navigate>
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">Riwayat Hitung dan Catat Persediaan</h1>
        </div>
        <div class="flex gap-2 items-center justify-end-safe">
            <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
            </button>
        </div>
    </div>

    <div class="flex justify-between items-center mb-7 w-full">
        <!-- Search Input -->
        <div class="p-4 flex w-full">
            <input wire:model.live="search" placeholder="Cari..."
                class="w-full px-4 py-2 border border-accent rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <flux:button :loading="false" class="ml-2" variant="ghost">
                <flux:icon.funnel variant="mini" />
                <span>Filter</span>
            </flux:button>
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
                    ({{ $hitungs->total() }})
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

    @if ($hitungs->isEmpty())
    <div class="col-span-7 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center">
        <p class="text-gray-700 font-semibold">Belum Ada Riwayat Aksi.</p>
        <p class="text-gray-700">Tekan tombol “Tambah aksi” di halaman utama untuk menambahkan aksi.</p>
    </div>
    @else
    <div class="bg-white rounded-xl border">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('hitung_number')">
                            ID Aksi
                            {{ $sortDirection === 'asc' && $sortField === 'hitung_number' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('hitung_date')">
                            Tanggal Dibuat
                            {{ $sortDirection === 'asc' && $sortField === 'hitung_date' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('action')">
                            Aksi
                            {{ $sortDirection === 'asc' && $sortField === 'action' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('status')">
                            Status
                            {{ $sortDirection === 'asc' && $sortField === 'status' ? '↑' : '↓' }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($hitungs as $hitung)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('hitung.rincian', $hitung->id) }}"
                                class="hover:bg-gray-50 cursor-pointer">
                                {{ $hitung->hitung_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $hitung->hitung_date ? \Carbon\Carbon::parse($hitung->hitung_date)->format('d-m-Y')
                            :
                            '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $hitung->hitung_date_finish ?
                            \Carbon\Carbon::parse($hitung->hitung_date_finish)->format('d-m-Y')
                            :
                            '-' }}
                        </td>
                        <td class="px-6 py-4 text-left whitespace-nowrap">
                            {{ $hitung->action ??'-' }}
                        </td>
                        <td class="px-6 py-4 text-left whitespace-nowrap">
                            {{ $hitung->status ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $hitungs->links() }}
        </div>
    </div>
    @endif

</div>