<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('belanja') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
            Kembali
        </a>
        <h1 class="text-2xl">Tambah Daftar Belanja</h1>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="exclamation-triangle" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Form ini digunakan untuk menambahkan belanja persediaan. Lengkapi informasi yang diminta, pastikan
                informasi yang dimasukan benar dan tepat. Informasi akan digunakan untuk menentukan harga barang baik
                sebagai bahan baku atau produk jual.
            </p>
        </div>
    </div>

    <div class="w-full flex md:flex-row flex-col gap-8 mt-4">
        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Pilih Toko Persediaan</flux:label>
            <p class="text-sm text-gray-500">
                Pilih nama toko persediaan, seperti “Toko Mawar”, “Toko Sarini”, atau “Minimarket Emly”.
            </p>
            <flux:select placeholder="- Pilih Toko Persediaan -" wire:model="supplier_id">
                @foreach ($suppliers as $supplier)
                    <flux:select.option value="{{ $supplier->id }}" class="text-gray-700">{{ $supplier->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="supplier_id" />
            <flux:label>Tanggal Belanja</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan tanggal belanja barang persediaan.
            </p>
            <input type="text"
                class="w-full border rounded-lg block disabled:shadow-none dark:shadow-none appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] pl-3 pr-3 bg-white dark:bg-white/10 dark:disabled:bg-white/[7%] text-zinc-700 disabled:text-zinc-500 placeholder-zinc-400 disabled:placeholder-zinc-400/70 dark:text-zinc-300 dark:disabled:text-zinc-400 dark:placeholder-zinc-400 dark:disabled:placeholder-zinc-500 shadow-xs border-zinc-200 border-b-zinc-300/80 disabled:border-b-zinc-200 dark:border-white/10 dark:disabled:border-white/5"
                x-ref="datepicker" x-init="picker = new Pikaday({
                    field: $refs.datepicker,
                    format: 'DD/MM/YYYY',
                    toString(date, format) {
                        const day = String(date.getDate()).padStart(2, 0);
                        const month = String(date.getMonth() + 1).padStart(2, 0);
                        const year = date.getFullYear();
                        return `${day}/${month}/${year}`;
                    },
                    onSelect: function() {
                        @this.set('expense_date', moment(this.getDate()).format('DD/MM/YYYY'));
                    }
                });" wire:model.defer="expense_date" id="datepicker" readonly />
            <flux:error name="expense_date" />
        </div>

        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Catatan Belanja</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan catatan belanja apabila ada pesan atau sesuatu yang penting untuk diberitahu.
            </p>
            <flux:textarea wire:model.defer="note" rows="7" placeholder="Ketik Catatan Belanja...." />
        </div>
    </div>


    <div class="w-full mt-8 flex items-center flex-col gap-4">
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <flux:label>Daftar Belanja Persediaan</flux:label>
        </div>
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <p class="text-sm text-gray-500">
                Tambah satuan lainya untuk mengubah satuan utama menjadi satuan lain yang lebih kecil ataupun lebih
                besar. Satuan lain digunakan untuk menentukan jumlah rinci saat menambahkan bahan ke dalam resep kue.
            </p>
            <flux:button icon="plus" type="button" variant="primary" wire:click="addDetail">Tambah Belanja
            </flux:button>
        </div>


        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-6 py-3">Barang Persediaan</th>
                        <th class="text-left px-6 py-3">Total Persediaan</th>
                        <th class="text-left px-6 py-3">Jumlah Belanja</th>
                        <th class="text-left px-6 py-3">Satuan Ukur Belanja</th>
                        <th class="text-left px-6 py-3">Harga / Satuan</th>
                        <th class="text-left px-6 py-3">Total Harga</th>
                        <th class="text-left px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expense_details as $index => $detail)
                        <tr>
                            <td class="px-6 py-3">
                                <select
                                    class="w-full border-gray-300 focus:border-b-blue-500 focus:outline-none focus:ring-0 rounded"
                                    wire:model="expense_details.{{ $index }}.material_id"
                                    wire:change="setMaterial({{ $index }}, $event.target.value)">
                                    <option value="" class="text-gray-700">- Pilih Bahan Baku -</option>
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->id }}" class="text-gray-700">{{ $material->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-3">
                                {{ $detail['material_quantity'] }}
                            </td>
                            <td class="px-6 py-3">
                                <input type="number" placeholder="0" min="0"
                                    wire:model.number.live="expense_details.{{ $index }}.quantity_expect"
                                    class="w-full border-gray-300 focus:border-b-blue-500 focus:outline-none focus:ring-0 rounded text-right" />
                            </td>
                            <td class="px-6 py-3">
                                <select
                                    class="border-gray-300 focus:border-b-blue-500 focus:outline-none focus:ring-0 rounded"
                                    wire:model="expense_details.{{ $index }}.unit_id"
                                    wire:change="setUnit({{ $index }}, $event.target.value)">
                                    @php
                                        $material = $materials->firstWhere('id', $detail['material_id']);
                                        $units = $material?->material_details
                                            ->map(function ($detail) {
                                                return $detail->unit; // pastikan ada relasi unit() di MaterialDetail
                                            })
                                            ->filter(); // filter() untuk menghindari null jika unit tidak ditemukan
                                    @endphp
                                    <option value="" class="text-gray-700">- Pilih Satuan Ukur -</option>
                                    @foreach ($units ?? [] as $unit)
                                        <option value="{{ $unit->id }}" class="text-gray-700">
                                            {{ $unit->name }} ({{ $unit->alias }})
                                        </option>
                                    @endforeach

                                </select>
                            </td>
                            <td class="px-6 py-3">
                                <input type="number" placeholder="0" min="0"
                                    wire:model.number.live="expense_details.{{ $index }}.price_expect"
                                    class="border-gray-300 focus:border-b-blue-500 focus:outline-none focus:ring-0 rounded text-right" />
                                @if (isset($prevInputs[$index]))
                                    <span class="text-xs text-blue-500 block">Harga Sebelumnya:
                                        Rp{{ number_format($prevPrice[$index], 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                Rp{{ number_format($detail['detail_total_expect'], 0, ',', '.') }}
                            </td>
                            <td class="flex items-center justify-start gap-4 px-6 py-3">
                                <flux:button icon="trash" type="button" variant="danger"
                                    wire:click.prevent="removeDetail({{ $index }})" />
                            </td>
                        </tr>
                    @endforeach

                </tbody>
                <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <td class="px-6 py-3" colspan="5">
                            <span class="text-gray-700">Total Harga Keseluruhan</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                Rp{{ number_format($grand_total_expect, 0, ',', '.') }}
                            </span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>



    <div class="flex justify-end mt-16 gap-4">
        <a href="{{ route('belanja') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
            <flux:icon.x-mark class="w-4 h-4 mr-2" />
            Batal
        </a>
        <flux:button icon="pencil-square" type="button" variant="primary" wire:click.prevent="store">Simpan Sebagai
            Draft
        </flux:button>
        <flux:button icon="shopping-cart" type="button" variant="primary" wire:click.prevent="start">Mulai Belanja
        </flux:button>
    </div>

</div>
