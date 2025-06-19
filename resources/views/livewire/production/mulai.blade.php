<div>
    <div class="mb-4 flex justify-between items-center">
        <div class="flex gap-2 items-center">
            <a href="{{ route('produksi.rincian', $production_id) }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
                Kembali
            </a>
            <h1 class="text-2xl">Dapatkan Hasil Produksi</h1>
        </div>
        <div class="flex gap-2 items-center">
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
                Lorem ipsum dolor sit amet consectetur. Sed pharetra netus gravida non curabitur fermentum etiam. Lorem
                orci auctor adipiscing vel blandit. In in integer viverra proin risus eu eleifend.
            </p>
        </div>
    </div>


    <div class="w-full mt-8 flex items-center flex-col gap-4">
        <div class="w-full flex items-center justify-between gap-4 flex-row">
            <h2 class="text-lg font-semibold">Daftar Produk</h2>
            <flux:button type="button" variant="primary" wire:click="markAllReceived">
                Tandai Didapatkan Semua
            </flux:button>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-6 py-3 text-nowrap">Produk</th>
                        <th class="text-right px-6 py-3">Rencana Produksi</th>
                        <th class="text-right px-6 py-3">
                            Jumlah Didapatkan
                        </th>
                        <th class="text-right px-6 py-3">
                            Resep atau Barang (Hasil)
                        </th>
                        <th class="text-right px-6 py-3">Unit Gagal</th>
                        <th class="text-right px-6 py-3">
                            Jumlah Ulang
                        </th>
                        <th class="text-right px-6 py-3">
                            Tandai Ulang
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($production_details as $index => $detail)
                        <tr x-data="{ selectedProducts: @entangle('selectedProducts') }">
                            <td class="px-6 py-3 text-nowrap">
                                <span class="text-sm">
                                    {{ $detail['product_name'] ?? 'Produk Tidak Ditemukan' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="text-sm">
                                    {{ $detail['quantity_plan'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="text-sm">
                                    {{ $detail['quantity_get'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right flex flex-row items-center gap-2">
                                <input type="text"
                                    wire:model.lazy="production_details.{{ $index }}.recipe_quantity"
                                    class="w-full border-gray-300 rounded focus:outline-none focus:ring-0 text-right" />
                                ({{ $detail['quantity'] }})
                            </td>
                            <td class="px-6 py-3 text-right">
                                <input type="number" placeholder="0"
                                    wire:model.number="production_details.{{ $index }}.quantity_fail"
                                    class="w-full border-gray-300 rounded focus:outline-none focus:ring-0 text-right" />
                            </td>
                            <td class="px-6 py-3 text-right">
                                {{ $detail['cycle'] }}
                            </td>
                            <td class="px-6 py-3 text-right">
                                <input type="checkbox" :value="'{{ $detail['id'] }}'" x-model="selectedProducts"
                                    {{ $detail['quantity_fail_raw'] == 0 ? 'disabled' : '' }}
                                    class="
                                form-checkbox h-4 w-4 text-black border-0 rounded-full focus:ring-0 focus:ring-offset-0 not-checked:bg-gray-400">
                                <label for="selectedProducts" class="sr-only">Pilih Produk</label>
                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>
    </div>



    <div class="flex justify-end mt-16 gap-4">
        <a href="{{ route('produksi.rincian', $production_id) }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
            <flux:icon.x-mark class="w-4 h-4 mr-2" />
            Batal
        </a>
        <flux:button icon="archive-box" type="button" variant="primary" wire:click="save">
            Simpan
        </flux:button>
    </div>




    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <h1 size="lg">Riwayat Pembaruan {{ $production->production_number }}</h1>
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
