<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Daftar Belanja Persediaan</h1>
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
                Belanja Persediaan dapat dilakukan apabila telah menambahkan daftar toko dan daftar barang persediaan.
                Belanja Persediaan digunakan untuk menentukan harga dari barang persediaan, sehingga harga jual suatu
                produk produksi dapat ditentukan dengan tepat. Belanja dapat dilakukan dengan mendatangi langsung toko
                untuk belanja atau dapat dilakukan lewat telepon atau whatsapp kepada toko.
            </p>
        </div>
    </div>

    <div class="flex justify-between items-center mb-2">
        <!-- Search Input -->
        <div class="p-4 flex">
            <input wire:model.live="search" placeholder="Cari..."
                class="w-lg px-4 py-2 border border-accent rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <flux:button :loading="false" class="ml-2" variant="ghost">
                <flux:icon.funnel variant="mini" />
                <span>Filter</span>
            </flux:button>
        </div>
        <div class="p-4 flex gap-4">
            <div class="flex gap-2 items-center">
                <a href="{{ route('belanja.riwayat') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150"
                    wire:navigate>
                    <flux:icon.history class="mr-2" />
                    Riwayat Belanja
                </a>
            </div>
            <div class="flex gap-2 items-center">
                <a href="{{ route('belanja.tambah') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150"
                    wire:navigate>
                    <flux:icon.plus class="mr-2" />
                    Tambah Belanja
                </a>
            </div>
        </div>
    </div>
    <div class="flex justify-between items-center mb-3">
        <div class="p-4 flex">
            <flux:dropdown>
                <flux:button variant="ghost">
                    @if($filterStatus)
                    {{ $filterStatus === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                    @else
                    Semua Toko
                    @endif
                    ({{ $expenses->total() }})
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

    @if ($expenses->isEmpty())
    <div class="col-span-7 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center">
        <p class="text-gray-700 font-semibold">Belum Ada Barang.</p>
        <p class="text-gray-700">Tekan tombol “Tambah Barang” untuk menambahkan barang.</p>
    </div>
    @else
    <div class="bg-white rounded-xl border">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('expense_number')">
                            Nomor Belanja
                            {{ $sortDirection === 'asc' && $sortField === 'expense_number' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('expense_date')">
                            Tanggal Belanja
                            {{ $sortDirection === 'asc' && $sortField === 'expense_date' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('supplier_name')">
                            Toko Persediaan
                            {{ $sortDirection === 'asc' && $sortField === 'supplier_name' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('status')">
                            Status
                            {{ $sortDirection === 'asc' && $sortField === 'status' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Barang Didapatkan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('grand_total_expect')">
                            Total Harga (Perkiraan)
                            {{ $sortDirection === 'asc' && $sortField === 'grand_total_expect' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('grand_total_actual')">
                            Total Harga (Sebenarnya)
                            {{ $sortDirection === 'asc' && $sortField === 'grand_total_actual' ? '↑' : '↓' }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($expenses as $expense)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('belanja.rincian', $expense->id) }}"
                                class="hover:bg-gray-50 cursor-pointer">
                                {{ $expense->expense_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y')
                            :
                            '-' }}
                        </td>
                        <td class="px-6 py-4 text-left whitespace-nowrap">
                            {{ $expense->supplier->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-left whitespace-nowrap">
                            {{ $expense->status ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-left whitespace-nowrap">
                            {{-- <div class="flex items-center space-x-2 flex-col">
                                <div class="w-full h-4 mb-4 bg-gray-200 rounded-full dark:bg-gray-700">
                                    <div class="h-4 bg-blue-600 rounded-full dark:bg-blue-500"
                                        style="width: {{ number_format($expense->expenseDetails->where('is_quantity_get', true)->count() / $expense->expenseDetails->count() * 100, 0) }}%">
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ $expense->expenseDetails->where('is_quantity_get', true)->count() ?? '0' }} dari
                                    {{ $expense->expenseDetails->count() ?? '0' }} barang
                                </span>
                            </div> --}}
                            @php
                            $total_expect = $expense->expenseDetails->sum('quantity_expect');
                            $total_get = $expense->expenseDetails->sum('quantity_get');
                            $percentage = $total_expect > 0 ? ($total_get / $total_expect) * 100 : 0;
                            @endphp

                            <div class="flex items-center space-x-2 flex-col">
                                <div class="w-full h-4 mb-4 bg-gray-200 rounded-full dark:bg-gray-700">
                                    <div class="h-4 bg-blue-600 rounded-full dark:bg-blue-500"
                                        style="width: {{ number_format($percentage, 0) }}%">
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ number_format($percentage, 0) }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-left whitespace-nowrap">
                            Rp{{ number_format($expense->grand_total_expect, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-left space-x-2 whitespace-nowrap">
                            Rp{{ number_format($expense->grand_total_actual, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $expenses->links() }}
        </div>
    </div>
    @endif

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Barang Persediaan</flux:heading>
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