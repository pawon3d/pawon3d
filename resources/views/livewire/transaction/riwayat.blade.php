<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('transaksi') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white"
                wire:navigate>
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">Riwayat {{ $methodName }}</h1>
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
                ({{ $transactions->total() }})
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

    @if ($transactions->isEmpty())
    <div class="col-span-5 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center">
        <p class="text-gray-700 font-semibold">Belum ada transaksi.</p>
        <p class="text-gray-700">Tambah transaksi di menu utama.</p>
    </div>
    @else
    <div class="bg-white rounded-xl border shadow-sm">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 font-semibold">
                            ID Pesanan
                            <span class="cursor-pointer">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        </th>
                        <th class="px-6 py-3 font-semibold">Tanggal Pesanan</th>
                        <th class="px-6 py-3 font-semibold">Daftar Produk</th>
                        <th class="px-6 py-3 font-semibold">Pemesan</th>
                        <th class="px-6 py-3 font-semibold">Kasir</th>
                        <th class="px-6 py-3 font-semibold">Status Pembayaran</th>
                        <th class="px-6 py-3 font-semibold">Status Pesanan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-900">
                    @foreach($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- ID Produk -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('transaksi.rincian-pesanan', $transaction->id) }}">
                                {{ $transaction->invoice_number }}
                            </a>
                        </td>

                        <!-- Jadwal Produksi -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $transaction->start_date
                            ? \Carbon\Carbon::parse($transaction->start_date)->format('d-m-Y')
                            : '-' }}
                        </td>

                        <!-- Daftar Produk -->
                        <td class="px-6 py-4 max-w-xs truncate">
                            {{ $transaction->details->count() > 0
                            ? $transaction->details->map(fn($d) => $d->product?->name)->filter()->implode(', ')
                            : 'Tidak ada produk' }}
                        </td>

                        <!-- Pemesan -->
                        <td class="px-6 py-4 max-w-xs truncate">
                            {{ $transaction->name
                            ? $transaction->name
                            : '-' }}
                        </td>

                        <!-- Kasir -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                {{ ucfirst($transaction->user->name) }}
                            </span>
                        </td>

                        <!-- Status Pembayaran -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                {{ $transaction->payment_status }}
                            </span>
                        </td>

                        <!-- Status Pesanan -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                {{ $transaction->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $transactions->links() }}
        </div>
    </div>
    @endif

</div>