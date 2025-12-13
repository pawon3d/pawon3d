<div style="background: #eaeaea; padding: 30px;">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8" style="height: 40px;">
        <!-- Tombol Kembali -->
        <flux:button href="{{ route('produksi.antrian-produksi') }}" icon="arrow-left" variant="secondary" wire:navigate>
            <span class="text-[16px] font-semibold text-[#f6f6f6]" style="font-family: Montserrat;">Kembali</span>
        </flux:button>

        <h1 class="text-[20px] font-semibold text-[#666666]" style="font-family: Montserrat;">
            {{ $isEditMode ? 'Ubah Produksi' : 'Tambah Produksi' }}</h1>
    </div>

    <!-- Info Box -->
    <x-alert.info>
        <p class="flex-1 text-[14px] font-semibold text-[#dcd7c9] text-justify" style="font-family: Montserrat;">
            @if ($isEditMode)
                Ubah Rencana Produksi. Lengkapi informasi yang diminta, pastikan informasi yang dimasukan benar dan
                tepat. Informasi akan digunakan untuk menghasilkan produk dan hasil produksi akan dijual.
            @else
                Tambah Rencana Produksi. Lengkapi informasi yang diminta, pastikan informasi yang dimasukan benar dan
                tepat. Informasi akan digunakan untuk menghasilkan produk dan hasil produksi akan dijual.
            @endif
        </p>
    </x-alert.info>

    <!-- Main Content -->
    <div class="flex flex-col gap-[30px]">
        <!-- Form Card -->
        <div
            class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] flex gap-[50px]">
            <!-- Tanggal Produksi -->
            <div class="flex flex-col gap-[15px] w-[450px]">
                <div class="flex flex-col gap-[15px]">
                    <p class="text-[16px] font-medium text-[#666666]" style="font-family: Montserrat; margin: 0;">
                        Tanggal Produksi
                    </p>
                    <p class="text-[14px] font-normal text-[#666666] text-justify"
                        style="font-family: Montserrat; margin: 0;">
                        Masukkan rencana tanggal dan jam mulai produksi.
                    </p>
                </div>

                <div class="flex gap-[15px] items-center">
                    <!-- Date Picker -->
                    <div x-data x-init="picker = new Pikaday({
                        field: $refs.datepicker,
                        format: 'DD/MM/YYYY',
                        toString(date, format) {
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            return `${day}/${month}/${year}`;
                        },
                        onSelect: function() {
                            @this.set('start_date', moment(this.getDate()).format('DD/MM/YYYY'));
                        }
                    });" class="relative">
                        <input type="text" x-ref="datepicker" wire:model.live="start_date"
                            class="bg-[#fafafa] border-[1.5px] border-[#adadad] rounded-[15px] px-[20px] py-[10px] text-[16px] font-normal text-[#666666] w-[200px]"
                            style="font-family: Montserrat;" placeholder="dd/mm/yyyy" readonly />
                        <svg class="w-[20px] h-[20px] text-[#666666] absolute right-[20px] top-1/2 transform -translate-y-1/2 pointer-events-none"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z" />
                        </svg>
                    </div>

                    <!-- Time Picker -->
                    <div x-data x-init="flatpickr($refs.timepicker, {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: 'H:i',
                        time_24hr: true,
                        onChange: function(selectedDates, dateStr) {
                            @this.set('time', dateStr);
                        }
                    });" class="relative">
                        <input type="text" x-ref="timepicker" wire:model.live="time"
                            class="bg-[#fafafa] border-[1.5px] border-[#adadad] rounded-[15px] px-[20px] py-[10px] text-[16px] font-normal text-[#666666] w-[110px]"
                            style="font-family: Montserrat;" placeholder="00:00" />
                        <svg class="w-[20px] h-[20px] text-[#666666] absolute right-[20px] top-1/2 transform -translate-y-1/2 pointer-events-none"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z" />
                        </svg>
                    </div>
                </div>

                @error('start_date')
                    <span class="text-[12px] text-red-500" style="font-family: Montserrat;">{{ $message }}</span>
                @enderror
                @error('time')
                    <span class="text-[12px] text-red-500" style="font-family: Montserrat;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Catatan Rencana Produksi -->
            <div class="flex flex-col gap-[15px] flex-1">
                <div class="flex flex-col gap-[15px]">
                    <p class="text-[16px] font-medium text-[#666666]" style="font-family: Montserrat; margin: 0;">
                        Catatan Rencana Produksi
                    </p>
                    <p class="text-[14px] font-normal text-[#666666] text-justify"
                        style="font-family: Montserrat; margin: 0;">
                        Masukkan catatan rencana produksi jika ada.
                    </p>
                </div>

                <input type="text" wire:model.defer="note"
                    class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px] text-[16px] font-normal text-[#666666] w-full focus:ring-0 focus:border-[#d4d4d4]"
                    style="font-family: Montserrat;" placeholder="Masukkan catatan" />

                @error('note')
                    <span class="text-[12px] text-red-500" style="font-family: Montserrat;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Daftar Produk Card -->
        <div
            class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] flex flex-col gap-[15px]">
            <!-- Header Daftar Produk -->
            <div class="flex flex-col gap-0 w-full">
                <div class="flex items-center w-full" style="margin-bottom: 0;">
                    <p class="text-[16px] font-medium text-[#666666]" style="font-family: Montserrat; margin: 0;">
                        Daftar Produk
                    </p>
                </div>
                <div class="flex items-center justify-between w-full">
                    <p class="flex-1 text-[14px] font-normal text-[#666666] text-justify"
                        style="font-family: Montserrat; margin: 0;">
                        Tambah produk sesuai dengan kebutuhan operasional.
                    </p>
                    <button wire:click="addProduct"
                        class="bg-[#74512d] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-2">
                        <svg class="w-[20px] h-[20px] text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                        <span class="text-[16px] font-semibold text-[#f6f6f6]" style="font-family: Montserrat;">Tambah
                            Produk</span>
                    </button>
                </div>
            </div>

            <!-- Tabel Produk -->
            <div class="flex flex-col w-full overflow-hidden rounded-[15px]">
                <!-- Table Header -->
                <div class="flex w-full bg-[#3f4e4f] rounded-t-[15px]" style="height: 60px;">
                    <div class="flex items-center px-[25px] py-[21px] w-[255px]">
                        <p class="text-[14px] font-bold text-[#f8f4e1]" style="font-family: Montserrat; margin: 0;">
                            Produk
                        </p>
                    </div>
                    <div class="flex flex-1 items-center justify-end px-[25px] py-[21px]">
                        <p class="text-[14px] font-bold text-[#f8f4e1] text-right"
                            style="font-family: Montserrat; margin: 0; line-height: 1.2;">
                            Jumlah<br>Diharapkan
                        </p>
                    </div>
                    <div class="flex flex-1 items-center justify-end px-[25px] py-[21px]">
                        <p class="text-[14px] font-bold text-[#f8f4e1] text-right"
                            style="font-family: Montserrat; margin: 0; line-height: 1.2;">
                            Jumlah<br>Disarankan
                        </p>
                    </div>
                    <div class="flex flex-1 items-center justify-end px-[25px] py-[21px]">
                        <p class="text-[14px] font-bold text-[#f8f4e1] text-right"
                            style="font-family: Montserrat; margin: 0; line-height: 1.2;">
                            Rencana<br>Produksi
                        </p>
                    </div>
                    <div class="flex items-center justify-end px-[25px] py-[21px] w-[72px]">
                        <!-- Empty for delete button column -->
                    </div>
                </div>

                <!-- Table Body -->
                @foreach ($production_details as $index => $detail)
                    <div class="flex w-full bg-[#fafafa] border-b border-[#d4d4d4]" style="height: 60px;"
                        wire:key="product-{{ $index }}">
                        <!-- Produk -->
                        <div class="flex items-center px-[25px] w-[255px]">
                            <select wire:model="production_details.{{ $index }}.product_id"
                                wire:change="setProduct({{ $index }})"
                                class="flex-1 text-[14px] font-medium text-[#666666] border-0 focus:ring-0 bg-transparent"
                                style="font-family: Montserrat;">
                                <option value="">- Pilih Produk -</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jumlah Diharapkan -->
                        <div class="flex flex-1 items-center justify-end px-[25px]">
                            <p class="text-[14px] font-medium text-[#666666] text-right"
                                style="font-family: Montserrat; margin: 0;">
                                {{ $detail['current_stock'] }}
                            </p>
                        </div>

                        <!-- Jumlah Disarankan -->
                        <div class="flex flex-1 items-center justify-end px-[25px]">
                            <p class="text-[14px] font-medium text-[#666666] text-right"
                                style="font-family: Montserrat; margin: 0;">
                                {{ $detail['suggested_amount'] }}
                            </p>
                        </div>

                        <!-- Rencana Produksi -->
                        <div class="flex flex-1 items-center justify-end px-[25px]">
                            <input type="number"
                                wire:model.number.live="production_details.{{ $index }}.quantity_plan"
                                class="w-full bg-[#fafafa] border border-[#666666] rounded-[5px] px-[10px] py-[6px] text-[14px] font-medium text-[#666666] text-right focus:ring-0 focus:border-[#666666]"
                                style="font-family: Montserrat; min-width: 70px;" placeholder="0" />
                        </div>

                        <!-- Hapus -->
                        <div class="flex items-center justify-center px-[25px] w-[72px]">
                            <button wire:click="removeProduct({{ $index }})" type="button">
                                <svg class="w-[22px] h-[22px] text-[#666666]" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach

                <!-- Table Footer -->
                <div class="flex w-full bg-[#eaeaea] border-b border-[#d4d4d4] rounded-b-[15px]"
                    style="height: 60px;">
                    <div class="flex items-center px-[25px] w-[255px]">
                        <p class="text-[14px] font-bold text-[#666666]" style="font-family: Montserrat; margin: 0;">
                            Total
                        </p>
                    </div>
                    <div class="flex flex-1 items-center justify-end px-[25px]">
                        <p class="text-[14px] font-bold text-[#666666] text-right"
                            style="font-family: Montserrat; margin: 0;">
                            {{ $current_stock_total }}
                        </p>
                    </div>
                    <div class="flex flex-1 items-center justify-end px-[25px]">
                        <p class="text-[14px] font-bold text-[#666666] text-right"
                            style="font-family: Montserrat; margin: 0;">
                            {{ $suggested_amount_total }}
                        </p>
                    </div>
                    <div class="flex flex-1 items-center justify-end px-[25px]">
                        <p class="text-[14px] font-bold text-[#666666] text-right"
                            style="font-family: Montserrat; margin: 0;">
                            {{ $quantity_plan_total }}
                        </p>
                    </div>
                    <div class="flex items-center justify-end px-[25px] w-[72px]">
                        <!-- Empty -->
                    </div>
                </div>
            </div>

            @error('production_details.*')
                <span class="text-[12px] text-red-500" style="font-family: Montserrat;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-[30px] justify-end">
            <flux:button variant="filled" icon="x-mark" href="{{ route('produksi.antrian-produksi') }}"
                wire:navigate
                class="bg-[#c4c4c4] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-2">
                <span class="text-[16px] font-semibold text-[#333333]" style="font-family: Montserrat;">Batal</span>
            </flux:button>

            <flux:button wire:click="store" icon="save" variant="secondary" type="button"
                class="bg-[#3f4e4f] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-2">
                <span class="text-[16px] font-medium text-white" style="font-family: Montserrat;">
                    {{ $isEditMode ? 'Simpan Perubahan' : 'Buat Rencana Produksi' }}
                </span>
            </flux:button>
        </div>
    </div>
</div>

@script
    <script type="text/javascript">
        document.addEventListener('livewire:initialized', function() {
            // Inisialisasi sudah di Alpine.js x-init
        });
    </script>
@endscript
