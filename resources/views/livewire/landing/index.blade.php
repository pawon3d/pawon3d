<div>
    <!-- Hero Section with Animation -->
    <section class="px-4 py-20 mb-4 bg-blue-50">
        <div class="flex flex-col lg:flex-row items-center gap-8">
            <div class="lg:w-3/5 space-y-6">
                <h3 class="text-xl font-semibold text-blue-600 animate-slideInLeft ml-2">
                    {{ ($storeProfile->name) ?? 'Pawon3D' }}
                </h3>
                @php
                // Ambil hero title dari storeProfile, jika tidak ada gunakan default
                $heroTitle = $storeProfile->tagline ?? 'Kue Rumahan Lezat, Sehangat Pelukan Ibu';

                // Pecah string menjadi array berdasarkan spasi
                $words = explode(' ', $heroTitle);
                $wordCount = count($words);

                // Jika jumlah kata lebih dari 2, pisahkan menjadi dua bagian
                if ($wordCount > 2) {
                $before = implode(' ', array_slice($words, 0, -2)); // Semua kata kecuali 2 kata terakhir
                $lastTwo = implode(' ', array_slice($words, -2)); // 2 kata terakhir
                } else {
                // Jika hanya ada 2 kata atau kurang, tampilkan seluruhnya di dalam span
                $before = '';
                $lastTwo = $heroTitle;
                }
                @endphp

                <h1 class="text-5xl lg:text-6xl leading-tight animate-slideInLeft pacifico-regular">
                    {{ $before }}
                    <span class="text-blue-600">{{ $lastTwo }}</span>
                </h1>

                <div class="flex gap-4 lg:text-xl mt-16">
                    <a href="https://wa.me/{{ $storeProfile->contact ?? '628123456789' }}" target="_blank"
                        class="px-8 py-4 border-2 border-gray-400 rounded-md hover:bg-gray-200 transition-all">
                        Pesan Sekarang
                    </a>
                    <a href="#menu" class="px-8 py-4 transition-all">
                        Pelajari Selengkapnya
                        <i class="bi bi-arrow-right ml-4"></i>
                    </a>
                </div>
            </div>
            {{-- <div class="w-1/2 mt-12 lg:mt-0">
                <div
                    class="overflow-hidden shadow-xl transform hover:scale-105 transition-transform duration-500 rounded-full">
                    @if (!empty($storeProfile->hero_image))
                    <img src="{{ asset('storage/' . $storeProfile->hero_image) }}" alt="Hero Image"
                        class="w-full h-full object-cover rounded-full">
                    @else
                    <img src="/assets/images/homepage/hero.jpeg" alt="Kue dan camilan Pawon3D"
                        class="w-full h-auto rounded-full object-cover">
                    @endif
                </div>
            </div> --}}
        </div>
    </section>

    <!-- Featured Section -->
    <section class="pt-8 pb-8">
        <div class="mx-auto px-4">
            <h1 class="text-4xl pacifico-regular font-bold text-center space-y-4 mb-8">
                Produk Unggulan Kami
            </h1>
            <p class="text-lg text-gray-600 m-auto text-center mb-8 lg:w-3/5">
                Pilihan kue yang paling disukai pembeli! Dibuat dari bahan berkualitas dan rasa yang bikin nagih,
                siap menemani hari-harimu.
            </p>

            @php
            $colCount = $products->count();
            @endphp
            <div class="grid md:grid-cols-{{ $colCount > 4 ? 4 : $colCount }} gap-4 overflow-x-auto">
                @forelse ($products as $product)
                <div class="bg-transparent p-8">
                    <div class="flex justify-center mb-4">
                        @if($product->product_image)
                        <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}"
                            class="w-40 h-40 rounded-md object-fill">
                        @else
                        <img src="{{ asset('/img/no-img.jpg')}}" alt="{{ $product->name }}"
                            class="w-40 h-40 rounded-md object-fill">
                        @endif
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg montserrat-regular font-semibold mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-600 mb-4 text-sm montserrat-regular">Rp {{
                            number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-600 mb-4 text-center">Tidak ada produk unggulan saat ini.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-blue-50 pt-8 pb-8">
        <div class="flex flex-col items-center justify-center text-center px-4">
            <h1 class="text-5xl lg:w-4xl pacifico-regular font-bold space-y-4 mb-8">
                Jadi Pelanggan dan Kumpulkan Poin!
            </h1>
            <p class="text-sm text-gray-500 m-auto montserrat-regular mb-8 lg:w-80">
                Dapatkan
                <span class="font-semibold">1 Poin</span>
                dengan belanja kelipatan
                <span class="font-semibold">Rp10.000.</span>
                Poin juga dapat dikumpulkan melalui review di
                <span class="font-semibold">story media sosial</span>
                dan review toko di
                <span class="font-semibold">Google Maps.</span>
            </p>

            <a href="#" class="px-8 py-4 border-2 border-gray-400 rounded-md hover:bg-gray-200 transition-all">
                Selengkapnya
            </a>
        </div>
    </section>

    <!-- Menu Section with Grid -->
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

        <div id="menu-content" class="max-h-96 overflow-y-auto">
            <div id="products-container"
                class="grid lg:grid-cols-5 grid-cols-3 gap-2 p-4 rounded-lg bg-white shadow-sm transition-all">
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

        <a href="/landing-produk" class="flex justify-center items-center">
            <button
                class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                Selengkapnya
            </button>
        </a>
    </section>



    <!-- Contact Section -->
    <section class="bg-blue-50 py-20">
        <div class="mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-transparent p-8">
                <div class="text-center mb-8">
                    <h2 class="text-4xl pacifico-regular font-bold mb-2">Lokasi Toko dan Wilayah Pemesanan</h2>
                </div>
                <div class="flex flex-col gap-6">
                    <div class="w-full flex flex-row gap-4 items-center justify-center">
                        <div class="flex flex-col items-start mb-4 md:mb-0">
                            <h3 class="text-lg font-semibold mb-2">Metode Pengambilan (Saat Ini)</h3>
                            <p class="text-gray-600 text-sm">
                                Ambil Sendiri, belum ada sistem pengantaran.
                            </p>
                        </div>
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!4v1742133851135!6m8!1m7!1sKwaGqQ5eD1Pjwm1wY_m6ng!2m2!1d-1.729048731821646!2d103.2726813742673!3f215.95130838744583!4f1.839142862723449!5f0.7820865974627469"
                            class="w-full h-[350px]" style="border: 0; border-radius: 10px;" allowfullscreen=""
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        <div class="flex flex-col items-end text-end mb-4 md:mb-0 ">
                            <h3 class="text-lg font-semibold mb-2">Wilayah Pemesanan (Saat Ini)</h3>
                            <p class="text-gray-600 text-sm">
                                Muara Bulian, Muara Tembesi, Muara Jambi, dan Kota Jambi.
                            </p>
                        </div>
                    </div>
                    <div class="w-full flex flex-col items-center justify-center">
                        <h3 class="text-lg font-semibold mb-2">Rincian Lokasi Toko</h3>
                        <div class="mb-4">
                            <p class="text-sm">Jl. Jenderal Sudirman Km.3, Muara Bulian, Batang Hari, Jambi.
                                Sebelah Puskesmas Muara Bulian</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto px-4 py-4 w-full">
        <div class="max-w-5xl mx-auto py-12 px-4 text-center">
            <h2 class="text-3xl font-bold font-[cursive] mb-6">Cara Pesan</h2>

            <!-- Tabs -->
            <div class="inline-flex bg-gray-200 rounded-full p-1 mb-10">
                <button
                    class="px-6 py-2 {{ $caraPesan === 'whatsapp' ? 'bg-black text-white' : 'text-black' }} rounded-full focus:outline-none"
                    wire:click="$set('caraPesan', 'whatsapp')">
                    WhatsApp
                </button>
                <button
                    class="px-6 py-2 {{ $caraPesan === 'toko' ? 'bg-black text-white' : 'text-black' }} rounded-full focus:outline-none"
                    wire:click="$set('caraPesan', 'toko')">
                    Langsung di Toko
                </button>
            </div>

            <!-- Konten Cara Pesan -->
            @if ($caraPesan === 'whatsapp')
            <div class="space-y-6 text-white font-medium">

                <div class="flex flex-col md:flex-row justify-center gap-6">
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-80 text-left">
                        <p class="text-sm">1. Pilih menu <strong>Pesanan Reguler</strong> atau <strong>Pesanan
                                Kotak</strong> di website
                        </p>
                    </div>
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-1/2 text-left">
                        <p class="text-sm">4. Lakukan <strong>konfirmasi pesanan</strong> dan
                            <strong>pembayaran</strong>, maka pesanan
                            akan dicatat (Pastikan pesanan berada di dalam <strong>wilayah pemesanan</strong>)
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-center gap-6">
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-80 text-left">
                        <p class="text-sm">2. Pilih <strong>satu</strong> atau lebih Produk yang diinginkan</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-96 text-left">
                        <p class="text-sm">5. Tunggu <strong>waktu pengambilan yang ditetapkan</strong> atau tunggu
                            <strong>kabar dari
                                penjual</strong>.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-center gap-6">
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-96 text-left">
                        <p class="text-sm">3. Tekan <strong>Checkout</strong> untuk beralih ke
                            <strong>WhatsApp</strong><br>
                            <span class="text-sm font-normal block mt-1">(Anda dapat melakukan konsultasi pembelian di
                                WhatsApp)</span>
                        </p>
                    </div>
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-96 text-left">
                        <p class="text-sm">6. Datang ke toko untuk <strong>mengambil pesanan</strong>, jika pesanan
                            belum lunas maka
                            lakukan pelunasan dan pesanan dapat diambil oleh pelanggan.</p>
                    </div>
                </div>
            </div>
            @else
            @endif
        </div>

    </section>


</div>