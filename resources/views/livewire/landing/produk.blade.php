<div class="bg-[#EAEAEA] min-h-screen">
    {{-- Back Button & Title --}}
    <div class="flex items-center gap-4 px-16 py-8">
        <a href="{{ route('home') }}" wire:navigate
            class="bg-[#313131] text-[#F6F6F6] px-6 py-2 rounded-2xl shadow flex items-center gap-2 hover:bg-[#252324] transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                    clip-rule="evenodd" />
            </svg>
            <span class="font-semibold text-base">Kembali</span>
        </a>
        <span class="text-[#666666] font-medium text-xl">Daftar Menu</span>
    </div>

    {{-- Main Content --}}
    <div class="flex flex-col gap-[30px] items-center w-full px-16 pb-16">
        {{-- Method Selector --}}
        <div class="w-full max-w-[1150px]">
            <div class="flex items-center bg-[#fafafa] rounded-[15px] shadow-sm h-[120px]">
                <button wire:click="$set('method', 'pesanan-reguler')"
                    class="flex-1 h-full flex flex-col items-center justify-center gap-1 rounded-l-[15px] {{ $method === 'pesanan-reguler' ? 'bg-[#fafafa] border-b-4 border-[#74512d]' : 'bg-[#fafafa]' }}">
                    <flux:icon icon="cake"
                        class="size-8 {{ $method === 'pesanan-reguler' ? 'text-[#74512d]' : 'text-[#6c7068] opacity-90' }}" />
                    <span
                        class="text-[16px] montserrat-{{ $method === 'pesanan-reguler' ? 'bold' : 'medium' }} {{ $method === 'pesanan-reguler' ? 'text-[#74512d]' : 'text-[#6c7068] opacity-90' }}">Pesanan
                        Reguler</span>
                </button>

                <button wire:click="$set('method', 'pesanan-kotak')"
                    class="flex-1 h-full flex flex-col items-center justify-center gap-1 {{ $method === 'pesanan-kotak' ? 'bg-[#fafafa] border-b-4 border-[#74512d]' : 'bg-[#fafafa]' }}">
                    <flux:icon icon="package-open"
                        class="size-8 {{ $method === 'pesanan-kotak' ? 'text-[#74512d]' : 'text-[#6c7068] opacity-90' }}" />
                    <span
                        class="text-[16px] montserrat-{{ $method === 'pesanan-kotak' ? 'bold' : 'medium' }} {{ $method === 'pesanan-kotak' ? 'text-[#74512d]' : 'text-[#6c7068] opacity-90' }}">Pesanan
                        Kotak</span>
                </button>

                <button wire:click="$set('method', 'siap-beli')"
                    class="flex-1 h-full flex flex-col items-center justify-center gap-1 rounded-r-[15px] {{ $method === 'siap-beli' ? 'bg-[#fafafa] border-b-4 border-[#74512d]' : 'bg-[#fafafa]' }}">
                    <flux:icon icon="dessert"
                        class="size-8 {{ $method === 'siap-beli' ? 'text-[#74512d]' : 'text-[#6c7068] opacity-90' }}" />
                    <span
                        class="text-[16px] montserrat-{{ $method === 'siap-beli' ? 'bold' : 'medium' }} {{ $method === 'siap-beli' ? 'text-[#74512d]' : 'text-[#6c7068] opacity-90' }}">Siap
                        Saji</span>
                </button>
            </div>
        </div>

        {{-- Products Container --}}
        <div class="w-full max-w-[1150px]">
            <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-8">
                {{-- Search and Filter --}}
                <div class="flex items-center gap-4 mb-8 w-full">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-4 h-4 text-[#666666]" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="w-full py-2.5 pl-10 pr-4 text-[16px] montserrat-medium text-[#959595] border-[0.5px] border-[#666666] rounded-[20px] bg-white focus:ring-0 focus:border-[#666666]"
                            placeholder="Cari Produk">
                    </div>
                    <button class="flex items-center gap-2 px-2 py-2.5">
                        <svg class="w-6 h-6 text-[#666666]" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" />
                        </svg>
                        <span class="text-[16px] montserrat-medium text-[#666666]">Filter</span>
                    </button>
                </div>

                {{-- Products Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-[15px]">
                    @forelse ($exploreProducts as $product)
                        <a href="{{ route('landing-produk-detail', $product->id) }}" wire:navigate
                            class="flex flex-col gap-[15px] pb-[25px] max-w-[210px] min-w-[180px] hover:scale-105 transition-transform">
                            <div class="w-full h-[119px] rounded-[15px] shadow-sm overflow-hidden bg-[#eaeaea]">
                                @if ($product->product_image)
                                    <img src="{{ asset('storage/' . $product->product_image) }}"
                                        alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('/img/no-img.jpg') }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="text-center px-[15px] flex flex-col gap-[25px]">
                                <div class="min-h-[70px] flex flex-col gap-[10px] items-center">
                                    <h3 class="text-[16px] montserrat-medium text-[#666666] line-clamp-2">
                                        {{ $product->name }}</h3>
                                    @if ($product->pcs)
                                        <span class="text-[16px] montserrat-medium text-[#666666]">({{ $product->pcs }}
                                            pcs)</span>
                                    @endif
                                </div>
                                <p class="text-[18px] montserrat-semibold text-[#666666]">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full text-center py-8 text-[#666666]">
                            Tidak ada produk tersedia
                        </div>
                    @endforelse
                </div>

                {{-- Load More Button --}}
                @if ($hasMore)
                    <div class="flex justify-center mt-8">
                        <button wire:click="loadMore"
                            class="px-9 py-4 bg-[#74512d] rounded-[15px] montserrat-semibold text-[16px] text-white hover:bg-[#5d3f23] transition-all inline-flex items-center gap-2">
                            <span wire:loading.remove wire:target="loadMore">Lihat Lebih Banyak</span>
                            <span wire:loading wire:target="loadMore">Memuat...</span>
                        </button>
                    </div>
                @endif

                {{-- Product Count Info --}}
                <div class="text-center mt-4 text-[14px] montserrat-medium text-[#959595]">
                    Menampilkan {{ $exploreProducts->count() }} dari {{ $totalProducts }} produk
                </div>
            </div>
        </div>
    </div>
</div>
