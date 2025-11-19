<div class="space-y-[30px]">
    <div class="flex items-center justify-between gap-[15px] flex-wrap">
        <a href="{{ route('belanja.rincian', $expense_id) }}" wire:navigate
            class="px-[25px] py-[10px] rounded-[15px] bg-[#313131] flex items-center gap-[5px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
            <flux:icon.arrow-left class="size-5 text-[#f6f6f6]" />
            <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f6f6f6]">Kembali</span>
        </a>
        <h1 class="font-['Montserrat'] font-semibold text-[20px] text-[#666666]">Ubah Daftar Belanja</h1>
    </div>

    <div
        class="bg-[#3f4e4f] rounded-[20px] px-[30px] py-[24px] flex items-start gap-[20px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
        <flux:icon icon="message-square-warning" class="size-[48px] text-[#f8f4e1]" />
        <p class="font-['Montserrat'] font-semibold text-[14px] text-[#dcd7c9] leading-[1.5]">
            Anda dapat mengubah informasi daftar belanja. Sesuaikan informasi jika terdapat perubahan, pastikan
            informasi
            yang dimasukan benar dan tepat. Informasi akan digunakan untuk belanja persediaan sehingga dapat memperbarui
            harga produk produksi yang akan dijual.
        </p>
    </div>

    <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px]">
        <div class="flex flex-col lg:flex-row gap-[30px]">
            <div class="flex-1 space-y-[15px]">
                <div class="space-y-[6px]">
                    <p class="font-['Montserrat'] font-medium text-[18px] text-[#666666]">Pilih Toko Persediaan</p>
                    <p class="font-['Montserrat'] text-[14px] text-[#666666]">
                        Pilih nama toko persediaan, seperti “Toko Mawar”, “Toko Sarini”, atau “Minimarket Emly”.
                    </p>
                </div>
                <div class="bg-[#fafafa] border border-[#adadad] rounded-[15px] px-[20px] py-[10px]">
                    <flux:select placeholder="- Pilih Toko Persediaan -" wire:model="supplier_id"
                        class="w-full !border-none !bg-transparent !p-0 !font-['Montserrat'] !text-[16px] !text-[#666666]">
                        @foreach ($suppliers as $supplier)
                            <flux:select.option value="{{ $supplier->id }}" class="text-[#666666]">
                                {{ $supplier->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <flux:error name="supplier_id" />

                <div class="space-y-[6px]">
                    <p class="font-['Montserrat'] font-medium text-[18px] text-[#666666]">Tanggal Belanja</p>
                    <p class="font-['Montserrat'] text-[14px] text-[#666666]">
                        Masukkan tanggal belanja persediaan.
                    </p>
                </div>
                <div class="relative">
                    <input type="text"
                        class="w-full bg-[#fafafa] border border-[#adadad] rounded-[15px] px-[20px] py-[10px] font-['Montserrat'] text-[16px] text-[#666666] placeholder-[#959595] focus:outline-none focus:ring-0"
                        x-ref="datepicker" x-init="picker = new Pikaday({
                            field: $refs.datepicker,
                            format: 'DD MMM YYYY',
                            toString(date, format) {
                                const day = String(date.getDate()).padStart(2, '0');
                                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                const month = months[date.getMonth()];
                                const year = date.getFullYear();
                                return `${day} ${month} ${year}`;
                            },
                            onSelect: function() {
                                @this.set('expense_date', moment(this.getDate()).format('DD MMM YYYY'));
                            }
                        });" wire:model.defer="expense_date" id="datepicker"
                        readonly />
                    <flux:icon icon="calendar-date-range"
                        class="absolute right-[20px] top-1/2 -translate-y-1/2 text-[#666666] size-5" />
                </div>
                <flux:error name="expense_date" />
            </div>
            <div class="flex-1 space-y-[15px]">
                <div class="space-y-[6px]">
                    <p class="font-['Montserrat'] font-medium text-[18px] text-[#666666]">Catatan Rencana Belanja</p>
                    <p class="font-['Montserrat'] text-[14px] text-[#666666]">
                        Masukkan catatan rencana belanja apabila ada pesan atau sesuatu yang penting untuk diberitahu.
                    </p>
                </div>
                <flux:textarea wire:model.defer="note" rows="7" placeholder="Masukkan catatan"
                    class="!bg-[#fafafa] !border-[#d4d4d4] !rounded-[15px] !px-[20px] !py-[10px] !font-['Montserrat'] !text-[16px] !text-[#666666]" />
            </div>
        </div>
    </div>

    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] space-y-[20px]">
        <div class="space-y-[10px]">
            <div class="flex items-center justify-between gap-[15px] flex-wrap">
                <p class="font-['Montserrat'] font-medium text-[18px] text-[#666666]">Daftar Belanja Persediaan</p>
                <button type="button" wire:click="addDetail"
                    class="px-[25px] py-[10px] rounded-[15px] bg-[#74512d] text-[#f6f6f6] font-['Montserrat'] font-semibold text-[16px] flex items-center gap-[8px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
                    <flux:icon icon="plus" class="size-5" />
                    Tambah Belanja
                </button>
            </div>
            <p class="font-['Montserrat'] text-[14px] text-[#666666]">
                Tambahkan barang sesuai kebutuhan operasional. Satuan belanja akan dikonversi menjadi satuan persediaan
                terkini (satuan utama). Pastikan harga sesuai dengan harga beli. Harga akan menjadi acuan modal dalam
                produksi.
            </p>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#3f4e4f] text-left">
                        <th
                            class="px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1] min-w-[220px]">
                            Barang Persediaan
                        </th>
                        <th
                            class="px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1] text-right min-w-[130px]">
                            Jumlah Terkini
                        </th>
                        <th
                            class="px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1] text-right min-w-[130px]">
                            Jumlah Belanja
                        </th>
                        <th
                            class="px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1] min-w-[200px]">
                            Satuan Ukur Belanja
                        </th>
                        <th
                            class="px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1] text-right min-w-[150px]">
                            Harga Satuan
                        </th>
                        <th
                            class="px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1] text-right min-w-[150px]">
                            Total Harga
                        </th>
                        <th class="px-[25px] py-[21px]">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expense_details as $index => $detail)
                        <tr class="border-b border-[#d4d4d4]">
                            <td class="px-[25px] py-[18px]">
                                <select
                                    class="w-full bg-[#fafafa] border border-[#adadad] rounded-[10px] px-[15px] py-[8px] font-['Montserrat'] text-[14px] text-[#666666] focus:outline-none focus:ring-0"
                                    wire:model="expense_details.{{ $index }}.material_id"
                                    wire:change="setMaterial({{ $index }}, $event.target.value)">
                                    <option value="">- Pilih Bahan Baku -</option>
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->id }}">{{ $material->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-[25px] py-[18px] text-right">
                                <p class="font-['Montserrat'] text-[14px] text-[#666666]">
                                    {{ $detail['material_quantity'] }}</p>
                            </td>
                            <td class="px-[25px] py-[18px] text-right">
                                <input type="number" placeholder="0" min="0"
                                    wire:model.number.live="expense_details.{{ $index }}.quantity_expect"
                                    class="w-full bg-[#fafafa] border border-[#adadad] rounded-[10px] px-[15px] py-[6px] text-right font-['Montserrat'] text-[14px] text-[#666666] focus:outline-none focus:ring-0" />
                            </td>
                            <td class="px-[25px] py-[18px]">
                                <select
                                    class="w-full bg-[#fafafa] border border-[#adadad] rounded-[10px] px-[15px] py-[8px] font-['Montserrat'] text-[14px] text-[#666666] focus:outline-none focus:ring-0"
                                    wire:model="expense_details.{{ $index }}.unit_id"
                                    wire:change="setUnit({{ $index }}, $event.target.value)">
                                    @php
                                        $material = $materials->firstWhere('id', $detail['material_id']);
                                        $units = $material?->material_details
                                            ->map(function ($detail) {
                                                return $detail->unit;
                                            })
                                            ->filter();
                                    @endphp
                                    <option value="">- Pilih Satuan Ukur -</option>
                                    @foreach ($units ?? [] as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->alias }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-[25px] py-[18px] text-right">
                                <div class="space-y-1">
                                    <input type="number" placeholder="0" min="0"
                                        wire:model.number.live="expense_details.{{ $index }}.price_expect"
                                        class="w-full bg-[#fafafa] border border-[#adadad] rounded-[10px] px-[15px] py-[6px] text-right font-['Montserrat'] text-[14px] text-[#666666] focus:outline-none focus:ring-0" />
                                    @if (isset($prevInputs[$index]))
                                        <span class="block text-xs text-[#3f4e4f] font-['Montserrat']">Harga Sebelumnya:
                                            Rp{{ number_format($prevPrice[$index], 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-[25px] py-[18px] text-right">
                                <p class="font-['Montserrat'] text-[14px] text-[#666666]">
                                    Rp{{ number_format($detail['detail_total_expect'], 0, ',', '.') }}
                                </p>
                            </td>
                            <td class="px-[25px] py-[18px] text-right">
                                <button type="button" wire:click.prevent="removeDetail({{ $index }})"
                                    class="text-[#666666]">
                                    <flux:icon icon="trash" class="size-5" />
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-[#eaeaea]">
                        <td class="px-[25px] py-[18px] font-['Montserrat'] font-bold text-[14px] text-[#666666]"
                            colspan="5">
                            Total Harga Keseluruhan
                        </td>
                        <td
                            class="px-[25px] py-[18px] font-['Montserrat'] font-bold text-[14px] text-[#666666] text-right">
                            Rp{{ number_format($grand_total_expect, 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="flex justify-end gap-[15px] flex-wrap">
        <a href="{{ route('belanja.rincian', $expense_id) }}" wire:navigate
            class="px-[25px] py-[10px] rounded-[15px] bg-[#c4c4c4] flex items-center gap-[5px] font-['Montserrat'] font-semibold text-[16px] text-[#333333] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
            <flux:icon.x-mark class="size-5" />
            Batal
        </a>
        <button type="button" wire:click.prevent="update"
            class="px-[25px] py-[10px] rounded-[15px] bg-[#3f4e4f] flex items-center gap-[5px] font-['Montserrat'] font-semibold text-[16px] text-[#f6f6f6] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
            <flux:icon icon="save" class="size-5" />
            Simpan Perubahan
        </button>
    </div>

</div>
