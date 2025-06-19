<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Daftar Produk</h1>
        <div class="flex gap-2 items-center">
            <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
            </button>

            <!-- Tombol Riwayat Pembaruan -->
            <button type="button" wire:click="riwayatPembaruan"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Riwayat Pembaruan
            </button>
        </div>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
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


    <div class="flex items-center justify-between mt-4 mb-4 flex-row w-full">
        <div class="relative w-full">
            <input type="radio" name="method" id="pesanan-reguler" value="pesanan-reguler" wire:model.live="method"
                class="absolute opacity-0 w-0 h-0">
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
                <flux:button variant="primary" icon="history"
                    href="{{ route('transaksi.riwayat', ['method' => $method]) }}">
                    Riwayat Pesanan
                </flux:button>
                @if ($method != 'siap-beli')
                    <flux:button icon="clipboard" type="button" variant="primary"
                        href="{{ route('transaksi.pesanan', ['method' => $method]) }}">
                        Lihat Daftar Pesanan
                    </flux:button>
                @endif
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
        <div class="flex gap-2 mr-4 items-center">
            <span class="text-sm mr-2">Tampilan Produk:</span>

            <!-- Grid View -->
            <div class="relative">
                <input type="radio" name="viewMode" id="grid-view" value="grid" wire:model.live="viewMode"
                    class="absolute opacity-0 w-0 h-0">
                <label for="grid-view" class="cursor-pointer">
                    <flux:icon icon="squares-2x2"
                        class="{{ $viewMode === 'grid' ? 'text-gray-100 bg-gray-600' : 'text-gray-800 bg-white' }} rounded-xl border border-gray-600 hover:text-gray-100 hover:bg-gray-600 transition-colors size-8" />
                </label>
            </div>

            <!-- List View -->
            <div class="relative">
                <input type="radio" name="viewMode" id="list-view" value="list" wire:model.live="viewMode"
                    class="absolute opacity-0 w-0 h-0">
                <label for="list-view" class="cursor-pointer">
                    <flux:icon icon="list-bullet"
                        class="{{ $viewMode === 'list' ? 'text-gray-100 bg-gray-600' : 'text-gray-800 bg-white' }} rounded-xl border border-gray-600 hover:text-gray-100 hover:bg-gray-600 transition-colors size-8" />
                </label>
            </div>
        </div>
    </div>

    @if ($viewMode === 'grid')
        {{-- grid view --}}
        <div class="bg-white">

            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Kolom Produk (kiri) -->
                <div @class([
                    'w-full ',
                    'lg:w-3/4' => count($cart) > 0,
                    'lg:w-full' => count($cart) === 0,
                ])>
                    <div @class([
                        'grid grid-cols-2 gap-4 p-4 max-h-[400px] overflow-y-auto',
                        'sm:grid-cols-2 lg:grid-cols-3' => count($cart) > 0,
                        'sm:grid-cols-3 lg:grid-cols-5' => count($cart) === 0,
                    ])>
                        @forelse ($products as $product)
                            <div
                                class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                                <div class="p-3">
                                    <div class="relative mb-3">
                                        @if ($product->product_image)
                                            <img src="{{ asset('storage/' . $product->product_image) }}"
                                                alt="{{ $product->name }}"
                                                class="w-full h-32 object-cover rounded-md">
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
                                            {{ number_format($product->pcs > 1 ? $product->pcs_price : $product->price, 0, ',', '.') }}
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
                <div @class(['w-full lg:w-1/4', 'hidden' => count($cart) === 0])>
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
                            </div>

                            <div class="p-4 border-t">
                                <div class="flex justify-between font-bold text-lg mb-2">
                                    <span>Total:</span>
                                    <span>Rp
                                        {{ number_format(array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart)), 0, ',', '.') }}</span>
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
    @elseif ($viewMode === 'list')
        {{-- list view --}}
        @if ($products->isEmpty())
            <div class="col-span-5 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center">
                <p class="text-gray-700 font-semibold">Belum ada produk.</p>
                <p class="text-gray-700">Tekan tombol “Tambah Produk” untuk menambahkan produk.</p>
            </div>
        @else
            <div class="bg-white rounded-xl border">
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Produk</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status Tampil</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status Rekomendasi
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Nilai</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Penilai</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Harga Jual</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($products as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('produk.edit', $product->id) }}"
                                            class="hover:bg-gray-50 cursor-pointer">
                                            {{ $product->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 space-x-2 whitespace-nowrap">
                                        @if ($product->is_active)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ">
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 space-x-2 whitespace-nowrap">
                                        @if ($product->is_recommended)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ">
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @if ($product->reviews->count() > 0)
                                            {{ number_format($product->reviews->avg('rating'), 1) }}
                                            <i class="bi bi-star-fill text-yellow-500"></i>
                                        @else
                                            Belum ada penilaian
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @if ($product->reviews->count() > 0)
                                            ({{ $product->reviews->count() }} Penilai)
                                        @else
                                            Belum ada penilaian
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        Rp
                                        {{ number_format($product->pcs > 1 ? $product->pcs_price : $product->price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4">
                    {{ $products->links() }}
                </div>
            </div>
        @endif
    @endif


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


</div>
