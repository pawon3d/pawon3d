<div>
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-8" style="margin-bottom: 30px; gap: 15px;">
        <a href="{{ route('transaksi.rincian-pesanan', $transactionId) }}" wire:navigate
            class="w-full sm:w-auto inline-flex items-center justify-center gap-1 px-6 py-2 rounded-lg font-semibold transition-colors"
            style="background-color: #313131; color: #f6f6f6; font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; border-radius: 15px; box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.1); padding: 10px 25px; gap: 5px;">
            <flux:icon.arrow-left class="size-4" />
            Kembali
        </a>
        <h1 class="text-xl font-semibold text-center sm:text-left"
            style="font-family: Montserrat, sans-serif; font-size: 20px; line-height: 1; color: #666666; font-weight: 600;">
            Ubah Pesanan</h1>
    </div>

    <!-- Info Banner -->
    <x-alert.info>
        <p class="font-semibold text-justify"
            style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1.4; color: #dcd7c9; font-weight: 600;">
            Ubah Pesanan. Lengkapi informasi yang diminta, pastikan informasi yang dimasukan benar dan tepat. Informasi
            akan digunakan untuk membuat pesanan dan melakukan produksi.
        </p>
    </x-alert.info>

    <!-- Form Section -->
    <div class="w-full flex flex-col lg:flex-row gap-8 lg:gap-20 p-4 sm:px-[30px] sm:py-[25px] rounded-2xl mb-8"
        style="background-color: #fafafa; border-radius: 15px;">
        <!-- Left Column -->
        <div class="flex-1 flex flex-col gap-8" style="gap: 30px; width: 100%;">
            <!-- Phone Number -->
            <div class="flex flex-col gap-4" style="gap: 15px;">
                <div class="flex flex-col gap-4" style="gap: 15px;">
                    <p class="font-medium"
                        style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666; font-weight: 500;">
                        No. Telepon</p>
                    <p class="font-normal text-justify"
                        style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #666666;">
                        Masukkan nomorn telepon aktif.</p>
                </div>
                <div class="flex flex-col gap-2" style="gap: 10px;">
                    <input type="text" placeholder="081122345678" wire:model.live="phone"
                        class="px-5 py-2 rounded-2xl border-2 focus:outline-none focus:ring-0 w-full"
                        style="background-color: #fafafa; border: 1.5px solid #adadad; border-radius: 15px; padding: 10px 20px; font-family: Montserrat, sans-serif; font-size: 16px; color: #666666; " />
                    @if ($phone)
                        @if ($customer)
                            <div class="flex items-center justify-between text-xs w-full"
                                style="font-family: Montserrat, sans-serif; font-size: 12px; line-height: 1; color: #666666; ">
                                <span style="font-weight: 400;">Terdaftar sebagai Pelanggan.</span>
                                <span style="font-weight: 600;">{{ $customer->points ?? 0 }} Poin</span>
                            </div>
                        @else
                            <div class="flex items-center justify-between text-xs w-full"
                                style="font-family: Montserrat, sans-serif; font-size: 12px; line-height: 1; color: #666666; ">
                                <span>Ingin menjadi pelanggan?</span>
                                <span class="underline cursor-pointer" wire:click="showCustomerModal">Tambah
                                    Pelanggan</span>
                            </div>
                        @endif
                    @endif
                </div>
                <flux:error name="phone" />
            </div>

            <!-- Buyer Name -->
            <div class="flex flex-col gap-4" style="gap: 15px;">
                <div class="flex flex-col gap-4" style="gap: 15px;">
                    <p class="font-medium"
                        style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666; font-weight: 500;">
                        Nama Pembeli</p>
                    <p class="font-normal text-justify"
                        style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #666666;">
                        Masukkan nama Pembeli.</p>
                </div>
                <input type="text" placeholder="Masukkan Nama Pembeli..." wire:model="name"
                    class="px-5 py-2 rounded-2xl border focus:outline-none focus:ring-0 w-full"
                    style="background-color: #eaeaea; border: 1px solid #d4d4d4; border-radius: 15px; padding: 10px 20px; font-family: Montserrat, sans-serif; font-size: 16px; color: #666666;" />
                <flux:error name="name" />
            </div>

            <!-- Pickup Date -->
            <div class="flex flex-col gap-4" style="gap: 15px;">
                <div class="flex flex-col gap-4" style="gap: 15px;">
                    <p class="font-medium"
                        style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666; font-weight: 500;">
                        Tanggal Ambil Pesanan</p>
                    <p class="font-normal text-justify"
                        style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #666666;">
                        Masukkan tanggal Ambil Pesanan.</p>
                </div>
                <div class="flex flex-col xl:flex-row gap-4 w-full" style="gap: 15px;">
                    <div x-init="picker = new Pikaday({
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
                    });" class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <flux:icon.calendar class="size-5" style="color: #666666;" />
                        </div>
                        <input type="text" x-ref="datepicker" wire:model.defer="date" placeholder="dd mm yyyy"
                            readonly class="pr-10 px-5 py-2 rounded-2xl border-2 focus:outline-none focus:ring-0 w-full"
                            style="background-color: #fafafa; border: 1.5px solid #adadad; border-radius: 15px; padding: 10px 20px; font-family: Montserrat, sans-serif; font-size: 16px; color: #666666;" />
                    </div>
                    <div x-data="{ time: @entangle('time').defer }" x-init="flatpickr($refs.input, {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: 'H:i',
                        time_24hr: true,
                        disableMobile: true,
                        onChange: function(selectedDates, dateStr) {
                            time = dateStr;
                            @this.set('time', dateStr);
                        }
                    });" class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="#666666">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <input x-ref="input" wire:model="time" type="text" placeholder="hh:mm"
                            class="pr-10 px-5 py-2 rounded-2xl border-2 focus:outline-none focus:ring-0 w-full"
                            style="background-color: #fafafa; border: 1.5px solid #adadad; border-radius: 15px; padding: 10px 20px; font-family: Montserrat, sans-serif; font-size: 16px; color: #666666;" />
                    </div>
                </div>
                <flux:error name="date" />
            </div>
        </div>

        <!-- Right Column - Notes -->
        <div class="flex-1 flex flex-col gap-4" style="gap: 15px; width: 100%; lg:height: 399px;">
            <div class="flex flex-col gap-4" style="gap: 15px;">
                <p class="font-medium"
                    style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666; font-weight: 500;">
                    Catatan Pesanan</p>
                <p class="font-normal text-justify"
                    style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #666666;">
                    Masukkan catatan pesanan apabila diperlukan.</p>
            </div>
            <textarea wire:model.defer="note" placeholder="Masukkan catatan pesanan..." rows="7"
                class="flex-1 px-5 py-2 rounded-2xl border focus:outline-none focus:ring-0 w-full resize-none min-h-[150px]"
                style="background-color: #fafafa; border: 1px solid #adadad; border-radius: 15px; padding: 10px 20px; font-family: Montserrat, sans-serif; font-size: 16px; color: #666666;"></textarea>
        </div>
    </div>


    <!-- Product List Section -->
    <div class="w-full border rounded-2xl overflow-hidden mb-8"
        style="background-color: #fafafa; border: 1px solid #d4d4d4; border-radius: 15px;">
        <div class="p-4 sm:px-[30px] sm:py-[25px] flex flex-col gap-5" style="gap: 20px;">
            <!-- Title -->
            <div class="flex items-center gap-5" style="gap: 20px;">
                <p class="font-medium"
                    style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666; font-weight: 500;">
                    Daftar Pesanan</p>
            </div>

            <!-- Product Items -->
            <div class="flex flex-col gap-0 pb-4" style="padding-bottom: 15px;">
                @foreach ($details as $id => $item)
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 py-4 border-b border-white"
                        style="border-bottom: 1px solid #ffffff;">
                        <!-- Left Side: Product Info -->
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-1" style="gap: 5px;">
                                <p class="font-medium"
                                    style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666;">
                                    {{ $item['name'] }}</p>
                            </div>
                            <div class="flex items-center gap-1" style="gap: 5px;">
                                <span class="font-normal"
                                    style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666;">{{ $item['quantity'] }}</span>
                                <span class="font-normal"
                                    style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666;">x</span>
                                <span class="font-normal"
                                    style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666;">Rp{{ number_format($item['price'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Right Side: Total and Controls -->
                        <div class="flex flex-col md:flex-row items-end md:items-center gap-4 w-full md:w-auto">
                            <!-- Total -->
                            <div class="flex items-center gap-1" style="gap: 5px;">
                                <span class="font-normal"
                                    style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666;">=</span>
                                <span class="font-medium"
                                    style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666;">Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                            </div>

                            <!-- Controls -->
                            <div class="flex items-center gap-4 sm:gap-6">
                                <!-- Delete Button -->
                                <button type="button" wire:click="removeItem('{{ $id }}')"
                                    class="flex items-center justify-center rounded-2xl shrink-0"
                                    style="background-color: #eb5757; width: 32px; height: 32px; border-radius: 15px;">
                                    <flux:icon.trash class="size-4" style="color: #f8f4e1;" />
                                </button>

                                <!-- Quantity Controls -->
                                <div class="flex items-center gap-3 shrink-0" style="gap: 11px;">
                                    <!-- Minus Button -->
                                    <button type="button" wire:click="decrementItem('{{ $id }}')"
                                        class="flex items-center justify-center rounded-full border-2"
                                        style="width: 30px; height: 30px; border: 1.5px solid #74512d; border-radius: 30px;">
                                        <flux:icon.minus class="size-4" style="color: #74512d;" />
                                    </button>

                                    <!-- Quantity Display -->
                                    <div class="flex items-center justify-center px-2 py-1 rounded-lg border"
                                        style="width: 36px; padding: 3px 10px; border: 1px solid rgba(116,81,45,0.2); border-radius: 10px;">
                                        <span class="font-semibold"
                                            style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666; font-weight: 600;">{{ $item['quantity'] }}</span>
                                    </div>

                                    <!-- Plus Button -->
                                    <button type="button" wire:click="incrementItem('{{ $id }}')"
                                        class="flex items-center justify-center rounded-full border-2"
                                        style="width: 30px; height: 30px; border: 1.5px solid #74512d; border-radius: 30px;">
                                        <flux:icon.plus class="size-4" style="color: #74512d;" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Add Product Button -->
            <button type="button" wire:click="$set('showItemModal', true)"
                class="flex items-center justify-center gap-1 px-6 py-2 rounded-2xl w-full font-semibold"
                style="background-color: #74512d; color: #f8f4e1; font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; border-radius: 15px; box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.1); padding: 10px 25px; gap: 5px;">
                <flux:icon.plus class="size-5" />
                Tambah Produk
            </button>
        </div>

        <!-- Subtotal Section -->
        <div class="border-t p-4 sm:px-[30px] sm:py-[25px] flex flex-col items-end gap-4"
            style="background-color: #fafafa; border-top: 1px solid #d4d4d4; gap: 15px;">
            <div class="flex items-center justify-between w-full font-medium"
                style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666; font-weight: 500;">
                <span>Subtotal {{ count($details) }} Produk</span>
                <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between w-full font-bold"
                style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666; font-weight: 700;">
                <span>Total Tagihan</span>
                <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="border-t p-4 sm:px-[30px] sm:py-[25px] flex flex-col items-end gap-4"
            style="background-color: #fafafa; border-top: 1px solid #d4d4d4; gap: 15px;">
            <div class="flex items-center justify-between w-full font-medium"
                style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #666666; font-weight: 500;">
                <span>Total Bayar</span>
                <span>Rp{{ number_format($paidAmount, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between w-full font-medium"
                style="font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; color: #eb5757; font-weight: 500;">
                <span>Sisa Tagihan</span>
                <span>Rp{{ number_format($total - $paidAmount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-end gap-4 mt-8">
        <a href="{{ route('transaksi.rincian-pesanan', $transactionId) }}" wire:navigate
            class="w-full sm:w-auto inline-flex items-center justify-center gap-1 px-6 py-2 rounded-2xl font-semibold transition-colors"
            style="background-color: #c4c4c4; color: #333333; font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; border-radius: 15px; box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.1); padding: 10px 25px; gap: 5px;">
            <flux:icon.x-mark class="size-5" />
            Batal
        </a>
        <button type="button" wire:click.prevent="save"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-1 px-6 py-2 rounded-2xl font-semibold transition-colors"
            style="background-color: #3f4e4f; color: #f8f4e1; font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; border-radius: 15px; box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.1); padding: 10px 25px; gap: 5px;">
            <flux:icon.check class="size-5" />
            Simpan Perubahan
        </button>
    </div>

    <flux:modal name="tambah-item" class="w-full max-w-[700px] h-full" variant="bare" wire:model="showItemModal">
        <div class="bg-[#fafafa] rounded-tl-[15px] h-full rounded-tr-[15px] p-[30px] flex flex-col gap-[30px]">
            {{-- Search & Filter --}}
            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-[30px]">
                <div class="flex-1 flex flex-col sm:flex-row items-center gap-4 sm:gap-[15px] w-full">
                    {{-- Search Input --}}
                    <div
                        class="flex-1 bg-[#ffffff] border border-[#666666] rounded-[20px] px-[15px] flex items-center w-full">
                        <flux:icon icon="magnifying-glass" class="size-[30px]" style="color: #666666;" />
                        <input type="text" wire:model.live="search" placeholder="Cari Produk"
                            class="flex-1 font-['Montserrat'] font-medium text-[16px] text-[#959595] bg-transparent border-none focus:outline-none focus:ring-0 p-[10px]"
                            style="line-height: 1;" />
                    </div>

                    {{-- Filter --}}
                    <div class="flex items-center justify-center">
                        <flux:icon icon="funnel" class="size-[25px]" style="color: #666666;" />
                        <span class="font-['Montserrat'] font-medium text-[16px] text-[#666666] px-[5px] py-[10px]"
                            style="line-height: 1;">Filter</span>
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <div class="h-0 w-full border-t border-[#d4d4d4]"></div>

            {{-- Product Grid --}}
            <div class="flex gap-[15px] h-[530px] overflow-y-auto w-full">
                <div class="flex-1 flex flex-wrap gap-[30px] content-start justify-center sm:justify-start">
                    @forelse ($products as $product)
                        <div class="w-full sm:min-w-[180px] sm:max-w-[210px] flex flex-col gap-[20px] pb-[25px]">
                            {{-- Product Card --}}
                            <div class="flex flex-col gap-[15px] items-center">
                                @if ($product->product_image)
                                    <img src="{{ asset('storage/' . $product->product_image) }}"
                                        alt="{{ $product->name }}"
                                        class="h-[119px] w-full sm:w-[182px] object-cover rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
                                @else
                                    <div
                                        class="h-[119px] w-full sm:w-[182px] bg-[#eaeaea] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center justify-center">
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
                            <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666]">Tidak ada produk yang
                                ditemukan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div
            class="bg-[#fafafa] shadow-[4px_0px_4px_0px_rgba(0,0,0,0.25)] p-[20px] flex flex-col sm:flex-row items-center justify-end gap-[10px]">
            <flux:modal.close class="w-full sm:w-auto">
                <button
                    class="w-full sm:w-auto bg-[#c4c4c4] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center justify-center gap-[5px]">
                    <flux:icon icon="x-mark" class="size-5" style="color: #333333;" />
                    <span class="font-['Montserrat'] font-semibold text-[16px] text-[#333333]"
                        style="line-height: 1;">Batal</span>
                </button>
            </flux:modal.close>

            <flux:modal.close class="w-full sm:w-auto">
                <button
                    class="w-full sm:w-auto bg-[#3f4e4f] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[25px] py-[10px] flex items-center justify-center gap-[5px]">
                    <flux:icon icon="check" class="size-5" style="color: #f6f6f6;" />
                    <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f6f6f6]"
                        style="line-height: 1;">Simpan Perubahan</span>
                </button>
            </flux:modal.close>
        </div>
    </flux:modal>

    <flux:modal name="tambah-customer" class="w-full max-w-md" wire:model="customerModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Pelanggan</flux:heading>
            </div>
            <div class="space-y-4">
                <div>
                    <label for="phoneCustomer" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <input type="text" id="phoneCustomer" wire:model="phoneCustomer"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required />
                    @error('phoneCustomer')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="nameCustomer" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="nameCustomer" wire:model="nameCustomer"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required />
                    @error('nameCustomer')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button type="button" icon="x-mark">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="button" icon="save" variant="primary" wire:click="addCustomer">Simpan
                </flux:button>
            </div>
        </div>
    </flux:modal>


</div>
