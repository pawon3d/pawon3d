<div>
    <div class="mb-4 flex justify-between items-center">
        <div class="flex gap-2 items-center">
            <a href="{{ route('belanja.rincian', $expense->id) }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
                Kembali
            </a>
            <h1 class="text-2xl">Dapatkan Belanja</h1>
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
        <flux:icon icon="exclamation-triangle" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Lorem ipsum dolor sit amet consectetur. Sed pharetra netus gravida non curabitur fermentum etiam. Lorem
                orci auctor adipiscing vel blandit. In in integer viverra proin risus eu eleifend.
            </p>
        </div>
    </div>


    <div class="w-full mt-8 flex items-center flex-col gap-4">
        <div class="w-full flex items-center justify-between gap-4 flex-row">
            <h2 class="text-lg font-semibold">Daftar Belanja Persediaan</h2>
            <flux:button type="button" variant="primary" wire:click="markAllReceived">Tandai Didapatkan Semua
            </flux:button>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-6 py-3 text-nowrap">Barang Persediaan</th>
                        <th class="text-left px-6 py-3">Rencana Belanja</th>
                        <th class="text-left px-6 py-3">Selisih Jumlah</th>
                        <th class="text-left px-6 py-3">Jumlah Didapatkan</th>
                        <th class="text-left px-6 py-3">Satuan Ukur Belanja</th>
                        <th class="text-left px-6 py-3">Belanja Didapatkan</th>
                        <th class="text-left px-6 py-3">Tanggal Kadaluarsa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenseDetails as $index => $detail)
                        <tr>
                            <td class="px-6 py-3 text-nowrap">
                                <span class="text-sm">
                                    {{ $detail['material_name'] ?? 'Barang Tidak Ditemukan' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail['quantity_expect'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail['quantity_get'] - $detail['quantity_expect'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail['quantity_get'] ? $detail['quantity_get'] : 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail['unit'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <input type="number" placeholder="0"
                                    wire:model.number.live="expenseDetails.{{ $index }}.quantity"
                                    class="
                                {{ isset($errorInputs[$index]) ? 'border-red-500' : 'border-gray-300' }}
                                focus:outline-none focus:ring-0 rounded text-right" />
                                @if (isset($errorInputs[$index]))
                                    <span class="text-xs text-red-500 block">Nilai melebihi jumlah yang
                                        diharapkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <div x-init="picker = new Pikaday({
                                    field: $refs['datepicker{{ $index }}'],
                                    format: 'DD/MM/YYYY',
                                    toString(date, format) {
                                        const day = String(date.getDate()).padStart(2, 0);
                                        const month = String(date.getMonth() + 1).padStart(2, 0);
                                        const year = date.getFullYear();
                                        return `${day}/${month}/${year}`;
                                    },
                                    onSelect: function() {
                                        @this.set('expenseDetails.{{ $index }}.expiry_date', moment(this.getDate()).format('DD/MM/YYYY'));
                                    }
                                })" class="relative w-full">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <flux:icon icon="calendar-date-range" />
                                    </div>

                                    <input type="text"
                                        class="pr-10 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 w-full cursor-pointer"
                                        x-ref="datepicker{{ $index }}" id="datepicker{{ $index }}"
                                        wire:model.defer="expenseDetails.{{ $index }}.expiry_date"
                                        placeholder="dd/mm/yyyy" readonly />
                                </div>

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

        <div class="w-full flex items-start text-start space-x-2 gap-3 flex-col mt-4">
            <flux:heading class="text-lg font-semibold">Catatan Belanja</flux:heading>
            <flux:textarea rows="4" class="bg-gray-300" disabled>{{ $expense->note }}</flux:textarea>
        </div>

    </div>



    <div class="flex justify-end mt-16 gap-4">
        <a href="{{ route('belanja.rincian', $expense_id) }}"
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
                <h1 size="lg">Riwayat Pembaruan Daftar Belanja</h1>
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
