<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold text-gray-600">Daftar Pelanggan</h1>
    </div>

    <x-alert.info>
        Pelanggan. Pelanggan adalah pembeli atau pemesan yang telah didaftarkan sebagai pelanggan, sehingga memiliki
        hak untuk mendapatkan dan menggunakan poin. Poin dapat berasal dari transaksi pembayaran maupun dari kegiatan
        lain yang disediakan.
    </x-alert.info>

    <div class="mt-4 bg-[#fafafa] shadow-md rounded-[15px] p-6 overflow-hidden">
        {{-- Search and Add Button --}}
        <div class="flex items-center justify-between gap-4 mb-6">
            <div
                class="flex-1 bg-white border border-gray-500 rounded-full flex items-center px-4 py-0 focus-within:ring-2 focus-within:ring-blue-500">
                <flux:icon icon="magnifying-glass" class="size-5 text-gray-500" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Pelanggan"
                    class="flex-1 px-3 py-2 border-0 focus:outline-none focus:ring-0 bg-transparent text-gray-600 placeholder-gray-400" />
            </div>
            <flux:button icon="plus" variant="primary" wire:click="showModalTambah" type="button">
                Tambah Pelanggan
            </flux:button>
        </div>

        {{-- Table --}}
        <x-table.paginated :paginator="$customers" :headers="[
            ['label' => 'No. Telepon'],
            ['label' => 'Nama Pelanggan', 'sortable' => true, 'sort-by' => 'name'],
            ['label' => 'Transaksi Terbaru'],
            ['label' => 'Total Transaksi', 'sortable' => true, 'sort-by' => 'totalTransaction', 'align' => 'right'],
            ['label' => 'Saldo Poin', 'sortable' => true, 'sort-by' => 'points', 'align' => 'right'],
        ]" headerBg="#3F4E4F" headerText="#F8F4E1"
            emptyMessage="Tidak ada pelanggan yang tersedia.">
            @foreach ($customers as $customer)
                <tr onclick="window.location='{{ route('customer.show', $customer->id) }}'"
                    class="border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors duration-200">
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $customer->phone }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $customer->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if ($customer->transactions()->latest()->first())
                            {{ \Carbon\Carbon::parse($customer->transactions()->latest()->first()->created_at)->translatedFormat('d M Y') }}
                            {{ \Carbon\Carbon::parse($customer->transactions()->latest()->first()->created_at)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 text-right">
                        {{ $customer->transactions()->count() ?? 0 }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 text-right">
                        {{ $customer->points ?? 0 }}
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Pelanggan</flux:heading>
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

    <flux:modal name="tambah-customer" class="w-full max-w-md" wire:model="customerModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Pelanggan</flux:heading>
            </div>
            <div class="space-y-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <input type="text" id="phone" wire:model="phone" placeholder="Contoh: 08123456789"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required />
                    @error('phone')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="name" wire:model="name" placeholder="Contoh: Fani"
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
                <flux:button type="button" icon="save" variant="primary" wire:click="addCustomer">Simpan
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
