<div>
    <section class="mx-auto px-4 py-4 w-full" id="menu">
        <div class="text-center mb-4">
            <h2 class="text-4xl pacifico-regular font-bold mb-4 ">Jelajahi Produk</h2>
        </div>
        <div class="flex justify-center py-8 w-full">
            <div class="relative w-lg">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="text" wire:model.live="search"
                    class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari kue...">
            </div>
            <flux:button :loading="false" class="ml-2" variant="ghost">
                <flux:icon.funnel variant="mini" />
                <span>Filter</span>
            </flux:button>
        </div>
        <div class="flex items-center justify-between mt-4 mb-4 flex-row mx-8 bg-white rounded-lg shadow-xl">
            <div
                class="relative w-full py-8 {{ $method === 'pesanan-reguler' ? 'bg-[#222222] text-[#F8F4E1] border-b-4 border-b-[#E9BD8C]' : 'text-gray-800' }} hover:bg-[#222222] hover:text-[#F8F4E1] hover:border-b-4 hover:border-b-[#E9BD8C] transition-colors rounded-lg">
                <input type="radio" name="method" id="pesanan-reguler" value="pesanan-reguler" wire:model.live="method"
                    class="absolute opacity-0 w-0 h-0">
                <label for="pesanan-reguler" class="cursor-pointer">
                    <div class="w-full flex flex-col items-center">
                        <flux:icon icon="cake" class=" size-8" />
                        <span class="text-center hidden md:block">Pesanan Kue Reguler</span>
                    </div>
                </label>
            </div>
            <div
                class="relative w-full py-8 {{ $method === 'pesanan-kotak' ? 'bg-[#222222] text-[#F8F4E1] border-b-4 border-b-[#E9BD8C]' : 'text-gray-800' }} hover:bg-[#222222] hover:text-[#F8F4E1] hover:border-b-4 hover:border-b-[#E9BD8C] transition-colors rounded-lg">
                <input type="radio" name="method" id="pesanan-kotak" value="pesanan-kotak" wire:model.live="method"
                    class="absolute opacity-0 w-0 h-0">
                <label for="pesanan-kotak" class="cursor-pointer">
                    <div class="w-full flex flex-col items-center">
                        <flux:icon icon="package-open" class="size-8" />
                        <span class="text-center hidden md:block">Pesanan Kue Kotak</span>
                    </div>
                </label>
            </div>

            <div
                class="relative w-full py-8 {{ $method === 'siap-beli' ? 'bg-[#222222] text-[#F8F4E1] border-b-4 border-b-[#E9BD8C]' : 'text-gray-800' }} hover:bg-[#222222] hover:text-[#F8F4E1] hover:border-b-4 hover:border-b-[#E9BD8C] transition-colors rounded-lg">
                <input type="radio" name="method" id="siap-beli" value="siap-beli" wire:model.live="method"
                    class="absolute opacity-0 w-0 h-0">
                <label for="siap-beli" class="cursor-pointer">
                    <div class=" w-full flex flex-col items-center">
                        <flux:icon icon="dessert" class="size-8" />
                        <span class="text-center hidden md:block">Siap Saji</span>
                    </div>
                </label>
            </div>
        </div>
        <!-- Menu Items -->
        <div class="mb-4 mt-8">
            <ul class="flex flex-nowrap gap-4 overflow-x-auto scroll-hide pb-3 px-8">
                <!-- Tambahkan opsi 'Semua' di awal -->
                <li class="flex-shrink-0 ml-4">
                    <button
                        class="inline-block px-2 py-2 md:px-4 text-xs md:text-sm border border-gray-800 {{ $categorySelected == 'semua' ? 'bg-gray-600 text-white' : 'bg-gray-200' }} rounded-full hover:text-white hover:border-gray-800 hover:bg-gray-600 cursor-pointer transition-colors whitespace-nowrap"
                        wire:click="$set('categorySelected', 'semua')">
                        Semua
                    </button>
                </li>

                @foreach ($categories as $category)
                <li class="flex-shrink-0 {{ $loop->last ? 'mr-4' : '' }}">
                    <button
                        class="inline-block px-2 py-2 md:px-4 text-xs md:text-sm border border-gray-800 {{ $categorySelected == $category->name ? 'bg-gray-600 text-white' : 'bg-gray-200' }} rounded-full hover:text-white hover:border-gray-800 hover:bg-gray-600 cursor-pointer transition-colors whitespace-nowrap"
                        wire:click="$set('categorySelected', '{{ $category->name }}')">
                        {{ $category->name }}
                    </button>
                </li>
                @endforeach
            </ul>
        </div>

        <div id="menu-content">
            <div id="products-container" class="grid lg:grid-cols-5 grid-cols-3 gap-2 p-4  transition-all">
                @forelse ($exploreProducts as $product)
                <div class="mb-6">
                    <div class="flex justify-center">
                        @if($product->product_image)
                        <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}"
                            class="w-full h-40 rounded-lg border-gray-400 border object-fill">
                        @else
                        <img src="{{ asset('/img/no-img.jpg')}}" alt="{{ $product->name }}"
                            class="w-full h-40 rounded-lg border-gray-400 border object-fill">

                        @endif
                    </div>
                    <div class="p-4 bg-white text-center">
                        <h3 class="text-xs md:text-lg font-semibold text-gray-800 mb-2">{{ $product->name }}
                        </h3>
                        <div class="flex justify-center items-center">
                            <p class="text-blue-600 font-medium text-xs md:text-md">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    Tidak ada produk tersedia di kategori atau metode ini
                </div>
                @endforelse

                <!-- Pagination -->
                <div class="pagination col-span-full mt-4">
                    {{ $exploreProducts->links(data: ['scrollTo' => false]) }}
                </div>
            </div>

        </div>
    </section>
</div>