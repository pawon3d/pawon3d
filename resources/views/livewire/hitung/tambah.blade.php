<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('hitung') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
            Kembali
        </a>
        <h1 class="text-2xl">Tambah Aksi</h1>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Form ini digunakan untuk menambahkan aksi. Lengkapi informasi yang diminta, pastikan informasi yang
                dimasukan benar dan tepat.
                <span class="font-bold">Hitung Persediaan</span>
                untuk keakuratan jumlah barang secara fisik dengan sistem,
                <span class="font-bold">Catat Persediaan Rusak</span>
                untuk barang yang tidak layak konsumsi atau kadaluarsa, dan
                <span class="font-bold">Catat Persediaan Hilang</span>
                untuk barang yang jumlahnya tidak sesuai dengan jumlah barang secara fisik.
            </p>
        </div>
    </div>

    <div class="w-full flex md:flex-row flex-col gap-8 mt-4">
        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Pilih Aksi</flux:label>
            <p class="text-sm text-gray-500">
                Pilih aksi hitung atau hitung (catat rusak atau hilang) persediaan sesuai dengan kebutuhan.
            </p>
            <flux:select placeholder="- Pilih Aksi Persediaan -" wire:model="action">

                <flux:select.option value="Hitung Persediaan" class="text-gray-700">
                    Hitung Persediaan
                </flux:select.option>
                <flux:select.option value="Catat Persediaan Rusak" class="text-gray-700">
                    Catat Persediaan Rusak
                </flux:select.option>
                <flux:select.option value="Catat Persediaan Hilang" class="text-gray-700">
                    Catat Persediaan Hilang
                </flux:select.option>
            </flux:select>
            <flux:error name="action" />
            <flux:label>Tanggal Aksi</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan tanggal aksi persediaan.
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
                        @this.set('hitung_date', moment(this.getDate()).format('DD/MM/YYYY'));
                    }
                });" wire:model.defer="hitung_date" id="datepicker" readonly />
            <flux:error name="hitung_date" />
        </div>

        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Catatan Aksi</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan catatan aksi apabila ada pesan atau sesuatu yang penting untuk diberitahu.
            </p>
            <flux:textarea wire:model.defer="note" rows="7" placeholder="Ketik Catatan Aksi...." />
        </div>
    </div>


    <div class="w-full mt-8 flex items-center flex-col gap-4">
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <flux:label>Daftar Persediaan</flux:label>
        </div>
        <div class="w-full flex items-center justify-between gap-4 flex-row">
            <p class="text-sm text-gray-500">
                Tambah barang yang akan dilakukan hitung atau hitung, barang dihitung atau dihitung agar jumlah dan
                kondisi yang dimiliki secara fisik dan sistem sama.
            </p>
            <flux:button icon="plus" type="button" variant="primary" wire:click="addDetail">
                Tambah Barang
            </flux:button>
        </div>

        <flux:error name="hitung_details.*" />
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-6 py-3">Barang Persediaan</th>
                        <th class="text-left px-6 py-3">Satuan Ukur</th>
                        <th class="text-left px-6 py-3">Persediaan Terkini</th>
                        <th class="text-left px-6 py-3">Modal</th>
                        <th class="text-left px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hitung_details as $index => $detail)
                        <tr>
                            <td class="px-6 py-3">
                                <select class="w-full border-0 focus:outline-none focus:ring-0 rounded-none"
                                    wire:model="hitung_details.{{ $index }}.material_id"
                                    wire:change="setMaterial({{ $index }}, $event.target.value)">
                                    <option value="" class="text-gray-700">- Pilih Barang -</option>
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->id }}" class="text-gray-700">{{ $material->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-3">
                                <select class="w-full border-0 focus:outline-none focus:ring-0 rounded-none"
                                    wire:model="hitung_details.{{ $index }}.material_batch_id"
                                    wire:change="setBatch({{ $index }}, $event.target.value)">
                                    @php
                                        $material = $materials->firstWhere('id', $detail['material_id']);
                                        $batches = $material?->batches->filter(); // filter() untuk menghindari null jika unit tidak ditemukan
                                    @endphp
                                    <option value="" class="text-gray-700">- Pilih Batch -</option>
                                    @foreach ($batches ?? [] as $batch)
                                        <option value="{{ $batch->id }}" class="text-gray-700">
                                            {{ $batch->batch_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-3">
                                {{ $detail['material_quantity'] }} {{ $detail['unit_name'] }}
                            </td>
                            <td class="px-6 py-3">
                                Rp{{ number_format($detail['total'], 0, ',', '.') }}
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
                        <td class="px-6 py-3" colspan="3">
                            <span class="text-gray-700">Total</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                Rp{{ number_format($grand_total, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>



    <div class="flex justify-end mt-16 gap-4">
        <a href="{{ route('hitung') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
            <flux:icon.x-mark class="w-4 h-4 mr-2" />
            Batal
        </a>
        <flux:button icon="pencil-square" type="button" variant="primary" wire:click.prevent="store">Simpan Sebagai
            Draft
        </flux:button>
        <flux:button icon="shopping-cart" type="button" variant="primary" wire:click.prevent="start">Mulai Aksi
        </flux:button>
    </div>

</div>
