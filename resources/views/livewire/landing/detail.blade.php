<div class="bg-[#EAEAEA] w-full min-h-screen">
    {{-- Back Button & Title --}}
    <div class="flex items-center gap-4 px-16 py-8">
        <a href="{{ route('landing-produk') }}" wire:navigate
            class="bg-[#313131] text-[#F6F6F6] px-6 py-2 rounded-2xl shadow flex items-center gap-2 hover:bg-[#252324] transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                    clip-rule="evenodd" />
            </svg>
            <span class="font-semibold text-base">Kembali</span>
        </a>
        <span class="text-[#666666] font-medium text-xl">Deskripsi Produk</span>
    </div>

    {{-- Main Content --}}
    <div class="px-16 pb-8 flex flex-col gap-8 max-w-[1280px]">
        {{-- Product Detail Card --}}
        <div class="bg-[#FAFAFA] rounded-2xl p-8 shadow flex flex-col gap-12">
            {{-- Top Section: Info & Image --}}
            <div class="flex items-start justify-between">
                {{-- Left: Product Info --}}
                <div class="flex flex-col gap-8 flex-1">
                    {{-- Name & Price --}}
                    <div class="flex flex-col gap-3">
                        <div class="flex items-start gap-2">
                            <h1 class="font-medium text-[30px] text-[#666666]">{{ $product->name }}</h1>
                            @if ($product->unit && $product->unit->name)
                                <span class="font-medium text-[30px] text-[#666666]">
                                    ({{ $product->quantity }} {{ $product->unit->name }})
                                </span>
                            @endif
                        </div>
                        <p class="font-semibold text-2xl text-[#666666]">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>
                    </div>

                    {{-- Categories --}}
                    @if ($product->product_categories && $product->product_categories->count() > 0)
                        <div class="flex flex-col gap-5 text-[#666666] max-w-md">
                            <p class="font-medium text-lg">Kategori Produk</p>
                            <p class="font-normal text-base text-justify">
                                {{ $product->product_categories->pluck('category.name')->join(', ') }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Right: Product Image --}}
                <div class="rounded-2xl shadow overflow-hidden w-[239px] h-[172px] flex-shrink-0">
                    @if ($product->product_image)
                        <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover">
                    @else
                        <img src="{{ asset('/img/no-img.jpg') }}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover">
                    @endif
                </div>
            </div>

            {{-- Bottom Section: Description --}}
            @if ($product->description)
                <div class="flex flex-col gap-5 text-[#666666]">
                    <p class="font-medium text-lg">Deskripsi Produk</p>
                    <p class="font-normal text-base text-justify leading-relaxed">
                        {{ $product->description }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Related Products Section --}}
        <div class="bg-[#FAFAFA] rounded-2xl p-8 shadow flex flex-col gap-8">
            {{-- Header --}}
            <div class="flex items-center justify-between h-10">
                <p class="font-medium text-lg text-[#666666]">Produk Lainnya</p>
                <a href="{{ route('landing-produk') }}" wire:navigate
                    class="bg-[#74512D] text-[#F6F6F6] px-6 py-2 rounded-2xl shadow hover:bg-[#5c3f23] transition-colors">
                    <span class="font-semibold text-base">Lihat Lainnya</span>
                </a>
            </div>

            {{-- Products Grid --}}
            <div class="overflow-y-auto max-h-[542px] pr-2">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach ($relatedProducts as $relatedProduct)
                        <a href="{{ route('landing-produk-detail', $relatedProduct->id) }}" wire:navigate
                            class="flex flex-col gap-4 pb-6 max-w-[210px] min-w-[180px] hover:scale-105 transition-transform">
                            {{-- Image --}}
                            <div class="rounded-2xl shadow overflow-hidden w-[182px] h-[119px]">
                                @if ($relatedProduct->product_image)
                                    <img src="{{ asset('storage/' . $relatedProduct->product_image) }}"
                                        alt="{{ $relatedProduct->name }}" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('/img/no-img.jpg') }}" alt="{{ $relatedProduct->name }}"
                                        class="w-full h-full object-cover">
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="px-4 flex flex-col gap-6">
                                {{-- Name & Quantity --}}
                                <div class="flex flex-col gap-2 min-h-[70px] items-center">
                                    <div class="flex flex-col gap-1 items-center w-full">
                                        <p class="font-medium text-base text-[#666666] text-center line-clamp-2 w-full">
                                            {{ $relatedProduct->name }}
                                        </p>
                                    </div>
                                    @if ($relatedProduct->unit && $relatedProduct->unit->name)
                                        <div class="flex items-start justify-center w-full">
                                            <span class="font-medium text-base text-[#666666]">(</span>
                                            <span
                                                class="font-medium text-base text-[#666666]">{{ $relatedProduct->quantity }}</span>
                                            <span class="font-medium text-base text-[#666666]">
                                                {{ $relatedProduct->unit->name }}</span>
                                            <span class="font-medium text-base text-[#666666]">)</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Price --}}
                                <div
                                    class="flex items-start justify-center font-semibold text-lg text-[#666666] text-center">
                                    <span>Rp</span>
                                    <span>{{ number_format($relatedProduct->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
