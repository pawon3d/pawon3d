<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('transaksi') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white"
                wire:navigate>
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">Rincian Produk</h1>
        </div>
        <div class="flex gap-2 items-center justify-end-safe">
            <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
            </button>
        </div>
    </div>

    <div class="w-full flex flex-row gap-4 justify-between">
        <div class="flex flex-col gap-2">
            <h2 class="text-lg font-semibold">
                {{ $product->name }}
            </h2>
            <p class="text-gray-500">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </p>
            <div class="flex flex-col gap-2">
                <h3 class="text-sm font-semibold">
                    Kategori Produk
                </h3>
                <div class="flex items-center gap-2">
                    @foreach ($product->product_categories as $pc)
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">
                            {{ $pc->category->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex items-center">
            <img src="{{ $product->product_image ? asset('storage/' . $product->product_image) : asset('img/no-img.jpg') }}"
                alt="{{ $product->name }}" class="w-60 h-32 object-fill rounded">
        </div>
    </div>
    <div class="w-full mt-4">
        <h3 class="text-sm font-semibold">Deskripsi Produk</h3>
        <p class="text-gray-500">
            {{ $product->description ?? 'Tidak ada deskripsi' }}
        </p>
    </div>

    <div class="w-full mt-4">
        <h3 class="text-sm font-semibold mb-4">Penilaian Produk</h3>
        <div class="flex flex-row justify-between p-4 items-center gap-2 rounded border border-gray-200">
            <div class="flex items-center gap-2">
                <flux:icon.star variant="mini" class="text-yellow-500" />
                <span
                    class="text-gray-800 font-semibold">{{ $product->reviews->avg('rating') ?? 'Belum ada penilaian' }}/5.0</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-gray-500">Dari {{ $product->reviews->count() ?? 0 }} penilaian</span>
            </div>
        </div>
    </div>

    <div class="w-full flex flex-row justify-between items-center">
        <div class="flex items-center gap-2">
            Semua Nilai ({{ $product->reviews->count() }})
        </div>
        <div class="flex items-center gap-2">
            <flux:dropdown>
                <flux:button variant="ghost">
                    Urutkan Berdasarkan
                    <flux:icon.chevron-down variant="mini" />
                </flux:button>
                <flux:menu>
                    <flux:menu.radio.group wire:model.live="sortBy">
                        <flux:menu.radio value="rating">Rating</flux:menu.radio>
                        <flux:menu.radio value="created_at">Tanggal Dibuat</flux:menu.radio>
                    </flux:menu.radio.group>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>


    <div class="mt-4">
        @if ($product->reviews->isEmpty())
            <p class="text-gray-500">Belum ada penilaian untuk produk ini.</p>
        @else
            <div class="space-y-4">
                @foreach ($product->reviews as $review)
                    <div class="p-4 border-b rounded-lg bg-white shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:icon.user variant="mini" class="text-gray-500" />
                            <span class="font-semibold">{{ \Illuminate\Support\Str::uuid() }}</span>
                            <span
                                class="text-gray-500 text-xs">({{ \Carbon\Carbon::parse($review->created_at)->format('d-m-Y') }})</span>
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            <flux:icon.star variant="mini" class="text-yellow-500" />
                            <span class="text-gray-800 font-semibold">{{ $review->rating }}/5.0</span>
                        </div>
                        <p class="text-gray-500">
                            {{ $review->comment ?? 'Tidak ada komentar' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
