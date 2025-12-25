<div class="px-4 sm:px-0 py-4 sm:py-0">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
        <div class="flex items-center gap-4 w-full sm:w-auto">
            <a href="{{ route('transaksi') }}"
                class="w-full sm:w-auto mr-0 sm:mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center justify-center text-white"
                wire:navigate>
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block text-gray-600">Deskripsi Produk</h1>
        </div>
    </div>

    <div class="flex flex-col gap-6">
        {{-- Card Informasi Produk --}}
        <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="flex flex-col md:flex-row justify-between gap-6">
                <div class="flex flex-col gap-6 flex-1">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-2">
                            <h2 class="text-2xl md:text-3xl font-medium text-gray-600">
                                {{ $product->name }}
                            </h2>
                            <span class="text-2xl md:text-3xl font-medium text-gray-600">
                                ({{ $product->pcs ?? 1 }} pcs)
                            </span>
                        </div>
                        <p class="text-xl md:text-2xl font-semibold text-gray-600">
                            Rp{{ number_format($product->price, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-4">
                        <h3 class="text-lg font-medium text-gray-600">Kategori Produk</h3>
                        <div class="flex flex-wrap items-center gap-2">
                            @forelse ($product->product_categories as $pc)
                                <span class="text-gray-600">
                                    {{ $pc->category->name }}@if (!$loop->last)
                                        ,
                                    @endif
                                </span>
                                @empty
                                    <span class="text-gray-500">Tidak ada kategori</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start justify-center md:justify-end">
                        <img src="{{ $product->product_image ? asset('storage/' . $product->product_image) : asset('img/no-img.jpg') }}"
                            alt="{{ $product->name }}" class="w-60 h-44 object-cover rounded-xl shadow-sm">
                    </div>
                </div>

                <div class="flex flex-col gap-4 mt-6">
                    <h3 class="text-lg font-medium text-gray-600">Deskripsi Produk</h3>
                    <p class="text-gray-600 text-justify">
                        {{ $product->description ?? 'Tidak ada deskripsi' }}
                    </p>
                </div>
            </div>

            {{-- Card Produk Lainnya --}}
            @if ($relatedProducts->isNotEmpty())
                <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
                    <h3 class="text-lg font-medium text-gray-600 text-center sm:text-left">Produk Lainnya</h3>
                    <a href="{{ route('transaksi') }}"
                        class="w-full sm:w-auto px-6 py-2 bg-amber-800 text-white rounded-xl font-semibold hover:bg-amber-900 transition flex items-center justify-center"
                        wire:navigate>
                        Lihat Lainnya
                    </a>
                </div>

                <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6 justify-items-center">
                        @foreach ($relatedProducts as $relatedProduct)
                            <a href="{{ route('transaksi.rincian-produk', $relatedProduct->id) }}"
                                class="flex flex-col items-center gap-4 w-full max-w-[180px] pb-4 hover:opacity-80 transition"
                                wire:navigate>
                                <img src="{{ $relatedProduct->product_image ? asset('storage/' . $relatedProduct->product_image) : asset('img/no-img.jpg') }}"
                                    alt="{{ $relatedProduct->name }}"
                                    class="w-full h-28 object-cover rounded-xl shadow-sm">

                                <div class="flex flex-col items-center gap-2 px-2 w-full">
                                    <div class="flex flex-col items-center gap-1 min-h-[70px]">
                                        <p class="text-gray-600 font-medium text-center line-clamp-2">
                                            {{ $relatedProduct->name }}
                                        </p>
                                        <p class="text-gray-600 font-medium text-center">
                                            ({{ $relatedProduct->pcs ?? 1 }} pcs)
                                        </p>
                                    </div>
                                    <p class="text-gray-600 font-semibold text-lg">
                                        Rp{{ number_format($relatedProduct->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
