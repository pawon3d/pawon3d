<div>
    <div class="mb-[30px] flex items-center gap-[15px]">
        <a href="{{ route('belanja.rencana') }}"
            class="px-[25px] py-[10px] rounded-[15px] bg-[#313131] flex items-center gap-[5px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]"
            wire:navigate>
            <flux:icon.arrow-left class="size-5 text-[#f6f6f6]" />
            <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f6f6f6]">Kembali</span>
        </a>
        <h1 class="font-['Montserrat'] font-semibold text-[20px] text-[#666666]">Tambah Daftar Belanja</h1>
    </div>
    <x-alert.info>
        Tambah belanja persediaan. Lengkapi informasi yang diminta, pastikan informasi yang dimasukan benar dan tepat.
        Informasi akan digunakan untuk belanja persediaan sehingga dapat memperbarui harga produk produksi yang akan
        dijual.
    </x-alert.info>

    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] mt-[30px] flex gap-[130px]">
        <div class="flex-1 flex flex-col gap-[30px]">
            <div class="flex flex-col gap-[15px]">
                <h3 class="font-['Montserrat'] font-medium text-[18px] text-[#666666]">Pilih Toko Persediaan</h3>
                <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666] text-justify">
                    Pilih nama toko persediaan, seperti "Toko Mawar", "Toko Sarini", atau "Minimarket Emly".
                </p>
                <div class="bg-[#fafafa] border-[1.5px] border-[#adadad] rounded-[15px] px-[20px] py-[10px]">
                    <select wire:model="supplier_id"
                        class="w-full bg-transparent border-0 font-['Montserrat'] font-normal text-[16px] text-[#666666] focus:outline-none focus:ring-0">
                        <option value="">- Pilih Toko Persediaan -</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <flux:error name="supplier_id" />
            </div>
            <div class="flex flex-col gap-[15px]">
                <h3 class="font-['Montserrat'] font-medium text-[18px] text-[#666666]">Tanggal Belanja</h3>
                <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666] text-justify">
                    Masukkan tanggal belanja persediaan.
                </p>
                <div
                    class="bg-[#fafafa] border-[1.5px] border-[#adadad] rounded-[15px] px-[20px] py-[10px] flex items-center justify-between">
                    <input type="text"
                        class="bg-transparent border-0 font-['Montserrat'] font-normal text-[16px] text-[#666666] focus:outline-none focus:ring-0 w-full"
                        x-ref="datepicker" x-init="picker = new Pikaday({
                            field: $refs.datepicker,
                            format: 'DD MMM YYYY',
                            toString(date, format) {
                                const day = String(date.getDate()).padStart(2, 0);
                                const month = String(date.getMonth() + 1).padStart(2, 0);
                                const year = date.getFullYear();
                                return `${day}/${month}/${year}`;
                            },
                            onSelect: function() {
                                @this.set('expense_date', moment(this.getDate()).format('DD MMM YYYY'));
                            }
                        });" wire:model.defer="expense_date" id="datepicker"
                        readonly placeholder="01 Jun 2025" />
                    <flux:icon.calendar class="size-5 text-[#666666]" />
                </div>
                <flux:error name="expense_date" />
            </div>
        </div>

        <div class="flex-1 flex flex-col gap-[15px]">
            <h3 class="font-['Montserrat'] font-medium text-[18px] text-[#666666]">Catatan Rencana Belanja</h3>
            <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666] text-justify">
                Masukkan catatan rencana belanja apabila ada pesan atau sesuatu yang penting untuk diberitahu.
            </p>
            <textarea wire:model.defer="note" rows="5" placeholder="Masukkan catatan"
                class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px] font-['Montserrat'] font-normal text-[16px] text-[#666666] placeholder-[#959595] focus:outline-none focus:ring-0 resize-none"></textarea>
        </div>
    </div>


    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] mt-[30px] flex flex-col gap-[15px]">
        <div class="flex flex-col gap-[15px]">
            <h3 class="font-['Montserrat'] font-medium text-[18px] text-[#666666]">Daftar Belanja Persediaan</h3>
            <div class="flex items-center justify-between gap-[15px]">
                <p class="flex-1 font-['Montserrat'] font-normal text-[14px] text-[#666666] text-justify">
                    Tambahkan barang sesuai dengan kebutuhan operasional. Satuan belanja akan dikonversi menjadi satuan
                    persediaan terkini (satuan utama). Pastikan harga sesuai dengan harga beli. Harga akan manjadi acuan
                    modal dalam produksi.
                </p>
                <button type="button" wire:click="addDetail"
                    class="bg-[#74512d] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-[5px]">
                    <flux:icon.plus class="size-5 text-[#f6f6f6]" />
                    <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f6f6f6]">Tambah Belanja</span>
                </button>
            </div>
        </div>


        <div class="w-full overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#3f4e4f]">
                        <th
                            class="text-left px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1] min-w-[255px]">
                            Barang Persediaan</th>
                        <th
                            class="text-right px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1] max-w-[120px]">
                            <div>Jumlah</div>
                            <div>Diharapkan</div>
                        </th>
                        <th
                            class="text-right px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1] w-[130px]">
                            <div>Jumlah</div>
                            <div>Belanja</div>
                        </th>
                        <th
                            class="text-left px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1] w-[210px]">
                            <div>Satuan Ukur</div>
                            <div>Belanja</div>
                        </th>
                        <th
                            class="text-right px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1]">
                            Harga Satuan</th>
                        <th
                            class="text-right px-[25px] py-[21px] font-['Montserrat'] font-bold text-[14px] text-[#f8f4e1]">
                            Total Harga</th>
                        <th class="text-right px-[25px] py-[21px] w-[72px]"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expense_details as $index => $detail)
                        <tr class="bg-[#fafafa] border-b border-[#d4d4d4]">
                            <td class="px-[25px] py-0 h-[60px]">
                                <div class="flex items-center gap-[10px]">
                                    <select
                                        class="flex-1 bg-transparent border-0 font-['Montserrat'] font-medium text-[14px] text-[#666666] focus:outline-none focus:ring-0"
                                        wire:model="expense_details.{{ $index }}.material_id"
                                        wire:change="setMaterial({{ $index }}, $event.target.value)">
                                        <option value="">- Pilih Bahan Baku -</option>
                                        @foreach ($materials as $material)
                                            <option value="{{ $material->id }}">{{ $material->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td
                                class="px-[25px] py-0 h-[60px] text-right font-['Montserrat'] font-medium text-[14px] text-[#666666] max-w-[120px]">
                                {{ $detail['material_quantity'] }}
                            </td>
                            <td class="px-[25px] py-0 h-[60px] w-[130px]">
                                <input type="number" placeholder="0" min="0"
                                    wire:model.number.live="expense_details.{{ $index }}.quantity_expect"
                                    class="w-full bg-[#fafafa] border border-[#adadad] rounded-[5px] px-[10px] py-[6px] text-right font-['Montserrat'] font-medium text-[14px] text-[#666666] focus:outline-none focus:ring-0" />
                            </td>
                            <td class="px-[25px] py-0 h-[60px] w-[210px]">
                                <div class="flex items-center gap-[10px]">
                                    <select
                                        class="flex-1 bg-transparent border-0 font-['Montserrat'] font-medium text-[14px] text-[#666666] focus:outline-none focus:ring-0"
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
                                            <option value="{{ $unit->id }}">
                                                {{ $unit->name }} ({{ $unit->alias }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td class="px-[25px] py-1 h-[60px]">
                                <input type="number" placeholder="0" min="0"
                                    wire:model.number.live="expense_details.{{ $index }}.price_expect"
                                    class="w-full bg-[#fafafa] border border-[#adadad] rounded-[5px] px-[10px] py-[6px] text-right font-['Montserrat'] font-medium text-[14px] text-[#666666] focus:outline-none focus:ring-0" />
                                @if (isset($prevInputs[$index]))
                                    <div class="text-xs text-blue-500 mt-1">Harga Sebelumnya:
                                        Rp{{ number_format($prevPrice[$index], 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td
                                class="px-[25px] py-0 h-[60px] text-right font-['Montserrat'] font-medium text-[14px] text-[#666666]">
                                Rp{{ number_format($detail['detail_total_expect'], 0, ',', '.') }}
                            </td>
                            <td class="px-[25px] py-0 h-[60px] text-center w-[72px]">
                                <button type="button" wire:click.prevent="removeDetail({{ $index }})"
                                    class="inline-flex items-center justify-center">
                                    <flux:icon.trash class="size-[22px] text-[#666666]" />
                                </button>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
                <tfoot>
                    <tr class="bg-[#eaeaea] border-b border-[#d4d4d4]">
                        <td class="px-[25px] py-0 h-[60px] font-['Montserrat'] font-bold text-[14px] text-[#666666] rounded-bl-[15px]"
                            colspan="5">
                            Total
                        </td>
                        <td
                            class="px-[25px] py-0 h-[60px] text-right font-['Montserrat'] font-bold text-[14px] text-[#666666]">
                            Rp{{ number_format($grand_total_expect, 0, ',', '.') }}
                        </td>
                        <td class="px-[25px] py-0 h-[60px] rounded-br-[15px]"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>



    <div class="flex justify-end items-center gap-[30px] mt-[50px]">
        <a href="{{ route('belanja') }}"
            class="bg-[#c4c4c4] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-[5px]"
            wire:navigate>
            <flux:icon.x-mark class="size-5 text-[#333333]" />
            <span class="font-['Montserrat'] font-semibold text-[16px] text-[#333333]">Batal</span>
        </a>
        <button type="button" wire:click.prevent="store" wire:loading.attr="disabled"
            class="bg-[#3f4e4f] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-[5px] cursor-pointer">
            <flux:icon.archive-box class="size-5 text-[#f8f4e1]" />
            <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f8f4e1]">Buat Rencana Belanja</span>
        </button>
    </div>

</div>
