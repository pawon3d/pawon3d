<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('produksi') }}" wire:navigate
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
            Kembali
        </a>
        <h1 class="text-2xl">Tambah Produksi</h1>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Form ini digunakan untuk menambahkan belanja persediaan. Lengkapi informasi yang diminta, pastikan
                informasi yang dimasukan benar dan tepat. Informasi akan digunakan untuk menentukan harga barang baik
                sebagai bahan baku atau produk jual.
            </p>
        </div>
    </div>

    <div class="w-full flex md:flex-row flex-col gap-4 mt-4">
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

            <div class="flex flex-row gap-2 w-full">
                <div x-init="picker = new Pikaday({
                    field: $refs.datepicker,
                    format: 'DD/MM/YYYY',
                    toString(date, format) {
                        const day = String(date.getDate()).padStart(2, 0);
                        const month = String(date.getMonth() + 1).padStart(2, 0);
                        const year = date.getFullYear();
                        return `${day}/${month}/${year}`;
                    },
                    onSelect: function() {
                        @this.set('start_date', moment(this.getDate()).format('DD/MM/YYYY'));
                    }
                });" class="relative w-3/4">
                    <!-- Icon kalender -->
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <!-- Heroicons outline calendar icon -->
                        <flux:icon icon="calendar-date-range" />
                    </div>

                    <input type="text"
                        class="pr-10 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 w-full cursor-pointer"
                        x-ref="datepicker" wire:model.defer="start_date" id="datepicker" placeholder="dd/mm/yyyy"
                        readonly />
                </div>
                <div x-init="flatpickr($refs.input, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'H:i',
                    time_24hr: true,
                    onChange: function(selectedDates, dateStr) {
                        time = dateStr;
                        @this.set('time', dateStr);
                    }
                });" class="relative w-1/4">
                    <!-- Icon jam -->
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <!-- Heroicons outline clock icon -->
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <input x-ref="input" wire:model='time' type="text"
                        class="pr-10 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 w-full"
                        placeholder="00:00" />
                </div>

            </div>

            <flux:error name="date" />

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
                        <th class="text-right px-6 py-3">Jumlah Terkini</th>
                        <th class="text-right px-6 py-3">Jumlah Disarankan</th>
                        <th class="text-right px-6 py-3">Rencana Produksi</th>
                        <th class="text-right px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($production_details as $index => $detail)
                        <tr>
                            <td class="px-6 py-3">
                                <select class="w-full border-0 focus:outline-none focus:ring-0 rounded-none"
                                    wire:model="production_details.{{ $index }}.product_id"
                                    wire:change="setProduct({{ $index }})">
                                    <option value="" class="text-gray-700">- Pilih Barang -</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" class="text-gray-700">{{ $product->name }}
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
                                    class="w-full border-gray-300 focus:outline-none focus:ring-0 rounded text-right" />
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
        <a href="{{ route('produksi') }}" wire:navigate
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
                function loadJavascript() {
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
