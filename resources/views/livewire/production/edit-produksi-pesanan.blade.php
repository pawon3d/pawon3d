<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('produksi.rincian', ['id' => $productionId]) }}" wire:navigate
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
            Kembali
        </a>
        <h1 class="text-2xl">Rencana Pesanan {{ $method }}</h1>
    </div>
    <div class="flex items-center bg-white shadow-lg rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Tambahkan rencana produksi. Lengkapi informasi yang diminta, pastikan informasi yang dimasukan benar dan
                tepat. Informasi akan digunakan untuk menghasilkan produk dan hasil produksi akan dijual.
            </p>
        </div>
    </div>

    <div class="w-full flex md:flex-row flex-col gap-4 mt-4 bg-white p-4 rounded-lg shadow">
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
            {{-- <flux:label>Jadwal Produksi</flux:label>
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
                        class="pr-10 border border-gray-300 bg-gray-200 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 w-full cursor-pointer"
                        x-ref="datepicker" wire:model.defer="start_date" id="datepicker" placeholder="dd/mm/yyyy"
                        disabled />
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

            <flux:error name="date" /> --}}

        </div>


        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Catatan Produksi</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan catatan produksi apabila ada pesan atau sesuatu yang penting untuk diberitahu.
            </p>
            <flux:textarea wire:model.defer="note" rows="7" placeholder="Ketik Catatan Produksi...." />
        </div>
    </div>

    <div class="w-full mt-8 flex flex-col gap-4 bg-white p-4 rounded-lg shadow">
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <flux:label>Daftar Produk</flux:label>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-6 py-3">Produk</th>
                        <th class="text-right px-6 py-3">Jumlah Pesanan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $detail)
                        <tr>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail->product->name ?? 'Produk Tidak Ditemukan' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="text-sm ">
                                    {{ $detail->quantity ?? 0 }}
                                </span>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
                <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <td class="px-6 py-3">
                            <span class="text-gray-700 font-bold">Total</span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <span class="text-gray-700">
                                {{ $details->sum('quantity') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>



    <div class="flex justify-end mt-16 gap-4">
        <a href="{{ route('produksi.rincian', ['id' => $productionId]) }}" wire:navigate
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
            <flux:icon.x-mark class="w-4 h-4 mr-2" />
            Batal
        </a>
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
    @section('css')
        <style>
            .select2-search__field {
                height: 10.5rem !important;
                /* sekitar 176px */
                min-height: 10.5rem !important;
                max-height: 10.5rem !important;
                padding: 0.75rem !important;
                font-size: 0.875rem !important;
                line-height: 1.5rem !important;
                resize: none !important;
                border-radius: 0.375rem !important;
                border: none !important;
            }

            .select2-container--default .select2-selection--multiple {
                display: flex;
                flex-wrap: wrap;
                align-items: flex-start;
                align-content: flex-start;
                min-height: 10.5rem;
                max-height: 10.5rem !important;
                padding: 0.5rem;
                gap: 0.25rem;
                border-radius: 0.375rem !important;
                border: 1px solid #e5e7eb !important;
            }


            .select2-selection__rendered {
                display: flex !important;
                flex-wrap: wrap !important;
                align-items: flex-start !important;
                align-content: flex-start !important;
                width: 100%;
            }
        </style>
    @endsection
</div>
