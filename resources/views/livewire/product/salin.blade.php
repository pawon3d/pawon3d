<div>
    <div class="flex items-center mb-4">
        <a href="{{ route('produk') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
            Kembali
        </a>
        <h1 class="text-xl font-bold">Salin Produk ke {{ $title }}</h1>

    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Pilih satu atau beberapa produk dari metode yang diinginkan, lalu salin produk. Anda dapat mengubah
                informasi produk tersalin tersebut seperti nama, deskripsi, dan harga produk.
            </p>
        </div>
    </div>


    <div class="flex items-center justify-between mt-4 mb-4 flex-row w-full">
        @if ($toMethod === 'pesanan-reguler')
            <div class="relative w-full">
                <input type="radio" name="fromMethod" id="pesanan-kotak" value="pesanan-kotak"
                    wire:model.live="fromMethod" class="absolute opacity-0 w-0 h-0">
                <label for="pesanan-kotak" class="cursor-pointer">
                    <div
                        class="{{ $fromMethod === 'pesanan-kotak' ? 'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
                        <flux:icon icon="cube" class="size-8" />
                        <span class="text-center hidden md:block">Pesanan Kue Kotak</span>
                    </div>
                </label>
            </div>

            <div class="relative w-full">
                <input type="radio" name="fromMethod" id="siap-beli" value="siap-beli" wire:model.live="fromMethod"
                    class="absolute opacity-0 w-0 h-0">
                <label for="siap-beli" class="cursor-pointer">
                    <div
                        class="{{ $fromMethod === 'siap-beli' ? 'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
                        <flux:icon icon="dessert" class="size-8" />
                        <span class="text-center hidden md:block">Kue Siap Beli</span>
                    </div>
                </label>
            </div>
        @elseif ($toMethod === 'pesanan-kotak')
            <div class="relative w-full">
                <input type="radio" name="fromMethod" id="pesanan-reguler" value="pesanan-reguler"
                    wire:model.live="fromMethod" class="absolute opacity-0 w-0 h-0">
                <label for="pesanan-reguler" class="cursor-pointer">
                    <div
                        class="{{ $fromMethod === 'pesanan-reguler' ? 'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
                        <flux:icon icon="cake" class=" size-8" />
                        <span class="text-center hidden md:block">Pesanan Kue Reguler</span>
                    </div>
                </label>
            </div>

            <div class="relative w-full">
                <input type="radio" name="fromMethod" id="siap-beli" value="siap-beli" wire:model.live="fromMethod"
                    class="absolute opacity-0 w-0 h-0">
                <label for="siap-beli" class="cursor-pointer">
                    <div
                        class="{{ $fromMethod === 'siap-beli' ? 'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
                        <flux:icon icon="dessert" class="size-8" />
                        <span class="text-center hidden md:block">Kue Siap Beli</span>
                    </div>
                </label>
            </div>
        @elseif ($toMethod === 'siap-beli')
            <div class="relative w-full">
                <input type="radio" name="fromMethod" id="pesanan-reguler" value="pesanan-reguler"
                    wire:model.live="fromMethod" class="absolute opacity-0 w-0 h-0">
                <label for="pesanan-reguler" class="cursor-pointer">
                    <div
                        class="{{ $fromMethod === 'pesanan-reguler' ? 'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
                        <flux:icon icon="cake" class=" size-8" />
                        <span class="text-center hidden md:block">Pesanan Kue Reguler</span>
                    </div>
                </label>
            </div>
            <div class="relative w-full">
                <input type="radio" name="fromMethod" id="pesanan-kotak" value="pesanan-kotak"
                    wire:model.live="fromMethod" class="absolute opacity-0 w-0 h-0">
                <label for="pesanan-kotak" class="cursor-pointer">
                    <div
                        class="{{ $fromMethod === 'pesanan-kotak' ? 'border-b-2 border-b-gray-600' : 'text-gray-800' }}  hover:border-b-2 hover:border-b-gray-600 w-full transition-colors flex flex-col items-center">
                        <flux:icon icon="cube" class="size-8" />
                        <span class="text-center hidden md:block">Pesanan Kue Kotak</span>
                    </div>
                </label>
            </div>
        @endif
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
            <flux:button icon="archive-box" type="button" variant="primary" wire:click.prevent="saveCopy">
                Simpan Salinan
            </flux:button>
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
            <div x-data="{ selectedProducts: @entangle('selectedProducts') }">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-4">
                    @forelse($products as $product)
                        <div class="p-4 text-center">
                            <!-- Gambar & Info -->
                            <div class="flex justify-center mb-4 relative">
                                <img src="{{ $product->product_image ? asset('storage/' . $product->product_image) : asset('img/no-img.jpg') }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-36 object-fill rounded-lg border border-gray-200" />
                                <!-- Status -->
                                <div class="absolute top-2 left-2 flex gap-2">
                                    @if ($product->is_recommended)
                                        <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full">
                                            <flux:icon icon="heart" variant="mini" class="size-3" />
                                        </span>
                                    @endif
                                    <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full">
                                        <flux:icon icon="{{ $product->is_active ? 'eye' : 'eye-slash' }}"
                                            variant="mini" class="size-3" />
                                    </span>
                                </div>
                                <div class="absolute top-2 right-2 flex gap-2">
                                    <input type="checkbox" :value="'{{ $product->id }}'" x-model="selectedProducts"
                                        class="
                                form-checkbox h-4 w-4 text-blue-600 border-0 rounded-full focus:ring-0 focus:ring-offset-0 not-checked:bg-gray-400">
                                    <label for="selectedProducts" class="sr-only">Pilih Produk</label>
                                </div>
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
                                        (10+ Penilai)
                                    @elseif ($product->reviews->count() > 0)
                                        ({{ $product->reviews->count() }} Penilai)
                                    @endif
                                </p>
                                <p class="text-gray-600 mb-4 text-sm montserrat-regular">
                                    Rp
                                    {{ number_format($product->pcs > 1 ? $product->pcs_price : $product->price, 0, ',', '.') }}
                                </p>
                            </div>
                            <flux:button class="w-full" variant="primary" type="button"
                                href="{{ route('produk.edit', $product->id) }}">
                                Lihat
                            </flux:button>
                        </div>
                    @empty
                        <div
                            class="col-span-5 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center">
                            <p class="text-gray-700 font-semibold">Belum ada produk.</p>
                            <p class="text-gray-700">Tekan tombol “Tambah Produk” untuk menambahkan produk.</p>
                        </div>
                    @endforelse
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
                                <tr x-data="{ selectedProducts: @entangle('selectedProducts') }">
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="checkbox" :value="'{{ $product->id }}'"
                                            x-model="selectedProducts"
                                            class="
                                form-checkbox h-4 w-4 text-black border-0 rounded focus:ring-0 focus:ring-offset-0 not-checked:bg-gray-400">
                                        <label for="selectedProducts" class="sr-only">Pilih Produk</label>
                                    </td>
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
                <flux:heading size="lg">Riwayat Pembaruan Produk</flux:heading>
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
