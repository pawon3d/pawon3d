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
        <flux:icon icon="exclamation-triangle" />
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
                    class="{{ $method === 'pesanan-reguler' ?  'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
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
                    class="{{ $method === 'pesanan-kotak' ?  'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
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
                    class="{{ $method === 'siap-beli' ?  'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
                    <flux:icon icon="at-symbol" class="size-8" />
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
            <a href="{{ route('produk.tambah', ['method' => $method]) }}"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produk
            </a>
        </div>
    </div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex gap-2 items-center">
            <flux:dropdown>
                <flux:button variant="ghost">
                    @if($filterStatus)
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
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4 mt-4">
            @forelse($products as $product)
            <div class="p-4 text-center">
                <a href="{{ route('produk.edit', $product->id) }}" class="hover:bg-gray-50 cursor-pointer">
                    <div class="flex justify-center mb-4">
                        @if($product->product_image)
                        <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->name }}"
                            class="w-full h-36 object-fill rounded-lg border border-gray-200" />
                        @else
                        <img src="{{ asset('img/no-img.jpg') }}" alt="Gambar Produk"
                            class="w-full h-36 object-fill rounded-lg border border-gray-200" />
                        @endif
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg montserrat-regular font-semibold mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-600 mb-4 text-sm montserrat-regular">
                            @if ($product->reviews->count() > 0)
                            {{ number_format($product->reviews->avg('rating'), 1) }}
                            <i class="bi bi-star-fill text-yellow-500"></i>
                            @else
                            Belum ada penilaian
                            @endif
                            @if ($product->reviews->count() > 10)
                            ({{ $product->reviews->count() }}+ Penilai)
                            @elseif ($product->reviews->count() > 0)
                            ({{ $product->reviews->count() }} Penilai)
                            @endif
                        </p>
                        <p class="text-gray-600 mb-4 text-sm montserrat-regular">Rp {{
                            number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-span-5 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center">
                <p class="text-gray-700 font-semibold">Belum ada produk.</p>
                <p class="text-gray-700">Tekan tombol “Tambah Produk” untuk menambahkan produk.</p>
            </div>
            @endforelse
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
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Nama Produk</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Kategori</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Harga</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Stok</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('produk.edit', $product->id) }}" class="hover:bg-gray-50 cursor-pointer">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 space-x-2 whitespace-nowrap">
                            @forelse ($product->product_categories as $product_category)
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-800 bg-gray-200 rounded-full">
                                {{ $product_category->category->name }}
                            </span>
                            @empty
                            -
                            @endforelse
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">Rp. {{ number_format($product->price, 0, ',', '.')
                            }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $product->stock }}</td>
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
                <flux:heading size="lg">Riwayat Pembaruan Produk</flux:heading>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @foreach($activityLogs as $log)
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