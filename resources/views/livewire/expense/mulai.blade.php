<div class="space-y-[30px]">
    <div class="flex flex-col gap-[15px]">
        <div class="flex items-center justify-between gap-[15px] flex-wrap">
            <div class="flex items-center gap-[15px]">
                <flux:button type="button" variant="secondary" icon="arrow-left"
                    href="{{ route('belanja.rincian', $expense->id) }}" wire:navigate>
                    Kembali
                </flux:button>
                <h1 class="font-['Montserrat'] font-semibold text-[20px] text-[#666666]">Dapatkan Belanja</h1>
            </div>
        </div>
        <x-alert.info>
            <p class="font-['Montserrat'] font-semibold text-[14px] text-[#dcd7c9] mb-1">Dapatkan Belanja. Masukkan
                jumlah
                belanja yang selesai secara bertahap.</p>
            <ul class="list-disc ms-[18px] font-['Montserrat'] text-[14px] text-[#dcd7c9] space-y-[6px]">
                <li>Jika terjadi kesalahan dalam memasukkan jumlah, masukkan jumlah pengurangan dengan tanda minus (-)
                </li>
                <li>Sertakan juga tanggal expired untuk melengkapi informasi persediaan.</li>
            </ul>
        </x-alert.info>
    </div>

    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] space-y-[20px]">
        <div class="flex items-center justify-between gap-[15px] flex-wrap">
            <h2 class="font-['Montserrat'] font-medium text-[18px] text-[#666666]">Daftar Belanja Persediaan</h2>
            <flux:button type="button" wire:click="markAllReceived" variant="primary">
                Tandai Didapatkan Semua
            </flux:button>
        </div>

        <x-table.form :headers="[
            ['label' => 'Barang Persediaan', 'class' => 'text-left px-[25px] py-[21px] min-w-[200px]'],
            ['label' => 'Rencana Belanja', 'class' => 'text-right px-[25px] py-[21px] min-w-[120px]'],
            ['label' => 'Selisih Didapatkan', 'class' => 'text-right px-[25px] py-[21px] min-w-[120px]'],
            ['label' => 'Jumlah Didapatkan', 'class' => 'text-right px-[25px] py-[21px] min-w-[120px]'],
            ['label' => 'Satuan Ukur Belanja', 'class' => 'text-left px-[25px] py-[21px] min-w-[160px]'],
            ['label' => 'Belanja Didapatkan', 'class' => 'text-right px-[25px] py-[21px] min-w-[150px]'],
            ['label' => 'Tanggal Expired', 'class' => 'text-left px-[25px] py-[21px] min-w-[200px]'],
        ]" header-bg="bg-[#3f4e4f]" header-text="text-[#f8f4e1]" body-bg="bg-[#fafafa]"
            body-text="text-[#666666]" empty-message="Belum ada barang belanja.">
            <x-slot:rows>
                @foreach ($expenseDetails as $index => $detail)
                    <tr class="border-b border-[#d4d4d4] bg-[#fafafa]">
                        <td class="px-[25px] py-[18px] align-top">
                            <p class="font-['Montserrat'] font-medium text-[14px] text-[#666666]">
                                {{ $detail['material_name'] ?? 'Barang Tidak Ditemukan' }}
                            </p>
                        </td>
                        <td class="px-[25px] py-[18px] text-right align-top">
                            <p class="font-['Montserrat'] font-medium text-[14px] text-[#666666]">
                                {{ $detail['quantity_expect'] }}
                            </p>
                        </td>
                        <td class="px-[25px] py-[18px] text-right align-top">
                            <p class="font-['Montserrat'] font-medium text-[14px] text-[#666666]">
                                {{ ($detail['quantity_get'] ?? 0) - $detail['quantity_expect'] }}
                            </p>
                        </td>
                        <td class="px-[25px] py-[18px] text-right align-top">
                            <p class="font-['Montserrat'] font-medium text-[14px] text-[#666666]">
                                {{ $detail['quantity_get'] ?? 0 }}
                            </p>
                        </td>
                        <td class="px-[25px] py-[18px] align-top">
                            <p class="font-['Montserrat'] font-medium text-[14px] text-[#666666]">
                                {{ $detail['unit'] }}
                            </p>
                        </td>
                        <td class="px-[25px] py-[18px] align-top">
                            <div class="space-y-1">
                                <input type="number" placeholder="0" min="0"
                                    wire:model.number.live="expenseDetails.{{ $index }}.quantity"
                                    class="w-full bg-[#fafafa] border {{ isset($errorInputs[$index]) ? 'border-red-500' : 'border-[#adadad]' }} rounded-[5px] px-[10px] py-[6px] text-right font-['Montserrat'] font-medium text-[14px] text-[#666666] focus:outline-none focus:ring-0" />
                                @if (isset($errorInputs[$index]))
                                    <span class="text-xs text-red-500 font-['Montserrat']">Nilai melebihi jumlah
                                        yang
                                        diharapkan</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-[25px] py-[18px] align-top">
                            <div x-data x-init="picker = new Pikaday({
                                field: $refs['datepicker{{ $index }}'],
                                format: 'DD MMM YYYY',
                                toString(date, format) {
                                    return moment(date).format('DD MMM YYYY');
                                },
                                parse(dateString, format) {
                                    return moment(dateString, 'DD MMM YYYY').toDate();
                                },
                                onSelect: function() {
                                    @this.set('expenseDetails.{{ $index }}.expiry_date', moment(this.getDate()).format('DD MMM YYYY'));
                                }
                            })" class="relative w-full">
                                <input type="text" x-ref="datepicker{{ $index }}"
                                    id="datepicker{{ $index }}"
                                    wire:model.defer="expenseDetails.{{ $index }}.expiry_date"
                                    placeholder="dd/mm/yyyy" readonly
                                    class="w-full bg-[#fafafa] border border-[#d4d4d4] rounded-[5px] px-[15px] py-[7px] font-['Montserrat'] text-[14px] text-[#666666] placeholder-[#959595] focus:outline-none focus:ring-0 cursor-pointer" />
                                <flux:icon icon="calendar-date-range"
                                    class="absolute right-[15px] top-1/2 -translate-y-1/2 text-[#666666] size-4" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-slot:rows>
        </x-table.form>
    </div>

    <div class="flex justify-end gap-[15px] flex-wrap">
        <flux:button type="button" variant="filled" icon="x-mark" href="{{ route('belanja.rincian', $expense_id) }}"
            wire:navigate>
            Batal
        </flux:button>
        <flux:button type="button" wire:click="save" variant="secondary" icon="save">
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
