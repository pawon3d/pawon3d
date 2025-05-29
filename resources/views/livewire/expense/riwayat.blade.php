<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center">
            <a href="{{ route('belanja') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white"
                wire:navigate>
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">Riwayat Belanja Persediaan</h1>
        </div>
        <div class="flex gap-2 items-center justify-end-safe">
            <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
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
        <p class="text-gray-700 font-semibold">Belum Ada Riwayat Belanja.</p>
        <p class="text-gray-700">Tekan tombol “Tambah belanja” di halaman utama untuk menambahkan belanja.</p>
    </div>
    @else
    <div class="bg-white rounded-xl border">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nomor Belanja
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Belanja
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Toko Persediaan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Barang Didapatkan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Harga (Perkiraan)
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Harga (Sebenarnya)
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
                            <div class="flex items-center space-x-2 flex-col">
                                <div class="w-full h-4 mb-4 bg-gray-200 rounded-full dark:bg-gray-700">
                                    {{-- styling persentase barang didapatkan secara dinamis, hitung persen dari
                                    expenseDetails yang is_quantity_get true dibandingkan dengan total expenseDetails
                                    untuk expense_id ini --}}
                                    <div class="h-4 bg-blue-600 rounded-full dark:bg-blue-500"
                                        style="width: {{ number_format($expense->expenseDetails->where('is_quantity_get', true)->count() / $expense->expenseDetails->count() * 100, 0) }}%">
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{-- hitung berapa banyak detail yang is_quantity_get nya sudah true lalu
                                    dibandingkan dengan total detail untuk expense_id ini. contoh 2 dari 20 --}}
                                    {{ $expense->expenseDetails->where('is_quantity_get', true)->count() ?? '0' }} dari
                                    {{ $expense->expenseDetails->count() ?? '0' }} barang
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-left whitespace-nowrap">
                            Rp{{ number_format($expense->total_expect, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-left space-x-2 whitespace-nowrap">
                            Rp{{ number_format($expense->total_actual, 0, ',', '.') }}
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

</div>