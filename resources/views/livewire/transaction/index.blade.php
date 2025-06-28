<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Daftar Produk</h1>
        <div class="flex gap-2 items-center">
            <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
            </button>
        </div>
    </div>
    <div class="bg-white shadow-lg rounded-lg p-4">
        @if ($todayShiftStatus == 'open')
        <div class="flex flex-row justify-between items-center">
            <div class="flex flex-row items-center gap-6">
                <p class="text-sm text-gray-500">No Sesi : {{ $todayShiftNumber }}</p>
                <p class="text-sm text-gray-500">Tanggal Buka : {{
                    \Carbon\Carbon::parse($todayShiftStartTime)->format('d/m/Y H:i') }}</p>
                <p class="text-sm text-gray-500">Dibuka Oleh : {{ $todayShiftOpenedBy }}</p>
            </div>
            <div class="flex gap-2 items-center">
                <flux:button variant="primary" icon="cashier" wire:click="$set('closeShiftModal', true)"
                    label="Tutup Sesi Penjualan">
                    <span>Tutup Sesi Penjualan</span>
                </flux:button>
                <flux:button variant="primary" icon="history" label="Riwayat Sesi Penjualan"
                    wire:click='openHistoryShiftModal' />
            </div>
        </div>
        @else
        <div class="flex flex-row justify-between items-center">
            <p class="text-sm text-gray-500">Sesi Belum Dibuka</p>
            <div class="flex gap-2 items-center">
                <flux:button variant="primary" icon="cashier" wire:click="$set('openShiftModal', true)"
                    label="Buka Sesi Penjualan">
                    <span>Buka Sesi Penjualan</span>
                </flux:button>
                <flux:button variant="primary" icon="history" label="Riwayat Sesi Penjualan"
                    wire:click='openHistoryShiftModal' />
            </div>
        </div>
        @endif
    </div>
    <div class="flex items-center border bg-white shadow-lg rounded-lg p-4 mt-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">Pilih salah satu metode penjualan terlebih dahulu (Siap Beli, Pesanan
                Reguler, atau Pesanan Box), lalu tekan tombol "Tambah Produk" untuk menambahkan produk ke metode yang
                diinginkan.
            </p>
            <ul class="mt-2 list-disc pl-5">
                <li class="text-sm text-gray-500">
                    <strong>Siap Beli</strong>
                    untuk produk yang ada di rak penjualan yang bentuknya per potong atau per buah.
                </li>
                <li class="text-sm text-gray-500">
                    <strong>Pesanan Reguler</strong>
                    untuk produk pesanan yang bentuknya loyangan atau paketan.
                </li>
                <li class="text-sm text-gray-500">
                    <strong>Pesanan Kotak</strong>
                    untuk paket khusus atau snack box dengan banyak produk dalam satu kotak.
                </li>
            </ul>

        </div>
    </div>


    <div class="bg-white shadow-lg rounded-lg p-4 mt-4">
        @if ($todayShiftStatus == 'open')
        <div>
            <div class="flex items-center justify-between mt-4 mb-4 flex-row w-full">
                <div class="relative w-full">
                    <input type="radio" name="method" id="pesanan-reguler" value="pesanan-reguler"
                        wire:model.live="method" class="absolute opacity-0 w-0 h-0">
                    <label for="pesanan-reguler" class="cursor-pointer">
                        <div
                            class="{{ $method === 'pesanan-reguler' ? 'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
                            <flux:icon icon="cake" class=" size-8" />
                            <span class="text-center hidden md:block">Pesanan Kue Reguler</span>
                        </div>
                    </label>
                </div>
                <div class="relative w-full">
                    <input type="radio" name="method" id="pesanan-kotak" value="pesanan-kotak" wire:model.live="method"
                        class="absolute opacity-0 w-0 h-0">
                    <label for="pesanan-kotak" class="cursor-pointer">
                        <div
                            class="{{ $method === 'pesanan-kotak' ? 'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
                            <flux:icon icon="cube" class="size-8" />
                            <span class="text-center hidden md:block">Pesanan Kue Kotak</span>
                        </div>
                    </label>
                </div>

                <div class="relative w-full">
                    <input type="radio" name="method" id="siap-beli" value="siap-beli" wire:model.live="method"
                        class="absolute opacity-0 w-0 h-0">
                    <label for="siap-beli" class="cursor-pointer">
                        <div
                            class="{{ $method === 'siap-beli' ? 'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
                            <flux:icon icon="dessert" class="size-8" />
                            <span class="text-center hidden md:block">Kue Siap Beli</span>
                        </div>
                    </label>
                </div>
            </div>
            <div class="flex justify-between items-center mb-4">
                <!-- Search Input -->
                <div class="p-4 flex">
                    <input wire:model.live="search" placeholder="Cari..."
                        class="w-lg px-4 py-2 border border-accent rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <flux:button :loading="false" class="ml-2" variant="ghost">
                        <flux:icon.funnel variant="mini" />
                        <span>Filter</span>
                    </flux:button>
                </div>
                <div class="flex gap-2 items-center">
                    <div class="flex gap-2 items-center">
                        @if ($method != 'siap-beli')
                        <flux:button icon="clipboard" type="button" variant="primary"
                            href="{{ route('transaksi.pesanan', ['method' => $method]) }}">
                            Daftar Pesanan
                        </flux:button>
                        @endif
                        <flux:button variant="primary" icon="history"
                            href="{{ route('transaksi.riwayat', ['method' => $method]) }}" />
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-center mb-4">
                <div class="flex gap-2 items-center">
                    <flux:dropdown>
                        <flux:button variant="ghost">
                            @if ($filterStatus)
                            {{ $filterStatus === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                            @else
                            Semua Kategori
                            @endif
                            ({{ $products->total() }})
                            <flux:icon.chevron-down variant="mini" />
                        </flux:button>
                        <flux:menu>
                            <flux:menu.radio.group wire:model.live="filterStatus">
                                <flux:menu.radio value="">Semua Kategori</flux:menu.radio>
                                <flux:menu.radio value="aktif">Aktif</flux:menu.radio>
                                <flux:menu.radio value="nonaktif">Tidak Aktif</flux:menu.radio>
                            </flux:menu.radio.group>
                        </flux:menu>
                    </flux:dropdown>
                </div>
                <flux:dropdown>
                    <flux:button variant="ghost">
                        Urutkan Produk
                        <flux:icon.chevron-down variant="mini" />

                    </flux:button>

                    <flux:menu>
                        <flux:menu.radio.group wire:model="sortByCategory">
                            <flux:menu.radio value="name">Nama</flux:menu.radio>
                            <flux:menu.radio value="status">Status</flux:menu.radio>
                            <flux:menu.radio value="product" checked>Jenis Produk</flux:menu.radio>
                        </flux:menu.radio.group>
                    </flux:menu>
                </flux:dropdown>
            </div>
            {{-- grid view --}}
            <div>

                <div class="flex flex-col lg:flex-row gap-4">
                    <!-- Kolom Produk (kiri) -->
                    <div @class([ 'w-full ' , 'lg:w-3/4'=> count($cart) > 0,
                        'lg:w-full' => count($cart) === 0,
                        ])>
                        <div @class([ 'grid grid-cols-2 gap-4 p-4 max-h-[400px] overflow-y-auto'
                            , 'sm:grid-cols-2 lg:grid-cols-3'=> count($cart) > 0,
                            'sm:grid-cols-3 lg:grid-cols-5' => count($cart) === 0,
                            ])>
                            @forelse ($products as $product)
                            <div class="border-0 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                                <div class="p-3">
                                    <div class="relative mb-3">
                                        @if ($product->product_image)
                                        <img src="{{ asset('storage/' . $product->product_image) }}"
                                            alt="{{ $product->name }}" class="w-full h-32 object-cover rounded-md">
                                        @else
                                        <img src="{{ asset('img/no-img.jpg') }}" alt="Gambar Produk"
                                            class="w-full h-32 object-cover rounded-md bg-gray-100">
                                        @endif
                                        @if ($method == 'siap-beli')
                                        <div class="absolute top-2 left-2 flex gap-1">
                                            <span
                                                class="bg-gray-600 text-white text-xs px-1.5 py-1 rounded-full flex items-center">
                                                {{ $product->stock }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="text-center">
                                        <a href="{{ route('transaksi.rincian-produk', $product->id) }}"
                                            class="text-sm font-semibold mb-1 truncate">{{ $product->name }}</a>

                                        <div class="flex items-center justify-center gap-1 text-xs mb-1">
                                            @if ($product->reviews->count() > 0)
                                            <span class="text-yellow-500 flex items-center">
                                                {{ number_format($product->reviews->avg('rating'), 1) }}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-0.5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </span>
                                            <span class="text-gray-500">
                                                @if ($product->reviews->count() > 10)
                                                (10+)
                                                @else
                                                ({{ $product->reviews->count() }})
                                                @endif
                                            </span>
                                            @else
                                            <span class="text-gray-500 text-xs">Belum ada penilaian</span>
                                            @endif
                                        </div>

                                        <p class="text-gray-800 font-bold mb-3">
                                            Rp
                                            {{ number_format($product->price, 0, ',', '.') }}
                                        </p>

                                        @if (count($cart) > 0 && isset($cart[$product->id]))
                                        @php
                                        $id = $product->id;
                                        $item = $cart[$id];
                                        @endphp
                                        <div
                                            class="w-full flex items-center mt-2 justify-between gap-2 bg-gray-100 rounded-xl">
                                            <flux:button variant="ghost" icon="minus" type="button"
                                                wire:click="decrementItem('{{ $id }}')" />
                                            <span class="mx-2 text-sm bg-white px-3 py-1 my-1 rounded border">{{
                                                $item['quantity'] }}</span>
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
                            @empty
                            <div
                                class="col-span-5 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center">
                                <p class="text-gray-700 font-semibold">Belum ada produk.</p>
                                <p class="text-gray-700">Tekan tombol “Tambah Produk” di Halaman Produk untuk
                                    menambahkan produk.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Kolom Ringkasan Pesanan (kanan) -->
                    <div @class(['w-full lg:w-1/4', 'hidden'=> count($cart) === 0])>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-4">
                            <div class="p-4 border-b">
                                <h2 class="text-lg font-bold">Ringkasan Pesanan</h2>
                            </div>

                            @if (count($cart) > 0)
                            <div class="p-4 max-h-[250px] overflow-y-auto">
                                <ul class="space-y-3">
                                    @foreach ($cart as $id => $item)
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
                                                    Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.')
                                                    }}
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
                                                    <span class="mx-2 text-sm bg-white px-3 py-1 my-1 rounded border">{{
                                                        $item['quantity'] }}</span>
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
                            </div>

                            <div class="p-4 border-t">
                                <div class="flex justify-between font-bold text-lg mb-2">
                                    <span>Total:</span>
                                    <span>Rp
                                        {{ number_format(array_sum(array_map(fn($item) => $item['price'] *
                                        $item['quantity'],
                                        $cart)), 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <flux:button class="w-full" icon="x-mark" wire:click="clearCart">
                                        Batal
                                    </flux:button>
                                    <flux:button class="w-full" variant="primary" icon="shopping-cart"
                                        wire:click="checkout">
                                        Checkout
                                    </flux:button>
                                </div>
                            </div>
                            @else
                            <div class="p-8 text-center">
                                <div class="flex justify-center mb-4 text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 mb-1">Keranjang kosong</p>
                                <p class="text-sm text-gray-400">Tambahkan produk terlebih dahulu</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center flex-col gap-4 bg-gray-200 rounded-lg p-4 mb-4">
            <flux:icon icon="cashier" class="size-16" />
            <h2 class="text-2xl font-bold">Sesi Penjualan Belum Dibuka</h2>
            <p class="text-gray-500">Buka Sesi Penjualan untuk melakukan Penjualan</p>
        </div>
        @endif

    </div>


    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Transaksi</flux:heading>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @foreach ($activityLogs as $log)
                <div class="border-b py-2">
                    <div class="text-sm font-medium">{{ $log->description }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $log->causer->name ?? 'System' }} -
                        {{ $log->created_at->format('d M Y H:i') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </flux:modal>

    <!-- Modal Buka Sesi Penjualan -->
    <flux:modal name="open-shift-modal" class="w-full max-w-md" wire:model="openShiftModal">
        <div class="space-y-6">
            <div class="p-4">
                <flux:heading size="lg">Mulai Sesi Penjualan</flux:heading>
                <flux:label class="mt-8 mb-2">Jumlah awal uang di Laci Kasir</flux:label>
                <input type="number" class="border border-gray-300 rounded-md p-2 w-full text-right"
                    wire:model.number="initialCash" placeholder="Rp0" />
            </div>
        </div>
        <div class="mt-6 flex justify-end space-x-2">
            <flux:modal.close>
                <flux:button type="button" icon="x-mark">Batal</flux:button>
            </flux:modal.close>
            <flux:button type="button" icon="play" iconVariant="solid" variant="primary" wire:click="openShift">Buka
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal Tutup Sesi Penjualan -->
    <flux:modal name="close-shift-modal" class="w-full max-w-md" wire:model="closeShiftModal">
        <div class="space-y-6">
            <div class="p-4">
                <flux:heading size="lg">Rincian Sesi</flux:heading>
                <div class="flex items-center justify-between mt-4 mb-2">
                    <span class="text-sm text-gray-500">No Sesi</span>
                    <span class="text-sm text-gray-500">{{ $todayShiftNumber }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Tanggal Buka</span>
                    <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($todayShiftStartTime)->format('d/m/Y
                        H:i') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Dibuka Oleh</span>
                    <span class="text-sm text-gray-500">{{ $todayShiftOpenedBy }}</span>
                </div>
                <h4 class="text-sm font-semibold mt-2">Laci Kasir</h4>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Jumlah awal</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($initialCash, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Penerimaan</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($receivedCash, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Refund</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($refundTotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Diskon/Hadian</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($discountToday, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Jumlah Yang Diharapkan</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($expectedCash, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Jumlah akhir uang di Laci Kasir</span>
                    <input type="number" class="border border-gray-300 rounded-md w-28 text-right"
                        wire:model.number="finalCash" placeholder="Rp0" />
                </div>
            </div>
        </div>
        <div class="mt-6 flex justify-end space-x-2">
            <flux:modal.close>
                <flux:button type="button" icon="x-mark">Batal</flux:button>
            </flux:modal.close>
            <flux:button type="button" icon="stop" iconVariant="solid" variant="primary" wire:click="closeShift">Tutup
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal Tutup Sesi Penjualan -->
    <flux:modal name="finish-shift-modal" class="w-full max-w-md" wire:model="finishShiftModal">
        <div class="space-y-6">
            <div class="p-4">
                <flux:heading size="lg">Rincian Sesi</flux:heading>
                <div class="flex items-center justify-between mt-4 mb-2">
                    <span class="text-sm text-gray-500">No Sesi</span>
                    <span class="text-sm text-gray-500">{{ $todayShiftNumber }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Tanggal Buka</span>
                    <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($todayShiftStartTime)->format('d/m/Y
                        H:i') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Tanggal Tutup</span>
                    <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($todayShiftEndTime)->format('d/m/Y
                        H:i') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Dibuka Oleh</span>
                    <span class="text-sm text-gray-500">{{ $todayShiftOpenedBy }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Ditutup Oleh</span>
                    <span class="text-sm text-gray-500">{{ $todayShiftClosedBy }}</span>
                </div>
                <h4 class="text-sm font-semibold mt-2">Laci Kasir</h4>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Jumlah awal</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($initialCash, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Penerimaan</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($receivedCash, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Refund</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($refundTotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Diskon/Hadian</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($discountToday, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Jumlah Yang Diharapkan</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($expectedCash, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Jumlah Sebenarnya</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($finalCash, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Selisih Jumlah</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($finalCash - $expectedCash, 0, ',', '.')
                        }}</span>
                </div>
            </div>
        </div>
        <div class="mt-6 space-x-2 w-full">
            <flux:modal.close class="w-full">
                <flux:button type="button" variant="primary" icon="arrow-long-left" class="w-full">Kembali ke Kasir
                </flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    <!-- Modal Riwayat Sesi Penjualan -->
    <flux:modal name="history-shift-modal" class="w-full max-w-md max-h-[90vh] flex flex-col"
        wire:model="showHistoryShiftModal">
        <div class="flex flex-col flex-1 min-h-0">
            <!-- Container utama flex -->
            <!-- Header -->
            <div class="p-4">
                <flux:heading size="lg">Riwayat Sesi Penjualan</flux:heading>
            </div>

            <!-- Search Input -->
            <div class="px-4 pb-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live="searchHistoryShift"
                        class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari shift...">
                </div>
                <div class="relative mt-2">
                    <input type="date" wire:model.live="searchDate" onclick="this.showPicker()"
                        data-date="{{ $searchDate ? \Carbon\Carbon::parse($searchDate)->format('d/m/Y') : 'dd/mm/yyyy' }}"
                        class="tanggal" placeholder="Cari berdasarkan tanggal...">

                </div>
            </div>
            <!-- Area Scroll (Hanya bagian inilah yang discroll) -->
            <div class="flex-1 overflow-y-auto px-4 pb-2">
                <!-- flex-1 + overflow-auto -->
                @forelse ($historyShifts as $shift)
                <div class="border-b py-2">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-semibold">{{ $shift->shift_number }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $shift->start_time ? \Carbon\Carbon::parse($shift->start_time)->format('d/m/Y H:i') :
                                'Belum Dibuka' }} -
                                {{ $shift->end_time ? \Carbon\Carbon::parse($shift->end_time)->format('d/m/Y H:i') :
                                'Belum Ditutup' }}
                            </p>
                        </div>
                        <div>
                            @if ($shift->status === 'open')
                            <span class="text-green-500 text-xs font-semibold">Terbuka</span>
                            @else
                            <span class="text-red-500 text-xs font-semibold">Ditutup</span>
                            @endif
                        </div>
                        <div>
                            <flux:button variant="ghost" icon="eye" wire:click="viewShift('{{ $shift->id }}')"
                                class="text-gray-500" />
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-center text-gray-500">Tidak ada riwayat sesi penjualan.</p>
                @endforelse
            </div>

            <!-- Footer (Tetap di bawah tanpa scroll) -->
            <div class="p-4 border-t mt-auto">
                <flux:modal.close class="w-full">
                    <flux:button type="button" variant="primary" icon="arrow-long-left" class="w-full">
                        Kembali ke Kasir
                    </flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Detail Riwayat Sesi Penjualan -->
    <flux:modal name="detail-history-shift-modal" class="w-full max-w-md" wire:model="showDetailHistoryShiftModal">
        <div class="space-y-6">
            @if ($selectedShift)

            <div class="p-4">
                <flux:heading size="lg">Rincian Sesi</flux:heading>
                <div class="flex items-center justify-between mt-4 mb-2">
                    <span class="text-sm text-gray-500">No Sesi</span>
                    <span class="text-sm text-gray-500">{{ $selectedShift->shift_number }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Tanggal Buka</span>
                    <span class="text-sm text-gray-500">{{ $selectedShift->start_time ?
                        \Carbon\Carbon::parse($selectedShift->start_time)->format('d/m/Y
                        H:i') : 'Belum Dibuka' }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Tanggal Tutup</span>
                    <span class="text-sm text-gray-500">{{ $selectedShift->end_time ?
                        \Carbon\Carbon::parse($selectedShift->end_time)->format('d/m/Y
                        H:i') : 'Belum Ditutup' }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Dibuka Oleh</span>
                    <span class="text-sm text-gray-500">{{ $selectedShift->openedBy->name }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Ditutup Oleh</span>
                    <span class="text-sm text-gray-500">{{ $selectedShift->closedBy->name ?? '-' }}</span>
                </div>
                <h4 class="text-sm font-semibold mt-2">Laci Kasir</h4>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Jumlah awal</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($selectedShift->initial_cash, 0, ',', '.')
                        }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Penerimaan</span>
                    @php
                    $transactionPenerimaan = \App\Models\Transaction::where('created_by_shift', $selectedShift->id)
                    ->whereHas('payments', function ($query) {
                    $query->where('payment_method', 'tunai');
                    })
                    ->sum('total_amount');
                    @endphp
                    <span class="text-sm text-gray-500">Rp{{ number_format($transactionPenerimaan, 0, ',', '.')
                        }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Refund</span>
                    @php
                    $transactionRefund = \App\Models\Transaction::where('refund_by_shift', $selectedShift->id)
                    ->sum('total_refund');
                    @endphp
                    <span class="text-sm text-gray-500">Rp{{ number_format($transactionRefund, 0, ',', '.')
                        }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Diskon/Hadian</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format(0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Jumlah Yang Diharapkan</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($selectedShift->initial_cash +
                        $transactionPenerimaan - $transactionRefund, 0, ',',
                        '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Jumlah Sebenarnya</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($selectedShift->final_cash, 0, ',', '.')
                        }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 mb-2">
                    <span class="text-sm text-gray-500">Selisih Jumlah</span>
                    <span class="text-sm text-gray-500">Rp{{ number_format($selectedShift->final_cash -
                        ($selectedShift->initial_cash + $transactionPenerimaan - $transactionRefund), 0, ',', '.')
                        }}</span>
                </div>
            </div>
            @else
            <div class="p-4 flex flex-col items-center">
                <h2 class="text-lg font-semibold">
                    Tidak ada rincian sesi yang tersedia.
                </h2>
            </div>
            @endif
        </div>
        <div class="mt-6 space-x-2 w-full">
            <flux:modal.close class="w-full">
                <flux:button type="button" variant="primary" class="w-full">Tutup
                </flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    @section('css')
    <style>
        .tanggal {
            position: relative;
            width: 100%;
            height: 2.5rem;
            /* Sesuaikan tinggi input */
            padding: 0.5rem 2.5rem 0.5rem 0.75rem;
            /* Biar ada ruang untuk teks dan ikon */
            color: transparent;
            /* Sembunyikan teks aslinya */
            background-color: #f9fafb;
            /* gray-50 */
            border: 1px solid #d1d5db;
            /* gray-300 */
            border-radius: 0.5rem;
            /* rounded-lg */
            font-size: 0.875rem;
            /* text-sm */
            outline: none;
        }

        .tanggal:before {
            position: absolute;
            top: 50%;
            left: 0.75rem;
            transform: translateY(-50%);
            content: attr(data-date);
            display: inline-block;
            color: #111827;
            /* gray-900 */
            pointer-events: none;
            font-size: 0.875rem;
            /* text-sm */
        }

        .tanggal::-webkit-datetime-edit,
        .tanggal::-webkit-inner-spin-button,
        .tanggal::-webkit-clear-button {
            display: none;
        }

        .tanggal::-webkit-calendar-picker-indicator {
            position: absolute;
            top: 50%;
            right: 0.75rem;
            transform: translateY(-50%);
            opacity: 1;
            color: #6b7280;
            /* gray-500 */
            cursor: pointer;
        }
    </style>
    @endsection
</div>