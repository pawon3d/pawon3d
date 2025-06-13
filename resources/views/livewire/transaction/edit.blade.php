<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('transaksi.rincian-pesanan', $transactionId) }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
            Kembali
        </a>
        <h1 class="text-2xl">Ubah Pesanan</h1>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="exclamation-triangle" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Lorem ipsum dolor sit amet consectetur. Viverra erat aenean mauris adipiscing nibh. Nullam adipiscing
                dignissim consequat volutpat augue. Auctor euismod arcu at euismod. Odio cras proin eget facilisis vitae
                at. Non at vitae lorem nec quis urna.
            </p>
        </div>
    </div>

    <div class="w-full flex md:flex-row flex-col gap-8 mt-4">
        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Nama Pemesan</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan Nama Pemesan
            </p>
            <flux:input placeholder="Masukkan Nama Pemesan..." wire:model="name" />
            <flux:error name="name" />
            <flux:label>Nomor Telepon</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan Nomor Telepon Aktif
            </p>
            <flux:input placeholder="081122345678" wire:model="phone" />
            <flux:error name="phone" />
            <flux:label>Tanggal Pengambilan Pesanan</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan kapan tanggal pengambilan pesanan akan dilakukan.
            </p>
            <div class="flex flex-row gap-2 w-full">
                <div x-init="picker = new Pikaday({
                    field: $refs.datepicker,
                    format: 'DD-MM-YYYY',
                    toString(date, format) {
                        const day = String(date.getDate()).padStart(2, 0);
                        const month = String(date.getMonth() + 1).padStart(2, 0);
                        const year = date.getFullYear();
                        return `${day}-${month}-${year}`;
                    },
                    onSelect: function() {
                        @this.set('date', moment(this.getDate()).format('DD-MM-YYYY'));
                        console.log(moment(this.getDate()).format('DD-MM-YYYY'));
                    }
                });" class="relative">
                    <!-- Icon kalender -->
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <!-- Heroicons outline calendar icon -->
                        <flux:icon icon="calendar-date-range" />
                    </div>

                    <input type="text"
                        class="pr-10 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 w-full"
                        x-ref="datepicker" wire:model.defer="date" id="datepicker" placeholder="dd-mm-yyyy" readonly />
                </div>
                <div x-data="{ time: @entangle('time').defer }" x-init="flatpickr($refs.input, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'H:i',
                    time_24hr: true,
                    onChange: function(selectedDates, dateStr) {
                        time = dateStr;
                        @this.set('time', dateStr);
                    }
                });" class="relative">
                    <!-- Icon jam -->
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <!-- Heroicons outline clock icon -->
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <input x-ref="input" wire:model="time" type="text"
                        class="pr-10 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 w-full"
                        placeholder="00:00" />
                </div>

            </div>

            <flux:error name="date" />
        </div>

        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Catatan Pesanan</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan catatan pesanan apabila ada pesan atau sesuatu yang penting untuk diberitahu.
            </p>
            <flux:textarea wire:model.defer="note" rows="7" placeholder="Masukkan Catatan Pesanan...." />
        </div>
    </div>


    <div class="w-full mt-8 mb-8 flex flex-col gap-4 rounded-lg bg-white border border-gray-200 p-4 shadow-sm">
        <div class="flex items-center justify-start mb-4">
            <h2 class="text-lg font-semibold">Detail Pesanan</h2>
        </div>
        <div>
            <ul class="space-y-3">
                @foreach ($details as $id => $item)
                    <li class="flex justify-between items-start border-b pb-2">
                        <div class="text-left">
                            <div class="flex items-center justify-start mt-1">
                                <p class="text-sm font-semibold">{{ $item['name'] }}</p>
                            </div>
                            <div class="flex items-center justify-start mt-2">
                                <p class="text-sm text-gray-500 py-1">{{ $item['quantity'] }} x
                                    Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center justify-end mt-1">
                                <p class="text-sm text-right text-gray-500">
                                    Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="flex items-center mt-2 justify-end gap-1">
                                <div class="flex items-center gap-2">
                                    <flux:button icon="trash" variant="ghost"
                                        wire:click="removeItem('{{ $id }}')" />
                                </div>
                                <div class="bg-gray-100 rounded-xl px-4 flex items-center gap-2">
                                    <button
                                        class="text-gray-500 hover:text-red-500 w-5 h-5 flex items-center justify-center rounded-full"
                                        wire:click="decrementItem('{{ $id }}')">
                                        -
                                    </button>
                                    <span
                                        class="mx-2 text-sm bg-white px-3 py-1 my-1 rounded border">{{ $item['quantity'] }}</span>
                                    <button
                                        class="text-gray-500 hover:text-green-500 w-5 h-5 flex items-center justify-center rounded-full"
                                        wire:click="incrementItem('{{ $id }}')">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <flux:button icon="plus" type="button" variant="primary" wire:click="$set('showItemModal', true)"
                class="w-full mt-4">
                Tambah Item
            </flux:button>
        </div>
        <div class="w-full flex flex-col border-t border-b border-gray-200">
            <div class="flex flex-row justify-between w-full">
                <p class="p-4 text-sm text-gray-500">Subtotal {{ count($details) }} Produk</p>
                <p class="p-4 text-sm text-gray-500">
                    Rp{{ number_format($total, 0, ',', '.') }}
                </p>
            </div>
            <div class="flex flex-row justify-between w-full">
                <p class="p-4 text-sm text-gray-500 font-bold">Total Tagihan</p>
                <p class="p-4 text-sm text-gray-500 font-bold">
                    Rp{{ number_format($total, 0, ',', '.') }}
                </p>
            </div>
        </div>
        <div class="w-full flex flex-col">
            <div class="flex flex-row justify-between w-full">
                <p class="p-4 text-sm text-gray-500">Pembayaran</p>
                <p class="p-4 text-sm text-gray-500">
                    Rp{{ number_format($paidAmount, 0, ',', '.') }}
                </p>
            </div>
            <div class="flex flex-row justify-between w-full">
                <p class="p-4 text-sm text-red-500 font-bold">Sisa Tagihan</p>
                <p class="p-4 text-sm text-red-500 font-bold">
                    Rp{{ number_format($total - $paidAmount, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="w-full flex flex-col gap-4">

        <flux:label>Metode Pembayaran</flux:label>
        <p class="text-sm text-gray-500">
            Pilih Metode Pembayaran (Tunai, Transfer, atau QRIS). Jika Bukan Tunai maka akan diminta bukti pembayaran
            berupa
            gambar (.jpg dan .png)
        </p>
        <flux:select wire:model.live="paymentMethod" class="mt-2" placeholder="Pilih Metode Pembayaran">
            <flux:select.option value="tunai" class="text-gray-700">Tunai</flux:select.option>
            <flux:select.option value="transfer" class="text-gray-700">Transfer</flux:select.option>
            <flux:select.option value="qris" class="text-gray-700">QRIS</flux:select.option>
        </flux:select>
        <flux:error name="paymentMethod" />

        @if ($paymentMethod == 'transfer')
            <div class="mt-2 flex flex-row gap-2 w-full">
                <div class="w-1/4">
                    <flux:select wire:model.live="paymentChannelId" placeholder="Pilih Bank Tujuan">
                        @foreach ($paymentChannels as $channel)
                            <flux:select.option value="{{ $channel->id }}" class="text-gray-700">
                                {{ $channel->bank_name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="paymentChannelId" />
                </div>
                <div class="w-3/4">
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
                @if ($paymentMethod == 'tunai')
                    <span class="text-xs text-gray-500">
                        Nominal Uang Yang Diterima
                    </span>
                @endif
                <flux:input placeholder="Masukkan Nominal Pembayaran..." wire:model.number.live="paidAmount" />
                <flux:error name="paidAmount" />
            </div>
            @if ($paymentMethod == 'tunai')
                <div class="flex flex-col gap-2 w-full">
                    <span class="text-xs text-gray-500">
                        Nominal Uang Kembalian
                    </span>
                    <flux:input placeholder="Kembalian"
                        value="{{ number_format(max(0, $paidAmount - $total), 0, ',', '.') }}" readonly />
                </div>
            @endif
        </div>

        @if ($paymentMethod == 'transfer')
            <div class="mb-5 w-full">
                <div class="flex flex-row items-center gap-4">
                    <label
                        class="relative items-center cursor-pointer font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg px-4 inline-flex  bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2)">
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

    <div class="flex justify-end mt-16 gap-4">
        <flux:button icon="x-mark" type="button" :loading="false"
            href="{{ route('transaksi.rincian-pesanan', $transactionId) }}">
            Batal
        </flux:button>
        <flux:button icon="archive-box" :loading="false" type="button" variant="primary"
            wire:click.prevent="save">
            Simpan Perubahan
        </flux:button>
    </div>

    <flux:modal name="tambah-item" class="w-1/2 max-w-sm" wire:model="showItemModal">
        <div class="w-full">
            <div class="grid grid-cols-1 gap-4 p-4 max-h[400px] overflow-y-auto">
                @foreach ($products as $product)
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-3">
                            <div class="relative mb-3">
                                @if ($product->product_image)
                                    <img src="{{ asset('storage/' . $product->product_image) }}"
                                        alt="{{ $product->name }}" class="w-full h-32 object-cover rounded-md">
                                @else
                                    <img src="{{ asset('img/no-img.jpg') }}" alt="Gambar Produk"
                                        class="w-full h-32 object-cover rounded-md bg-gray-100">
                                @endif

                                <div class="absolute top-2 left-2 flex gap-1">
                                    <span
                                        class="bg-gray-600 text-white text-xs px-1.5 py-1 rounded-full flex items-center">
                                        {{ $product->stock }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-center">
                                <h3 class="text-sm font-semibold mb-1 truncate">{{ $product->name }}</h3>

                                <p class="text-gray-800 font-bold mb-3">
                                    Rp
                                    {{ number_format($product->pcs > 1 ? $product->pcs_price : $product->price, 0, ',', '.') }}
                                </p>

                                @if (isset($details[$product->id]))
                                    @php
                                        $id = $product->id;
                                        $item = $details[$id];
                                    @endphp
                                    <div
                                        class="w-full flex items-center mt-2 justify-between gap-2 bg-gray-100 rounded-xl">
                                        <flux:button variant="ghost" icon="minus" type="button"
                                            wire:click="decrementItem('{{ $id }}')" />
                                        <span
                                            class="mx-2 text-sm bg-white px-3 py-1 my-1 rounded border">{{ $item['quantity'] }}</span>
                                        <flux:button variant="ghost" icon="plus" type="button"
                                            wire:click="incrementItem('{{ $id }}')" />
                                    </div>
                                @else
                                    <flux:button class="w-full" variant="primary"
                                        wire:click="addToCart('{{ $product->id }}')">
                                        Tambah
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </flux:modal>



</div>
