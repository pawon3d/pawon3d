<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('produksi') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white"
                wire:navigate>
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">Riwayat Produksi {{ $methodName }}</h1>
        </div>
        <div class="flex gap-2 items-center justify-end-safe">
            <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
            </button>
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
    </div>
    <div class="flex justify-between items-center mb-4">
        <flux:dropdown>
            <flux:button variant="ghost">
                @if($filterStatus)
                {{ $filterStatus === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                @else
                Semua Produksi
                @endif
                ({{ $productions->total() }})
                <flux:icon.chevron-down variant="mini" />
            </flux:button>
            <flux:menu>
                <flux:menu.radio.group wire:model.live="filterStatus">
                    <flux:menu.radio value="">Semua Produksi</flux:menu.radio>
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

    @if ($productions->isEmpty())
    <div class="col-span-5 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center">
        <p class="text-gray-700 font-semibold">Belum ada produksi.</p>
        <p class="text-gray-700">Tekan tombol “Tambah Produksi” untuk menambahkan produksi.</p>
    </div>
    @else
    <div class="bg-white rounded-xl border shadow-sm">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 font-semibold cursor-pointer" wire:click="sortBy('production_number')">
                            ID Produk
                            <span>{{ $sortDirection === 'asc' && $sortField === 'production_number' ? '↑' : '↓'
                                }}</span>
                        </th>
                        <th class="px-6 py-3 font-semibold cursor-pointer" wire:click="sortBy('start_date')">Jadwal
                            Produksi
                            <span>{{ $sortDirection === 'asc' && $sortField === 'start_date' ? '↑' : '↓' }}</span>
                        </th>
                        <th class="px-6 py-3 font-semibold cursor-pointer" wire:click='sortBy("product_name")'>
                            Daftar Produk
                            <span>{{ $sortDirection === 'asc' && $sortField === 'product_name' ? '↑' : '↓' }}</span>
                        </th>
                        <th class="px-6 py-3 font-semibold cursor-pointer" wire:click='sortBy("worker_name")'>
                            Pekerja
                            <span>{{ $sortDirection === 'asc' && $sortField === 'worker_name' ? '↑' : '↓' }}</span>
                        </th>
                        <th class="px-6 py-3 font-semibold cursor-pointer" wire:click="sortBy('status')">Status
                            <span>{{ $sortDirection === 'asc' && $sortField === 'status' ? '↑' : '↓' }}</span>
                        </th>
                        <th class="px-6 py-3 font-semibold">Kemajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-900">
                    @foreach($productions as $production)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- ID Produk -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('produksi.edit', $production->id) }}">
                                {{ $production->production_number }}
                            </a>
                        </td>

                        <!-- Jadwal Produksi -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $production->start_date
                            ? \Carbon\Carbon::parse($production->start_date)->format('d-m-Y')
                            : '-' }}
                        </td>

                        <!-- Daftar Produk -->
                        <td class="px-6 py-4 max-w-xs truncate">
                            {{ $production->details->count() > 0
                            ? $production->details->map(fn($d) => $d->product?->name)->filter()->implode(', ')
                            : 'Tidak ada produk' }}
                        </td>

                        <!-- Pekerja -->
                        <td class="px-6 py-4 max-w-xs truncate">
                            {{ $production->workers->count() > 0
                            ? $production->workers->map(fn($w) => $w->worker?->name)->filter()->implode(', ')
                            : 'Tidak ada pekerja' }}
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ 
                                $production->status === 'selesai' ? 'bg-green-100 text-green-800' :
                                ($production->status === 'berjalan' ? 'bg-blue-100 text-blue-800' :
                                'bg-gray-100 text-gray-700') 
                            }}">
                                {{ ucfirst($production->status) }}
                            </span>
                        </td>

                        <!-- Kemajuan Produksi -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $total_plan = $production->details->sum('quantity_plan');
                            $total_done = $production->details->sum('quantity_get');
                            $progress = $total_plan > 0 ? ($total_done / $total_plan) * 100 : 0;
                            @endphp

                            <div class="flex flex-col gap-1">
                                <div class="w-full bg-gray-200 h-4 rounded-full overflow-hidden">
                                    <div class="h-4 bg-blue-600 rounded-full transition-all"
                                        style="width: {{ number_format($progress, 0) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ number_format($progress, 0) }}% ({{ $total_done }} dari {{ $total_plan }})
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $productions->links() }}
        </div>
    </div>
    @endif

</div>