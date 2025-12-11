<div class="bg-[#eaeaea] min-h-screen">
    {{-- Header with Back Button --}}
    <div class="flex items-center gap-[15px] mb-[70px]">
        <button wire:click.prevent="delete"
            class="bg-[#313131] px-[25px] py-[10px] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-[5px]">
            <flux:icon icon="arrow-left" class="size-5" style="color: #ffffff;" />
            <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f6f6f6]"
                style="line-height: 1;">Kembali</span>
        </button>
        <h1 class="font-['Montserrat'] font-semibold text-[20px] text-[#666666]" style="line-height: 1;">Buat Pesanan
        </h1>
    </div>

    {{-- Info Penting Box --}}
    <x-alert.info>
        <p class="font-['Montserrat'] font-semibold text-[14px] text-[#dcd7c9] text-justify" style="line-height: 1;">
            Buat Pesanan. Lengkapi informasi yang diminta, pastikan informasi yang dimasukan benar dan tepat. Informasi
            akan digunakan untuk membuat pesanan dan melakukan produksi. Masukkan atau tambah pembeli sebagai pelanggan
            untuk mendapatkan hak poin.
        </p>
    </x-alert.info>

    @if ($transaction->method != 'siap-beli')
        <div
            class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] flex flex-wrap gap-[90px] mb-[30px]">
            {{-- Left Column --}}
            <div class="flex-1 min-w-[300px] flex flex-col gap-[30px]">
                {{-- No. Telepon --}}
                <div class="flex flex-col gap-[15px]">
                    <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]" style="line-height: 1;">
                        No.
                        Telepon</p>
                    <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666] text-justify"
                        style="line-height: 1;">Masukkan nomor telepon aktif.</p>

                    <div class="flex flex-col gap-[10px]">
                        <div class="bg-[#fafafa] border-[1.5px] border-[#adadad] rounded-[15px] px-[20px] py-[10px]">
                            <input type="text" wire:model.live="phone" placeholder="081122334455"
                                class="w-full font-['Montserrat'] font-normal text-[16px] text-[#666666] bg-transparent border-none focus:outline-none focus:ring-0 p-0"
                                style="line-height: 1;" />
                        </div>
                        @if ($phone)
                            @if ($customer)
                                <div class="flex justify-between items-center w-full">
                                    <span
                                        class="font-['Montserrat'] font-normal text-[12px] text-[#666666] text-justify"
                                        style="line-height: 1;">
                                        Terdaftar sebagai Pelanggan.
                                    </span>
                                    <button wire:click="$set('customerModal', true)"
                                        class="flex items-center gap-[2px] font-['Montserrat'] font-semibold text-[12px] text-[#666666]"
                                        style="line-height: 1;">
                                        <span>{{ $customer->points ?? 0 }}</span>
                                        <span>Poin</span>
                                    </button>
                                </div>
                            @else
                                <div class="flex justify-between items-center w-full">
                                    <span
                                        class="font-['Montserrat'] font-normal text-[12px] text-[#666666] text-justify"
                                        style="line-height: 1;">
                                        Ingin menjadi pelanggan?
                                    </span>
                                    <button wire:click="showCustomerModal"
                                        class="font-['Montserrat'] font-normal text-[12px] text-[#666666] underline cursor-pointer"
                                        style="line-height: 1;">
                                        Tambah Pelanggan
                                    </button>
                                </div>
                            @endif
                        @endif
                        <flux:error name="phone" />
                    </div>
                </div>

                {{-- Nama Pembeli --}}
                <div class="flex flex-col gap-[15px]">
                    <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]" style="line-height: 1;">
                        Nama
                        Pembeli</p>
                    <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666] text-justify"
                        style="line-height: 1;">Masukkan nama Pembeli.</p>

                    <div class="border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px]">
                        <input type="text" wire:model="name" placeholder="Contoh : Fani"
                            class="w-full font-['Montserrat'] font-normal text-[16px] text-[#666666] bg-transparent border-none focus:outline-none focus:ring-0 p-0"
                            style="line-height: 1;" />
                    </div>
                    <flux:error name="name" />
                </div>

                {{-- Tanggal Ambil Pesanan --}}
                <div class="flex flex-col gap-[15px]">
                    <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]" style="line-height: 1;">
                        Tanggal Ambil Pesanan</p>
                    <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666] text-justify"
                        style="line-height: 1;">Masukkan tanggal Ambil Pesanan.</p>

                    <div class="flex gap-[15px] w-full">
                        {{-- Date Input --}}
                        <div x-data="{ date: @entangle('date') }" x-init="picker = new Pikaday({
                            field: $refs.datepicker,
                            format: 'DD MMM YYYY',
                            toString(date, format) {
                                const day = String(date.getDate()).padStart(2, 0);
                                const month = String(date.getMonth() + 1).padStart(2, 0);
                                const year = date.getFullYear();
                                return `${day} ${month} ${year}`;
                            },
                            onSelect: function() {
                                @this.set('date', moment(this.getDate()).format('DD MMM YYYY'));
                            }
                        });" class="flex-1 min-w-[190px] relative">
                            <div
                                class="bg-[#fafafa] border-[1.5px] border-[#adadad] rounded-[15px] px-[20px] py-[10px] flex items-center justify-between cursor-pointer">
                                <span class="font-['Montserrat'] font-normal text-[16px] text-[#666666]"
                                    style="line-height: 1;" x-text="date || 'dd mm yyyy'"></span>
                                <flux:icon icon="calendar" class="size-5" style="color: #666666;" />
                            </div>
                            <input type="text" x-ref="datepicker" wire:model="date"
                                class="absolute inset-0 opacity-0 cursor-pointer" />
                        </div>

                        {{-- Time Input --}}
                        <div x-data="{ time: @entangle('time').defer }" x-init="flatpickr($refs.input, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                time = dateStr;
                                @this.set('time', dateStr);
                            }
                        });" class="flex-1 relative">
                            <div
                                class="bg-[#fafafa] border-[1.5px] border-[#adadad] rounded-[15px] px-[20px] py-[10px] flex items-center justify-between cursor-pointer">
                                <span class="font-['Montserrat'] font-normal text-[16px] text-[#666666]"
                                    style="line-height: 1;" x-text="time || 'hh:mm'"></span>
                                <flux:icon icon="clock" class="size-5" style="color: #666666;" />
                            </div>
                            <input x-ref="input" type="text" wire:model="time"
                                class="absolute inset-0 opacity-0 cursor-pointer" />
                        </div>
                    </div>

                    <div class="flex gap-[15px]">
                        <div class="flex-1">
                            <flux:error name="date" />
                        </div>
                        <div class="flex-1">
                            <flux:error name="time" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Catatan --}}
            <div class="flex-1 min-w-[300px] flex flex-col gap-[15px] h-[399px]">
                <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]" style="line-height: 1;">
                    Catatan
                    Pesanan</p>
                <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666] text-justify"
                    style="line-height: 1;">Masukkan catatan pesanan apabila diperlukan.</p>

                <div class="flex-1 bg-[#fafafa] border border-[#adadad] rounded-[15px] px-[20px] py-[10px]">
                    <textarea wire:model.defer="note" placeholder="Ini adalah catatan pesanan"
                        class="w-full h-full font-['Montserrat'] font-normal text-[16px] text-[#666666] bg-transparent border-none focus:outline-none focus:ring-0 p-0 resize-none"
                        style="line-height: 1;"></textarea>
                </div>
            </div>
        </div>
    @endif



    {{-- Daftar Pesanan --}}
    <div class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] mb-[30px]">
        <div class="px-[30px] py-[25px] flex flex-col gap-[20px]">
            {{-- Header --}}
            <div class="flex items-center gap-[20px]">
                <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]" style="line-height: 1;">
                    Daftar
                    Pesanan</p>
            </div>

            {{-- Product List --}}
            <div class="flex flex-col gap-[10px] pb-[15px]">
                @forelse ($details as $id => $item)
                    <div class="border-b border-[#ffffff] py-[10px] flex items-center justify-between min-w-[180px]">
                        {{-- Left Side --}}
                        <div class="flex flex-col gap-[10px] h-[73px]">
                            <div class="flex items-center gap-[5px] h-[24px]">
                                <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666] truncate"
                                    style="line-height: 1;">
                                    {{ $item['name'] }}
                                </p>
                            </div>
                            <div class="flex items-center gap-[5px] h-[40px]">
                                <span class="font-['Montserrat'] font-normal text-[16px] text-[#666666] truncate"
                                    style="line-height: 1;">{{ $item['quantity'] }}</span>
                                <span class="font-['Montserrat'] font-normal text-[16px] text-[#666666] truncate"
                                    style="line-height: 1;">x</span>
                                <span class="font-['Montserrat'] font-normal text-[16px] text-[#666666] truncate"
                                    style="line-height: 1;">Rp{{ number_format($item['price'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Right Side --}}
                        <div class="flex flex-col gap-[10px] items-end justify-center">
                            <div class="flex items-center gap-[5px]">
                                <span class="font-['Montserrat'] font-normal text-[16px] text-[#666666]"
                                    style="line-height: 1;">=</span>
                                <span class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                    style="line-height: 1;">Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center gap-[25px] h-[40px]">
                                {{-- Delete Button --}}
                                <button wire:click="removeItem('{{ $id }}')"
                                    class="bg-[#eb5757] rounded-[15px] flex items-center justify-center size-[32px]">
                                    <flux:icon icon="trash" class="size-3" style="color: #f8f4e1;" />
                                </button>

                                {{-- Quantity Controls --}}
                                <div class="flex items-center gap-[11px]">
                                    <button wire:click="decrementItem('{{ $id }}')"
                                        class="border-[1.5px] border-[#74512d] rounded-[30px] flex items-center justify-center size-[30px]">
                                        <flux:icon icon="minus" class="size-[18px]" style="color: #666666;" />
                                    </button>
                                    <div
                                        class="border border-[rgba(116,81,45,0.2)] rounded-[10px] px-[10px] py-[3px] w-[36px] flex items-center justify-center">
                                        <span class="font-['Montserrat'] font-semibold text-[16px] text-[#666666]"
                                            style="line-height: 1;">{{ $item['quantity'] }}</span>
                                    </div>
                                    <button wire:click="incrementItem('{{ $id }}')"
                                        class="border-[1.5px] border-[#74512d] rounded-[30px] flex items-center justify-center size-[30px]">
                                        <flux:icon icon="plus" class="size-[18px]" style="color: #666666;" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666]">Tidak ada produk yang
                            ditambahkan.</p>
                    </div>
                @endforelse
            </div>

            {{-- Add Product Button --}}
            <button wire:click="$set('showItemModal', true)"
                class="bg-[#74512d] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center justify-center gap-[5px] w-full">
                <flux:icon icon="plus" class="size-5" style="color: #f8f4e1;" />
                <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f8f4e1]"
                    style="line-height: 1;">Tambah Produk</span>
            </button>
        </div>

        {{-- Subtotal Section --}}
        <div
            class="bg-[#fafafa] border border-[#d4d4d4] px-[30px] py-[25px] flex flex-col gap-[15px] items-end justify-center">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-[5px] font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                    style="line-height: 1;">
                    <span>Subtotal</span>
                    <span>{{ count($details) }}</span>
                    <span>Produk</span>
                </div>
                <span class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                    style="line-height: 1;">Rp{{ number_format($total, 0, ',', '.') }}</span>
            </div>
            @if ($pointsUsed > 0)
                <div class="flex items-center justify-between w-full">
                    <span class="font-['Montserrat'] font-medium text-[16px] text-[#27ae60]"
                        style="line-height: 1;">Diskon Poin ({{ number_format($pointsUsed, 0, ',', '.') }}
                        poin)</span>
                    <span class="font-['Montserrat'] font-medium text-[16px] text-[#27ae60]" style="line-height: 1;">-
                        Rp{{ number_format($pointsUsed * 100, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex items-center justify-between w-full">
                <span class="font-['Montserrat'] font-bold text-[16px] text-[#666666]" style="line-height: 1;">Total
                    Tagihan</span>
                <span class="font-['Montserrat'] font-bold text-[16px] text-[#666666]"
                    style="line-height: 1;">Rp{{ number_format($total - $pointsUsed * 100, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Payment Status Section --}}
        <div
            class="bg-[#fafafa] border border-[#d4d4d4] px-[30px] py-[25px] flex flex-col gap-[15px] items-end justify-center">
            <div class="flex items-center justify-between w-full">
                <span class="font-['Montserrat'] font-medium text-[16px] text-[#666666]" style="line-height: 1;">Total
                    Bayar</span>
                <span class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                    style="line-height: 1;">Rp{{ number_format($paidAmount, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between w-full">
                <span class="font-['Montserrat'] font-medium text-[16px] text-[#eb5757]" style="line-height: 1;">Sisa
                    Tagihan</span>
                <span class="font-['Montserrat'] font-medium text-[16px] text-[#eb5757]" style="line-height: 1;">
                    @php
                        $totalAfterPoints = $total - $pointsUsed * 100;
                    @endphp
                    @if ($paidAmount >= $totalAfterPoints)
                        Rp0
                    @else
                        Rp{{ number_format($totalAfterPoints - $paidAmount, 0, ',', '.') }}
                    @endif
                </span>
            </div>
        </div>
    </div>
    {{-- Tukar Poin Section --}}
    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] flex flex-col gap-[30px] mb-[30px]">
        <div class="flex flex-col gap-[15px] h-[113px]">
            <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]" style="line-height: 1;">Tukar
                Poin
            </p>
            <div class="flex items-start justify-between w-full">
                <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666] text-justify"
                    style="line-height: 1;">Tukar poin untuk menerima potongan harga. Poin (1 poin = Rp 100) yang
                    dapat
                    ditukarkan adalah kelipatan 10 poin.</p>
                <div class="flex items-center gap-[2px] font-['Montserrat'] font-normal text-[14px] text-[#666666]"
                    style="line-height: 1;">
                    <span>{{ number_format($availablePoints, 0, ',', '.') }}</span>
                    <span>Poin</span>
                </div>
            </div>
            <div class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px]">
                <input type="number" wire:model.live="pointsUsed" placeholder="0" min="0" step="10"
                    {{ $availablePoints == 0 ? 'disabled' : '' }}
                    class="w-full font-['Montserrat'] font-normal text-[16px] text-[#959595] bg-transparent border-none focus:outline-none focus:ring-0 p-0"
                    style="line-height: 1;" />
            </div>
        </div>
    </div>

    {{-- Metode Pembayaran Section --}}
    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] flex flex-col gap-[30px] mb-[30px]">
        <div class="w-full flex flex-col gap-4">
            <flux:label>Metode Pembayaran</flux:label>
            <p class="text-sm text-gray-500">
                Pilih Metode Pembayaran (Tunai atau Non Tunai). Jika Bukan Tunai maka akan diminta bukti
                pembayaran
                berupa
                gambar (.jpg dan .png)
            </p>
            <flux:select wire:model.live="paymentGroup" class="mt-2" placeholder="Pilih Metode Pembayaran">
                <flux:select.option hidden value="" class="text-gray-700">Pilih Metode Pembayaran
                </flux:select.option>
                <flux:select.option value="tunai" class="text-gray-700">Tunai</flux:select.option>
                <flux:select.option value="non-tunai" class="text-gray-700">Non Tunai</flux:select.option>
            </flux:select>
            <flux:error name="paymentGroup" />

            @if ($paymentGroup == 'non-tunai')
                <div class="mt-2 flex flex-row gap-2 w-full">
                    <div class="w-1/3">
                        <flux:select wire:model.live="paymentMethod" placeholder="Pilih Metode Pembayaran">
                            @foreach ($paymentMethods as $pmethod)
                                <flux:select.option value="{{ $pmethod->type }}" class="text-gray-700">
                                    {{ ucfirst($pmethod->type) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="paymentMethod" />
                    </div>
                    <div class="w-1/3">
                        <flux:select wire:model.live="paymentChannelId" placeholder="Pilih Bank Tujuan">
                            @foreach ($paymentChannels as $channel)
                                <flux:select.option value="{{ $channel->id }}" class="text-gray-700">
                                    {{ $channel->bank_name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="paymentChannelId" />
                    </div>
                    <div class="w-1/3">
                        <flux:input wire:model="paymentAccount" placeholder="Masukkan Nomor Rekening" readonly />
                        <flux:error name="paymentAccount" />
                    </div>
                </div>
            @endif

            <flux:label>Nominal Pembayaran</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan atau pilih nominal pembayaran tagihan. Untuk uang muka dilakukan dengan minimal 50% atau
                setengah
                dari
                Total Tagihan.
            </p>
            <div class="flex flex-row gap-2 w-full">
                <div class="flex flex-col gap-2 w-full">
                    @if ($paymentGroup == 'tunai')
                        <span class="text-xs text-gray-500">
                            Nominal Uang Yang Diterima
                        </span>
                    @endif
                    <flux:input placeholder="Masukkan Nominal Pembayaran..." wire:model.number.live="paidAmount" />
                    <flux:error name="paidAmount" />
                </div>
                @if ($paymentGroup == 'tunai')
                    <div class="flex flex-col gap-2 w-full">
                        <span class="text-xs text-gray-500">
                            Nominal Uang Kembalian
                        </span>
                        <flux:input placeholder="Kembalian" value="Rp{{ number_format($changeAmount, 0, ',', '.') }}"
                            readonly />
                    </div>
                @endif
            </div>

            @if ($paymentGroup == 'non-tunai')
                <div class="mb-5 w-full">
                    <div class="flex flex-row items-center gap-4">
                        <label
                            class="relative items-center cursor-pointer font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg px-4 inline-flex  bg-[#74512D] hover:bg-[color-mix(in_oklab,_#74512D,_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2)">
                            Pilih Bukti Pembayaran
                            <input type="file" wire:model.live="image" accept="image/jpeg, image/png, image/jpg"
                                class="hidden" />
                        </label>

                        @if ($image)
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="{{ is_string($image) ? basename($image) : $image->getClientOriginalName() }}"
                                readonly wire:loading.remove wire:target="image">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload gambar..." readonly wire:loading wire:target="image">
                        @else
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Belum Ada Bukti Pembayaran" readonly wire:loading.remove wire:target="image">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload gambar..." readonly wire:loading wire:target="image">
                        @endif

                    </div>
                </div>
                <flux:error name="image" />
            @endif
        </div>
    </div>


    {{-- Action Buttons --}}
    <div class="flex items-center justify-end gap-[30px] mb-16">
        <button wire:click.prevent="delete"
            class="bg-[#c4c4c4] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-[5px]">
            <flux:icon icon="x-mark" class="size-5" style="color: #333333;" />
            <span class="font-['Montserrat'] font-semibold text-[16px] text-[#333333]"
                style="line-height: 1;">Batal</span>
        </button>

        <button wire:click.prevent="save"
            class="bg-[#fafafa] border border-[#3f4e4f] rounded-[15px] px-[25px] py-[10px] flex items-center gap-[5px]">
            <flux:icon icon="document-text" class="size-5" style="color: #3f4e4f;" />
            <span class="font-['Montserrat'] font-semibold text-[16px] text-[#3f4e4f]" style="line-height: 1;">Simpan
                sebagai Draft</span>
        </button>

        <button wire:click.prevent="pay"
            class="bg-[#3f4e4f] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-[5px]">
            <flux:icon icon="shopping-cart" class="size-5" style="color: #ffffff;" />
            <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f8f4e1]" style="line-height: 1;">Bayar
                dan Buat Pesanan</span>
        </button>
    </div>

    {{-- Modal Tambah Item --}}
    <flux:modal name="tambah-item" class="w-full max-w-[700px] h-full" variant="bare" wire:model="showItemModal">
        <div class="bg-[#fafafa] rounded-tl-[15px] h-full rounded-tr-[15px] p-[30px] flex flex-col gap-[30px]">
            {{-- Search & Filter --}}
            <div class="flex items-start gap-[30px]">
                <div class="flex-1 flex items-center gap-[15px]">
                    {{-- Search Input --}}
                    <div
                        class="flex-1 bg-[#ffffff] border border-[#666666] rounded-[20px] px-[15px] flex items-center">
                        <flux:icon icon="magnifying-glass" class="size-[30px]" style="color: #666666;" />
                        <input type="text" wire:model.live="search" placeholder="Cari Produk"
                            class="flex-1 font-['Montserrat'] font-medium text-[16px] text-[#959595] bg-transparent border-none focus:outline-none focus:ring-0 p-[10px]"
                            style="line-height: 1;" />
                    </div>

                    {{-- Filter --}}
                    <div class="flex items-center">
                        <flux:icon icon="funnel" class="size-[25px]" style="color: #666666;" />
                        <span class="font-['Montserrat'] font-medium text-[16px] text-[#666666] px-[5px] py-[10px]"
                            style="line-height: 1;">Filter</span>
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <div class="h-0 w-full border-t border-[#d4d4d4]"></div>

            {{-- Product Grid --}}
            <div class="flex gap-[15px] h-[530px] overflow-y-auto">
                <div class="flex-1 flex flex-wrap gap-[30px] content-start">
                    @forelse ($products as $product)
                        <div class="min-w-[180px] max-w-[210px] flex flex-col gap-[20px] pb-[25px]">
                            {{-- Product Card --}}
                            <div class="flex flex-col gap-[15px]">
                                @if ($product->product_image)
                                    <img src="{{ asset('storage/' . $product->product_image) }}"
                                        alt="{{ $product->name }}"
                                        class="h-[119px] w-[182px] object-cover rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
                                @else
                                    <div
                                        class="h-[119px] w-[182px] bg-[#eaeaea] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center justify-center">
                                        <span class="font-['Montserrat'] font-normal text-[14px] text-[#666666]">No
                                            Image</span>
                                    </div>
                                @endif

                                {{-- Product Info --}}
                                <div class="px-[15px] flex flex-col gap-[30px] items-center">
                                    <div class="flex flex-col gap-[10px] items-center min-h-[70px] w-full">
                                        {{-- Product Name --}}
                                        <div class="flex flex-col gap-[5px] items-center w-full">
                                            <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666] text-center truncate w-full"
                                                style="line-height: 1;">
                                                {{ $product->name }}
                                            </p>
                                        </div>

                                        {{-- Stock --}}
                                        <div class="flex items-center justify-center w-full">
                                            <span class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                                style="line-height: 1;">(</span>
                                            <span class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                                style="line-height: 1;">{{ $product->stock }}</span>
                                            <span class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                                style="line-height: 1;"> pcs)</span>
                                        </div>
                                    </div>

                                    {{-- Price --}}
                                    <div class="flex items-center justify-center w-full">
                                        <span
                                            class="font-['Montserrat'] font-semibold text-[18px] text-[#666666] text-center truncate"
                                            style="line-height: 1;">
                                            Rp{{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Add/Quantity Controls --}}
                            @if (isset($details[$product->id]))
                                @php
                                    $id = $product->id;
                                    $item = $details[$id];
                                @endphp
                                <div
                                    class="bg-[#fafafa] h-[40px] rounded-[15px] px-[25px] flex items-center justify-between w-full">
                                    <button wire:click="decrementItem('{{ $id }}')"
                                        class="border-[1.5px] border-[#74512d] rounded-[30px] flex items-center justify-center size-[30px]">
                                        <flux:icon icon="minus" class="size-[18px]" style="color: #666666;" />
                                    </button>
                                    <div
                                        class="border border-[rgba(116,81,45,0.2)] rounded-[10px] px-[15px] py-[3px] flex items-center justify-center">
                                        <span class="font-['Montserrat'] font-semibold text-[16px] text-[#666666]"
                                            style="line-height: 1;">{{ $item['quantity'] }}</span>
                                    </div>
                                    <button wire:click="incrementItem('{{ $id }}')"
                                        class="border-[1.5px] border-[#74512d] rounded-[30px] flex items-center justify-center size-[30px]">
                                        <flux:icon icon="plus" class="size-[18px]" style="color: #666666;" />
                                    </button>
                                </div>
                            @else
                                <button wire:click="addToCart('{{ $product->id }}')"
                                    class="bg-[#ffffff] border-[1.5px] border-[#74512d] rounded-[20px] w-full px-[25px] py-[10px]">
                                    <span class="font-['Montserrat'] font-bold text-[16px] text-[#74512d]"
                                        style="line-height: 1;">Tambah</span>
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-3 text-center p-4 w-full">
                            <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666]">Tidak ada produk
                                yang
                                ditemukan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div
            class="bg-[#fafafa] shadow-[4px_0px_4px_0px_rgba(0,0,0,0.25)] p-[20px] flex items-center justify-end gap-[10px]">
            <flux:modal.close>
                <button
                    class="bg-[#c4c4c4] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-[5px]">
                    <flux:icon icon="x-mark" class="size-5" style="color: #333333;" />
                    <span class="font-['Montserrat'] font-semibold text-[16px] text-[#333333]"
                        style="line-height: 1;">Batal</span>
                </button>
            </flux:modal.close>

            <flux:modal.close>
                <button
                    class="bg-[#3f4e4f] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-[5px]">
                    <flux:icon icon="check" class="size-5" style="color: #f6f6f6;" />
                    <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f6f6f6]"
                        style="line-height: 1;">Simpan Perubahan</span>
                </button>
            </flux:modal.close>
        </div>
    </flux:modal>

    {{-- Modal Tambah Pelanggan --}}
    <flux:modal name="tambah-customer" class="w-full max-w-md" wire:model="customerModal">
        <div class="bg-[#fafafa] rounded-[15px] px-[30px] py-[35px] flex flex-col gap-[80px]">
            {{-- Content --}}
            <div class="flex flex-col gap-[30px] w-full">
                {{-- Header --}}
                <div class="flex items-center justify-center">
                    <p class="font-['Montserrat'] font-medium text-[18px] text-[#666666]" style="line-height: 1;">
                        Tambah Pelanggan</p>
                </div>

                {{-- No. Telepon --}}
                <div class="flex flex-col gap-[10px]">
                    <div class="flex items-center justify-center">
                        <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]" style="line-height: 1;">
                            No. Telepon</p>
                    </div>
                    <div
                        class="bg-[#fafafa] border-[1.5px] border-[#adadad] rounded-[15px] px-[20px] py-[10px] w-full">
                        <input type="text" wire:model="phoneCustomer" placeholder="081122334455"
                            class="w-full font-['Montserrat'] font-normal text-[16px] text-[#666666] bg-transparent border-none focus:outline-none focus:ring-0 p-0"
                            style="line-height: 1;" />
                    </div>
                    @error('phoneCustomer')
                        <span class="font-['Montserrat'] font-normal text-[12px] text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Nama Pelanggan --}}
                <div class="flex flex-col gap-[10px]">
                    <div class="flex items-center justify-center">
                        <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]" style="line-height: 1;">
                            Nama Pelanggan</p>
                    </div>
                    <div class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px] w-full">
                        <input type="text" wire:model="nameCustomer" placeholder="Contoh : Fani"
                            class="w-full font-['Montserrat'] font-normal text-[16px] text-[#959595] bg-transparent border-none focus:outline-none focus:ring-0 p-0"
                            style="line-height: 1;" />
                    </div>
                    @error('nameCustomer')
                        <span class="font-['Montserrat'] font-normal text-[12px] text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-[10px]">
                <flux:modal.close>
                    <button
                        class="bg-[#c4c4c4] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-[5px]">
                        <flux:icon icon="x-mark" class="size-5" style="color: #333333;" />
                        <span class="font-['Montserrat'] font-semibold text-[16px] text-[#333333]"
                            style="line-height: 1;">Batal</span>
                    </button>
                </flux:modal.close>

                <button wire:click="addCustomer"
                    class="bg-[#3f4e4f] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-[5px]">
                    <flux:icon icon="check" class="size-5" style="color: #f8f4e1;" />
                    <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f8f4e1]"
                        style="line-height: 1;">Simpan</span>
                </button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Rincian Pelanggan (untuk customer yang sudah terdaftar) --}}
    @if ($customer)
        <flux:modal name="rincian-customer" class="w-full max-w-md" wire:model="customerModal">
            <div class="bg-[#fafafa] rounded-[15px] px-[30px] py-[35px] flex flex-col gap-[80px]">
                {{-- Content --}}
                <div class="flex flex-col gap-[30px] w-full">
                    {{-- Header --}}
                    <div class="flex items-center justify-center">
                        <p class="font-['Montserrat'] font-medium text-[18px] text-[#666666]" style="line-height: 1;">
                            Rincian Pelanggan</p>
                    </div>

                    <div class="flex flex-col gap-[40px]">
                        {{-- Customer Info --}}
                        <div class="flex flex-col gap-[30px]">
                            {{-- No. Telepon --}}
                            <div class="flex flex-col gap-[10px]">
                                <div class="flex items-center justify-center">
                                    <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                        style="line-height: 1;">No. Telepon</p>
                                </div>
                                <div
                                    class="bg-[#fafafa] border border-[#adadad] rounded-[15px] px-[20px] py-[10px] w-full">
                                    <span class="font-['Montserrat'] font-normal text-[16px] text-[#666666]"
                                        style="line-height: 1;">{{ $customer->phone }}</span>
                                </div>
                            </div>

                            {{-- Nama Pelanggan --}}
                            <div class="flex flex-col gap-[10px]">
                                <div class="flex items-center justify-center">
                                    <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                        style="line-height: 1;">Nama Pelanggan</p>
                                </div>
                                <div
                                    class="bg-[#fafafa] border border-[#adadad] rounded-[15px] px-[20px] py-[10px] w-full">
                                    <span class="font-['Montserrat'] font-normal text-[16px] text-[#666666]"
                                        style="line-height: 1;">{{ $customer->name }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Customer Stats --}}
                        <div class="flex flex-col gap-[30px]">
                            {{-- Jumlah Transaksi --}}
                            <div class="flex items-center justify-between w-full">
                                <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                    style="line-height: 1;">Jumlah Transaksi</p>
                                <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                    style="line-height: 1;">0</p>
                            </div>

                            {{-- Jumlah Pembayaran --}}
                            <div class="flex items-center justify-between w-full">
                                <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                    style="line-height: 1;">Jumlah Pembayaran</p>
                                <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                    style="line-height: 1;">Rp0</p>
                            </div>

                            {{-- Saldo Poin --}}
                            <div class="flex items-center justify-between w-full">
                                <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                    style="line-height: 1;">Saldo Poin</p>
                                <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]"
                                    style="line-height: 1;">{{ $customer->points ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center gap-[10px]">
                    <flux:modal.close>
                        <button
                            class="bg-[#c4c4c4] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-[5px]">
                            <flux:icon icon="x-mark" class="size-5" style="color: #333333;" />
                            <span class="font-['Montserrat'] font-semibold text-[16px] text-[#333333]"
                                style="line-height: 1;">Batal</span>
                        </button>
                    </flux:modal.close>

                    <flux:modal.close>
                        <button
                            class="bg-[#3f4e4f] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center gap-[5px]">
                            <flux:icon icon="check" class="size-5" style="color: #f8f4e1;" />
                            <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f8f4e1]"
                                style="line-height: 1;">Simpan Perubahan</span>
                        </button>
                    </flux:modal.close>
                </div>
            </div>
        </flux:modal>
    @endif

</div>
