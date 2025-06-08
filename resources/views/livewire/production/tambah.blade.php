<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('produksi') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
            Kembali
        </a>
        <h1 class="text-2xl">Tambah Produksi</h1>
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
            <flux:label>Pilih Pekerja</flux:label>
            <p class="text-sm text-gray-500">
                Pilih Pekerja produksi, produksi akan dimulai dan dilakukan oleh Pekerja.
            </p>

            <select class="js-example-basic-multiple" wire:model.live="user_ids" multiple="multiple">
                @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <flux:error name="user_ids" />
            <flux:label>Jadwal Produksi</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan tanggal produksi.
            </p>

            <input type="text"
                class="w-full border rounded-lg block disabled:shadow-none dark:shadow-none appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] pl-3 pr-3 bg-white dark:bg-white/10 dark:disabled:bg-white/[7%] text-zinc-700 disabled:text-zinc-500 placeholder-zinc-400 disabled:placeholder-zinc-400/70 dark:text-zinc-300 dark:disabled:text-zinc-400 dark:placeholder-zinc-400 dark:disabled:placeholder-zinc-500 shadow-xs border-zinc-200 border-b-zinc-300/80 disabled:border-b-zinc-200 dark:border-white/10 dark:disabled:border-white/5"
                x-ref="datepicker" x-init="
                        picker = new Pikaday({
                        field: $refs.datepicker,
                        format: 'DD-MM-YYYY',
                        toString(date, format) {
                            const day = String(date.getDate()).padStart(2, 0);
                            const month = String(date.getMonth() + 1).padStart(2, 0);
                            const year = date.getFullYear();
                            return `${day}-${month}-${year}`;
                        },
                        onSelect: function() {
                            @this.set('start_date', moment(this.getDate()).format('DD-MM-YYYY'));
                            }
                        });
                        " wire:model.defer="start_date" id="datepicker" readonly />

            <flux:error name="start_date" />

        </div>


        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Catatan Produksi</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan catatan produksi apabila ada pesan atau sesuatu yang penting untuk diberitahu.
            </p>
            <flux:textarea wire:model.defer="note" rows="7" placeholder="Ketik Catatan Produksi...." />
        </div>
    </div>

    <div class="w-full mt-8 flex items-center flex-col gap-4">
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <flux:label>Daftar Produk</flux:label>
        </div>
        <div class="w-full flex items-center justify-between gap-4 flex-row">
            <p class="text-sm text-gray-500">
                Tambah produk sesuai dengan kebutuhan operasional.
            </p>
            <flux:button icon="plus" type="button" variant="primary" wire:click="addProduct">
                Tambah Produk
            </flux:button>
        </div>

        <flux:error name="production_details.*" />
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-6 py-3">Produk</th>
                        <th class="text-left px-6 py-3">Jumlah Terkini</th>
                        <th class="text-left px-6 py-3">Jumlah Disarankan</th>
                        <th class="text-left px-6 py-3">Rencana Produksi</th>
                        <th class="text-left px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($production_details as $index => $detail)
                    <tr>
                        <td class="px-6 py-3">
                            <select
                                class="w-full border-0 border-b border-b-gray-300 focus:border-b-blue-500 focus:outline-none focus:ring-0 rounded-none"
                                wire:model="production_details.{{ $index }}.product_id">
                                <option value="" class="text-gray-700">- Pilih Barang -</option>
                                @foreach ($products as $product)
                                @php
                                $pcs = $product->pcs > 1 ? ' (' . $product->pcs . ' pcs)' : '';
                                @endphp
                                <option value="{{ $product->id }}" class="text-gray-700">{{
                                    $product->name }} {{ $pcs }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <span class="text-gray-700">
                                {{ $detail['current_stock'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            {{ $detail['suggested_amount'] }}
                        </td>
                        <td class="px-6 py-3">
                            <input type="number" placeholder="0"
                                wire:model.number.live="production_details.{{ $index }}.quantity_plan"
                                class="w-full border-0 border-b focus:outline-none focus:ring-0 rounded-none text-right" />
                        </td>
                        <td class="flex items-center justify-start gap-4 px-6 py-3">
                            <flux:button icon="trash" type="button" variant="danger"
                                wire:click.prevent="removeProduct({{ $index }})" />
                        </td>
                    </tr>
                    @endforeach

                </tbody>
                <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">Total</span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <span class="text-gray-700">
                                {{ $current_stock_total }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <span class="text-gray-700">
                                {{ $suggested_amount_total }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <span class="text-gray-700">
                                {{ $quantity_plan_total }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <!-- Empty cell for alignment -->
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>



    <div class="flex justify-end mt-16 gap-4">
        <a href="{{ route('produksi') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
            <flux:icon.x-mark class="w-4 h-4 mr-2" />
            Batal
        </a>
        <flux:button icon="pencil-square" type="button" variant="primary" wire:click.prevent="store">Simpan Sebagai
            Draft
        </flux:button>
        <flux:button icon="dessert" type="button" variant="primary" wire:click.prevent="start">Mulai Produksi
        </flux:button>
    </div>


    @script
    <script type="text/javascript">
        document.addEventListener('livewire:initialized', function() {
            function loadJavascript(){
                $('.js-example-basic-multiple').select2({
                    placeholder: "Cari Pekerja...",
                    width: '100%',
                }).on("change", function() {
                    $wire.set("user_ids", $(this).val());
                });
            }
            loadJavascript();

            Livewire.hook("morphed", () => {
                loadJavascript();
            })
        });
    </script>
    @endscript
</div>